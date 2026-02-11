import { useState, useEffect, Suspense, lazy } from 'react';
import { useForm } from '@inertiajs/react';
import {
  DndContext,
  closestCenter,
  KeyboardSensor,
  PointerSensor,
  useSensor,
  useSensors,
} from '@dnd-kit/core';
import {
  arrayMove,
  SortableContext,
  sortableKeyboardCoordinates,
  verticalListSortingStrategy,
  useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';

// Material Design Icons
import {
  MdOutlineDragIndicator,
  MdOutlineVisibility,
  MdOutlineVisibilityOff,
  MdOutlineDelete,
  MdOutlinePreview,
  MdOutlineViewQuilt,
} from "react-icons/md";
import { HiChevronDown } from "react-icons/hi";

// Heroicons (outline) — only valid exports from react‑icons/hi
import {
  HiOutlineColorSwatch,
  HiOutlineSelector,
  HiOutlineEyeOff,
  HiOutlineArrowsExpand,
  HiOutlinePhotograph,
  HiOutlineExclamation,
  HiOutlineTemplate,
  HiOutlineInformationCircle,
  HiOutlineViewGrid,
} from "react-icons/hi";

// Feather Icons
import {
  FiSearch,
  FiX,
  FiPlus,
  FiChevronDown,
  FiSave,
  FiRefreshCw,
} from "react-icons/fi";


const MySwal = withReactContent(Swal);

// Lazy load all section components
const componentMap = {
  // Top Slider Variants
  TopSlider_1: lazy(() => import("../Home/TopSlider/TopSlider_1")),
  TopSlider_2: lazy(() => import("../Home/TopSlider/TopSlider_2")),
  TopSlider_3: lazy(() => import("../Home/TopSlider/TopSlider_3")),

  // About Section Variants
  AboutSection_1: lazy(() => import("../Home/AboutSection/AboutSection_1")),
  AboutSection_2: lazy(() => import("../Home/AboutSection/AboutSection_2")),
  AboutSection_3: lazy(() => import("../Home/AboutSection/AboutSection_3")),

  // Our Services Variants
  OurServices_1: lazy(() => import("../Home/OurServices/OurServices_1")),
  OurServices_2: lazy(() => import("../Home/OurServices/OurServices_2")),
  OurServices_3: lazy(() => import("../Home/OurServices/OurServices_3")),

  // Pricing Section Variants
  PricingSection_1: lazy(() => import("../Home/PricingSection/PricingSection_1")),
  PricingSection_2: lazy(() => import("../Home/PricingSection/PricingSection_2")),
  PricingSection_3: lazy(() => import("../Home/PricingSection/PricingSection_3")),

  // Book Appointment Variants
  BookAppointment_1: lazy(() => import("../Home/BookAppointment/BookAppointment_1")),
  BookAppointment_2: lazy(() => import("../Home/BookAppointment/BookAppointment_2")),
  BookAppointment_3: lazy(() => import("../Home/BookAppointment/BookAppointment_3")),

  // Our Dentist Variants
  OurDentist_1: lazy(() => import("../Home/OurDentist/OurDentist_1")),
  OurDentist_2: lazy(() => import("../Home/OurDentist/OurDentist_2")),
  OurDentist_3: lazy(() => import("../Home/OurDentist/OurDentist_3")),

  // Testimonials Variants
  Testimonials_1: lazy(() => import("../Home/TestimonialsSection/Testimonials_1")),
  Testimonials_2: lazy(() => import("../Home/TestimonialsSection/Testimonials_2")),
  Testimonials_3: lazy(() => import("../Home/TestimonialsSection/Testimonials_3")),

  // Latest News Variants
  LatestNews_1: lazy(() => import("../Home/LatestNewsSection/LatestNews_1")),
  LatestNews_2: lazy(() => import("../Home/LatestNewsSection/LatestNews_2")),
  LatestNews_3: lazy(() => import("../Home/LatestNewsSection/LatestNews_3")),
};

// Simple but reliable unique ID generator
function generateUniqueId() {
  return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}-${performance.now().toString(36)}`;
}

// Sortable Section Component for Layout Builder
function SortableSection({ section, index, onUpdateVariant, onToggleVisibility, onRemove, sections }) {

  // Drag and drop functionality using dnd-kit's useSortable hook
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: section.id });

  // Combine transform and transition for smooth dragging
  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      className={`bg-white rounded-xl border-2 transition-all ${isDragging
        ? 'border-blue-400 shadow-2xl rotate-1 scale-[1.02] opacity-50'
        : 'border-gray-200 hover:border-gray-300 hover:shadow-lg'
        }`}
    >
      <div className="p-4">
        <div className="flex items-start justify-between">
          <div className="flex items-start space-x-3 flex-1">
            {/* Drag Handle */}
            <div
              {...attributes}
              {...listeners}
              className="mt-1 cursor-move text-gray-400 hover:text-gray-600 transition-colors"
            >
              <MdOutlineDragIndicator className="w-5 h-5" />
            </div>

            {/* Section Info */}
            <div className="flex-1">
              {/* Section Type */}
              <div className="flex items-center space-x-2">
                {/* Section Number */}
                <div className="w-6 h-6 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs shadow-sm">
                  {index + 1}
                </div>

                {/* Section Name */}
                <div>
                  <h3 className="font-semibold text-gray-900 text-sm">
                    {sections[section.type]?.name}
                  </h3>
                </div>
              </div>

              {/* Variants */}
              <div className="flex items-center space-x-2 mt-1 ml-8">
                {/* Variant Name */}
                <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                  <HiOutlineColorSwatch className="w-3 h-3 mr-1" />
                  {sections[section.type]?.variants[section.variant]}
                </span>

                {/* Visibility */}
                {!section.settings?.is_visible && (
                  <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                    <HiOutlineEyeOff className="w-3 h-3 mr-1" />
                    Hidden
                  </span>
                )}
              </div>
            </div>

            {/* Controls */}
            <div className="flex items-center space-x-1">
              {/* Variant Selector */}
              <div className="relative">
                <select
                  value={section.variant}
                  onChange={(e) => onUpdateVariant(section.id, e.target.value)}
                  className="text-xs border border-gray-300 text-black w-40 rounded-lg pl-2 pr-6 py-1.5 bg-white hover:border-gray-400 transition-colors cursor-pointer appearance-none"
                  title="Change variant"
                >
                  {Object.entries(sections[section.type]?.variants || {}).map(([key, name]) => (
                    <option key={key} value={key}>{name}</option>
                  ))}
                </select>
                <HiOutlineSelector className="w-3 h-3 absolute right-1.5 top-2 text-gray-500 pointer-events-none" />
              </div>

              {/* Visibility Toggle */}
              <button
                type="button"
                onClick={() => onToggleVisibility(section.id)}
                className={`p-1.5 rounded-lg transition-all ${section.settings?.is_visible
                  ? 'text-green-600 hover:bg-green-50'
                  : 'text-gray-400 hover:bg-gray-100'
                  }`}
                title={section.settings?.is_visible ? 'Visible' : 'Hidden'}
              >
                {section.settings?.is_visible ? (
                  <MdOutlineVisibility className="w-4 h-4" />
                ) : (
                  <MdOutlineVisibilityOff className="w-4 h-4" />
                )}
              </button>

              {/* Remove Button */}
              <button
                type="button"
                onClick={() => onRemove(section.id)}
                className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                title="Remove section"
              >
                <MdOutlineDelete className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// Preview Panel Component
function PreviewPanel({ sections, activeSections }) {
  const [isFullScreen, setIsFullScreen] = useState(false);

  // Sort sections by order and filter visible ones
  const sortedSections = [...activeSections]
    .filter(section => section.settings?.is_visible !== false)
    .sort((a, b) => a.order - b.order);

  // Toggle full screen
  const toggleFullScreen = () => {
    setIsFullScreen(!isFullScreen);
  };

  // If no sections, show empty state
  if (sortedSections.length === 0) {
    return (
      <div className={`bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden ${!isFullScreen ? 'sticky top-8' : ''}`}>
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
      <div className="overflow-y-auto" style={{ maxHeight: isFullScreen ? 'calc(100vh - 120px)' : '600px' }}>
        <div className="divide-y divide-gray-100">
          <Suspense fallback={
            <div className="flex items-center justify-center min-h-[200px]">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
          }>
            {sortedSections.map((section) => {
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
                  {/* Preview watermark/badge */}
                  <div className="absolute top-2 right-2 z-10">
                    <span className="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white/90 backdrop-blur-sm shadow-sm border border-gray-200 text-gray-600">
                      <span className="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                      <HiOutlineColorSwatch className="w-3 h-3 mr-1" />
                      {sections[section.type]?.name} - {sections[section.type]?.variants[section.variant]}
                    </span>
                  </div>
                  {/* The actual component */}
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

  // Full screen mode - takes over the entire viewport with overlay
  if (isFullScreen) {
    return (
      <div className="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div className="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full h-full max-w-7xl mx-auto flex flex-col">
          {previewContent}
        </div>
      </div>
    );
  }

  // Normal mode - sticky in the layout
  return (
    <div className="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden sticky top-8 flex flex-col">
      {previewContent}
    </div>
  );
}

// Main Component 
export default function PageBuilder({ sections, layoutConfig, sectionSettings }) {
  const [activeSections, setActiveSections] = useState(() => {
    if (layoutConfig?.sections?.length) {
      return layoutConfig.sections.map(section => ({
        ...section,
        id: section.id || generateUniqueId(),
      }));
    }
    return generateDefaultSections(sections);
  });

  const { data, setData, post, processing } = useForm({
    layout_config: {
      sections: activeSections
    },
    section_settings: sectionSettings || {}
  });

  // DnD Kit sensors
  const sensors = useSensors(
    useSensor(PointerSensor, {
      activationConstraint: {
        distance: 8,
      },
    }),
    useSensor(KeyboardSensor, {
      coordinateGetter: sortableKeyboardCoordinates,
    })
  );

  const handleDragEnd = (event) => {
    const { active, over } = event;

    if (active.id !== over.id) {
      const oldIndex = activeSections.findIndex((item) => item.id === active.id);
      const newIndex = activeSections.findIndex((item) => item.id === over.id);

      const reorderedItems = arrayMove(activeSections, oldIndex, newIndex);
      const updatedItems = reorderedItems.map((item, index) => ({
        ...item,
        order: index + 1
      }));

      setActiveSections(updatedItems);
      setData({ ...data, layout_config: { sections: updatedItems } });
    }
  };

  const addSection = (type, variant) => {
    const newSection = {
      id: generateUniqueId(),
      type,
      variant,
      order: activeSections.length + 1,
      settings: {
        is_visible: true,
        title: sections[type].name,
        created_at: new Date().toISOString()
      }
    };

    const updatedSections = [...activeSections, newSection];
    setActiveSections(updatedSections);
    setData({ ...data, layout_config: { sections: updatedSections } });

    MySwal.fire({
      icon: 'success',
      title: 'Section Added',
      text: `${sections[type].variants[variant]} has been added to your layout.`,
      timer: 1500,
      showConfirmButton: false,
      position: 'bottom-end',
      toast: true,
      background: 'white'
    });
  };

  const removeSection = (id) => {
    MySwal.fire({
      title: 'Remove Section?',
      text: "This section will be removed from your layout.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Yes, remove it',
      cancelButtonText: 'Cancel',
      background: 'white'
    }).then((result) => {
      if (result.isConfirmed) {
        const updatedSections = activeSections
          .filter(section => section.id !== id)
          .map((section, index) => ({ ...section, order: index + 1 }));

        setActiveSections(updatedSections);
        setData({ ...data, layout_config: { sections: updatedSections } });

        MySwal.fire({
          icon: 'success',
          title: 'Removed!',
          text: 'Section has been removed.',
          timer: 1500,
          showConfirmButton: false,
          position: 'bottom-end',
          toast: true,
          background: 'white'
        });
      }
    });
  };

  const updateVariant = (id, variant) => {
    const updatedSections = activeSections.map(section =>
      section.id === id ? { ...section, variant } : section
    );

    setActiveSections(updatedSections);
    setData({ ...data, layout_config: { sections: updatedSections } });
  };

  const toggleVisibility = (id) => {
    const updatedSections = activeSections.map(section =>
      section.id === id
        ? { ...section, settings: { ...section.settings, is_visible: !section.settings?.is_visible } }
        : section
    );

    setActiveSections(updatedSections);
    setData({ ...data, layout_config: { sections: updatedSections } });
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    post(route('admin.page-builder.update'), {
      onSuccess: () => {
        MySwal.fire({
          icon: 'success',
          title: 'Saved!',
          text: 'Page configuration saved successfully.',
          timer: 2000,
          showConfirmButton: false,
          position: 'center',
          background: '#fff',
          backdrop: 'rgba(0,0,0,0.4)'
        });
      },
      onError: (errors) => {
        MySwal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Failed to save configuration. Please try again.',
          confirmButtonColor: '#3b82f6',
          background: 'white'
        });
        console.error(errors);
      }
    });
  };

  const [openSections, setOpenSections] = useState({});
  const [searchTerm, setSearchTerm] = useState('');

  const toggleSection = (key) => {
    setOpenSections((prev) => ({
      ...prev,
      [key]: !prev[key],
    }));
  };

  const filteredSections = Object.entries(sections)
    .map(([key, section]) => {
      // Add 3 predefined variants to each section
      const variants = {
        basic: "Basic",
        sweet: "Sweet",
        dark: "Dark",
      };

      return [key, { ...section, variants }];
    })
    .filter(([key, section]) =>
      section.name.toLowerCase().includes(searchTerm.toLowerCase())
    );


  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <div className="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="mb-8 flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900 flex items-center">
              <HiOutlineTemplate className="w-8 h-8 mr-3 text-blue-600" />
              Page Builder
            </h1>
            <p className="mt-2 text-sm text-gray-600 flex items-center">
              <HiOutlineInformationCircle className="w-4 h-4 mr-1 text-gray-400" />
              Drag and drop sections to build your page layout. Preview changes in real-time.
            </p>
          </div>
          <div className="flex items-center space-x-3">
            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
              <HiOutlineViewGrid className="w-4 h-4 mr-1" />
              {activeSections.length} {activeSections.length === 1 ? 'Section' : 'Sections'}
            </span>
            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
              <MdOutlineVisibility className="w-4 h-4 mr-1" />
              {activeSections.filter(s => s.settings?.is_visible).length} Visible
            </span>
          </div>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {/* Available Sections - 3 columns */}
            <div className="lg:col-span-3">
              <div className="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden sticky top-8">
                {/* Header */}
                <div className="p-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                  <h2 className="text-lg font-semibold text-gray-900 flex items-center">
                    <HiOutlineViewGrid className="w-5 h-5 mr-2 text-blue-500" />
                    Available Sections
                  </h2>
                  <div className="mt-4 relative">
                    <input
                      type="text"
                      placeholder="Search sections..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      className="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    />
                    <FiSearch className="w-4 h-4 absolute left-3 top-3 text-gray-400" />
                    {searchTerm && (
                      <button
                        type="button"
                        onClick={() => setSearchTerm('')}
                        className="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                      >
                        <FiX className="w-4 h-4" />
                      </button>
                    )}
                  </div>
                </div>

                {/* Available Sections */}
                <div className="p-5 max-h-[calc(100vh-12rem)] overflow-y-auto">
                  {filteredSections.length === 0 ? (
                    <div className="text-center py-8">
                      <HiOutlinePhotograph className="w-12 h-12 mx-auto text-gray-400" />
                      <p className="mt-4 text-gray-500">No sections found</p>
                      {searchTerm && (
                        <button
                          onClick={() => setSearchTerm('')}
                          className="mt-2 text-sm text-blue-600 hover:text-blue-800"
                        >
                          Clear search
                        </button>
                      )}
                    </div>
                  ) : (
                    filteredSections.map(([key, section]) => (
                      <div key={key} className="mb-3 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:border-blue-300 transition-all">
                        <button
                          type="button"
                          onClick={() => toggleSection(key)}
                          className="w-full px-4 py-2.5 flex items-center justify-between bg-white hover:bg-gray-50 transition-colors"
                        >
                          <div className="flex items-center space-x-2">
                            <div className="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                              <span className="text-white font-semibold text-xs">
                                {section.name.charAt(0)}
                              </span>
                            </div>
                            <span className="font-medium text-gray-900 text-sm">{section.name}</span>
                          </div>
                          <FiChevronDown
                            className={`w-4 h-4 text-gray-500 transition-transform ${openSections[key] ? 'transform rotate-180' : ''}`}
                          />
                        </button>

                        {openSections[key] && (
                          <div className="p-2 border-t border-gray-200 bg-gray-50/50">
                            <div className="space-y-1.5">
                              {Object.entries(section.variants).map(([variantKey, variantName]) => (
                                <button
                                  key={variantKey}
                                  type="button"
                                  onClick={() => addSection(key, variantKey)}
                                  className="w-full text-left px-3 py-2 text-xs bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-sm transition-all flex items-center justify-between group"
                                >
                                  <span className="text-gray-700 group-hover:text-blue-700 flex items-center">
                                    <HiOutlineColorSwatch className="w-3 h-3 mr-1.5 text-gray-400 group-hover:text-blue-500" />
                                    {variantName}
                                  </span>
                                  <FiPlus className="w-3 h-3 text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                                </button>
                              ))}
                            </div>
                          </div>
                        )}
                      </div>
                    ))
                  )}
                </div>
              </div>
            </div>

            {/* Layout Builder - 4 columns */}
            <div className="lg:col-span-4">
              <div className="bg-white rounded-2xl shadow-xl border border-gray-200 p-5">
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-lg font-semibold text-gray-900 flex items-center">
                    <MdOutlineViewQuilt className="w-5 h-5 mr-2 text-purple-500" />
                    Layout Structure
                  </h2>
                  <span className="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full flex items-center">
                    <MdOutlineDragIndicator className="w-3 h-3 mr-1" />
                    Drag to reorder
                  </span>
                </div>

                <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
                  <SortableContext items={activeSections.map(s => s.id)} strategy={verticalListSortingStrategy}>
                    <div className="space-y-3 min-h-[400px] max-h-[600px] overflow-y-auto pr-1">
                      {activeSections.length === 0 ? (
                        <div className="text-center py-12 px-4 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50">
                          <HiOutlineFolderAdd className="w-12 h-12 mx-auto text-gray-400" />
                          <h3 className="mt-3 text-md font-medium text-gray-900">No sections added</h3>
                          <p className="mt-1 text-xs text-gray-500">Add sections from the left panel.</p>
                        </div>
                      ) : (
                        activeSections.map((section, index) => (
                          <SortableSection
                            key={section.id}
                            section={section}
                            index={index}
                            onUpdateVariant={updateVariant}
                            onToggleVisibility={toggleVisibility}
                            onRemove={removeSection}
                            sections={sections}
                          />
                        ))
                      )}
                    </div>
                  </SortableContext>
                </DndContext>

                {/* Save Button */}
                <div className="mt-5 pt-4 border-t border-gray-200">
                  <button
                    type="submit"
                    disabled={processing}
                    className="w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl text-sm"
                  >
                    {processing ? (
                      <>
                        <FiRefreshCw className="w-4 h-4 animate-spin" />
                        <span>Saving...</span>
                      </>
                    ) : (
                      <>
                        <FiSave className="w-4 h-4" />
                        <span>Save Configuration</span>
                      </>
                    )}
                  </button>
                </div>
              </div>
            </div>

            {/* Live Preview - 5 columns */}
            <div className="lg:col-span-5">
              <Suspense fallback={
                <div className="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 flex items-center justify-center">
                  <div className="text-center">
                    <FiRefreshCw className="w-8 h-8 mx-auto text-gray-400 animate-spin" />
                    <p className="mt-2 text-sm text-gray-500">Loading preview...</p>
                  </div>
                </div>
              }>
                <PreviewPanel
                  sections={sections}
                  activeSections={activeSections}
                />
              </Suspense>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}

// Default section generator with guaranteed unique IDs
function generateDefaultSections(sections) {
  const defaultSections = [];
  let order = 1;
  const timestamp = Date.now();

  Object.entries(sections).forEach(([key, section], index) => {
    defaultSections.push({
      id: `${timestamp}-${index}-${order}-${Math.random().toString(36).substr(2, 9)}`,
      type: key,
      variant: section.default,
      order: order++,
      settings: {
        is_visible: true,
        title: section.name,
        created_at: new Date().toISOString()
      }
    });
  });

  return defaultSections;
}