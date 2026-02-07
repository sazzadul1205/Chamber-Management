<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $prescription->prescription_code }}</title>
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
        
        .prescription-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            padding: 40px;
            position: relative;
        }
        
        /* Header Section */
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #10b981;
            padding-bottom: 24px;
            margin-bottom: 32px;
        }
        
        .clinic-info h1 {
            color: #065f46;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .clinic-info .tagline {
            color: #047857;
            font-size: 16px;
            font-weight: 500;
        }
        
        .rx-header {
            text-align: right;
        }
        
        .rx-symbol {
            font-size: 48px;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 8px;
            line-height: 1;
        }
        
        .prescription-meta {
            color: #64748b;
            font-size: 14px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .status-expired {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .status-dispensed {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }
        
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 32px;
            padding: 24px;
            background: #f0fdf4;
            border-radius: 8px;
            border: 1px solid #bbf7d0;
        }
        
        .info-card h3 {
            color: #065f46;
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
        
        /* Prescription Details Cards */
        .details-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .detail-card {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        
        .detail-card.date {
            border-color: #60a5fa;
            background: #eff6ff;
        }
        
        .detail-card.validity {
            border-color: #34d399;
            background: #f0fdf4;
        }
        
        .detail-card.doctor {
            border-color: #a78bfa;
            background: #faf5ff;
        }
        
        .detail-card .value {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .detail-card .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Section Titles */
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #065f46;
            margin: 32px 0 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #d1fae5;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title::before {
            content: "▸";
            color: #10b981;
        }
        
        /* Medicines Table */
        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .medicines-table th {
            background: #10b981;
            color: white;
            text-align: left;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .medicines-table td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        
        .medicines-table tr:last-child td {
            border-bottom: none;
        }
        
        .medicines-table tr:hover {
            background: #f8fafc;
        }
        
        .medicine-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        .medicine-details {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }
        
        /* Instructions Card */
        .instructions-card {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 32px;
        }
        
        .instructions-card h4 {
            color: #92400e;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .instructions-card h4::before {
            content: "⚠️";
        }
        
        .instructions-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            list-style: none;
        }
        
        .instructions-list li {
            padding: 8px 0;
            border-bottom: 1px dashed #fbbf24;
            font-size: 14px;
        }
        
        .instructions-list li:last-child {
            border-bottom: none;
        }
        
        /* Notes Section */
        .notes-section {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 32px;
        }
        
        .notes-section h4 {
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        
        /* Signature Section */
        .signature-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 48px;
            padding-top: 32px;
            border-top: 2px solid #10b981;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            width: 250px;
            height: 1px;
            background: #1e293b;
            margin: 60px auto 10px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 8px;
        }
        
        .doctor-info {
            font-size: 13px;
            color: #475569;
            margin-top: 4px;
        }
        
        /* Footer */
        .prescription-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            margin-top: 32px;
            color: #64748b;
            font-size: 12px;
        }
        
        /* Dispensed Stamp */
        .dispensed-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            padding: 20px 40px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            font-size: 32px;
            font-weight: 700;
            border-radius: 8px;
            opacity: 0.1;
            pointer-events: none;
            z-index: 1;
        }
        
        /* Actions Bar */
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
            font-family: 'Inter', sans-serif;
        }
        
        .action-btn.print {
            background: #10b981;
            color: white;
        }
        
        .action-btn.print:hover {
            background: #059669;
        }
        
        .action-btn.download {
            background: #3b82f6;
            color: white;
        }
        
        .action-btn.download:hover {
            background: #2563eb;
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
            
            .prescription-container {
                box-shadow: none;
                padding: 20px;
            }
            
            .dispensed-stamp {
                opacity: 0.15;
            }
        }
        
        /* Utility Classes */
        .text-green-700 { color: #047857; }
        .text-blue-700 { color: #1d4ed8; }
        .text-red-600 { color: #dc2626; }
        .font-bold { font-weight: 700; }
        .capitalize { text-transform: capitalize; }
    </style>
</head>
<body>
    <div class="prescription-container">
        <!-- Dispensed Stamp -->
        @if($prescription->status === 'dispensed')
            <div class="dispensed-stamp">DISPENSED</div>
        @endif

        <!-- Header -->
        <div class="prescription-header">
            <div class="clinic-info">
                <h1>DENTAL CARE CLINIC</h1>
                <p class="tagline">Professional Dental Healthcare Services</p>
                <div style="margin-top: 12px; color: #64748b; font-size: 14px;">
                    <p>📍 123 Dental Street, Healthcare City, DC 12345</p>
                    <p>📞 (123) 456-7890 | ✉️ info@dentalclinic.com</p>
                </div>
            </div>
            
            <div class="rx-header">
                <div class="rx-symbol">℞</div>
                <div class="prescription-meta">
                    <div style="font-size: 18px; font-weight: 700; color: #1e293b;">PRESCRIPTION</div>
                    <div style="margin-top: 4px;">No: {{ $prescription->prescription_code }}</div>
                    <div style="margin-top: 8px;">
                        <span class="status-badge status-{{ $prescription->status }}">
                            {{ strtoupper($prescription->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient & Doctor Info -->
        <div class="info-grid">
            <div class="info-card">
                <h3>Patient Information</h3>
                <p style="font-size: 18px; font-weight: 700; margin-bottom: 4px; color: #065f46;">
                    {{ $prescription->treatment->patient->full_name }}
                </p>
                <div style="display: grid; grid-template-columns: auto 1fr; gap: 8px; margin-top: 8px;">
                    <span style="color: #64748b;">Code:</span>
                    <span class="font-bold">{{ $prescription->treatment->patient->patient_code }}</span>
                    
                    <span style="color: #64748b;">Age/Gender:</span>
                    <span class="font-bold">
                        {{ $prescription->treatment->patient->age ?? 'N/A' }} / 
                        {{ ucfirst($prescription->treatment->patient->gender ?? 'N/A') }}
                    </span>
                    
                    <span style="color: #64748b;">Treatment:</span>
                    <span class="font-bold text-blue-700">{{ $prescription->treatment->treatment_code }}</span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Prescribing Doctor</h3>
                <p style="font-size: 18px; font-weight: 700; margin-bottom: 4px; color: #065f46;">
                    {{ $prescription->treatment->doctor->user->full_name ?? 'Dr. Not Assigned' }}
                </p>
                <div style="display: grid; grid-template-columns: auto 1fr; gap: 8px; margin-top: 8px;">
                    <span style="color: #64748b;">License No:</span>
                    <span class="font-bold">{{ $prescription->treatment->doctor->medical_license ?? 'XXXXXX' }}</span>
                    
                    <span style="color: #64748b;">Prescribed By:</span>
                    <span class="font-bold">{{ $prescription->creator->full_name ?? 'System' }}</span>
                    
                    <span style="color: #64748b;">Specialty:</span>
                    <span class="font-bold">Dental Surgery</span>
                </div>
            </div>
        </div>

        <!-- Prescription Details Cards -->
        <div class="details-cards">
            <div class="detail-card date">
                <div class="value">{{ $prescription->prescription_date->format('d M Y') }}</div>
                <div class="label">Prescription Date</div>
            </div>
            
            <div class="detail-card validity">
                <div class="value">{{ $prescription->validity_days }} days</div>
                <div class="label">Validity Period</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                    Valid until: {{ $prescription->prescription_date->addDays($prescription->validity_days)->format('d M Y') }}
                </div>
            </div>
            
            <div class="detail-card doctor">
                <div class="value">Dr. {{ substr($prescription->treatment->doctor->user->full_name ?? 'N/A', 0, 10) }}</div>
                <div class="label">Prescribing Doctor</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                    {{ $prescription->treatment->doctor->qualification ?? 'Dental Surgeon' }}
                </div>
            </div>
        </div>

        <!-- Prescribed Medicines -->
        <h3 class="section-title">PRESCRIBED MEDICATIONS</h3>
        @if($prescription->items->count() > 0)
            <table class="medicines-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medication</th>
                        <th>Dosage & Frequency</th>
                        <th>Duration</th>
                        <th>Route</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescription->items as $index => $item)
                        <tr>
                            <td style="font-weight: 600; color: #64748b;">{{ $index + 1 }}</td>
                            <td>
                                <div class="medicine-name">{{ $item->medicine->brand_name }}</div>
                                <div class="medicine-details">
                                    {{ $item->medicine->generic_name }} ({{ $item->medicine->strength }})
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;">{{ $item->dosage }}</div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                    {{ ucfirst($item->frequency) }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;">{{ $item->duration }}</div>
                            </td>
                            <td>
                                <span style="padding: 4px 8px; background: #dbeafe; border-radius: 4px; font-size: 12px; font-weight: 600; color: #1d4ed8;">
                                    {{ ucfirst($item->route) }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #065f46;">
                                {{ $item->quantity }}
                            </td>
                        </tr>
                        @if($item->instructions)
                            <tr style="background: #f8fafc;">
                                <td colspan="6" style="padding-top: 0; border-top: none;">
                                    <div style="padding: 12px; background: #f0fdf4; border-radius: 6px; border-left: 4px solid #10b981;">
                                        <div style="font-size: 12px; color: #047857; font-weight: 600; margin-bottom: 4px;">
                                            📝 Special Instructions:
                                        </div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ $item->instructions }}</div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 40px; color: #64748b; background: #f8fafc; border-radius: 8px;">
                No medications prescribed.
            </div>
        @endif

        <!-- Important Instructions -->
        <div class="instructions-card">
            <h4>IMPORTANT MEDICATION INSTRUCTIONS</h4>
            <ul class="instructions-list">
                <li>✅ Take medicines exactly as prescribed by your doctor</li>
                <li>❌ Do not share your medicines with others</li>
                <li>✅ Complete the full course even if you feel better</li>
                <li>⚠️ Report any side effects immediately</li>
                <li>📦 Store medicines properly as per instructions</li>
                <li>⏰ Take medications at the same time each day</li>
                <li>🚫 Avoid alcohol while on medication</li>
                <li>🏥 Contact clinic for any allergic reactions</li>
            </ul>
        </div>

        <!-- Additional Notes -->
        @if($prescription->notes)
            <div class="notes-section">
                <h4>🔔 ADDITIONAL NOTES FROM DOCTOR</h4>
                <div style="padding: 12px; background: white; border-radius: 6px; margin-top: 8px;">
                    {{ $prescription->notes }}
                </div>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Patient's Signature</div>
                <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                    {{ $prescription->treatment->patient->full_name }}
                </div>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Doctor's Signature & Stamp</div>
                <div class="doctor-info">
                    <div>Dr. {{ $prescription->treatment->doctor->user->full_name ?? 'N/A' }}</div>
                    <div>{{ $prescription->treatment->doctor->qualification ?? 'Dental Surgeon' }}</div>
                    <div>License: {{ $prescription->treatment->doctor->medical_license ?? 'XXXXXX' }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="prescription-footer">
            <div>
                <p style="margin-bottom: 4px;">🦷 This is a computer-generated prescription</p>
                <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin-bottom: 4px;">For queries: 📞 (123) 456-7890</p>
                <p>Emergency: 🚨 Call 911 immediately</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions-bar">
        <button onclick="window.history.back()" class="action-btn back">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </button>
        
        <button onclick="window.print()" class="action-btn print">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Prescription
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
            const prescriptionElement = document.querySelector('.prescription-container');
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
                
                // Capture the prescription as an image
                const canvas = await html2canvas(prescriptionElement, {
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
                pdf.save(`prescription-{{ $prescription->prescription_code }}-{{ date('Y-m-d') }}.pdf`);
                
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

        // Auto-print after 1 second (optional)
        setTimeout(() => {
            // Uncomment if you want auto-print
            // window.print();
        }, 1000);

        // Close after print (optional)
        window.onafterprint = function() {
            // Uncomment if you want to auto-close after printing
            // window.close();
        };
    </script>
</body>
</html>