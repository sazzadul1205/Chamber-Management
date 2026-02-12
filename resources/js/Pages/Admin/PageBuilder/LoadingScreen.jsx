import { HiOutlineTemplate } from "react-icons/hi";

export function LoadingScreen() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
      <div className="text-center">
        <div className="inline-block p-4 bg-white rounded-2xl shadow-xl">
          <div className="relative">
            <div className="w-16 h-16 border-4 border-gray-200 border-t-blue-600 border-r-purple-600 border-b-pink-600 border-l-blue-600 rounded-full animate-spin"></div>
            <div className="absolute inset-0 flex items-center justify-center">
              <HiOutlineTemplate className="w-6 h-6 text-gray-600" />
            </div>
          </div>
          <h3 className="mt-4 text-lg font-semibold text-gray-900">Loading Page Builder</h3>
          <p className="mt-2 text-sm text-gray-600">Preloading components for smooth experience...</p>
          <p className="mt-1 text-xs text-gray-500">This may take a few seconds</p>
        </div>
      </div>
    </div>
  );
}