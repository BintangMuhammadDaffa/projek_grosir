@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="row">
    <div class="col-12">
        <h1>Kasir</h1>
    </div>
</div>

<div class="row">
    <!-- Product Search -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">📋 Cari Barang</h5>
                <button class="btn btn-outline-primary btn-sm" type="button" onclick="startQuagga()">
                    <i class="bi bi-upc-scan"></i> Scan Barcode
                </button>
            </div>
            <div class="card-body">
                <!-- Area Kamera -->
                <div id="scanner-wrapper" class="mb-3 text-center">
                    <button id="closeScannerBtn" class="btn btn-sm btn-outline-danger d-none mb-2" onclick="stopQuagga()">
                        <i class="bi bi-x"></i> Tutup Kamera
                    </button>
                    <div id="scanner-container" class="d-none mx-auto"
                        style="width:600px; height:400px; border-radius:12px; 
                            border:2px solid #0d6efd; box-shadow:0 4px 12px rgba(0,0,0,0.15); 
                            background:#000; overflow:hidden;">
                    </div>
                </div>


                <!-- Manual Input -->
                <div class="input-group mb-3 mt-3">
                    <input type="text" id="barcodeInput" class="form-control" placeholder="Ketik barcode atau kode produk">
                    <button class="btn btn-success" type="button" onclick="performManualScan()">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>


                <!-- Selected Product Info -->
                <div id="selectedProduct" class="alert alert-info d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 id="productName"></h5>
                            <p class="mb-0">
                                Kode: <span id="productCode"></span> |
                                Stok: <span id="productStock"></span> unit
                            </p>
                        </div>
                        <div class="text-end">
                            <h4 class="text-primary">Rp <span id="productPrice"></span></h4>
                        </div>
                    </div>
                </div>

                <!-- Quantity Section -->
                <div id="quantitySection" class="mt-3 d-none">
                    <label for="quantityInput" class="form-label">Jumlah</label>
                    <input type="number" id="quantityInput" class="form-control" value="1" min="1">
                </div>

                <!-- Add to Cart Button -->
                <button type="button" id="addToCartBtn" class="btn btn-primary mt-3 d-none">
                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🛒 Keranjang</h5>
            </div>
            <div class="card-body">
                <div id="cartItems">Keranjang kosong</div>
            </div>
            <div class="card-footer">
                <h5>Total: Rp <span id="cartTotal">0</span></h5>
            </div>
        </div>

        <!-- Checkout -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Checkout</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="customerName" class="form-label">Nama Pelanggan:</label>
                    <input type="text" id="customerName" class="form-control" placeholder="Nama pelanggan" required>
                </div>
                <div class="mb-3">
                    <label for="paymentMethod" class="form-label">Metode Pembayaran:</label>
                    <select id="paymentMethod" class="form-select">
                        <option value="cash">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer</option>
                        <option value="unpaid">Utang</option>
                    </select>
                </div>
                <button type="button" class="btn btn-success w-100" id="checkoutBtn" disabled >
                    <i class="bi bi-cash-stack"></i> Proses Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tambahkan QuaggaJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
let selectedProduct = null;
let cart = [];
let quaggaRunning = false;
let scanningInProgress = false;

function stopQuagga() {
    if (quaggaRunning) {
        Quagga.stop();
        quaggaRunning = false;
    }
    document.getElementById("scanner-container").classList.add("d-none");
    document.getElementById("closeScannerBtn").classList.add("d-none");
    document.getElementById("barcodeInput").value = '';
    scanningInProgress = false;
}

// === QuaggaJS Scanner ===
function startQuagga() {
    const scannerContainer = document.getElementById("scanner-container");
    scannerContainer.classList.remove("d-none");

    if (quaggaRunning) return;

    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            constraints: {
                width: 600,
                height: 400,
                facingMode: "environment" // kamera belakang
            },
            target: scannerContainer
        },
        decoder: {
            readers: ["code_128_reader"] // jenis barcode 1D
        }
    }, function (err) {
        if (err) {
            console.error(err);
    Swal.fire({
        title: 'Error',
        text: 'Tidak bisa mengakses kamera',
        icon: 'error',
        confirmButtonText: 'OK'
    });
            return;
        }
        Quagga.start();
        quaggaRunning = true;
        document.getElementById("closeScannerBtn").classList.remove("d-none");
    });

    Quagga.onDetected(function (result) {
        if (!result.codeResult || !result.codeResult.code) return;
        if (scanningInProgress) return;

        const barcode = result.codeResult.code;
        console.log("Barcode terbaca:", barcode);

        scanningInProgress = true;

        // Masukkan ke input dan cari produk
        document.getElementById("barcodeInput").value = barcode;
        performManualScan(true);
    });
}

// === Tampilkan Produk ===
function showProduct(product) {
    selectedProduct = product;
    const productNameEl = document.getElementById('productName');
    const productCodeEl = document.getElementById('productCode');
    const productStockEl = document.getElementById('productStock');
    const productPriceEl = document.getElementById('productPrice');
    const selectedProductEl = document.getElementById('selectedProduct');
    const quantitySectionEl = document.getElementById('quantitySection');
    const addToCartBtnEl = document.getElementById('addToCartBtn');
    const quantityInputEl = document.getElementById('quantityInput');

    if (productNameEl) productNameEl.textContent = product.name;
    if (productCodeEl) productCodeEl.textContent = product.product_code;
    if (productStockEl) productStockEl.textContent = product.stock_quantity;
    if (productPriceEl) productPriceEl.textContent = new Intl.NumberFormat('id-ID').format(product.selling_price);

    if (selectedProductEl) selectedProductEl.classList.remove('d-none');
    if (quantitySectionEl) quantitySectionEl.classList.remove('d-none');
    if (addToCartBtnEl) addToCartBtnEl.classList.remove('d-none');
    if (quantityInputEl) quantityInputEl.value = 1;
}

// === Manual Scan ===
function performManualScan(isFromScanner = false) {
    const barcode = document.getElementById('barcodeInput').value.trim();
    if (!barcode) {
        if (isFromScanner) {
            scanningInProgress = false;
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Masukkan barcode atau kode produk terlebih dahulu',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
        return;
    }

    fetch(`{{ route('api.products') }}?barcode=${encodeURIComponent(barcode)}`)
        .then(r => {
            if (!r.ok) throw new Error('Produk tidak ditemukan');
            return r.json();
        })
        .then(product => {
            showProduct(product);
            if (isFromScanner) {
                Quagga.stop();
                quaggaRunning = false;
                document.getElementById("scanner-container").classList.add("d-none");
                document.getElementById("closeScannerBtn").classList.add("d-none");
                document.getElementById("barcodeInput").focus();
                scanningInProgress = false;
            }
        })
        .catch(err => {
            console.error("Error:", err);
            const errorMsg = 'Produk tidak ditemukan';
            if (isFromScanner) {
                alert(errorMsg + '. Lanjutkan scanning...');
                document.getElementById('barcodeInput').value = '';
                scanningInProgress = false;
                // Camera remains open
            } else {
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
}

// === Cart ===
function addToCart() {
    if (!selectedProduct) {
        Swal.fire({
            title: 'Error',
            text: 'Pilih produk terlebih dahulu',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    if (quantity < 1) {
        Swal.fire({
            title: 'Error',
            text: 'Jumlah tidak valid',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }
    if (quantity > selectedProduct.stock_quantity) {
        Swal.fire({
            title: 'Error',
            text: 'Stok tidak cukup',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    const existing = cart.find(item => item.product_id === selectedProduct.id);
    if (existing) {
        existing.quantity += quantity;
        existing.total = existing.quantity * existing.unit_price;
    } else {
        cart.push({
            product_id: selectedProduct.id,
            name: selectedProduct.name,
            product_code: selectedProduct.product_code,
            unit_price: selectedProduct.selling_price,
            quantity: quantity,
            total: selectedProduct.selling_price * quantity
        });
    }

    renderCart();
    // reset
    selectedProduct = null;
    document.getElementById('selectedProduct').classList.add('d-none');
    document.getElementById('quantitySection').classList.add('d-none');
    document.getElementById('addToCartBtn').classList.add('d-none');
    document.getElementById('barcodeInput').value = '';
}

function renderCart() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartTotalSpan = document.getElementById('cartTotal');

    if (cart.length === 0) {
        cartItemsDiv.innerHTML = 'Keranjang kosong';
        cartTotalSpan.textContent = '0';
        return;
    }

    let html = '';
    let total = 0;
    cart.forEach((item, i) => {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                <div>
                    <strong>${item.name}</strong><br>
                    <small>Kode: ${item.product_code} | ${item.quantity} x Rp ${new Intl.NumberFormat('id-ID').format(item.unit_price)}</small>
                </div>
                <div class="text-end">
                    Rp ${new Intl.NumberFormat('id-ID').format(item.total)}
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart(${i})">Hapus</button>
                </div>
            </div>
        `;
        total += item.total;
    });

    cartItemsDiv.innerHTML = html;
    cartTotalSpan.textContent = new Intl.NumberFormat('id-ID').format(total);
    toggleCheckoutButton();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function toggleCheckoutButton() {
    const customerName = document.getElementById('customerName').value.trim();
    const checkoutBtn = document.getElementById('checkoutBtn');
    const isDisabled = cart.length === 0 || customerName === '';
    checkoutBtn.disabled = isDisabled;
    checkoutBtn.style.cursor = isDisabled ? 'not-allowed' : 'pointer';
}

// === Checkout ===
async function checkout() {
    if (cart.length === 0) {
        Swal.fire({
            title: 'Error',
            text: 'Keranjang kosong',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    const customerName = document.getElementById('customerName').value;
    const paymentMethod = document.getElementById('paymentMethod').value;

    const items = cart.map(i => ({ product_id: i.product_id, quantity: i.quantity }));

    const res = await fetch('{{ route("cashier.process") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ customer_name: customerName, payment_method: paymentMethod, items })
    });

    const data = await res.json();
    if (data.success) {
        Swal.fire({
            title: 'Berhasil',
            text: 'Transaksi berhasil! Kode: ' + data.transaction.transaction_code,
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '{{ route("cashier.receipt", ":code") }}'.replace(':code', data.transaction.transaction_code);
        });
    } else {
        Swal.fire({
            title: 'Error',
            text: data.message || 'Gagal memproses transaksi',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

// === Event Listeners ===
document.getElementById('addToCartBtn').addEventListener('click', addToCart);
document.getElementById('checkoutBtn').addEventListener('click', checkout);
document.getElementById('barcodeInput').addEventListener('keypress', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        performManualScan();
    }
});
document.getElementById('customerName').addEventListener('input', toggleCheckoutButton);
</script>
@endsection
