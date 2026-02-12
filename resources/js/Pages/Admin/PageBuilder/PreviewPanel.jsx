import { useState, Suspense, useEffect } from 'react';
import {
  MdOutlinePreview,
  MdOutlineVisibility,
} from "react-icons/md";
import {
  HiChevronDown,
  HiOutlineArrowsExpand,
  HiOutlinePhotograph,
  HiOutlineExclamation,
  HiOutlineColorSwatch,
  HiOutlineCube,
} from "react-icons/hi";
import { FiRefreshCw } from "react-icons/fi";
import { componentMap } from './constants';

export function PreviewPanel({ sections, activeSections, customComponentCache, setCustomComponentCache }) {
  const [isFullScreen, setIsFullScreen] = useState(false);
  const [loadingComponents, setLoadingComponents] = useState({});

  // Function to import a custom component
  const importCustomComponent = async (name) => {
    if (customComponentCache[name]) {
      return customComponentCache[name];
    }

    try {
      setLoadingComponents(prev => ({ ...prev, [name]: true }));

      // Dynamically import the component
      const module = await import(`../../Pages/Home/Custom/${name}.jsx`);

      setCustomComponentCache(prev => ({
        ...prev,
        [name]: module.default
      }));

      return module.default;
    } catch (error) {
      console.error(`Failed to load custom component: ${name}`, error);
      return null;
    } finally {
      setLoadingComponents(prev => ({ ...prev, [name]: false }));
    }
  };

  // Load custom components dynamically
  useEffect(() => {
    const loadCustomComponents = async () => {
      const customSections = activeSections.filter(s => s.is_custom);

      for (const section of customSections) {
        if (!customComponentCache[section.variant] && !loadingComponents[section.variant]) {
          await importCustomComponent(section.variant);
        }
      }
    };

    loadCustomComponents();
  }, [activeSections]);

  // Sort sections by order and filter visible ones
  const sortedSections = [...activeSections]
    .filter(section => section.settings?.is_visible !== false)
    .sort((a, b) => a.order - b.order);

  const toggleFullScreen = () => {
    setIsFullScreen(!isFullScreen);
  };

  // If no sections, show empty state
  if (sortedSections.length === 0) {
    return (
      <div className={`bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden min-h-screen ${!isFullScreen ? 'sticky top-8' : ''}`}>
        <div className="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900 flex items-center">
            <MdOutlinePreview className="w-5 h-5 mr-2 text-purple-500" />
            Live Preview
          </h2>
          <button
            type="button"
            onClick={toggleFullScreen}
            className="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all"
            title="Enter full screen"
          >
            <HiOutlineArrowsExpand className="w-4 h-4" />
          </button>
        </div>
        <div className="flex items-center justify-center min-h-[400px] p-8">
          <div className="text-center">
            <HiOutlinePhotograph className="w-16 h-16 mx-auto text-gray-400" />
            <h3 className="mt-4 text-lg font-medium text-gray-900">No visible sections</h3>
            <p className="mt-2 text-sm text-gray-500">Toggle visibility on sections to see them in preview.</p>
          </div>
        </div>
      </div>
    );
  }

  const previewContent = (
    <>
      {/* Header */}
      <div className="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
        <div className="flex items-center">
          <div className="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
          <h2 className="text-lg font-semibold text-gray-900 flex items-center">
            <MdOutlinePreview className="w-5 h-5 mr-2 text-purple-500" />
            Live Preview
          </h2>
        </div>
        <div className="flex items-center space-x-2">
          <span className="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full flex items-center">
            <MdOutlineVisibility className="w-3 h-3 mr-1" />
            {sortedSections.length} visible
            {sortedSections.filter(s => s.is_custom).length > 0 && (
              <span className="ml-1 text-purple-600">
                ({sortedSections.filter(s => s.is_custom).length} custom)
              </span>
            )}
          </span>
          <button
            type="button"
            onClick={toggleFullScreen}
            className="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all"
            title={isFullScreen ? "Exit full screen" : "Enter full screen"}
          >
            {isFullScreen ? (
              <HiChevronDown className="w-4 h-4" />
            ) : (
              <HiOutlineArrowsExpand className="w-4 h-4" />
            )}
          </button>
        </div>
      </div>

      {/* Scrollable Preview Content */}
      <div className="overflow-y-auto" style={{ maxHeight: isFullScreen ? 'calc(100vh - 120px)' : 'calc(100vh - 220px)' }}>
        <div className="divide-y divide-gray-100">
          <Suspense fallback={
            <div className="flex items-center justify-center min-h-[200px]">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
          }>
            {sortedSections.map((section) => {
              // For custom components
              if (section.is_custom) {
                const CustomComponent = customComponentCache[section.variant];
                const isLoading = loadingComponents[section.variant];

                if (isLoading) {
                  return (
                    <div key={section.id} className="p-8 bg-purple-50 border-2 border-purple-200 m-4 rounded-lg">
                      <div className="flex items-center space-x-3">
                        <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-purple-600"></div>
                        <div>
                          <h3 className="font-medium text-purple-800">Loading Custom Component...</h3>
                          <p className="text-sm text-purple-700">Component: {section.variant}</p>
                        </div>
                      </div>
                    </div>
                  );
                }

                if (!CustomComponent) {
                  return (
                    <div key={section.id} className="p-8 bg-yellow-50 border-2 border-yellow-200 m-4 rounded-lg">
                      <div className="flex items-center space-x-3">
                        <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                          <HiOutlineExclamation className="w-5 h-5 text-yellow-600" />
                        </div>
                        <div>
                          <h3 className="font-medium text-yellow-800">Custom Component Not Found</h3>
                          <p className="text-sm text-yellow-700">Component: {section.variant}</p>
                          <p className="text-xs text-yellow-600 mt-1">Please check if the file exists in Pages/Home/Custom/</p>
                        </div>
                      </div>
                    </div>
                  );
                }

                return (
                  <div key={section.id} className="relative">
                    <div className="absolute top-2 right-2 z-10">
                      <span className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100/90 backdrop-blur-sm shadow-sm border border-purple-200 text-purple-800">
                        <span className="w-1.5 h-1.5 bg-purple-500 rounded-full mr-1.5"></span>
                        <HiOutlineCube className="w-3 h-3 mr-1" />
                        Custom: {section.type}
                      </span>
                    </div>
                    <CustomComponent
                      settings={section.settings}
                      preview={true}
                    />
                  </div>
                );
              }

              // For regular components - CHECK IF SECTION EXISTS IN SECTIONS OBJECT
              if (!sections[section.type]) {
                console.warn(`Section type not found: ${section.type}`);
                return (
                  <div key={section.id} className="p-8 bg-yellow-50 border-2 border-yellow-200 m-4 rounded-lg">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <HiOutlineExclamation className="w-5 h-5 text-yellow-600" />
                      </div>
                      <div>
                        <h3 className="font-medium text-yellow-800">Section Type Not Found</h3>
                        <p className="text-sm text-yellow-700">Type: {section.type}</p>
                        <p className="text-xs text-yellow-600 mt-1">This section type is not available.</p>
                      </div>
                    </div>
                  </div>
                );
              }

              const Component = componentMap[section.variant];

              if (!Component) {
                console.warn(`Component not found: ${section.variant}`);
                return (
                  <div key={section.id} className="p-8 bg-yellow-50 border-2 border-yellow-200 m-4 rounded-lg">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <HiOutlineExclamation className="w-5 h-5 text-yellow-600" />
                      </div>
                      <div>
                        <h3 className="font-medium text-yellow-800">Component Not Found</h3>
                        <p className="text-sm text-yellow-700">Variant: {section.variant}</p>
                      </div>
                    </div>
                  </div>
                );
              }

              return (
                <div key={section.id} className="relative">
                  <div className="absolute top-2 right-2 z-10">
                    <span className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white/90 backdrop-blur-sm shadow-sm border border-gray-200 text-gray-600">
                      <span className="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                      <HiOutlineColorSwatch className="w-3 h-3 mr-1" />
                      {sections[section.type]?.name} - {sections[section.type]?.variants?.[section.variant] || section.variant}
                    </span>
                  </div>
                  <Component
                    settings={section.settings}
                    preview={true}
                  />
                </div>
              );
            })}
          </Suspense>
        </div>
      </div>

      {/* Footer */}
      <div className="p-3 border-t border-gray-200 bg-gray-50 flex items-center justify-center">
        <span className="text-xs text-gray-500 flex items-center">
          <span className="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>
          <MdOutlinePreview className="w-3 h-3 mr-1" />
          Preview Mode • Real components • Scroll independently
        </span>
      </div>
    </>
  );

  if (isFullScreen) {
    return (
      <div className="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div className="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full h-full max-w-7xl mx-auto flex flex-col">
          {previewContent}
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden sticky top-8 flex flex-col min-h-screen">
      {previewContent}
    </div>
  );
}
