<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Details - {{ $product->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .product-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-row {
            display: table-row;
        }
        .info-label, .info-value {
            display: table-cell;
            padding: 8px 15px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
            background-color: #f8f9fa;
        }
        .barcode-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #333;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .barcode-image {
            margin: 20px 0;
        }
        .barcode-code {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3px;
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            display: inline-block;
            margin-top: 10px;
        }
        .product-image {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="barcode-section">
        <h3>{{ $product->name }}</h3>
        <div class="barcode-image">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($product->product_code, 'C128',3,100) }}" alt="barcode" />
        </div>
        <div class="barcode-code">{{ $product->product_code }}</div>
    </div>


    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <p>Sistem Manajemen Grosir Sendal</p>
    </div>
</body>
</html>
