import { useState } from 'react';
import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';
import { HiOutlineCube, HiOutlineTrash } from "react-icons/hi";
import { FiPlus, FiRefreshCw } from "react-icons/fi";

const MySwal = withReactContent(Swal);

export function CustomComponentCard({ component, onDelete, onAdd }) {
  const [isDeleting, setIsDeleting] = useState(false);

  const handleDelete = () => {
    MySwal.fire({
      title: 'Delete Custom Component?',
      html: (
        <div className="text-left">
          <p className="text-sm text-gray-600 mb-2">
            Are you sure you want to delete <span className="font-semibold text-purple-700">{component.name}</span>?
          </p>
          <p className="text-xs text-gray-500">
            This will permanently delete the component file and remove it from all layouts.
          </p>
        </div>
      ),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Yes, delete it',
      cancelButtonText: 'Cancel',
      background: 'white',
      reverseButtons: true,
      customClass: {
        confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500',
        cancelButton: 'px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500'
      }
    }).then(async (result) => {
      if (result.isConfirmed) {
        setIsDeleting(true);
        try {
          await onDelete(component.name);
          MySwal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: `${component.name} has been deleted.`,
            timer: 1500,
            showConfirmButton: false,
            position: 'bottom-end',
            toast: true,
            background: 'white'
          });
        } catch (error) {
          MySwal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to delete component. Please try again.',
            confirmButtonColor: '#3b82f6',
            background: 'white'
          });
        } finally {
          setIsDeleting(false);
        }
      }
    });
  };

  const handleAdd = () => {
    onAdd(component.name);
  };

  return (
    <div className="flex items-center justify-between w-full text-left px-3 py-2.5 text-xs bg-white border border-purple-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 hover:shadow-sm transition-all group">
      <div className="flex items-center flex-1 min-w-0">
        <HiOutlineCube className="w-3 h-3 mr-1.5 text-purple-500 flex-shrink-0" />
        <span className="text-gray-700 group-hover:text-purple-700 truncate">
          {component.name}
        </span>
      </div>
      <div className="flex items-center gap-1 flex-shrink-0">
        <button
          type="button"
          onClick={handleAdd}
          className="p-1 text-purple-500 hover:text-purple-700 hover:bg-purple-100 rounded transition-colors"
          title="Add to layout"
        >
          <FiPlus className="w-3 h-3" />
        </button>
        <button
          type="button"
          onClick={handleDelete}
          disabled={isDeleting}
          className="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          title="Delete component"
        >
          {isDeleting ? (
            <FiRefreshCw className="w-3 h-3 animate-spin" />
          ) : (
            <HiOutlineTrash className="w-3 h-3" />
          )}
        </button>
      </div>
    </div>
  );
}