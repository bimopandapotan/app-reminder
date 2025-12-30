@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h5>Scan QR WhatsApp</h5>
                </div>

                <div class="card-body text-center">
                    <p class="text-sm mb-3">
                        Buka <b>WhatsApp</b> → <b>Linked Devices</b> → <b>Link a Device</b>
                    </p>

                    <div id="qr-container">
                        <div id="loading-spinner" class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div id="qrcode" class="d-flex justify-content-center mt-2"></div>
                        <p id="instruction-text" class="mt-2 text-secondary">Menunggu koneksi ke server Node.js...</p>
                    </div>

                    <div id="status" class="mt-3 fw-bold text-info">Menghubungkan...</div>
                    
                    <div id="mini-log" class="mt-3 p-2 text-start bg-light rounded shadow-sm d-none" style="font-size: 11px; max-height: 100px; overflow-y: auto;">
                        <b>Log:</b> <span id="log-content"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Load Socket.io Client & Library QR Code --}}
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    const qrDiv = document.getElementById('qrcode');
    const statusText = document.getElementById('status');
    const spinner = document.getElementById('loading-spinner');
    const instruction = document.getElementById('instruction-text');
    const miniLog = document.getElementById('mini-log');
    const logContent = document.getElementById('log-content');

    // Hubungkan ke server Node.js Sir (Ganti IP jika server berbeda)
    const socket = io("http://localhost:88");

    // 1. Saat Terhubung ke Socket Server
    socket.on('connect', () => {
        statusText.innerText = 'Terhubung ke Server WA 🟢';
        statusText.className = 'mt-3 fw-bold text-success';
        instruction.innerText = 'Menunggu kode QR terbaru...';
    });

    // 2. Menerima String QR Code dari Node.js
    socket.on('qr_code', (qrString) => {
        spinner.classList.add('d-none'); // Sembunyikan spinner
        instruction.innerText = 'Silakan scan QR Code di atas';
        
        // Bersihkan QR lama dan generate yang baru
        qrDiv.innerHTML = ""; 
        new QRCode(qrDiv, {
            text: qrString,
            width: 256,
            height: 256
        });
    });

    // 3. Update Status (Waiting, Authenticated, Connected, Disconnected)
    socket.on('status', (status) => {
        statusText.innerText = 'Status: ' + status;

        if (status === 'Connected') {
            qrDiv.innerHTML = "<h1 class='display-1 text-success'>✅</h1>";
            instruction.innerText = 'WhatsApp Aktif & Siap Mengirim Reminder';
            spinner.classList.add('d-none');
            miniLog.classList.remove('d-none'); // Tampilkan log kalau sudah ready
        }

        if (status === 'Disconnected') {
            qrDiv.innerHTML = "";
            spinner.classList.remove('d-none');
            instruction.innerText = 'Mencoba menghubungkan ulang...';
        }
    });

    // 4. Tangkap Log dari Node.js (Berhasil kirim pesan, dll)
    socket.on('log', (msg) => {
        const time = new Date().toLocaleTimeString();
        logContent.innerHTML = `<div>[${time}] ${msg}</div>` + logContent.innerHTML;
    });

    // Handle saat koneksi putus
    socket.on('disconnect', () => {
        statusText.innerText = 'Server Node.js Mati 🔴';
        statusText.className = 'mt-3 fw-bold text-danger';
    });
</script>
@endsection