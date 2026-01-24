<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_no',
        'payment_id',
        'patient_id',
        'receipt_date',
        'amount_words',
        'printed_at',
        'printed_by',
        'created_by'
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'printed_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            // Generate receipt number if not provided
            if (empty($receipt->receipt_no)) {
                $receipt->receipt_no = static::generateReceiptNumber();
            }

            // Set created_by if not set
            if (empty($receipt->created_by) && auth()->check()) {
                $receipt->created_by = auth()->id();
            }

            // Convert amount to words if not provided
            if (empty($receipt->amount_words) && $receipt->payment) {
                $receipt->amount_words = static::amountToWords($receipt->payment->amount);
            }
        });
    }

    /**
     * Get the payment associated with the receipt
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the patient associated with the receipt
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who printed the receipt
     */
    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    /**
     * Get the user who created the receipt
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Printed receipts
     */
    public function scopePrinted($query)
    {
        return $query->whereNotNull('printed_at');
    }

    /**
     * Scope: Unprinted receipts
     */
    public function scopeUnprinted($query)
    {
        return $query->whereNull('printed_at');
    }

    /**
     * Scope: Recent receipts
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('receipt_date', '>=', now()->subDays($days));
    }

    /**
     * Check if receipt is printed
     */
    public function getIsPrintedAttribute(): bool
    {
        return !is_null($this->printed_at);
    }

    /**
     * Mark receipt as printed
     */
    public function markAsPrinted($userId = null)
    {
        $this->printed_at = now();
        $this->printed_by = $userId ?? auth()->id();
        return $this->save();
    }

    /**
     * Generate receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCT';
        $year = date('Y');
        $month = date('m');

        // Get last receipt number for this month
        $lastReceipt = static::where('receipt_no', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('receipt_no', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_no, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$year}{$month}{$newNumber}";
    }

    /**
     * Convert amount to words (Indian numbering system)
     */
    public static function amountToWords(float $amount): string
    {
        $amount = number_format($amount, 2, '.', '');
        list($rupees, $paise) = explode('.', $amount);

        $words = self::numberToWords((int)$rupees) . ' Rupees';

        if ($paise > 0) {
            $words .= ' and ' . self::numberToWords((int)$paise) . ' Paise';
        }

        $words .= ' Only';

        return $words;
    }

    /**
     * Convert number to words (Indian numbering system)
     */
    private static function numberToWords(int $number): string
    {
        if ($number == 0) {
            return 'Zero';
        }

        $ones = [
            '',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Eleven',
            'Twelve',
            'Thirteen',
            'Fourteen',
            'Fifteen',
            'Sixteen',
            'Seventeen',
            'Eighteen',
            'Nineteen'
        ];

        $tens = [
            '',
            '',
            'Twenty',
            'Thirty',
            'Forty',
            'Fifty',
            'Sixty',
            'Seventy',
            'Eighty',
            'Ninety'
        ];

        $scales = ['', 'Thousand', 'Lakh', 'Crore'];

        // For numbers less than 20
        if ($number < 20) {
            return $ones[$number];
        }

        // For numbers less than 100
        if ($number < 100) {
            return $tens[(int)($number / 10)] .
                ($number % 10 ? ' ' . $ones[$number % 10] : '');
        }

        // For numbers less than 1000
        if ($number < 1000) {
            $hundreds = (int)($number / 100);
            $remainder = $number % 100;

            $words = $ones[$hundreds] . ' Hundred';
            if ($remainder) {
                $words .= ' ' . self::numberToWords($remainder);
            }
            return $words;
        }

        // For larger numbers (Indian numbering system)
        $words = '';
        $scaleIndex = 0;

        while ($number > 0) {
            // Handle last three digits
            $chunk = $number % 1000;
            $number = (int)($number / 1000);

            if ($chunk > 0) {
                $chunkWords = self::numberToWords($chunk);
                if ($scaleIndex > 0) {
                    $chunkWords .= ' ' . $scales[$scaleIndex];
                }

                if ($words) {
                    $words = $chunkWords . ' ' . $words;
                } else {
                    $words = $chunkWords;
                }
            }

            // For Indian numbering, next scale is lakh (2 more digits)
            if ($number > 0) {
                $chunk = $number % 100;
                $number = (int)($number / 100);

                if ($chunk > 0) {
                    $chunkWords = self::numberToWords($chunk);
                    $scaleIndex++;
                    if ($scaleIndex < count($scales)) {
                        $chunkWords .= ' ' . $scales[$scaleIndex];
                    }

                    if ($words) {
                        $words = $chunkWords . ' ' . $words;
                    } else {
                        $words = $chunkWords;
                    }
                }
                $scaleIndex++;
            }
        }

        return $words;
    }

    /**
     * Get receipt data for PDF generation
     */
    public function getReceiptData(): array
    {
        $payment = $this->payment->load(['invoice', 'patient', 'allocations.installment', 'allocations.treatmentSession']);
        $invoice = $payment->invoice;

        return [
            'receipt' => $this,
            'payment' => $payment,
            'patient' => $this->patient,
            'invoice' => $invoice,
            'allocations' => $payment->allocations,
            'clinic_name' => config('app.name', 'Dental Clinic'),
            'clinic_address' => '123 Clinic Street, City, State - 123456',
            'clinic_phone' => '+91 9876543210',
            'clinic_email' => 'contact@dentalclinic.com',
            'clinic_gst' => 'GSTIN: 12ABCDE1234F1Z5',
            'date' => now()->format('d/m/Y'),
            'time' => now()->format('h:i A'),
        ];
    }
}
