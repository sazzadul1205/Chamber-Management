<!-- Payment Card - Updated for Both Session and Procedure Payments -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-credit-card text-indigo-600"></i>
            Payment Information
        </h3>
    </div>
    <div class="p-4 space-y-4">
        <!-- Payment Breakdown -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total Session Fees:</span>
                <span class="font-medium text-blue-700">৳
                    {{ number_format($costBreakdown['session_costs'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Total Procedure Costs:</span>
                <span class="font-medium text-purple-700">৳
                    {{ number_format($costBreakdown['procedure_costs'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                <span class="text-gray-800 font-semibold">Subtotal:</span>
                <span class="font-bold text-gray-800">৳ {{ number_format($subtotal ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Discount:</span>
                <span class="font-medium text-red-600">-৳ {{ number_format($costBreakdown['discount'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                <span class="text-gray-800 font-semibold">Total Due:</span>
                <span class="text-xl font-bold text-indigo-700">৳
                    {{ number_format($costBreakdown['final_actual'] ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                <span class="text-gray-800 font-semibold">Total Paid:</span>
                <span class="text-xl font-bold text-green-700">৳ {{ number_format($paidAmount ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between items-center bg-red-50 p-2 rounded">
                <span class="text-gray-800 font-semibold">Balance Due:</span>
                <span class="text-xl font-bold text-red-700">৳ {{ number_format($balanceDue ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- Payment Progress -->
        @if (($costBreakdown['final_actual'] ?? 0) > 0)
            <div class="mt-4">
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Payment Progress</span>
                    <span class="text-sm font-bold text-indigo-600">{{ $paymentPercentage ?? 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                        style="width: {{ min($paymentPercentage ?? 0, 100) }}%"></div>
                </div>
            </div>
        @endif

        <!-- Payment Actions -->
        <div class="pt-3 space-y-3">


            <!-- Main Payment Actions -->
            <div class="space-y-2">
                <!-- Unified Payments Page -->
                <a href="{{ route('payments.treatment-payments', $treatment) }}"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-list-alt"></i>
                    All Payment & History
                </a>

                <!-- Session-wise Payment -->
                <a href="{{ route('backend.treatments.session-payments', $treatment) }}"
                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-clock"></i>
                    Session Payments
                </a>

                <!-- Procedure-wise Payment -->
                <a href="{{ route('backend.treatments.procedure-payments', $treatment) }}"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-teeth"></i>
                    Procedure Payments
                </a>

                <!-- View Invoice if exists -->
                {{-- <a href="{{ route('invoices.show', $treatment->invoices->first()) }}"
                    class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-file-invoice-dollar"></i>
                    View Invoice
                </a> --}}

            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Quick Payment Modal -->
@push('scripts')
    <script>
        // Function to open the unified payments page with modal
        function openOverallPaymentModal() {
            // Store the current balance in localStorage for the payments page
            localStorage.setItem('quickPaymentBalance', {{ $balanceDue }});
            localStorage.setItem('quickPaymentType', 'full');

            // Redirect to the unified payments page
            window.location.href = "{{ route('payments.treatment-payments', $treatment) }}";
        }

        // Function to handle quick payments (you can customize this)
        function quickPayment(type) {
            if (type === 'full') {
                // For full payment, open the unified payments page
                openOverallPaymentModal();
            } else {
                // For partial payment, just go to the unified payments page
                window.location.href = "{{ route('payments.treatment-payments', $treatment) }}";
            }
        }

        // Check if we should auto-open modal on page load (for the payments page)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.pathname.includes('/payments') && localStorage.getItem('quickPaymentType') ===
                'full') {
                // This would be in the payments page - auto-open modal
                setTimeout(() => {
                    const overallModalBtn = document.querySelector('[onclick*="openOverallPaymentModal"]');
                    if (overallModalBtn) {
                        overallModalBtn.click();
                    }
                    // Clear the stored values
                    localStorage.removeItem('quickPaymentBalance');
                    localStorage.removeItem('quickPaymentType');
                }, 500);
            }
        });
    </script>
@endpush
