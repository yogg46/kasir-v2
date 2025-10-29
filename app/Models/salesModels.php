<?php

namespace App\Models;

use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class salesModels extends Model
{
   use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sales_models';

    protected $fillable = [
        'branch_id',
        'cashier_id',
        'sale_date',
        'invoice_number',
        'subtotal',
        'total_amount',
        'discount_total',
        'payment_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->invoice_number)) {
                $model->invoice_number = self::generateIncrementCodeWithRetry('INV');
            }
        });
    }

    /**
     * Generate invoice number dengan retry logic
     *
     * @param string $prefix
     * @param int $maxRetries
     * @return string
     * @throws \Exception
     */
    protected static function generateIncrementCodeWithRetry($prefix, $maxRetries = 5)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                return self::generateIncrementCode($prefix);
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;

                // Log percobaan retry
                // \Log::warning("Invoice generation retry attempt {$attempt}/{$maxRetries}", [
                //     'prefix' => $prefix,
                //     'error' => $e->getMessage()
                // ]);

                // Tunggu sebentar sebelum retry (exponential backoff)
                usleep(50000 * $attempt); // 50ms, 100ms, 150ms, dst
            }
        }

        // Jika semua retry gagal
        // Log::error("Failed to generate invoice number after {$maxRetries} attempts", [
        //     'prefix' => $prefix,
        //     'last_error' => $lastException->getMessage()
        // ]);

        throw new \Exception("Gagal membuat nomor invoice setelah {$maxRetries} percobaan. Silakan coba lagi.");
    }

    /**
     * Generate increment code untuk invoice number
     *
     * @param string $prefix
     * @return string
     */
    protected static function generateIncrementCode($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = now()->format('Ymd');

            // Ambil invoice terakhir untuk hari ini dengan lock
            $latest = static::where('invoice_number', 'like', "{$prefix}-{$today}-%")
            ->withTrashed()
                ->orderByDesc('invoice_number')
                ->lockForUpdate()
                ->first();

            // Tentukan nomor berikutnya
            if ($latest && preg_match('/-(\d+)$/', $latest->invoice_number, $matches)) {
                $number = intval($matches[1]) + 1;
            } else {
                $number = 1;
            }

            // Generate invoice number
            $invoiceNumber = sprintf('%s-%s-%06d', $prefix, $today, $number);

            // Double check: pastikan belum ada yang pakai
            $checkAttempts = 0;
            while (static::where('invoice_number', $invoiceNumber)->exists() && $checkAttempts < 10) {
                $number++;
                $invoiceNumber = sprintf('%s-%s-%06d', $prefix, $today, $number);
                $checkAttempts++;
            }

            // Jika masih ada duplikasi setelah 10 kali check
            if (static::where('invoice_number', $invoiceNumber)->exists()) {
                throw new \Exception("Duplicate invoice number detected: {$invoiceNumber}");
            }

            return $invoiceNumber;
        });
    }

    public function toCabang()
    {
        return $this->belongsTo(cabangModel::class, 'branch_id');
    }

    public function toKasir()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function toItems()
    {
        return $this->hasMany(saleitemsModels::class, 'sale_id');
    }

     // Format nominal
    protected function subtotalFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->subtotal, 0, ',', '.')
        );
    }

    protected function totalAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->total_amount, 0, ',', '.')
        );
    }

    protected function discountTotalFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->discount_total, 0, ',', '.')
        );
    }

    // Status badge
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'paid' => 'success',
                'void' => 'danger',
                'refund' => 'warning',
                default => 'secondary'
            }
        );
    }

    // Payment method label
    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->payment_method) {
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'bank_transfer' => 'Transfer Bank',
                default => 'Unknown'
            }
        );
    }
}
