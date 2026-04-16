<?php
$title = 'Branch QR Scanner';
ob_start();
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #000;
        min-height: 100vh;
        overflow: hidden;
    }

    .scanner-container {
        position: relative;
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .scanner-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100;
        background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .branch-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .branch-badge {
        background: #ffd700;
        color: #000;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .branch-name {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .logout-btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .logout-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Camera Area */
    .camera-container {
        flex: 1;
        position: relative;
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Scan Overlay */
    .scan-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 280px;
        height: 280px;
        border: 2px solid rgba(255, 215, 0, 0.5);
        border-radius: 20px;
    }

    .scan-corner {
        position: absolute;
        width: 40px;
        height: 40px;
        border: 4px solid #ffd700;
    }

    .scan-corner.top-left {
        top: -2px;
        left: -2px;
        border-right: none;
        border-bottom: none;
        border-radius: 20px 0 0 0;
    }

    .scan-corner.top-right {
        top: -2px;
        right: -2px;
        border-left: none;
        border-bottom: none;
        border-radius: 0 20px 0 0;
    }

    .scan-corner.bottom-left {
        bottom: -2px;
        left: -2px;
        border-right: none;
        border-top: none;
        border-radius: 0 0 0 20px;
    }

    .scan-corner.bottom-right {
        bottom: -2px;
        right: -2px;
        border-left: none;
        border-top: none;
        border-radius: 0 0 20px 0;
    }

    .scan-line {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: #ffd700;
        box-shadow: 0 0 10px #ffd700;
        animation: scan 2s linear infinite;
    }

    @keyframes scan {
        0% { top: 0; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }

    .scan-instruction {
        position: absolute;
        bottom: -50px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: 0.9rem;
        white-space: nowrap;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    /* Status Panel */
    .status-panel {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
        padding: 30px 24px 40px;
        min-height: 180px;
    }

    .last-scan {
        display: flex;
        align-items: center;
        gap: 16px;
        color: #fff;
    }

    .scan-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .scan-icon.success {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }

    .scan-icon.error {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .scan-icon.check-in {
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }

    .scan-icon.check-out {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }

    .scan-details h3 {
        font-size: 1.1rem;
        margin-bottom: 4px;
    }

    .scan-details p {
        color: #888;
        font-size: 0.9rem;
    }

    /* Audio feedback disabled indicator */
    .audio-indicator {
        position: absolute;
        top: 80px;
        right: 24px;
        color: rgba(255,255,255,0.5);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Offline indicator */
    .offline-indicator {
        position: absolute;
        top: 80px;
        left: 24px;
        background: rgba(239, 68, 68, 0.9);
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        display: none;
        align-items: center;
        gap: 8px;
    }

    .offline-indicator.show {
        display: flex;
    }

    /* Loading state */
    .loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: 1rem;
    }
</style>

<div class="scanner-container">
    <!-- Header -->
    <div class="scanner-header">
        <div class="branch-info">
            <div class="branch-badge">
                <i class="fas fa-building"></i>
                <?= htmlspecialchars($branch['branch_code']) ?>
            </div>
            <div class="branch-name"><?= htmlspecialchars($branch['branch_name']) ?></div>
        </div>
        <button class="logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>

    <!-- Offline Indicator -->
    <div class="offline-indicator" id="offlineIndicator">
        <i class="fas fa-wifi-slash"></i> Offline Mode
    </div>

    <!-- Audio Disabled Indicator -->
    <div class="audio-indicator">
        <i class="fas fa-volume-mute"></i> Audio feedback disabled
    </div>

    <!-- Camera Area -->
    <div class="camera-container">
        <div class="loading" id="loading">Initializing camera...</div>
        <video id="video" playsinline></video>
        
        <!-- Scan Overlay -->
        <div class="scan-overlay">
            <div class="scan-corner top-left"></div>
            <div class="scan-corner top-right"></div>
            <div class="scan-corner bottom-left"></div>
            <div class="scan-corner bottom-right"></div>
            <div class="scan-line"></div>
            <div class="scan-instruction">Align QR code within frame</div>
        </div>
    </div>

    <!-- Status Panel -->
    <div class="status-panel">
        <div class="last-scan" id="lastScan">
            <div class="scan-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="scan-details">
                <h3>Ready to scan</h3>
                <p>Point camera at employee QR code</p>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
    const video = document.getElementById('video');
    const loading = document.getElementById('loading');
    const lastScan = document.getElementById('lastScan');
    const offlineIndicator = document.getElementById('offlineIndicator');
    const branchCode = '<?= htmlspecialchars($branch['branch_code']) ?>';
    
    let scanning = true;
    let scanCooldown = false;

    // Check online status
    function updateOnlineStatus() {
        if (navigator.onLine) {
            offlineIndicator.classList.remove('show');
        } else {
            offlineIndicator.classList.add('show');
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    // Initialize camera with fallback constraints for mobile compatibility
    async function initCamera() {
        const constraints = [
            // Try environment camera first (back camera)
            { video: { facingMode: { exact: 'environment' } } },
            // Try facingMode: environment without exact constraint
            { video: { facingMode: 'environment' } },
            // Try any camera with high resolution
            { video: { width: { ideal: 1920 }, height: { ideal: 1080 } } },
            // Finally, try any video
            { video: true }
        ];

        for (let i = 0; i < constraints.length; i++) {
            try {
                console.log('Trying camera constraint:', constraints[i]);
                const stream = await navigator.mediaDevices.getUserMedia(constraints[i]);
                video.srcObject = stream;
                await video.play();
                loading.style.display = 'none';
                requestAnimationFrame(scanQR);
                console.log('Camera initialized successfully with constraint', i);
                return;
            } catch (err) {
                console.warn(`Camera constraint ${i} failed:`, err.message);
                // Continue to next constraint
            }
        }

        // All constraints failed
        loading.innerHTML = `
            <div style="text-align: center; padding: 20px;">
                <p style="margin-bottom: 15px;">Camera access failed.</p>
                <p style="font-size: 0.9rem; color: #888; margin-bottom: 15px;">
                    Please ensure:<br>
                    - You're using HTTPS (required for camera)<br>
                    - Camera permissions are allowed in browser settings<br>
                    - Try refreshing the page
                </p>
                <button onclick="initCamera()" style="
                    background: #ffd700;
                    color: #000;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                ">Retry Camera</button>
            </div>
        `;
        console.error('All camera constraints failed');
    }

    // Scan QR code from video frame
    function scanQR() {
        if (!scanning || video.paused || video.ended) {
            requestAnimationFrame(scanQR);
            return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);

        if (code && !scanCooldown) {
            handleScan(code.data);
        }

        requestAnimationFrame(scanQR);
    }

    // Handle scanned QR code
    async function handleScan(qrData) {
        scanCooldown = true;
        
        try {
            const response = await fetch('/branch/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'qr_data=' + encodeURIComponent(qrData)
            });

            const result = await response.json();
            displayResult(result);
        } catch (error) {
            console.error('Scan error:', error);
            displayResult({
                error: 'Network error. Check connection.'
            });
        }

        // 3 second cooldown between scans
        setTimeout(() => {
            scanCooldown = false;
        }, 3000);
    }

    // Display scan result
    function displayResult(result) {
        let iconClass = 'error';
        let icon = 'fa-exclamation-circle';
        let title = 'Error';
        let message = result.error || 'Unknown error';

        if (result.success) {
            if (result.action === 'check_in') {
                iconClass = 'check-in';
                icon = 'fa-sign-in-alt';
                title = 'Checked In';
                message = `${result.employee.name} at ${result.employee.time}`;
            } else if (result.action === 'check_out') {
                iconClass = 'check-out';
                icon = 'fa-sign-out-alt';
                title = 'Checked Out';
                message = `${result.employee.name} at ${result.employee.time}`;
            }
        }

        lastScan.innerHTML = `
            <div class="scan-icon ${iconClass}">
                <i class="fas ${icon}"></i>
            </div>
            <div class="scan-details">
                <h3>${title}</h3>
                <p>${message}</p>
            </div>
        `;

        // Reset after 5 seconds
        setTimeout(() => {
            lastScan.innerHTML = `
                <div class="scan-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="scan-details">
                    <h3>Ready to scan</h3>
                    <p>Point camera at employee QR code</p>
                </div>
            `;
        }, 5000);
    }

    function logout() {
        if (confirm('Logout from this branch device?')) {
            window.location.href = '/branch/logout';
        }
    }

    // Start camera (HTTPS check disabled - handled by browser)
    initCamera();
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/scanner.php'; ?>
