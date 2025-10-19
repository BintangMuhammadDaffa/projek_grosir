<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost_code',
        'description',
        'category',
        'amount',
        'cost_date',
        'user_id',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cost_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCostDateAttribute()
    {
        return $this->cost_date->format('d/m/Y');
    }

    /**
     * Generate kode biaya operasional unik dengan format BC + 8 digit
     */
    public static function generateUniqueCostCode()
    {
        do {
            $code = 'BC' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('cost_code', $code)->exists());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cost) {
            // Generate cost_code jika kosong
            if (empty($cost->cost_code)) {
                $cost->cost_code = self::generateUniqueCostCode();
            }
        });
    }
}
