<div id="{{ $id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-md shadow-lg w-full max-w-md p-6">
        <h5 class="text-lg font-semibold mb-4">{{ $title }}</h5>
        <p>{{ $message }}</p>
        <p class="text-red-600 font-medium mt-2">Warning: This action cannot be undone!</p>

        <div class="flex justify-end gap-2 mt-4">
            <button class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                onclick="document.getElementById('{{ $id }}').classList.add('hidden')">Cancel</button>

            <form action="{{ $route }}" method="POST" class="delete-modal-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Handle all buttons that point to this modal
        document.querySelectorAll('[data-modal-target="{{ $id }}"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = document.getElementById('{{ $id }}');
                if (!modal) return;

                // Update form action dynamically if button has data-route
                const form = modal.querySelector('.delete-modal-form');
                if (form && btn.dataset.route) {
                    form.action = btn.dataset.route;
                }

                // Update message dynamically if button has data-name
                const messageEl = modal.querySelector('p');
                if (messageEl && btn.dataset.name) {
                    messageEl.textContent =
                        `Are you sure you want to delete "${btn.dataset.name}"?`;
                }

                modal.classList.remove('hidden');
            });
        });
    });
</script>
