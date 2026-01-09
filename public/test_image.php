<!DOCTYPE html>
<html>
<head>
    <title>Test Bukti Transfer</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        img { max-width: 500px; border: 2px solid #ccc; margin: 10px 0; }
        .info { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Bukti Transfer Display</h1>
    
    <div class="info">
        <strong>File Path:</strong> deposits/JGfvgyLtwrIYp3VrEzeBNCXKv422xyCGJA2HMsWe.png
    </div>

    <h2>Method 1: asset('storage/...')</h2>
    <img src="<?php echo asset('storage/deposits/JGfvgyLtwrIYp3VrEzeBNCXKv422xyCGJA2HMsWe.png'); ?>" alt="Test 1">

    <h2>Method 2: Direct URL</h2>
    <img src="/storage/deposits/JGfvgyLtwrIYp3VrEzeBNCXKv422xyCGJA2HMsWe.png" alt="Test 2">

    <h2>Method 3: Full path</h2>
    <img src="http://127.0.0.1:8000/storage/deposits/JGfvgyLtwrIYp3VrEzeBNCXKv422xyCGJA2HMsWe.png" alt="Test 3">
</body>
</html>
