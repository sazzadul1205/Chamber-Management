<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #3490dc;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px;
            background: #f8f9fa;
        }

        .details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }

        .custom-message {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Appointment Reminder</h1>
        </div>

        <div class="content">
            <p>Dear {{ $patientName }},</p>

            <p>This is a friendly reminder about your upcoming dental appointment.</p>

            @if ($customMessage && is_string($customMessage) && !Str::contains($customMessage, '<html>'))
                <div class="custom-message">
                    <strong>Special Message:</strong><br>
                    {{ $customMessage }}
                </div>
            @endif

            <div class="details">
                <h3>Appointment Details:</h3>
                <p><strong>Type:</strong> {{ $typeName }}</p>
                <p><strong>Doctor:</strong> Dr. {{ $doctorName }}</p>
                <p><strong>Date:</strong> {{ $date }}</p>
                <p><strong>Time:</strong> {{ $time }}</p>
                <p><strong>Duration:</strong> {{ $appointment->expected_duration }} minutes</p>
                @if ($appointment->chair)
                    <p><strong>Chair:</strong> {{ $appointment->chair->name }}</p>
                @endif
                @if ($appointment->chief_complaint)
                    <p><strong>Chief Complaint:</strong> {{ $appointment->chief_complaint }}</p>
                @endif
            </div>

            <p>Please arrive 10-15 minutes before your scheduled time.</p>

            @if ($appointment->status == 'scheduled')
                <p>
                    <a href="{{ route('backend.appointments.show', $appointment->id) }}" class="button">
                        View Appointment Details
                    </a>
                </p>
            @endif

            <p>
                <strong>Need to reschedule or cancel?</strong><br>
                Please contact us at least 24 hours in advance.<br>
                Phone: (123) 456-7890<br>
                Email: appointments@dentalclinic.com
            </p>

            <p>We look forward to seeing you!</p>

            <p>Best regards,<br>
                The Dental Clinic Team</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Dental Clinic. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p>
                <small>
                    To manage your notification preferences, please contact our office.
                </small>
            </p>
        </div>
    </div>
</body>

</html>
