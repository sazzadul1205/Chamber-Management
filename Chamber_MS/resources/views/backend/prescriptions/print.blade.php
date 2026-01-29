<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Print - {{ $prescription->prescription_code }}</title>
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                line-height: 1.4;
                color: #000;
                background: #fff;
                margin: 0;
                padding: 20px;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }

            .prescription-header {
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }

            .clinic-info {
                text-align: center;
                margin-bottom: 15px;
            }

            .clinic-name {
                font-size: 24px;
                font-weight: bold;
                color: #1e40af;
            }

            .clinic-address {
                font-size: 12px;
                color: #666;
                margin: 5px 0;
            }

            .section-title {
                font-size: 16px;
                font-weight: bold;
                margin: 15px 0 10px 0;
                padding-bottom: 5px;
                border-bottom: 1px solid #ccc;
            }

            .patient-info {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin-bottom: 20px;
                padding: 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .info-row {
                display: flex;
                margin-bottom: 5px;
            }

            .info-label {
                font-weight: bold;
                min-width: 120px;
            }

            .prescription-details {
                margin-bottom: 20px;
            }

            .medicine-table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }

            .medicine-table th {
                background: #f3f4f6;
                font-weight: bold;
                text-align: left;
                padding: 8px;
                border: 1px solid #ccc;
            }

            .medicine-table td {
                padding: 8px;
                border: 1px solid #ccc;
            }

            .signature-area {
                margin-top: 50px;
                padding-top: 20px;
                border-top: 1px solid #000;
            }

            .signature-line {
                width: 300px;
                margin-top: 40px;
                border-top: 1px solid #000;
                text-align: center;
                padding-top: 5px;
            }

            .footer {
                font-size: 11px;
                color: #666;
                text-align: center;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #ccc;
            }

            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 80px;
                color: rgba(0, 0, 0, 0.1);
                pointer-events: none;
                z-index: -1;
            }

            .prescription-box {
                border: 2px solid #000;
                padding: 20px;
                margin: 10px 0;
            }

            .header-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .rx-symbol {
                font-size: 24px;
                font-weight: bold;
                color: #1e40af;
            }

            .urgency-stamp {
                border: 2px solid #dc2626;
                color: #dc2626;
                padding: 5px 10px;
                font-weight: bold;
                transform: rotate(-15deg);
                display: inline-block;
            }

            .warnings {
                background: #fef3c7;
                border: 1px solid #f59e0b;
                padding: 10px;
                margin: 15px 0;
                border-radius: 5px;
                font-size: 12px;
            }

            .dispensed-stamp {
                color: #059669;
                font-weight: bold;
                border: 2px solid #059669;
                padding: 5px 10px;
                text-align: center;
                margin: 10px 0;
            }
        }

        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .print-controls {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .print-button {
            background: #1e40af;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .print-button:hover {
            background: #1e3a8a;
        }

        .prescription-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .patient-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
        }

        .medicine-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .medicine-table th {
            background: #f3f4f6;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ccc;
        }

        .medicine-table td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .signature-area {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #000;
        }

        .footer {
            font-size: 11px;
            color: #666;
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <!-- Print Controls (Hidden when printing) -->
    <div class="print-controls no-print">
        <button class="print-button" onclick="window.print()">🖨️ Print Prescription</button>
        <button class="print-button" onclick="window.close()" style="background: #6b7280; margin-left: 10px;">✕
            Close</button>
        <button class="print-button" onclick="window.history.back()" style="background: #059669; margin-left: 10px;">←
            Back</button>
    </div>

    <!-- Watermark -->
    <div class="watermark">PRESCRIPTION</div>

    <!-- Prescription Content -->
    <div class="prescription-box">
        <!-- Clinic Header -->
        <div class="prescription-header">
            <div class="clinic-info">
                <div class="clinic-name">DENTAL CLINIC</div>
                <div class="clinic-address">123 Dental Street, City, State 12345</div>
                <div class="clinic-address">📞 (123) 456-7890 | ✉️ info@dentalclinic.com</div>
                <div class="clinic-address">🕒 Mon-Fri: 9AM-6PM, Sat: 9AM-1PM</div>
            </div>
        </div>

        <!-- Header with RX Symbol -->
        <div class="header-row">
            <div class="rx-symbol">℞</div>
            <div>
                <h1 style="margin: 0;">PRESCRIPTION</h1>
                <div style="font-size: 12px; color: #666;">No: {{ $prescription->prescription_code }}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 12px; color: #666;">Date: {{ $prescription->prescription_date->format('d/m/Y') }}
                </div>
                @if ($prescription->status === 'active')
                    <div class="urgency-stamp">ACTIVE</div>
                @endif
            </div>
        </div>

        <!-- Patient Information -->
        <div class="section-title">PATIENT INFORMATION</div>
        <div class="patient-info">
            <div class="info-row">
                <div class="info-label">Patient Name:</div>
                <div>{{ $prescription->treatment->patient->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Patient Code:</div>
                <div>{{ $prescription->treatment->patient->patient_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Age/Gender:</div>
                <div>
                    {{ $prescription->treatment->patient->age ?? 'N/A' }} /
                    {{ ucfirst($prescription->treatment->patient->gender ?? 'N/A') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Treatment:</div>
                <div>{{ $prescription->treatment->treatment_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Doctor:</div>
                <div>{{ $prescription->treatment->doctor->user->full_name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Prescribed By:</div>
                <div>{{ $prescription->creator->full_name ?? 'System' }}</div>
            </div>
        </div>

        <!-- Prescription Details -->
        <div class="section-title">PRESCRIPTION DETAILS</div>
        <div class="prescription-details">
            <div class="info-row">
                <div class="info-label">Prescription Date:</div>
                <div>{{ $prescription->prescription_date->format('F d, Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Valid Until:</div>
                <div>
                    {{ $prescription->prescription_date->addDays($prescription->validity_days)->format('F d, Y') }}
                    ({{ $prescription->validity_days }} days)
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div>
                    <strong>{{ strtoupper($prescription->status) }}</strong>
                </div>
            </div>
        </div>

        <!-- Prescribed Medicines -->
        <div class="section-title">PRESCRIBED MEDICINES</div>
        @if ($prescription->items->count() > 0)
            <table class="medicine-table">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th width="250">Medicine</th>
                        <th width="100">Dosage</th>
                        <th width="120">Frequency</th>
                        <th width="100">Duration</th>
                        <th width="80">Qty</th>
                        <th width="100">Route</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prescription->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->medicine->brand_name }}</strong><br>
                                <small>{{ $item->medicine->generic_name }} ({{ $item->medicine->strength }})</small>
                            </td>
                            <td>{{ $item->dosage }}</td>
                            <td>{{ $item->frequency }}</td>
                            <td>{{ $item->duration }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ ucfirst($item->route) }}</td>
                        </tr>
                        @if ($item->instructions)
                            <tr style="background: #f9fafb;">
                                <td colspan="7">
                                    <strong>Instructions:</strong> {{ $item->instructions }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; padding: 20px;">No medicines prescribed.</p>
        @endif

        <!-- Additional Notes -->
        @if ($prescription->notes)
            <div class="section-title">ADDITIONAL NOTES</div>
            <div style="padding: 15px; background: #f9fafb; border-radius: 5px; margin: 10px 0;">
                {{ $prescription->notes }}
            </div>
        @endif

        <!-- Warnings -->
        <div class="warnings">
            <strong>⚠️ IMPORTANT:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Take medicines exactly as prescribed</li>
                <li>Do not share your medicines with others</li>
                <li>Complete the full course even if you feel better</li>
                <li>Report any side effects immediately</li>
                <li>Store medicines properly as per instructions</li>
            </ul>
        </div>

        <!-- Dispensed Stamp -->
        @if ($prescription->items->where('status', 'dispensed')->count() > 0)
            <div class="dispensed-stamp">
                DISPENSED ON: {{ now()->format('d/m/Y h:i A') }}
            </div>
        @endif

        <!-- Signature Area -->
        <div class="signature-area">
            <div style="float: left; width: 50%;">
                <div class="signature-line"></div>
                <div style="text-align: center; font-size: 12px;">Patient's Signature</div>
            </div>
            <div style="float: right; width: 50%;">
                <div class="signature-line"></div>
                <div style="text-align: center; font-size: 12px;">
                    Dr. {{ $prescription->treatment->doctor->user->full_name ?? 'Doctor' }}<br>
                    Dental Surgeon<br>
                    License No: {{ $prescription->treatment->doctor->medical_license ?? 'XXXXXX' }}
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated prescription. No signature required.</p>
            <p>Generated on: {{ now()->format('F d, Y h:i A') }} | Prescription ID:
                {{ $prescription->prescription_code }}</p>
            <p>For any queries, contact: (123) 456-7890 | In case of emergency, call 911 immediately</p>
            <p>© {{ date('Y') }} Dental Clinic. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto-print after 1 second (optional)
        setTimeout(() => {
            window.print();
        }, 1000);

        // Close window after print (optional)
        window.onafterprint = function() {
            // Uncomment if you want to auto-close after printing
            // window.close();
        };
    </script>
</body>

</html>
