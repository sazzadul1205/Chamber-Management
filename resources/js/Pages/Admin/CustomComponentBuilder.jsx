import React, { useEffect, useRef, useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import {
  HiOutlineTemplate,
  HiOutlineArrowLeft,
  HiOutlineCode,
  HiOutlineCube,
  HiOutlineInformationCircle,
  HiOutlineSearch,
  HiOutlineViewGrid,
  HiOutlineViewList,
  HiOutlineStar,
  HiOutlineClock,
  HiOutlineExclamation,
  HiOutlineCheckCircle,
} from "react-icons/hi";
import { FiSave, FiSettings, FiDownload, FiCopy, FiGrid, FiList, FiFolderPlus } from "react-icons/fi";
import { MdOutlineCategory } from "react-icons/md";
import grapesjs from "grapesjs";
import "grapesjs/dist/css/grapes.min.css";
import axios from 'axios';

// Import component blocks
import { componentBlocks } from "./Builder/ComponentBlocks";

export default function CustomComponentBuilder({ availableComponents, savedComponents }) {
  const editorRef = useRef(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [viewMode, setViewMode] = useState("grid");
  const [exportFormat, setExportFormat] = useState("jsx");
  const [showExportModal, setShowExportModal] = useState(false);
  const [exportedCode, setExportedCode] = useState("");
  const [showSaveModal, setShowSaveModal] = useState(false);
  const [componentName, setComponentName] = useState("");
  const [folderExists, setFolderExists] = useState(null);
  const [isSaving, setIsSaving] = useState(false);
  const [saveSuccess, setSaveSuccess] = useState(false);
  const [saveError, setSaveError] = useState(null);
  const [isEditorInitialized, setIsEditorInitialized] = useState(false);

  // Get unique categories
  const categories = ["All", ...new Set(componentBlocks.map(block => block.category))];

  // Filter blocks based on search and category
  const filteredBlocks = componentBlocks.filter(block => {
    const matchesSearch = block.label.toLowerCase().includes(searchTerm.toLowerCase()) ||
      block.category.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = selectedCategory === "All" || block.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  // Initialize editor only once
  useEffect(() => {
    if (isEditorInitialized) return;

    const editor = grapesjs.init({
      container: "#gjs-editor",
      height: "650px",
      width: "100%",
      fromElement: false,
      storageManager: false,
      undoManager: { trackSelection: true },
      selectorManager: { componentFirst: true },
      blockManager: {
        appendTo: "#blocks",
        blocks: [] // Start with empty blocks
      },
      styleManager: {
        sectors: [
          {
            name: "Layout",
            open: false,
            buildProps: ["display", "position", "width", "height", "max-width", "min-width", "margin", "padding"],
          },
          {
            name: "Flex",
            open: false,
            buildProps: ["flex-direction", "justify-content", "align-items", "flex-wrap", "align-content", "order", "flex"],
          },
          {
            name: "Typography",
            open: false,
            buildProps: [
              "font-family",
              "font-size",
              "font-weight",
              "letter-spacing",
              "color",
              "line-height",
              "text-align",
              "text-decoration",
              "text-transform",
            ],
          },
          {
            name: "Background",
            open: false,
            buildProps: ["background-color", "background-image", "background-repeat", "background-position", "background-size"],
          },
          {
            name: "Effects",
            open: false,
            buildProps: ["border-radius", "box-shadow", "opacity", "transition", "transform"],
          },
        ],
      },
      canvas: {
        styles: [
          'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
        ],
      },
    });

    editorRef.current = editor;
    setIsEditorInitialized(true);

    // Add custom commands
    editor.Commands.add("export-jsx", {
      run: handleExport,
    });

    editor.Commands.add("preview-component", {
      run(editor) {
        editor.runCommand("preview");
      },
    });

    return () => {
      editor.destroy();
      setIsEditorInitialized(false);
    };
  }, []); // Empty dependency array - only run once

  // Update blocks when filteredBlocks changes
  useEffect(() => {
    if (!editorRef.current || !isEditorInitialized) return;

    const editor = editorRef.current;
    const blockManager = editor.Blocks;

    // Clear existing blocks
    blockManager.getAll().reset();

    // Add filtered blocks
    filteredBlocks.forEach(block => {
      blockManager.add(block.id, {
        label: block.label,
        content: block.content,
        category: block.category,
      });
    });
  }, [filteredBlocks, isEditorInitialized]);

  const handleExport = () => {
    const html = editorRef.current.getHtml();
    const css = editorRef.current.getCss();

    let exportedContent = "";

    if (exportFormat === "jsx") {
      exportedContent = html.replace(/class=/g, "className=").replace(/for=/g, "htmlFor=");
    } else {
      exportedContent = html;
    }

    setExportedCode(exportedContent);
    setShowExportModal(true);
  };

  const handleSaveComponent = async () => {
    if (!componentName.trim()) {
      setSaveError("Please enter a component name");
      return;
    }

    // Format component name (PascalCase)
    const formattedName = componentName
      .trim()
      .replace(/[^a-zA-Z0-9]/g, ' ')
      .split(' ')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join('')
      .replace(/[^a-zA-Z0-9]/g, '');

    if (!formattedName) {
      setSaveError("Invalid component name");
      return;
    }

    setIsSaving(true);
    setSaveError(null);

    try {
      // Get the HTML from the editor
      const html = editorRef.current.getHtml();

      // Extract ONLY the content inside the body tag
      let content = html;
      const bodyMatch = html.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
      if (bodyMatch && bodyMatch[1]) {
        content = bodyMatch[1].trim();
      } else {
        // If no body tag, try to clean the HTML
        content = html
          .replace(/<!DOCTYPE[^>]*>/i, '')
          .replace(/<html[^>]*>|<\/html>/gi, '')
          .replace(/<head[^>]*>[\s\S]*?<\/head>/gi, '')
          .trim();
      }

      // If content is empty, show error
      if (!content || content === '' || content === '<body></body>') {
        setSaveError("Canvas is empty. Please add some components first.");
        setIsSaving(false);
        return;
      }

      // First check if Custom folder exists in Pages/Home
      const checkResponse = await axios.post('/admin/check-custom-folder');
      setFolderExists(checkResponse.data.exists);

      // Convert to JSX
      const jsxContent = content
        .replace(/class=/g, "className=")
        .replace(/for=/g, "htmlFor=");

      // Format the JSX content with proper indentation
      const formattedJsxContent = jsxContent
        .split('\n')
        .map(line => '      ' + line)
        .join('\n');

      const componentCode = `import React from 'react';

const ${formattedName} = () => {
  return (
${formattedJsxContent}
  );
};

export default ${formattedName};`;

      // Save the component
      const saveResponse = await axios.post('/admin/save-component', {
        componentName: formattedName,
        componentCode: componentCode,
        html: content,
        jsx: jsxContent,
      });

      if (saveResponse.data.success) {
        setSaveSuccess(true);
        setTimeout(() => {
          setShowSaveModal(false);
          setSaveSuccess(false);
          setComponentName("");
        }, 2000);
      }
    } catch (error) {
      console.error('Error saving component:', error);
      setSaveError(error.response?.data?.message || 'Failed to save component');
    } finally {
      setIsSaving(false);
    }
  };

  const downloadComponent = () => {
    const blob = new Blob([exportedCode], { type: exportFormat === "jsx" ? "text/jsx" : "text/html" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `Component.${exportFormat === "jsx" ? "jsx" : "html"}`;
    link.click();
  };

  const copyToClipboard = () => {
    navigator.clipboard.writeText(exportedCode);
    alert("Copied to clipboard!");
  };

  const addCustomBlock = () => {
    const label = prompt("Enter block label:");
    if (!label) return;

    const category = prompt("Enter category:", "Custom");
    const content = prompt("Enter HTML content:");

    if (content) {
      const editor = editorRef.current;
      editor.BlockManager.add(`custom-${Date.now()}`, {
        label,
        content,
        category: category || "Custom",
      });
    }
  };

  const openSaveModal = () => {
    const html = editorRef.current.getHtml();

    // Extract body content
    let content = html;
    const bodyMatch = html.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
    if (bodyMatch && bodyMatch[1]) {
      content = bodyMatch[1].trim();
    }

    // Check if there's actual content
    if (!content || content === '' || content === '<body></body>' || html === '<body></body>') {
      alert('Please add some components to the canvas first');
      return;
    }

    setShowSaveModal(true);
    setComponentName("");
    setSaveError(null);
    setSaveSuccess(false);
  };

  return (
    <div className="text-black">
      <Head title="Custom Component Builder" />

      <div className="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100">
        <div className="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Header */}
          <div className="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div className="flex items-start space-x-4">
              <Link
                href={route("admin.page-builder")}
                className="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm"
              >
                <HiOutlineArrowLeft className="w-4 h-4 mr-2" />
                Back to Page Builder
              </Link>

              <div>
                <h1 className="text-3xl font-bold text-gray-900 flex items-center">
                  <div className="bg-gradient-to-br from-purple-500 to-pink-500 p-2 rounded-xl mr-3">
                    <HiOutlineCube className="w-6 h-6 text-white" />
                  </div>
                  Custom Component Builder
                </h1>
                <p className="mt-2 text-sm text-gray-600 flex items-center">
                  <HiOutlineInformationCircle className="w-4 h-4 mr-1 text-gray-400" />
                  Create and manage custom section components with 30+ pre-built templates
                </p>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <button
                onClick={addCustomBlock}
                className="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm"
              >
                <HiOutlineCode className="w-4 h-4 mr-2" />
                Add Custom Block
              </button>
              <button
                onClick={openSaveModal}
                className="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300"
              >
                <FiFolderPlus className="w-4 h-4" />
                <span>Save Component</span>
              </button>
            </div>
          </div>

          {/* Search and Filters */}
          <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
            <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
              <div className="flex items-center gap-4 w-full md:w-auto">
                <div className="relative flex-1 md:w-80">
                  <HiOutlineSearch className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                  <input
                    type="text"
                    placeholder="Search components..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  />
                </div>

                <div className="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                  <button
                    onClick={() => setViewMode("grid")}
                    className={`p-2.5 ${viewMode === "grid" ? "bg-purple-50 text-purple-600" : "text-gray-600 hover:bg-gray-50"}`}
                  >
                    <HiOutlineViewGrid className="w-5 h-5" />
                  </button>
                  <button
                    onClick={() => setViewMode("list")}
                    className={`p-2.5 ${viewMode === "list" ? "bg-purple-50 text-purple-600" : "text-gray-600 hover:bg-gray-50"}`}
                  >
                    <HiOutlineViewList className="w-5 h-5" />
                  </button>
                </div>
              </div>

              <div className="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                <MdOutlineCategory className="w-5 h-5 text-gray-400 mr-1" />
                {categories.map((category) => (
                  <button
                    key={category}
                    onClick={() => setSelectedCategory(category)}
                    className={`px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all
                      ${selectedCategory === category
                        ? "bg-purple-600 text-white shadow-md"
                        : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                      }`}
                  >
                    {category}
                    {category !== "All" && (
                      <span className="ml-2 text-xs">
                        ({componentBlocks.filter(b => b.category === category).length})
                      </span>
                    )}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Main Editor Area */}
          <div className="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden p-6 mb-8">
            <div className="flex flex-col lg:flex-row gap-4">
              {/* Blocks Panel */}
              <div className="lg:w-80 bg-gray-50 border border-gray-200 rounded-xl p-4 h-[650px] overflow-y-auto">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="font-semibold text-gray-900 flex items-center">
                    <HiOutlineTemplate className="w-5 h-5 mr-2 text-purple-600" />
                    Components ({filteredBlocks.length})
                  </h3>
                  <span className="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">
                    {selectedCategory}
                  </span>
                </div>

                <div id="blocks" className="space-y-2">
                  {/* Blocks will be injected here by GrapesJS */}
                </div>

                {filteredBlocks.length === 0 && (
                  <div className="text-center py-12">
                    <HiOutlineCube className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                    <p className="text-gray-500">No components found</p>
                    <p className="text-sm text-gray-400 mt-1">Try adjusting your search</p>
                  </div>
                )}
              </div>

              {/* Canvas */}
              <div className="flex-1 bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div className="bg-gray-50 border-b border-gray-200 px-4 py-2 flex items-center justify-between">
                  <span className="text-sm font-medium text-gray-700">Canvas Preview</span>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition">
                      <FiGrid className="w-4 h-4" />
                    </button>
                  </div>
                </div>
                <div id="gjs-editor"></div>
              </div>
            </div>
          </div>

          {/* Popular Components Grid */}
          <div className="border-t border-gray-200 bg-white/50 backdrop-blur-sm px-8 py-12 rounded-3xl shadow-inner">
            <div className="flex items-center justify-between mb-8">
              <h2 className="text-2xl font-bold text-gray-900 flex items-center">
                <HiOutlineStar className="w-6 h-6 mr-2 text-yellow-500" />
                Popular Component Categories
              </h2>
              <span className="text-sm text-gray-500 flex items-center">
                <HiOutlineClock className="w-4 h-4 mr-1" />
                Updated weekly
              </span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {[
                {
                  icon: HiOutlineTemplate,
                  title: "Hero Sections",
                  count: "12 templates",
                  description: "Eye-catching headers and hero areas",
                  color: "text-purple-600",
                  bgColor: "bg-purple-100",
                },
                {
                  icon: MdOutlineCategory,
                  title: "Feature Grids",
                  count: "8 templates",
                  description: "Showcase your product features",
                  color: "text-blue-600",
                  bgColor: "bg-blue-100",
                },
                {
                  icon: FiSettings,
                  title: "Pricing Cards",
                  count: "6 templates",
                  description: "Beautiful pricing tables",
                  color: "text-pink-600",
                  bgColor: "bg-pink-100",
                },
                {
                  icon: HiOutlineCode,
                  title: "Forms",
                  count: "5 templates",
                  description: "Contact and newsletter forms",
                  color: "text-green-600",
                  bgColor: "bg-green-100",
                },
              ].map((feature, index) => (
                <div key={index} className="relative group cursor-pointer" onClick={() => setSelectedCategory(feature.title.replace("s", ""))}>
                  <div className="absolute inset-0 bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity" />
                  <div className="relative p-6 bg-white rounded-2xl border border-gray-100 hover:border-gray-200 transition-all shadow-sm hover:shadow-md">
                    <div className="flex items-center justify-between mb-4">
                      <div className={`inline-flex p-3 ${feature.bgColor} rounded-xl`}>
                        <feature.icon className={`w-6 h-6 ${feature.color}`} />
                      </div>
                      <span className="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                        {feature.count}
                      </span>
                    </div>
                    <h3 className="text-lg font-semibold text-gray-900 mb-2">{feature.title}</h3>
                    <p className="text-gray-600 text-sm">{feature.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Save Component Modal */}
      {showSaveModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50">
              <h3 className="text-lg font-bold text-gray-900 flex items-center">
                <FiFolderPlus className="w-5 h-5 mr-2 text-purple-600" />
                Save Custom Component
              </h3>
              <button
                onClick={() => setShowSaveModal(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                ×
              </button>
            </div>

            <div className="p-6">
              {saveSuccess ? (
                <div className="text-center py-8">
                  <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <HiOutlineCheckCircle className="w-8 h-8 text-green-600" />
                  </div>
                  <h4 className="text-xl font-semibold text-gray-900 mb-2">Component Saved!</h4>
                  <p className="text-gray-600">
                    Your component has been saved to <span className="font-mono text-purple-600">Pages/Home/Custom/{componentName}.jsx</span>
                  </p>
                </div>
              ) : (
                <>
                  <div className="mb-6">
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Component Name
                    </label>
                    <input
                      type="text"
                      value={componentName}
                      onChange={(e) => setComponentName(e.target.value)}
                      placeholder="e.g., HeroSection, PricingCard, ContactForm"
                      className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                      autoFocus
                    />
                    <p className="mt-2 text-xs text-gray-500">
                      This will be saved as <span className="font-mono">Pages/Home/Custom/[ComponentName].jsx</span>
                    </p>
                  </div>

                  <div className="bg-gray-50 rounded-xl p-4 mb-6">
                    <div className="flex items-start gap-3">
                      <HiOutlineInformationCircle className="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" />
                      <div className="text-sm text-gray-600">
                        <p className="font-medium text-gray-700 mb-1">Save Location:</p>
                        <p className="font-mono text-xs bg-white p-2 rounded border border-gray-200">
                          /resources/js/Pages/Home/Custom/{componentName || '[ComponentName]'}.jsx
                        </p>
                        {folderExists === false && (
                          <p className="mt-2 text-amber-600 flex items-center gap-1">
                            <HiOutlineExclamation className="w-4 h-4" />
                            Custom folder will be created automatically
                          </p>
                        )}
                      </div>
                    </div>
                  </div>

                  {saveError && (
                    <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                      {saveError}
                    </div>
                  )}

                  <div className="flex items-center justify-end gap-3">
                    <button
                      onClick={() => setShowSaveModal(false)}
                      className="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                    >
                      Cancel
                    </button>
                    <button
                      onClick={handleSaveComponent}
                      disabled={isSaving || !componentName.trim()}
                      className={`px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg transition flex items-center gap-2 shadow-md
                        ${isSaving || !componentName.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-90'}`}
                    >
                      {isSaving ? (
                        <>
                          <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                          <span>Saving...</span>
                        </>
                      ) : (
                        <>
                          <FiSave className="w-4 h-4" />
                          <span>Save Component</span>
                        </>
                      )}
                    </button>
                  </div>
                </>
              )}
            </div>
          </div>
        </div>
      )}

      {/* Export Modal */}
      {showExportModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
            <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50">
              <h3 className="text-lg font-bold text-gray-900 flex items-center">
                <FiDownload className="w-5 h-5 mr-2 text-purple-600" />
                Export Component
              </h3>
              <button
                onClick={() => setShowExportModal(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                ×
              </button>
            </div>

            <div className="p-6">
              <div className="flex items-center gap-4 mb-4">
                <label className="text-sm font-medium text-gray-700">Format:</label>
                <select
                  value={exportFormat}
                  onChange={(e) => setExportFormat(e.target.value)}
                  className="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
                  <option value="jsx">React JSX</option>
                  <option value="html">HTML</option>
                </select>
              </div>

              <div className="bg-gray-900 rounded-xl p-4 overflow-x-auto max-h-96 overflow-y-auto">
                <pre className="text-sm text-gray-100 whitespace-pre-wrap font-mono">
                  <code>{exportedCode}</code>
                </pre>
              </div>

              <div className="flex items-center justify-end gap-3 mt-6">
                <button
                  onClick={copyToClipboard}
                  className="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2"
                >
                  <FiCopy className="w-4 h-4" />
                  Copy Code
                </button>
                <button
                  onClick={downloadComponent}
                  className="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:opacity-90 transition flex items-center gap-2 shadow-md"
                >
                  <FiDownload className="w-4 h-4" />
                  Download {exportFormat === "jsx" ? "JSX" : "HTML"}
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}