<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashierController extends Controller
{
    public function index()
    {
        if (!auth()->user()->canAccessCashier()) {
            abort(403, 'Unauthorized access');
        }

        return view('cashier.index');
    }

    public function getProduct(Request $request)
    {
        $code = $request->get('barcode');

        $product = Product::where('barcode', $code)
            ->orWhere('product_code', $code)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($product);
    }


    public function processTransaction(Request $request)
    {
        if (!auth()->user()->canAccessCashier()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'customer_name' => 'required|string',
            'payment_method' => 'required|in:cash,transfer,qris,unpaid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $transactionCode = 'TRX' . date('Ymd') . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);

        $totalAmount = 0;
        $totalProfit = 0;

        // Calculate totals
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $subtotal = $product->selling_price * $item['quantity'];
            $profit = ($product->selling_price - $product->purchase_price) * $item['quantity'];

            $totalAmount += $subtotal;
            $totalProfit += $profit;
        }

        // Create transaction
        $status = $request->payment_method === 'unpaid' ? 'uncomplete' : 'completed';
        $transaction = Transaction::create([
            'transaction_code' => $transactionCode,
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'total_amount' => $totalAmount,
            'total_profit' => $totalProfit,
            'payment_method' => $request->payment_method,
            'status' => $status,
            'transaction_date' => Carbon::now()
        ]);

        // Create transaction items and update stock
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $product->selling_price,
                'total_price' => $product->selling_price * $item['quantity'],
                'profit' => ($product->selling_price - $product->purchase_price) * $item['quantity']
            ]);

            // Reduce stock
            $product->decrement('stock_quantity', $item['quantity']);
        }

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'message' => 'Transaksi berhasil diproses'
        ]);
    }

    public function receipt($transactionCode)
    {
        $transaction = Transaction::with(['transactionItems', 'user'])
            ->where('transaction_code', $transactionCode)
            ->firstOrFail();

        return view('cashier.receipt', compact('transaction'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required'], 400);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('product_code', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'product_code', 'barcode', 'selling_price', 'stock_quantity')
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}
