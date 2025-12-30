@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Scan QR WhatsApp</h5>

                    {{-- Tombol Logout WA --}}
                    <button id="btn-logout-wa" class="btn btn-sm btn-outline-danger d-none">
                        <i class="fas fa-plug-circle-xmark me-1"></i> Logout WA
                    </button>
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

                        <p id="instruction-text" class="mt-2 text-secondary">
                            Menunggu koneksi ke server Node.js...
                        </p>
                    </div>

                    <div id="status" class="mt-3 fw-bold text-info">
                        Menghubungkan...
                    </div>

                    <div id="mini-log"
                        class="mt-3 p-2 text-start bg-light rounded shadow-sm d-none"
                        style="font-size:11px; max-height:120px; overflow-y:auto;">
                        <b>Log:</b>
                        <div id="log-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Socket.io & QR --}}
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    const qrDiv        = document.getElementById('qrcode');
    const statusText   = document.getElementById('status');
    const spinner      = document.getElementById('loading-spinner');
    const instruction  = document.getElementById('instruction-text');
    const miniLog      = document.getElementById('mini-log');
    const logContent   = document.getElementById('log-content');
    const logoutBtn    = document.getElementById('btn-logout-wa');

    // ===============================
    // Connect ke Node.js (via config)
    // ===============================
    const socket = io("{{ config('services.whatsapp.node_endpoint') }}");

    // ===============================
    // Socket Events
    // ===============================
    socket.on('connect', () => {
        statusText.innerText = 'Terhubung ke Server WA 🟢';
        statusText.className = 'mt-3 fw-bold text-success';
        instruction.innerText = 'Menunggu QR Code...';
    });

    socket.on('qr_code', (qrString) => {
        spinner.classList.add('d-none');
        instruction.innerText = 'Silakan scan QR Code';

        qrDiv.innerHTML = '';
        new QRCode(qrDiv, {
            text: qrString,
            width: 256,
            height: 256
        });
    });

    socket.on('status', (status) => {
        statusText.innerText = 'Status: ' + status;

        // ===============================
        // STATE HANDLING
        // ===============================
        if (status === 'Connected') {
            qrDiv.innerHTML = "<h1 class='display-1 text-success'>✅</h1>";
            instruction.innerText = 'WhatsApp Aktif & Siap Digunakan';
            spinner.classList.add('d-none');

            logoutBtn.classList.remove('d-none');
            logoutBtn.disabled = false;
            miniLog.classList.remove('d-none');
        }

        if (status === 'Waiting for Scan' || status === 'Authenticated') {
            logoutBtn.classList.add('d-none');
        }

        if (status === 'Disconnected' || status === 'Logging Out') {
            qrDiv.innerHTML = '';
            spinner.classList.remove('d-none');
            instruction.innerText = 'Menunggu koneksi ulang...';

            logoutBtn.disabled = true;
        }
    });

    socket.on('log', (msg) => {
        const time = new Date().toLocaleTimeString();
        logContent.innerHTML =
            `<div>[${time}] ${msg}</div>` + logContent.innerHTML;
    });

    socket.on('disconnect', () => {
        statusText.innerText = 'Server Node.js Mati 🔴';
        statusText.className = 'mt-3 fw-bold text-danger';
        logoutBtn.classList.add('d-none');
    });

    // ===============================
    // Logout WhatsApp
    // ===============================
    logoutBtn.addEventListener('click', () => {
        if (!confirm('Logout WhatsApp dan login dengan nomor lain?')) return;

        logoutBtn.disabled = true;
        statusText.innerText = 'Logging out WhatsApp...';
        statusText.className = 'mt-3 fw-bold text-warning';

        socket.emit('logout-wa');
    });
</script>
@endsection
