<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Milon\Barcode\DNS1D;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'description',
        'image',
        'barcode',
        'stock_quantity',
        'purchase_price',
        'selling_price',
        'supplier_name',
        'supplier_contact'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    protected $appends = ['profit', 'profit_margin', 'barcode_image_path'];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getProfitAttribute()
    {
        return $this->selling_price - $this->purchase_price;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->selling_price == 0) return 0;
        return (($this->selling_price - $this->purchase_price) / $this->selling_price) * 100;
    }

    /**
     * Generate barcode string dari product code
     */
    public static function generateBarcode($productCode)
    {
        // Bersihkan product code, gunakan sebagai barcode
        return preg_replace('/[^A-Za-z0-9]/', '', $productCode);
    }

    public function getBarcodeImagePathAttribute()
    {
        if (!$this->barcode) return null;

        $barcodePath = 'barcodes/' . $this->barcode . '.png';

        // Generate barcode image jika belum ada
        if (!file_exists(public_path($barcodePath))) {
            $this->generateBarcodeImage();
        }

        return asset($barcodePath);
    }

    public function generateBarcodeImage()
    {
        if (!$this->barcode) return;

        $barcodePath = public_path('barcodes');
        if (!file_exists($barcodePath)) {
            mkdir($barcodePath, 0755, true);
        }

        try {
            $barcode = new DNS1D();

            // Generate EAN8 barcode PNG
            $barcodeData = $barcode->getBarcodePNG($this->barcode, 'EAN8');

            $filename = $this->barcode . '.png';

            file_put_contents($barcodePath . '/' . $filename, $barcodeData);

        } catch (\Exception $e) {
            Log::error('Barcode generation failed: ' . $e->getMessage());
            Log::error('Barcode value: ' . $this->barcode);
        }
    }

    /**
     * Generate kode produk unik 8 digit
     */
    public static function generateUniqueProductCode()
    {
        do {
            $code = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('product_code', $code)->exists());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Generate product_code jika kosong
            if (empty($product->product_code)) {
                $product->product_code = self::generateUniqueProductCode();
            }

            // Generate barcode dari product_code jika kosong
            if (empty($product->barcode)) {
                $product->barcode = self::generateBarcode($product->product_code);
            }
        });

        static::saved(function ($product) {
            // Generate gambar barcode setelah produk tersimpan
            if ($product->barcode) {
                $product->generateBarcodeImage();
            }
        });
    }
}
