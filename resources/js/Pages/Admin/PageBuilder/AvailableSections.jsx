import { useState } from 'react';
import { router } from '@inertiajs/react';
import {
  HiOutlineViewGrid,
  HiOutlineCube,
  HiOutlinePhotograph,
  HiOutlineColorSwatch,
} from "react-icons/hi";
import {
  FiSearch,
  FiX,
  FiChevronDown,
  FiPlus,
  FiRefreshCw,
} from "react-icons/fi";
import { DEFAULT_VARIANTS } from './constants';
import { CustomComponentCard } from './CustomComponentCard';

export function AvailableSections({
  sections,
  customComponents,
  loadingCustom,
  openCustomSection,
  setOpenCustomSection,
  searchTerm,
  setSearchTerm,
  openSections,
  toggleSection,
  onAddSection,
  onAddCustomSection,
  onDeleteCustomComponent,
  filteredCustomComponents,
  filteredSections
}) {
  return (
    <div className="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden sticky top-8 min-h-screen flex flex-col">
      {/* Header */}
      <div className="p-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
        <h2 className="text-lg font-semibold text-gray-900 flex items-center">
          <HiOutlineViewGrid className="w-5 h-5 mr-2 text-blue-500" />
          Available Sections
        </h2>
        <div className="mt-4 relative">
          <input
            type="text"
            placeholder="Search sections and custom components..."
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
      <div className="p-5 overflow-y-auto flex-1 min-h-0" style={{ maxHeight: "calc(100vh - 220px)" }}>
        {/* Custom Components Section */}
        <div className="mb-4 bg-purple-50 rounded-xl border border-purple-200 overflow-hidden">
          <button
            type="button"
            onClick={() => setOpenCustomSection(!openCustomSection)}
            className="w-full px-4 py-3 flex items-center justify-between bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 transition-all"
          >
            <div className="flex items-center space-x-2">
              <div className="w-7 h-7 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center">
                <HiOutlineCube className="w-4 h-4 text-white" />
              </div>
              <span className="font-medium text-white text-sm">Custom Components</span>
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-xs bg-white/30 text-white px-2 py-0.5 rounded-full">
                {customComponents.length}
              </span>
              <FiChevronDown
                className={`w-4 h-4 text-white transition-transform ${openCustomSection ? 'transform rotate-180' : ''
                  }`}
              />
            </div>
          </button>

          {openCustomSection && (
            <div className="p-3 bg-purple-50/50">
              {loadingCustom ? (
                <div className="flex items-center justify-center py-4">
                  <FiRefreshCw className="w-5 h-5 text-purple-600 animate-spin" />
                  <span className="ml-2 text-sm text-purple-600">Loading...</span>
                </div>
              ) : filteredCustomComponents.length === 0 ? (
                <div className="text-center py-6">
                  <HiOutlineCube className="w-10 h-10 mx-auto text-purple-300" />
                  <p className="mt-2 text-sm text-purple-600">No custom components found</p>
                  <button
                    type="button"
                    onClick={() => router.get(route('admin.page-builder.custom'))}
                    className="mt-2 text-xs text-purple-700 hover:text-purple-900 underline"
                  >
                    Create your first component
                  </button>
                </div>
              ) : (
                <div className="space-y-1.5">
                  {filteredCustomComponents.map((component) => (
                    <CustomComponentCard
                      key={component.name}
                      component={component}
                      onDelete={onDeleteCustomComponent}
                      onAdd={onAddCustomSection}
                    />
                  ))}
                </div>
              )}
            </div>
          )}
        </div>

        {/* Regular Sections */}
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
                  className={`w-4 h-4 text-gray-500 transition-transform ${openSections[key] ? 'transform rotate-180' : ''
                    }`}
                />
              </button>

              {openSections[key] && (
                <div className="p-2 border-t border-gray-200 bg-gray-50/50">
                  <div className="space-y-1.5">
                    {Object.entries(section.variants).map(([variantKey, variantName]) => (
                      <button
                        key={variantKey}
                        type="button"
                        onClick={() => onAddSection(key, variantKey)}
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
  );
}