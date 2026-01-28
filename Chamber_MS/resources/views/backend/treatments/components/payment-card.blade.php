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
            <!-- Quick Payment Options -->
            <div class="grid grid-cols-2 gap-2">
                <button onclick="quickPayment('full')"
                    class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg py-2.5 font-medium text-sm transition-all">
                    <i class="fas fa-check-circle mr-1"></i>
                    Pay Full
                </button>
                <button onclick="quickPayment('partial')"
                    class="bg-gradient-to-r from-yellow-600 to-amber-600 hover:from-yellow-700 hover:to-amber-700 text-white rounded-lg py-2.5 font-medium text-sm transition-all">
                    <i class="fas fa-money-bill-wave mr-1"></i>
                    Partial Pay
                </button>
            </div>

            <!-- Main Payment Actions -->
            <div class="space-y-2">
                <a href="{{ route('backend.payments.create', ['treatment_id' => $treatment->id]) }}"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-plus-circle"></i>
                    Record Payment
                </a>

                <!-- Session-wise Payment -->
                <a href="{{ route('backend.treatments.session-payments', $treatment->id) }}"
                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-clock"></i>
                    Session Payments
                </a>

                <!-- Procedure-wise Payment -->
                <a href="{{ route('backend.treatments.procedure-payments', $treatment->id) }}"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg py-3 font-medium text-center flex items-center justify-center gap-2 transition-all">
                    <i class="fas fa-teeth"></i>
                    Procedure Payments
                </a>
            </div>
        </div>
    </div>
</div>
