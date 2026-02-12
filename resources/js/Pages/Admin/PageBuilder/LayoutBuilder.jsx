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
} from '@dnd-kit/sortable';
import {
  MdOutlineViewQuilt,
  MdOutlineDragIndicator,
} from "react-icons/md";
import {
  HiOutlineFolder,
} from "react-icons/hi";
import { FiSave, FiRefreshCw } from "react-icons/fi";
import { SortableSection } from './SortableSection';

export function LayoutBuilder({
  activeSections,
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
      activationConstraint: {
        distance: 8,
      },
    }),
    useSensor(KeyboardSensor, {
      coordinateGetter: sortableKeyboardCoordinates,
    })
  );

  return (
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

      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={onDragEnd}>
        <SortableContext items={activeSections.map(s => s.id)} strategy={verticalListSortingStrategy}>
          <div className="space-y-3 min-h-[400px] max-h-[600px] overflow-y-auto pr-1">
            {activeSections.length === 0 ? (
              <div className="text-center py-12 px-4 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50">
                <HiOutlineFolder className="w-12 h-12 mx-auto text-gray-400" />
                <h3 className="mt-3 text-md font-medium text-gray-900">No sections added</h3>
                <p className="mt-1 text-xs text-gray-500">Add sections from the left panel.</p>
              </div>
            ) : (
              activeSections.map((section, index) => (
                <SortableSection
                  key={section.id}
                  section={section}
                  index={index}
                  onUpdateVariant={onUpdateVariant}
                  onToggleVisibility={onToggleVisibility}
                  onRemove={onRemoveSection}
                  sections={sections}
                  isCustom={section.is_custom}
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
          onClick={onSave}
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