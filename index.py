# app.py
from flask import Flask, render_template_string

app = Flask(__name__)

# HTML template langsung di-embed biar simpel
HTML_PAGE = """
<!DOCTYPE html>
<html>
<head>
    <title>Barcode Scanner 1D</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
</head>
<body>
    <h2>Scan Barcode 1D dengan Kamera Laptop</h2>
    <div id="scanner-container" style="width: 600px; height: 400px; border: 1px solid black;"></div>
    <p><b>Hasil Scan:</b> <span id="result">Belum ada</span></p>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === "function") {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#scanner-container'),
                        constraints: {
                            width: 600,
                            height: 400,
                            facingMode: "environment" // bisa diganti "user" untuk kamera depan
                        }
                    },
                    decoder: {
                        readers: [
                            "code_128_reader"
                        ]
                    }
                }, function(err) {
                    if (err) {
                        console.log(err);
                        return;
                    }
                    console.log("Quagga initialized");
                    Quagga.start();
                });

                Quagga.onDetected(function(data) {
                    document.getElementById("result").innerText = data.codeResult.code;
                });
            } else {
                alert("Browser tidak mendukung kamera!");
            }
        });
    </script>
</body>
</html>
"""


@app.route("/")
def index():
    return render_template_string(HTML_PAGE)


if __name__ == "__main__":
    app.run(debug=True)
