<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\OperationalCostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard - Owner only
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products - Both owner and staff
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/export-pdf', [ProductController::class, 'exportPdf'])->name('products.export-pdf');

    // Test route for barcode generation
    Route::get('/test-barcode', function () {
        try {
            $barcode = new Milon\Barcode\DNS1D();
            $barcode->setStorPath(public_path('barcodes'));
            $result = $barcode->getBarcodePNGPath('TEST123456789', 'C128', 2, 60);

            return response()->json([
                'success' => true,
                'result' => $result,
                'barcode_path' => public_path('barcodes/TEST123456789.png'),
                'file_exists' => file_exists(public_path('barcodes/TEST123456789.png'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    });

    Route::post('products/{product}/add-stock', [ProductController::class, 'addStock'])->name('products.add-stock');
    Route::post('products/generate-barcode', [ProductController::class, 'generateBarcode'])->name('products.generate-barcode');

    // Cashier - Both owner and staff
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::get('/api/products', [CashierController::class, 'getProduct'])->name('api.products');
    Route::post('/api/products/search-image', [CashierController::class, 'searchProductByImage'])->name('api.products.search-image');
    
    // Route BARU: Pencarian manual produk (untuk button "Cari Manual")
    Route::get('/cashier/search', [CashierController::class, 'searchProducts'])->name('cashier.search');
    
    Route::post('/cashier/process', [CashierController::class, 'processTransaction'])->name('cashier.process');
    Route::get('/cashier/receipt/{transactionCode}', [CashierController::class, 'receipt'])->name('cashier.receipt');

    // Transactions - Owner only
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');

    // Reports - Owner only
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Profit Analysis - Owner only
    Route::get('/profit', [ProfitController::class, 'index'])->name('profit.index');

    // Operational Costs - Owner only
    Route::resource('operational-costs', OperationalCostController::class);
});

require __DIR__.'/auth.php';
