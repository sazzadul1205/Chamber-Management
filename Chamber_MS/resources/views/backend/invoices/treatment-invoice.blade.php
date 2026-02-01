<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Invoice - {{ $treatment->treatment_code }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            padding: 40px;
            position: relative;
        }
        
        /* Header Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 24px;
            margin-bottom: 32px;
        }
        
        .clinic-info h1 {
            color: #1e40af;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .clinic-info .tagline {
            color: #64748b;
            font-size: 16px;
            font-weight: 500;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-number {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .invoice-dates {
            color: #64748b;
            font-size: 14px;
        }
        
        /* Patient & Treatment Info */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 32px;
            padding: 24px;
            background: #f1f5f9;
            border-radius: 8px;
        }
        
        .info-card h3 {
            color: #1e40af;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        
        .info-card p {
            color: #1e293b;
            font-size: 16px;
            font-weight: 500;
        }
        
        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .summary-card {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .summary-card.total {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }
        
        .summary-card.paid {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .summary-card.due {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .summary-card.sessions {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }
        
        .summary-card .amount {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .summary-card .label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Items Table */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e40af;
            margin: 32px 0 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        
        .items-table th {
            background: #1e40af;
            color: white;
            text-align: left;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .items-table td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        
        .items-table tr:hover {
            background: #f8fafc;
        }
        
        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .type-session {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .type-procedure {
            background: #f3e8ff;
            color: #7c3aed;
        }
        
        /* Payment History */
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        
        .payment-row {
            border-bottom: 1px solid #e2e8f0;
        }
        
        .payment-row:last-child {
            border-bottom: none;
        }
        
        .payment-method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            background: #f0f9ff;
            color: #0369a1;
        }
        
        /* Totals Section */
        .totals-section {
            background: #f8fafc;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 32px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
        }
        
        .total-row.grand-total {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            border-top: 2px solid #cbd5e1;
            margin-top: 12px;
            padding-top: 16px;
        }
        
        .total-row.due {
            color: #dc2626;
            font-weight: 600;
        }
        
        /* Footer */
        .invoice-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            margin-top: 32px;
            color: #64748b;
            font-size: 14px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-partial {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        /* Actions */
        .actions-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 12px;
            z-index: 100;
        }
        
        .action-btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }
        
        .action-btn.print {
            background: #3b82f6;
            color: white;
        }
        
        .action-btn.print:hover {
            background: #2563eb;
        }
        
        .action-btn.download {
            background: #10b981;
            color: white;
        }
        
        .action-btn.download:hover {
            background: #059669;
        }
        
        .action-btn.back {
            background: #6b7280;
            color: white;
        }
        
        .action-btn.back:hover {
            background: #4b5563;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .actions-bar {
                display: none;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="clinic-info">
                <h1>MEDICAL CLINIC</h1>
                <p class="tagline">Comprehensive Healthcare Services</p>
                <div style="margin-top: 12px; color: #64748b; font-size: 14px;">
                    <p>123 Medical Street, Healthcare City</p>
                    <p>Phone: (123) 456-7890 | Email: info@medicalclinic.com</p>
                </div>
            </div>
            
            <div class="invoice-meta">
                <div class="invoice-number">INVOICE #{{ $invoice->invoice_no }}</div>
                <div class="invoice-dates">
                    <div>Date: {{ date('M d, Y', strtotime($invoice->invoice_date)) }}</div>
                    <div>Due: {{ date('M d, Y', strtotime($invoice->due_date)) }}</div>
                </div>
                <div style="margin-top: 12px;">
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Patient & Treatment Info -->
        <div class="info-grid">
            <div class="info-card">
                <h3>Patient Information</h3>
                <p style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">{{ $treatment->patient->full_name }}</p>
                <p>Code: {{ $treatment->patient->patient_code }}</p>
                <p>Phone: {{ $treatment->patient->phone ?? 'N/A' }}</p>
            </div>
            
            <div class="info-card">
                <h3>Treatment Information</h3>
                <p style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">{{ $treatment->treatment_code }}</p>
                <p>Doctor: {{ $treatment->doctor->user->full_name ?? 'N/A' }}</p>
                <p>Start Date: {{ date('M d, Y', strtotime($treatment->start_date)) }}</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card total">
                <div class="amount">৳{{ number_format($overallTotalCost, 2) }}</div>
                <div class="label">Total Cost</div>
            </div>
            
            <div class="summary-card paid">
                <div class="amount">৳{{ number_format($overallTotalPaid, 2) }}</div>
                <div class="label">Amount Paid</div>
            </div>
            
            <div class="summary-card due">
                <div class="amount">৳{{ number_format($overallBalance, 2) }}</div>
                <div class="label">Balance Due</div>
            </div>
            
            <div class="summary-card sessions">
                <div class="amount">{{ $treatment->sessions->count() + $treatment->procedures->count() }}</div>
                <div class="label">Total Items</div>
            </div>
        </div>

        <!-- Sessions -->
        @if($treatment->sessions->count() > 0)
        <h3 class="section-title">Treatment Sessions</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Session</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th style="text-align: right;">Cost</th>
                    <th style="text-align: right;">Paid</th>
                    <th style="text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($treatment->sessions as $session)
                <tr>
                    <td>
                        <span class="type-badge type-session">Session</span>
                        <div style="margin-top: 4px; font-weight: 600;">#{{ $session->session_number }}</div>
                    </td>
                    <td>{{ date('M d, Y', strtotime($session->scheduled_date)) }}</td>
                    <td>{{ $session->session_title }}</td>
                    <td>
                        <span style="text-transform: capitalize;">{{ $session->status }}</span>
                    </td>
                    <td style="text-align: right; font-weight: 600;">৳{{ number_format($session->cost_for_session, 2) }}</td>
                    <td style="text-align: right; color: #10b981; font-weight: 600;">
                        ৳{{ number_format($session->payments->sum('amount'), 2) }}
                    </td>
                    <td style="text-align: right; color: #ef4444; font-weight: 600;">
                        ৳{{ number_format($session->cost_for_session - $session->payments->sum('amount'), 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background: #f1f5f9;">
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 600; padding: 12px 16px;">Sessions Total:</td>
                    <td style="text-align: right; font-weight: 700; padding: 12px 16px;">৳{{ number_format($sessionTotalCost, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; padding: 12px 16px; color: #10b981;">৳{{ number_format($sessionTotalPaid, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; padding: 12px 16px; color: #ef4444;">৳{{ number_format($sessionBalance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Procedures -->
        @if($treatment->procedures->count() > 0)
        <h3 class="section-title">Medical Procedures</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Procedure</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th style="text-align: right;">Cost</th>
                    <th style="text-align: right;">Paid</th>
                    <th style="text-align: right;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($treatment->procedures as $procedure)
                <tr>
                    <td>
                        <span class="type-badge type-procedure">Procedure</span>
                        <div style="margin-top: 4px; font-weight: 600;">{{ $procedure->procedure_name }}</div>
                    </td>
                    <td>{{ $procedure->procedure_code }}</td>
                    <td>{{ $procedure->notes ?? 'No description' }}</td>
                    <td>
                        <span style="text-transform: capitalize;">{{ $procedure->status }}</span>
                    </td>
                    <td style="text-align: right; font-weight: 600;">৳{{ number_format($procedure->cost, 2) }}</td>
                    <td style="text-align: right; color: #10b981; font-weight: 600;">
                        ৳{{ number_format($procedure->payments->sum('amount'), 2) }}
                    </td>
                    <td style="text-align: right; color: #ef4444; font-weight: 600;">
                        ৳{{ number_format($procedure->cost - $procedure->payments->sum('amount'), 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background: #f1f5f9;">
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 600; padding: 12px 16px;">Procedures Total:</td>
                    <td style="text-align: right; font-weight: 700; padding: 12px 16px;">৳{{ number_format($procedureTotalCost, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; padding: 12px 16px; color: #10b981;">৳{{ number_format($procedureTotalPaid, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; padding: 12px 16px; color: #ef4444;">৳{{ number_format($procedureBalance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Payment History -->
        <h3 class="section-title">Payment History</h3>
        @if($payments->count() > 0)
        <table class="payments-table">
            <thead>
                <tr>
                    <th style="padding: 12px 16px; text-align: left;">Date</th>
                    <th style="padding: 12px 16px; text-align: left;">Payment #</th>
                    <th style="padding: 12px 16px; text-align: left;">Method</th>
                    <th style="padding: 12px 16px; text-align: left;">Reference</th>
                    <th style="padding: 12px 16px; text-align: left;">Notes</th>
                    <th style="padding: 12px 16px; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr class="payment-row">
                    <td style="padding: 12px 16px;">{{ date('M d, Y', strtotime($payment->payment_date)) }}</td>
                    <td style="padding: 12px 16px; font-weight: 600;">{{ $payment->payment_no }}</td>
                    <td style="padding: 12px 16px;">
                        <span class="payment-method">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                    </td>
                    <td style="padding: 12px 16px;">{{ $payment->reference_no ?? '—' }}</td>
                    <td style="padding: 12px 16px;">{{ $payment->remarks ?? '—' }}</td>
                    <td style="padding: 12px 16px; text-align: right; font-weight: 700; color: #10b981;">
                        ৳{{ number_format($payment->amount, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 40px; color: #64748b; background: #f8fafc; border-radius: 8px;">
            No payments recorded yet.
        </div>
        @endif

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span>Sessions Total:</span>
                <span>৳{{ number_format($sessionTotalCost, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Procedures Total:</span>
                <span>৳{{ number_format($procedureTotalCost, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>GRAND TOTAL:</span>
                <span>৳{{ number_format($overallTotalCost, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Total Paid:</span>
                <span style="color: #10b981; font-weight: 600;">৳{{ number_format($overallTotalPaid, 2) }}</span>
            </div>
            <div class="total-row due">
                <span>BALANCE DUE:</span>
                <span>৳{{ number_format($overallBalance, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div>
                <p style="margin-bottom: 4px;">Thank you for choosing our medical services.</p>
                <p>Please contact us if you have any questions about this invoice.</p>
            </div>
            <div style="text-align: right;">
                <p>Authorized Signature</p>
                <div style="margin-top: 20px; border-top: 1px solid #cbd5e1; padding-top: 8px; width: 200px;"></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions-bar">
        <a href="{{ route('payments.treatment-payments', $treatment) }}" class="action-btn back">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Treatment
        </a>
        
        <button onclick="window.print()" class="action-btn print">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Invoice
        </button>
        
        <button onclick="downloadPDF()" class="action-btn download">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download PDF
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <script>
        async function downloadPDF() {
            const invoiceElement = document.querySelector('.invoice-container');
            const loadingBtn = event.target;
            const originalText = loadingBtn.innerHTML;
            
            // Show loading state
            loadingBtn.innerHTML = `
                <svg class="animate-spin" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generating PDF...
            `;
            loadingBtn.disabled = true;
            
            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'pt', 'a4');
                
                // Capture the invoice as an image
                const canvas = await html2canvas(invoiceElement, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                });
                
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = pdf.internal.pageSize.getWidth() - 40;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                // Add image to PDF
                pdf.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
                
                // Save the PDF
                pdf.save(`invoice-{{ $treatment->treatment_code }}-{{ date('Y-m-d') }}.pdf`);
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF. Please try again or use the print option.');
            } finally {
                // Restore button state
                loadingBtn.innerHTML = originalText;
                loadingBtn.disabled = false;
            }
        }

        // Add print button functionality
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });

        // Add confirmation for print
        window.addEventListener('beforeprint', () => {
            console.log('Printing invoice...');
        });
    </script>
</body>
</html>