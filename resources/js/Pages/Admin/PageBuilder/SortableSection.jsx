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
} from "react-icons/hi";
import { generateNavId } from './constants';
import { useState } from 'react';

export function SortableSection({
  section,
  index,
  onUpdateVariant,
  onToggleVisibility,
  onRemove,
  onUpdateNavLabel,
  sections
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

  const [isEditing, setIsEditing] = useState(false);

  const getSectionName = () => {
    return sections[section.type]?.name || section.type;
  };

  const getVariantName = () => {
    return sections[section.type]?.variants?.[section.variant] || section.variant;
  };

  // Handle nav label change with auto ID generation
  const handleNavLabelChange = (value) => {
    onUpdateNavLabel(section.id, value);
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      className={`bg-white rounded-xl border-2 p-4 transition-all ${isDragging
        ? 'border-blue-400 shadow-2xl rotate-1 scale-[1.02] opacity-50'
        : 'border-gray-200 hover:border-gray-300 hover:shadow-lg'
        }`}
    >
      <div className="flex items-start justify-between">

        <div className="flex items-start space-x-3 flex-1">

          {/* Drag */}
          <div
            {...attributes}
            {...listeners}
            className="mt-1 cursor-move text-gray-400 hover:text-gray-600"
          >
            <MdOutlineDragIndicator className="w-5 h-5" />
          </div>

          {/* Info */}
          <div className="flex-1">

            <div className="flex items-center space-x-2">
              <div className="w-6 h-6 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs">
                {index + 1}
              </div>

              <h3 className="font-semibold text-gray-900 text-sm">
                {getSectionName()}
              </h3>

              {section.is_custom && (
                <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-purple-100 text-purple-800">
                  Custom
                </span>
              )}
            </div>

            {/* Variant */}
            <div className="flex items-center space-x-2 mt-2 ml-8">
              <span className="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">
                <HiOutlineColorSwatch className="w-3 h-3 mr-1" />
                {getVariantName()}
              </span>

              {!section.settings?.is_visible && (
                <span className="text-xs text-gray-500">
                  Hidden
                </span>
              )}
            </div>

            {/* IMPROVED NAV EDITOR */}
            <div className="mt-3 ml-8 bg-gray-50 p-3 rounded-lg border border-gray-200">
              <div className="flex items-center justify-between mb-1">
                <label className="block text-xs font-medium text-gray-700">
                  Navbar Text
                </label>
                <button
                  type="button"
                  onClick={() => setIsEditing(!isEditing)}
                  className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                >
                  {isEditing ? 'Done' : 'Edit'}
                </button>
              </div>

              {isEditing ? (
                <div className="space-y-2">
                  <input
                    type="text"
                    value={section.navLabel || ''}
                    onChange={(e) => handleNavLabelChange(e.target.value)}
                    placeholder="Enter navigation label"
                    className="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    autoFocus
                  />
                  <div className="flex items-center justify-between">
                    <span className="text-xs text-gray-500">
                      ID will be auto-generated
                    </span>
                    <span className="text-[10px] font-mono bg-gray-200 px-2 py-1 rounded">
                      {section.navId || generateNavId(section.navLabel || '')}
                    </span>
                  </div>
                </div>
              ) : (
                <div className="flex items-center justify-between">
                  <span className="text-sm font-medium text-gray-900">
                    {section.navLabel || getSectionName()}
                  </span>
                  <span className="text-[10px] font-mono bg-gray-200 px-2 py-1 rounded text-gray-600">
                    ID: {section.navId || generateNavId(section.navLabel || getSectionName())}
                  </span>
                </div>
              )}
            </div>

          </div>

          {/* Controls */}
          <div className="flex items-center space-x-1">

            {sections[section.type] && !section.is_custom && (
              <div className="relative">
                <select
                  value={section.variant}
                  onChange={(e) => onUpdateVariant(section.id, e.target.value)}
                  className="text-xs border border-gray-300 rounded-lg pl-2 pr-6 py-1.5 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
              className={`p-1.5 rounded-lg transition-colors ${section.settings?.is_visible
                ? 'text-gray-600 hover:text-blue-600 hover:bg-blue-50'
                : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'
                }`}
              title={section.settings?.is_visible ? 'Hide section' : 'Show section'}
            >
              {section.settings?.is_visible
                ? <MdOutlineVisibility className="w-4 h-4" />
                : <MdOutlineVisibilityOff className="w-4 h-4" />
              }
            </button>

            <button
              type="button"
              onClick={() => onRemove(section.id)}
              className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
              title="Remove section"
            >
              <MdOutlineDelete className="w-4 h-4" />
            </button>

          </div>
        </div>
      </div>
    </div>
  );
}
export default SortableSection;