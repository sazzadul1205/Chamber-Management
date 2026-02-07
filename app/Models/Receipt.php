<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    /*-----------------------------------
     | Fillable Attributes
     *-----------------------------------*/
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

    /*-----------------------------------
     | Boot: Auto-generate fields on create
     *-----------------------------------*/
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            // Auto-generate receipt number
            if (empty($receipt->receipt_no)) {
                $receipt->receipt_no = static::generateReceiptNumber();
            }

            // Set creator
            if (empty($receipt->created_by) && auth()->check()) {
                $receipt->created_by = auth()->id();
            }

            // Convert payment amount to words
            if (empty($receipt->amount_words) && $receipt->payment) {
                $receipt->amount_words = static::amountToWords($receipt->payment->amount);
            }
        });
    }

    /*-----------------------------------
     | Relationships
     *-----------------------------------*/
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*-----------------------------------
     | Scopes
     *-----------------------------------*/
    public function scopePrinted($query)
    {
        return $query->whereNotNull('printed_at');
    }

    public function scopeUnprinted($query)
    {
        return $query->whereNull('printed_at');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('receipt_date', '>=', now()->subDays($days));
    }

    /*-----------------------------------
     | Accessors
     *-----------------------------------*/
    public function getIsPrintedAttribute(): bool
    {
        return !is_null($this->printed_at);
    }

    /*-----------------------------------
     | Actions
     *-----------------------------------*/
    public function markAsPrinted($userId = null)
    {
        $this->printed_at = now();
        $this->printed_by = $userId ?? auth()->id();
        return $this->save();
    }

    /*-----------------------------------
     | Receipt Number Generator
     *-----------------------------------*/
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCT';
        $year = date('Y');
        $month = date('m');

        $lastReceipt = static::where('receipt_no', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('receipt_no', 'desc')
            ->first();

        $newNumber = $lastReceipt
            ? str_pad((int)substr($lastReceipt->receipt_no, -4) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return "{$prefix}{$year}{$month}{$newNumber}";
    }

    /*-----------------------------------
     | Convert Amount to Words (INR)
     *-----------------------------------*/
    public static function amountToWords(float $amount): string
    {
        $amount = number_format($amount, 2, '.', '');
        [$rupees, $paise] = explode('.', $amount);

        $words = self::numberToWords((int)$rupees) . ' Rupees';
        if ((int)$paise > 0) {
            $words .= ' and ' . self::numberToWords((int)$paise) . ' Paise';
        }
        return $words . ' Only';
    }

    /*-----------------------------------
     | Convert number to words (Indian system)
     *-----------------------------------*/
    private static function numberToWords(int $number): string
    {
        if ($number === 0) return 'Zero';

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

        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $scales = ['', 'Thousand', 'Lakh', 'Crore'];

        $words = '';
        $scaleIndex = 0;

        while ($number > 0) {
            if ($scaleIndex === 0) {
                $chunk = $number % 1000;
                $number = (int)($number / 1000);
            } else {
                $chunk = $number % 100;
                $number = (int)($number / 100);
            }

            if ($chunk > 0) {
                $chunkWords = '';
                if ($chunk < 20) {
                    $chunkWords = $ones[$chunk];
                } elseif ($chunk < 100) {
                    $chunkWords = $tens[(int)($chunk / 10)] . ' ' . $ones[$chunk % 10];
                } else {
                    $chunkWords = $ones[(int)($chunk / 100)] . ' Hundred';
                    if ($chunk % 100 > 0) {
                        $chunkWords .= ' ' . self::numberToWords($chunk % 100);
                    }
                }

                if ($scaleIndex > 0) $chunkWords .= ' ' . $scales[$scaleIndex];
                $words = $chunkWords . ($words ? ' ' . $words : '');
            }

            $scaleIndex++;
        }

        return $words;
    }

    /*-----------------------------------
     | Receipt Data for PDF/Print
     *-----------------------------------*/
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
