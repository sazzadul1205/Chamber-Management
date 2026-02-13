import {
  DndContext,
  closestCenter,
  KeyboardSensor,
  PointerSensor,
  useSensor,
  useSensors,
} from '@dnd-kit/core';
import {
  SortableContext,
  sortableKeyboardCoordinates,
  verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import {
  MdOutlineViewQuilt,
} from "react-icons/md";
import {
  HiOutlineFolder,
} from "react-icons/hi";
import { FiSave, FiRefreshCw } from "react-icons/fi";
import { SortableSection } from './SortableSection';
import { generateNavId } from './constants';

export function LayoutBuilder({
  activeSections,
  setActiveSections,   // 🔥 REQUIRED
  sections,
  onDragEnd,
  onUpdateVariant,
  onToggleVisibility,
  onRemoveSection,
  onSave,
  processing
}) {

  const sensors = useSensors(
    useSensor(PointerSensor, {
      activationConstraint: { distance: 8 },
    }),
    useSensor(KeyboardSensor, {
      coordinateGetter: sortableKeyboardCoordinates,
    })
  );

  // 🔥 SLUG GENERATOR - Moved to constants.js, but keeping here for reference
  const generateNavId = (text) => {
    return text
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-');
  };

  // 🔥 UPDATE NAV LABEL - Auto generate ID
  const onUpdateNavLabel = (id, value) => {
    setActiveSections(prev =>
      prev.map(section =>
        section.id === id
          ? {
            ...section,
            navLabel: value,
            navId: generateNavId(value || section.navLabel || sections[section.type]?.name || section.type)
          }
          : section
      )
    );
  };

  return (
    <div className="bg-white rounded-2xl shadow-xl border border-gray-200 p-5 min-h-screen flex flex-col overflow-hidden">

      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold text-gray-900 flex items-center">
          <MdOutlineViewQuilt className="w-5 h-5 mr-2 text-purple-500" />
          Layout Structure
        </h2>
        <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
          {activeSections.length} sections
        </span>
      </div>

      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={onDragEnd}>
        <SortableContext
          items={activeSections.map(s => s.id)}
          strategy={verticalListSortingStrategy}
        >
          <div className="space-y-3 overflow-y-auto pr-1 flex-1" style={{ maxHeight: "calc(100vh - 280px)" }}>

            {activeSections.length === 0 ? (
              <div className="text-center py-12 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50">
                <HiOutlineFolder className="w-12 h-12 mx-auto text-gray-400" />
                <h3 className="mt-3 text-md font-medium text-gray-900">No sections added</h3>
                <p className="mt-1 text-xs text-gray-500">Add sections from the left panel</p>
              </div>
            ) : (
              [...activeSections]
                .sort((a, b) => a.order - b.order)
                .map((section, index) => (
                  <SortableSection
                    key={section.id}
                    section={section}
                    index={index}
                    onUpdateVariant={onUpdateVariant}
                    onToggleVisibility={onToggleVisibility}
                    onRemove={onRemoveSection}
                    onUpdateNavLabel={onUpdateNavLabel}   // 🔥 NOW WORKS
                    sections={sections}
                  />
                ))
            )}

          </div>
        </SortableContext>
      </DndContext>

      <div className="mt-5 pt-4 border-t border-gray-200">
        <button
          type="button"
          onClick={onSave}
          disabled={processing}
          className="w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg text-sm hover:from-blue-700 hover:to-purple-700 transition-all transform hover:-translate-y-0.5"
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
  );
}
export default LayoutBuilder;