import { useState, useEffect } from 'react';
import { useForm, router } from '@inertiajs/react';

// sweetalert2 
import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';


// Import icons
import { HiOutlineTemplate, HiOutlineInformationCircle, HiOutlineViewGrid, HiOutlineCube } from "react-icons/hi";
import { MdOutlineVisibility } from "react-icons/md";

// Other imports
import axios from 'axios';
import { arrayMove } from '@dnd-kit/sortable';

// Import components
import { LoadingScreen } from './LoadingScreen';
import { AvailableSections } from './AvailableSections';
import { LayoutBuilder } from './LayoutBuilder';
import { PreviewPanel } from './PreviewPanel';
import { generateUniqueId, preloadComponents, preloadCustomComponents } from './constants';

const MySwal = withReactContent(Swal);

export default function PageBuilder({ sections, layoutConfig, sectionSettings }) {
  const [isPreloaded, setIsPreloaded] = useState(false);

  // CRITICAL: Ensure every section has a unique ID and correct is_custom flag on initial load
  const [activeSections, setActiveSections] = useState(() => {
    if (layoutConfig?.sections?.length) {
      return layoutConfig.sections.map(section => ({
        ...section,
        id: section.id || generateUniqueId(),
        // CRITICAL: Explicitly set is_custom based on either:
        // 1. The existing is_custom flag
        // 2. Check if the section type doesn't exist in the sections object (prebuilt sections)
        is_custom: section.is_custom === true || !sections[section.type]
      }));
    }
    return generateDefaultSections(sections);
  });

  // Debug log to verify initial state
  const { data, setData, post, processing } = useForm({
    layout_config: {
      sections: activeSections
    },
    section_settings: sectionSettings || {}
  });

  // Load custom components when activeSections change
  const [searchTerm, setSearchTerm] = useState('');
  const [openSections, setOpenSections] = useState({});
  const [loadingCustom, setLoadingCustom] = useState(false);
  const [customComponents, setCustomComponents] = useState([]);
  const [openCustomSection, setOpenCustomSection] = useState(false);
  const [customComponentCache, setCustomComponentCache] = useState({});

  // Preload all components before rendering
  useEffect(() => {
    const preloadAll = async () => {
      try {
        // Preload regular components
        await preloadComponents();

        // Get unique custom component names from active sections
        const customSectionNames = [...new Set(
          activeSections
            .filter(s => s.is_custom)
            .map(s => s.variant)
        )];

        if (customSectionNames.length > 0) {
          await preloadCustomComponents(customSectionNames);

          // Also preload each custom component into cache
          for (const name of customSectionNames) {
            try {
              const module = await import(`../../Pages/Home/Custom/${name}.jsx`);
              setCustomComponentCache(prev => ({
                ...prev,
                [name]: module.default
              }));
            } catch (error) {
              console.error(`Failed to preload custom component: ${name}`, error);
            }
          }
        }

        setIsPreloaded(true);
      } catch (error) {
        console.error('Failed to preload components:', error);
        setIsPreloaded(true);
      }
    };

    preloadAll();
  }, []);

  // Update form data when activeSections changes
  useEffect(() => {
    setData('layout_config', { sections: activeSections });
  }, [activeSections]);

  // Fetch custom components on mount
  useEffect(() => {
    fetchCustomComponents();
  }, []);

  const fetchCustomComponents = async () => {
    setLoadingCustom(true);
    try {
      const response = await axios.get('/admin/custom-components');
      setCustomComponents(response.data.components || []);
    } catch (error) {
      console.error('Failed to fetch custom components:', error);
      MySwal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to load custom components',
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 3000
      });
    } finally {
      setLoadingCustom(false);
    }
  };

  // Delete custom component
  const deleteCustomComponent = async (name) => {
    try {
      await axios.delete(`/admin/delete-component/${name}`);

      // Remove from cache if exists
      setCustomComponentCache(prev => {
        const newCache = { ...prev };
        delete newCache[name];
        return newCache;
      });

      // Refresh custom components list
      await fetchCustomComponents();
      return true;
    } catch (error) {
      console.error('Failed to delete custom component:', error);
      throw error;
    }
  };

  const handleDragEnd = (event) => {
    const { active, over } = event;

    if (active.id !== over.id) {
      const oldIndex = activeSections.findIndex((item) => item.id === active.id);
      const newIndex = activeSections.findIndex((item) => item.id === over.id);

      const reorderedItems = arrayMove(activeSections, oldIndex, newIndex);
      const updatedItems = reorderedItems.map((item, index) => ({
        ...item,
        order: index + 1 // CRITICAL: Update order based on new position
      }));

      setActiveSections(updatedItems);
    }
  };

  const addSection = (type, variant) => {
    const newSection = {
      id: generateUniqueId(),
      type,
      variant,
      order: activeSections.length + 1, // Always add to the bottom
      is_custom: false,
      settings: {
        is_visible: true,
        title: sections[type]?.name || type,
        created_at: new Date().toISOString()
      }
    };

    const updatedSections = [...activeSections, newSection]; // Add to end of array
    setActiveSections(updatedSections);

    MySwal.fire({
      icon: 'success',
      title: 'Section Added',
      text: `${sections[type]?.variants[variant] || type} has been added to your layout.`,
      timer: 1500,
      showConfirmButton: false,
      position: 'bottom-end',
      toast: true,
      background: 'white'
    });
  };

  const addCustomSection = async (componentName) => {
    // Preload the custom component immediately when adding
    try {
      const module = await import(`../../Pages/Home/Custom/${componentName}.jsx`);
      setCustomComponentCache(prev => ({
        ...prev,
        [componentName]: module.default
      }));
    } catch (error) {
      console.error(`Failed to load custom component: ${componentName}`, error);
    }

    const newSection = {
      id: generateUniqueId(),
      type: componentName,
      variant: componentName,
      order: activeSections.length + 1, // Always add to the bottom
      is_custom: true,
      settings: {
        is_visible: true,
        title: componentName,
        created_at: new Date().toISOString()
      }
    };

    const updatedSections = [...activeSections, newSection]; // Add to end of array
    setActiveSections(updatedSections);

    MySwal.fire({
      icon: 'success',
      title: 'Custom Component Added',
      text: `${componentName} has been added to your layout.`,
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
  };

  const toggleVisibility = (id) => {
    const updatedSections = activeSections.map(section =>
      section.id === id
        ? { ...section, settings: { ...section.settings, is_visible: !section.settings?.is_visible } }
        : section
    );

    setActiveSections(updatedSections);
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

  const toggleSection = (key) => {
    setOpenSections((prev) => ({
      ...prev,
      [key]: !prev[key],
    }));
  };

  // Filter sections based on search term
  const filteredSections = Object.entries(sections)
    .map(([key, section]) => {
      // Add predefined variants to each section
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

  // Filter custom components
  const filteredCustomComponents = customComponents.filter(component =>
    component.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const activeCustomCount = activeSections.filter(s => s.is_custom).length;

  // Show loading state while preloading
  if (!isPreloaded) {
    return <LoadingScreen />;
  }

  return (
    <div className="h-screen bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
      <div className="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="mb-8 flex items-center justify-between">
          {/* Title & Subtitle */}
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

          {/* Information & Buttons */}
          <div className="flex items-center space-x-3">
            {/* Active Sections */}
            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
              <HiOutlineViewGrid className="w-4 h-4 mr-1" />
              {activeSections.length} {activeSections.length === 1 ? 'Section' : 'Sections'}
            </span>

            {/* Visible Sections */}
            <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
              <MdOutlineVisibility className="w-4 h-4 mr-1" />
              {activeSections.filter(s => s.settings?.is_visible).length} Visible
            </span>

            {/* Custom Components Count */}
            {activeCustomCount > 0 && (
              <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                <HiOutlineCube className="w-4 h-4 mr-1" />
                {activeCustomCount} Custom
              </span>
            )}

            {/* Create Custom Component */}
            <button
              type="button"
              onClick={() => router.get(route('admin.page-builder.custom'))}
              className="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-xl"
            >
              <HiOutlineCube className="w-4 h-4 mr-2" />
              Create Custom Component
            </button>
          </div>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className='overflow-y-hidden'>
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-4">
            {/* Available Sections - 3 columns */}
            <div className="lg:col-span-3">
              <AvailableSections
                sections={sections}
                customComponents={customComponents}
                loadingCustom={loadingCustom}
                openCustomSection={openCustomSection}
                setOpenCustomSection={setOpenCustomSection}
                searchTerm={searchTerm}
                setSearchTerm={setSearchTerm}
                openSections={openSections}
                toggleSection={toggleSection}
                onAddSection={addSection}
                onAddCustomSection={addCustomSection}
                onDeleteCustomComponent={deleteCustomComponent}
                filteredCustomComponents={filteredCustomComponents}
                filteredSections={filteredSections}
              />
            </div>

            {/* Layout Builder - 4 columns */}
            <div className="lg:col-span-4">
              <LayoutBuilder
                activeSections={activeSections}
                sections={sections}
                onDragEnd={handleDragEnd}
                onUpdateVariant={updateVariant}
                onToggleVisibility={toggleVisibility}
                onRemoveSection={removeSection}
                onSave={handleSubmit}
                processing={processing}
              />
            </div>

            {/* Live Preview - 5 columns */}
            <div className="lg:col-span-5">
              <PreviewPanel
                sections={sections}
                activeSections={activeSections}
                customComponentCache={customComponentCache}
                setCustomComponentCache={setCustomComponentCache}
              />
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}

// Also update the generateDefaultSections function:
function generateDefaultSections(sections) {
  const defaultSections = [];
  let order = 1;
  const timestamp = Date.now();

  Object.entries(sections).forEach(([key, section], index) => {
    defaultSections.push({
      id: `${timestamp}-${index}-${order}-${Math.random().toString(36).substr(2, 9)}`,
      type: key,
      variant: section.default || 'basic',
      order: order++,
      is_custom: false, // Explicitly false for prebuilt sections
      settings: {
        is_visible: true,
        title: section.name,
        created_at: new Date().toISOString()
      }
    });
  });

  return defaultSections;
}