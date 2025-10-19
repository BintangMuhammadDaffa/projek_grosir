<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Milon\Barcode\DNS1D;
use PDF;

class ProductController extends Controller
{
    public function exportPdf(Product $product)
    {
        // Generate EAN8 barcode PNG base64
        $dns1d = new DNS1D();
        $barcodePNG = $dns1d->getBarcodePNG($product->product_code, 'C128');
        $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodePNG);
        // Kirim data ke view PDF
        $pdf = PDF::loadView('pdf.product', compact('product', 'barcodeBase64'));
        // Tampilkan atau download PDF
        return $pdf->download('product-' . $product->product_code . '.pdf');
    }

    public function index()
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        $products = Product::orderBy('name')
            ->paginate(10)
            ->appends(request()->query());

        return view('products.index', compact('products'));
    }

    public function create()
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        return view('products.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required',
            'stock_quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier_name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $data['image'] = $imageName;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required',
            'stock_quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier_name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                File::delete(public_path('images/products/'.$product->image));
            }

            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $data['image'] = $imageName;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        if ($product->image) {
            File::delete(public_path('images/products/'.$product->image));
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }

    public function addStock(Request $request, Product $product)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product->increment('stock_quantity', $request->quantity);

        return redirect()->back()->with('success', 'Stock berhasil ditambahkan');
    }

    public function generateBarcode(Request $request)
    {
        if (!auth()->user()->canManageStock()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'product_code' => 'required|string',
            'product_id' => 'nullable|integer|exists:products,id'
        ]);

        $productId = $request->product_id;
        $barcode = Product::generateBarcode($request->product_code, $productId);

        $barcode = Product::generateBarcode($request->product_code, $productId);

        return response()->json([
            'success' => true,
            'barcode' => $barcode
        ]);
    }

    public function apiProducts(Request $request)
    {
        $barcode = $request->query('barcode');
        if (!$barcode) {
            return response()->json(['error' => 'Barcode parameter is required'], 400);
        }

        $product = Product::where('product_code', $barcode)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
