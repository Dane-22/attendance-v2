<?php
// qr-scanner.php - QR Code Login Scanner

// Load environment variables from .env file
require_once __DIR__ . '/core/Dotenv.php';
Dotenv::load();

include __DIR__ . '/conn/db_connection.php';
session_start();

// Get base URL for subdirectory installs
$baseUrl = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = ($baseUrl === '/' || $baseUrl === '\\') ? '' : $baseUrl;

// If already logged in, redirect to dashboard
if (!empty($_SESSION['employee_id'])) {
    header('Location: ' . $baseUrl . '/employee/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle QR code login via POST (from scanner)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_data'])) {
    $qrData = $_POST['qr_data'];
    
    // Parse QR code data - expected format: employee_code or encrypted token
    // For demo, we'll check if it matches an employee code
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE employee_code = ? LIMIT 1");
    $stmt->execute([$qrData]);
    $employee = $stmt->fetch();
    
    if ($employee) {
        $_SESSION['employee_id'] = $employee['id'];
        $_SESSION['employee_name'] = $employee['first_name'] . ' ' . $employee['last_name'];
        $success = 'Login successful! Redirecting...';
        header('Refresh: 2; URL=' . $baseUrl . '/employee/dashboard.php');
    } else {
        $error = 'Invalid QR code. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner - JAJR Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/universal.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        .scanner-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            min-height: 100vh;
        }
        .scanner-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        #reader {
            border-radius: 12px;
            overflow: hidden;
        }
        #reader video {
            border-radius: 12px;
        }
        .scan-line {
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #FF6B00, transparent);
            animation: scan 2s linear infinite;
        }
        @keyframes scan {
            0% { transform: translateY(-100px); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(100px); opacity: 0; }
        }
        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 4px solid #FF6B00;
        }
        .corner-tl { top: 20px; left: 20px; border-right: none; border-bottom: none; }
        .corner-tr { top: 20px; right: 20px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 20px; left: 20px; border-right: none; border-top: none; }
        .corner-br { bottom: 20px; right: 20px; border-left: none; border-top: none; }
    </style>
</head>
<body class="scanner-bg font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="scanner-container w-full max-w-lg rounded-2xl shadow-2xl p-6">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">QR Code Scanner</h1>
                <p class="text-gray-500 mt-1">Position the QR code within the frame</p>
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <!-- Scanner Area -->
            <div class="relative bg-black rounded-xl overflow-hidden mb-6">
                <div id="reader" class="w-full"></div>
                <!-- Scan overlay -->
                <div class="absolute inset-0 pointer-events-none">
                    <div class="corner corner-tl"></div>
                    <div class="corner corner-tr"></div>
                    <div class="corner corner-bl"></div>
                    <div class="corner corner-br"></div>
                    <div class="scan-line"></div>
                </div>
            </div>

            <!-- Manual Entry Option -->
            <div class="border-t border-gray-200 pt-6">
                <p class="text-sm text-gray-500 text-center mb-4">Camera not working?</p>
                <form method="POST" action="" class="space-y-4">
                    <div>
                        <input 
                            type="text" 
                            name="qr_data" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Enter Employee Code manually"
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all"
                    >
                        Verify Code
                    </button>
                </form>
            </div>

            <!-- Back Button -->
            <div class="mt-6 text-center">
                <a href="<?= $baseUrl ?>/login" class="text-sm text-gray-500 hover:text-orange-600 flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        // Initialize QR Scanner
        function onScanSuccess(decodedText, decodedResult) {
            // Send the scanned data to server
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'qr_data';
            input.value = decodedText;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        function onScanFailure(error) {
            // Handle scan failure (optional - usually just wait for next scan)
            console.warn(`Code scan error = ${error}`);
        }

        // Start scanner when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                /* verbose= */ false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });
    </script>
</body>
</html>
