import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';

const MySwal = withReactContent(Swal);

export default function PageBuilder({ sections, layoutConfig, sectionSettings, pageConfig }) {
  const [activeSections, setActiveSections] = useState(
    layoutConfig?.sections || generateDefaultSections(sections)
  );

  const { data, setData, post, processing } = useForm({
    layout_config: {
      sections: activeSections
    },
    section_settings: sectionSettings || {}
  });

  const handleDragEnd = (result) => {
    if (!result.destination) return;

    const items = Array.from(activeSections);
    const [reorderedItem] = items.splice(result.source.index, 1);
    items.splice(result.destination.index, 0, reorderedItem);

    const updatedItems = items.map((item, index) => ({
      ...item,
      order: index + 1
    }));

    setActiveSections(updatedItems);
    setData({ ...data, layout_config: { sections: updatedItems } });
  };

  const addSection = (type, variant) => {
    const newSection = {
      id: Date.now().toString(),
      type,
      variant,
      order: activeSections.length + 1,
      settings: { is_visible: true, title: sections[type].name }
    };

    const updatedSections = [...activeSections, newSection];
    setActiveSections(updatedSections);
    setData({ ...data, layout_config: { sections: updatedSections } });
  };

  const removeSection = (id) => {
    const updatedSections = activeSections
      .filter(section => section.id !== id)
      .map((section, index) => ({ ...section, order: index + 1 }));

    setActiveSections(updatedSections);
    setData({ ...data, layout_config: { sections: updatedSections } });
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
      onStart: () => {
        // Optional: could disable button or show loading toast
      },
      onSuccess: () => {
        MySwal.fire({
          icon: 'success',
          title: 'Saved!',
          text: 'Page configuration saved successfully.',
          timer: 2000,
          showConfirmButton: false
        });
      },
      onError: (errors) => {
        MySwal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Failed to save configuration. Check console for details.'
        });
        console.error(errors);
      }
    });
  };

  const [openSections, setOpenSections] = useState({});

  const toggleSection = (key) => {
    setOpenSections((prev) => ({
      ...prev,
      [key]: !prev[key],
    }));
  };

  return (
    <div className="p-8 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-3xl font-bold mb-8 text-gray-800">Page Builder</h1>

        <form onSubmit={handleSubmit}>
          <div className="grid grid-cols-4 gap-8">
            {/* Available Sections */}
            <div className="col-span-1 bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
              <h2 className="text-lg font-semibold mb-6 text-gray-700">Available Sections</h2>
              {Object.entries(sections).map(([key, section]) => (
                <div
                  key={key}
                  className="mb-6 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:shadow-sm transition-all"
                >
                  <h3
                    onClick={() => toggleSection(key)}
                    className="font-medium text-gray-800 cursor-pointer flex justify-between items-center"
                  >
                    {section.name}
                    <span className="ml-2">{openSections[key] ? "▲" : "▼"}</span>
                  </h3>

                  {openSections[key] && (
                    <div className="mt-3 space-y-2">
                      {Object.entries(section.variants).map(([variantKey, variantName]) => (
                        <button
                          key={variantKey}
                          type="button"
                          onClick={() => addSection(key, variantKey)}
                          className="w-full text-left text-black px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all"
                        >
                          + {variantName}
                        </button>
                      ))}
                    </div>
                  )}
                </div>
              ))}
            </div>

            {/* Layout Builder */}
            <div className="col-span-3">
              <DragDropContext onDragEnd={handleDragEnd}>
                <Droppable droppableId="sections">
                  {(provided) => (
                    <div {...provided.droppableProps} ref={provided.innerRef} className="space-y-4">
                      {activeSections.map((section, index) => (
                        <Draggable key={section.id} draggableId={section.id} index={index}>
                          {(provided) => (
                            <div
                              ref={provided.innerRef}
                              {...provided.draggableProps}
                              className="bg-white rounded-2xl shadow-lg p-5 hover:shadow-2xl transition-all border border-gray-200"
                            >
                              <div className="flex items-center justify-between">
                                {/* Drag handle + Section Info */}
                                <div className="flex items-center space-x-4">
                                  <div {...provided.dragHandleProps} className="cursor-move text-gray-400 text-xl">⋮⋮</div>
                                  <div>
                                    <h3 className="font-semibold text-gray-800">{sections[section.type]?.name}</h3>
                                    <p className="text-sm text-gray-500 mt-1">Order: {section.order}</p>
                                  </div>
                                </div>

                                {/* Controls */}
                                <div className="flex items-center space-x-3">
                                  <select
                                    value={section.variant}
                                    onChange={(e) => updateVariant(section.id, e.target.value)}
                                    className="text-sm border text-black w-32 border-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                  >
                                    {Object.entries(sections[section.type]?.variants || {}).map(([key, name]) => (
                                      <option key={key} value={key}>{name}</option>
                                    ))}
                                  </select>

                                  <button
                                    type="button"
                                    onClick={() => toggleVisibility(section.id)}
                                    className={`px-3 py-1 text-sm rounded-full font-medium transition-all
                                  ${section.settings?.is_visible
                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'}`
                                    }
                                  >
                                    {section.settings?.is_visible ? 'Visible' : 'Hidden'}
                                  </button>

                                  <button
                                    type="button"
                                    onClick={() => removeSection(section.id)}
                                    className="text-red-600 hover:text-red-800 font-semibold"
                                  >
                                    Remove
                                  </button>
                                </div>
                              </div>
                            </div>
                          )}
                        </Draggable>
                      ))}
                      {provided.placeholder}
                    </div>
                  )}
                </Droppable>
              </DragDropContext>

              {/* Save Button */}
              <div className="mt-8 flex justify-end">
                <button
                  type="submit"
                  disabled={processing}
                  className="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 disabled:opacity-50 transition-all flex items-center gap-2"
                >
                  {processing && (
                    <svg className="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                  )}
                  {processing ? 'Saving...' : 'Save Configuration'}
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}

// Default section generator
function generateDefaultSections(sections) {
  const defaultSections = [];
  let order = 1;
  Object.entries(sections).forEach(([key, section]) => {
    defaultSections.push({
      id: Date.now() + order.toString(),
      type: key,
      variant: section.default,
      order: order++,
      settings: { is_visible: true, title: section.name }
    });
  });
  return defaultSections;
}
