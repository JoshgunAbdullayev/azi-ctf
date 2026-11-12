<?php
if (isset($_GET['download'])) {
    $apk_path = __DIR__ . '/novabank.apk';
    if (file_exists($apk_path)) {
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename="novabank-v0.1-alpha.apk"');
        header('Content-Length: ' . filesize($apk_path));
        header('Cache-Control: no-cache');
        readfile($apk_path);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaBank Mobile - Internal Distribution</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f5;min-height:100vh;display:flex;align-items:center;justify-content:center}
        .card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);max-width:420px;width:90%;padding:2.5rem;text-align:center}
        .icon{width:80px;height:80px;background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem;color:#fff}
        h1{font-size:1.3rem;color:#1a1a2e;margin-bottom:.25rem}
        .version{color:#888;font-size:.85rem;margin-bottom:1.5rem}
        .info{background:#f8f9fa;border-radius:8px;padding:1rem;margin-bottom:1.5rem;font-size:.82rem;text-align:left;color:#555;line-height:1.6}
        .info strong{color:#333}
        .dl-btn{display:inline-flex;align-items:center;gap:.5rem;background:#1a1a2e;color:#fff;border:none;padding:.85rem 2rem;border-radius:10px;font-size:.95rem;cursor:pointer;transition:all .2s}
        .dl-btn:hover{background:#16213e;transform:translateY(-1px);box-shadow:0 4px 12px rgba(26,26,46,.3)}
        .dl-btn svg{width:20px;height:20px}
        .warning{margin-top:1.5rem;padding:.75rem;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;font-size:.78rem;color:#856404;text-align:left}
        .overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.85);z-index:999;align-items:center;justify-content:center;flex-direction:column}
        .overlay.active{display:flex}
        .overlay-text{color:#fff;font-size:1.4rem;max-width:500px;text-align:center;padding:2rem;font-family:'Courier New',monospace;line-height:1.8;letter-spacing:.05rem}
        .overlay-sub{color:#666;font-size:.85rem;margin-top:2rem;font-family:'Courier New',monospace}
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">NB</div>
        <h1>NovaBank Mobile</h1>
        <div class="version">Version 0.1-alpha (Internal Build)</div>

        <div class="info">
            <strong>Build:</strong> debug-84729361<br>
            <strong>Platform:</strong> Android (API 26+)<br>
            <strong>Size:</strong> ~2.1 KB (prototype)<br>
            <strong>Date:</strong> 2026-02-10<br>
            <strong>Signed:</strong> Debug keystore
        </div>

        <button class="dl-btn" id="dlBtn" onclick="startDownload()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download APK
        </button>

        <div class="warning">
            <strong>INTERNAL USE ONLY</strong><br>
            Bu yapi yalnizca test amaclidir ve ilk alfa surumudur. Uretim ortamina dagitilmamalidir.
            Debug sembolleri ve test kimlik bilgileri icerir. Harici taraflara iletmeyin.
        </div>
    </div>

    <div class="overlay" id="overlay">
        <div class="overlay-text">
            Web bitti el&#601; bilirsn bundan sonrasi asandi h&#601;&#601;?
        </div>
        <div class="overlay-sub">[ reverse engineering challenge awaits ]</div>
    </div>

    <script>
    function startDownload() {
        var btn = document.getElementById('dlBtn');
        btn.textContent = 'Downloading...';
        btn.disabled = true;

        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '/mobile/app/84729361/?download=1';
        document.body.appendChild(iframe);

        setTimeout(function() {
            document.getElementById('overlay').classList.add('active');
        }, 1500);
    }
    </script>
</body>
</html>
