import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import {
  MdOutlineDragIndicator,
  MdOutlineVisibility,
  MdOutlineVisibilityOff,
  MdOutlineDelete,
} from "react-icons/md";
import {
  HiOutlineColorSwatch,
  HiOutlineSelector,
  HiOutlineEyeOff,
  HiOutlineCube,
} from "react-icons/hi";

export function SortableSection({
  section,
  index,
  onUpdateVariant,
  onToggleVisibility,
  onRemove,
  sections,
  isCustom
}) {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: section.id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  // Get section name safely
  const getSectionName = () => {
    if (isCustom) return section.type;
    return sections[section.type]?.name || section.type;
  };

  // Get variant name safely
  const getVariantName = () => {
    if (isCustom) return `Custom: ${section.variant}`;
    return sections[section.type]?.variants?.[section.variant] || section.variant;
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      className={`bg-white rounded-xl border-2 transition-all ${isDragging
          ? 'border-blue-400 shadow-2xl rotate-1 scale-[1.02] opacity-50'
          : isCustom
            ? 'border-purple-300 hover:border-purple-400 hover:shadow-lg bg-gradient-to-r from-purple-50/30 to-pink-50/30'
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
              <div className="flex items-center space-x-2">
                <div className={`w-6 h-6 rounded-lg ${isCustom
                    ? 'bg-gradient-to-br from-purple-500 to-pink-600'
                    : 'bg-gradient-to-br from-blue-500 to-purple-600'
                  } flex items-center justify-center text-white font-semibold text-xs shadow-sm`}>
                  {index + 1}
                </div>
                <div>
                  <h3 className="font-semibold text-gray-900 text-sm">
                    {getSectionName()}
                  </h3>
                </div>
              </div>

              <div className="flex items-center space-x-2 mt-1 ml-8">
                <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${isCustom
                    ? 'bg-purple-100 text-purple-800'
                    : 'bg-blue-100 text-blue-800'
                  }`}>
                  {isCustom ? (
                    <>
                      <HiOutlineCube className="w-3 h-3 mr-1" />
                      {getVariantName()}
                    </>
                  ) : (
                    <>
                      <HiOutlineColorSwatch className="w-3 h-3 mr-1" />
                      {getVariantName()}
                    </>
                  )}
                </span>

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
              {!isCustom && sections[section.type] && (
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
              )}

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