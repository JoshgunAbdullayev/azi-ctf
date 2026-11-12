<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$secret = 'adnsu7floor';
$authenticated = false;
$token_raw = '';

$token_raw = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? '';
if (!$token_raw && function_exists('apache_request_headers')) {
    $h = apache_request_headers();
    foreach ($h as $k => $v) {
        if (strtolower($k) === 'x-auth-token') { $token_raw = $v; break; }
    }
}

if (!$token_raw && isset($_POST['token'])) $token_raw = $_POST['token'];
if (!$token_raw && isset($_GET['token']))  $token_raw = $_GET['token'];

$input = @file_get_contents('php://input');
if (!$token_raw && $input) {
    $json_body = @json_decode($input, true);
    if ($json_body && isset($json_body['user'], $json_body['role'], $json_body['signature'])) {
        $token_raw = $input;
    }
}

if ($token_raw !== '') {
    $data = @json_decode($token_raw, true);
    if ($data && isset($data['user'], $data['role'], $data['signature'])) {
        $expected = hash_hmac('sha256', $data['user'] . $data['role'], $secret);
        if (hash_equals($expected, $data['signature']) && $data['role'] === 'admin') {
            $_SESSION['vault_auth'] = true;
            $authenticated = true;
        }
    }
}

if (!$authenticated && isset($_SESSION['vault_auth']) && $_SESSION['vault_auth'] === true) {
    $authenticated = true;
}

if (!$authenticated) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'status' => 403,
        'error' => 'Unauthorized access',
        'message' => 'Authentication token required.',
        'auth_method' => 'HMAC-SHA256',
        'token_delivery' => 'X-Auth-Token header, ?token= parameter, or POST body'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NB Classified Vault</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#0a0a0a;color:#00ff41;min-height:100vh}
        .vc{max-width:1000px;margin:0 auto;padding:2rem}
        .vh{text-align:center;padding:3rem 0 2rem;border-bottom:1px solid #1a3a1a;margin-bottom:2rem}
        .vh h1{font-size:1.8rem;letter-spacing:.3rem;text-transform:uppercase;margin-bottom:.5rem;text-shadow:0 0 10px #00ff41}
        .vh .sub{color:#006b1a;font-size:.85rem}
        .sec{background:#0d1a0d;border:1px solid #1a3a1a;border-radius:4px;padding:1.5rem;margin-bottom:1.5rem}
        .sec h2{color:#00cc33;font-size:1rem;margin-bottom:1rem;border-bottom:1px solid #1a3a1a;padding-bottom:.5rem}
        .fb{background:#001a00;border:2px solid #00ff41;border-radius:4px;padding:1.5rem;text-align:center;margin:1.5rem 0}
        .fb .flag{font-size:1.3rem;color:#00ff41;text-shadow:0 0 15px #00ff41;letter-spacing:.1rem}
        .stamp{text-align:center;color:#ff3333;font-size:2rem;font-weight:bold;letter-spacing:.5rem;text-transform:uppercase;border:3px double #ff3333;padding:.5rem 2rem;display:inline-block;transform:rotate(-3deg);margin:1rem 0;opacity:.9}
        .et{width:100%;border-collapse:collapse;font-size:.8rem;margin:1rem 0}
        .et th{text-align:left;padding:.5rem;border-bottom:1px solid #333;color:#00cc33}
        .et td{padding:.5rem;border-bottom:1px solid #1a1a1a;color:#00ff41}
        .et tr:hover{background:#001a00}
        .sus{color:#ff4444!important}
        .amt{text-align:right}
        .tg{display:inline-block;font-size:.7rem;padding:.1rem .4rem;border-radius:2px;margin-left:.3rem}
        .tr{background:#330000;color:#ff4444;border:1px solid #ff4444}
        .ty{background:#332200;color:#ffaa00;border:1px solid #ffaa00}
        .cls{border:2px solid #ff3333}
        .cls h2{color:#ff3333}
        .conc{background:#1a0000;border:1px solid #ff3333;border-radius:4px;padding:1.5rem;margin-top:1.5rem;color:#ff6666;font-size:.85rem;line-height:1.6}

        .chat-box{background:#111;border:1px solid #222;border-radius:6px;max-height:420px;overflow-y:auto;padding:1rem;margin-top:.75rem}
        .chat-msg{padding:.6rem .8rem;margin-bottom:.5rem;border-radius:6px;font-size:.82rem;opacity:0;animation:fadeIn .4s forwards}
        @keyframes fadeIn{to{opacity:1}}
        .cm-admin{background:#1a0a0a;border-left:3px solid #ff4444}
        .cm-mgr{background:#0a1a0a;border-left:3px solid #00cc33}
        .cm-fin{background:#1a1a0a;border-left:3px solid #ffaa00}
        .cm-name{font-weight:700;font-size:.75rem;text-transform:uppercase}
        .cm-time{font-size:.7rem;color:#444;float:right}
        .cm-text{margin-top:.3rem;color:#aaa;line-height:1.5}
        .typing-ind{color:#444;font-size:.78rem;padding:.5rem;display:none}
        .typing-ind.active{display:block}
        .typing-dot{animation:blink 1s infinite}
        @keyframes blink{0%,100%{opacity:.2}50%{opacity:1}}

        .search-box{display:flex;gap:.5rem;margin-top:.75rem}
        .search-input{flex:1;background:#000;border:1px solid #1a3a1a;color:#00ff41;font-family:inherit;font-size:.85rem;padding:.6rem;border-radius:4px;outline:none}
        .search-input:focus{border-color:#00ff41;box-shadow:0 0 5px rgba(0,255,65,.2)}
        .search-btn{background:#003300;color:#00ff41;border:1px solid #00ff41;padding:.6rem 1.5rem;font-family:inherit;font-size:.85rem;cursor:pointer;border-radius:4px}
        .search-btn:hover{background:#004400}
        .search-result{background:#000;border:1px solid #1a1a1a;border-radius:4px;padding:.75rem;margin-top:.5rem;font-size:.82rem;color:#666;display:none}
    </style>
</head>
<body>
<div class="vc">
    <div class="vh">
        <h1>NovaBank Classified Vault</h1>
        <div class="sub">[ RESTRICTED ACCESS - INTERNAL INVESTIGATION DIVISION ]</div>
    </div>

    <div style="text-align:center;margin-bottom:2rem"><div class="stamp">CLASSIFIED</div></div>

    <div class="fb">
        <div style="font-size:.8rem;color:#006b1a;margin-bottom:.5rem">VAULT ACCESS GRANTED - AUTHENTICATION SUCCESSFUL</div>
        <div class="flag">ADNSU7{hmac_token_forgery_7c2d9a}</div>
        <div style="font-size:.75rem;color:#006b1a;margin-top:.5rem">Case File: NB-INV-2026-0047</div>
    </div>

    <div class="sec cls">
        <h2>// INTERNAL INVESTIGATION - MONEY LAUNDERING EVIDENCE</h2>
        <p style="color:#ff6666;font-size:.85rem;margin-bottom:1rem">
            The following records were extracted from NovaBank's internal transaction database.
            Analysis reveals systematic money laundering operations through shell company accounts.
        </p>

        <h3 style="color:#ffaa00;font-size:.9rem;margin:1.5rem 0 .5rem">Suspicious Transaction Pattern: Structuring (Smurfing)</h3>
        <table class="et">
            <thead><tr><th>Date</th><th>Account</th><th>Entity</th><th class="amt">Amount (TRY)</th><th>Flag</th></tr></thead>
            <tbody>
                <tr class="sus"><td>2025-09-14</td><td>NBK-CORP-044</td><td>TechVenture Holdings LLC</td><td class="amt">9,980.00</td><td><span class="tg tr">STRUCTURING</span></td></tr>
                <tr class="sus"><td>2025-09-14</td><td>NBK-CORP-044</td><td>TechVenture Holdings LLC</td><td class="amt">9,950.00</td><td><span class="tg tr">STRUCTURING</span></td></tr>
                <tr class="sus"><td>2025-09-15</td><td>NBK-CORP-044</td><td>TechVenture Holdings LLC</td><td class="amt">9,999.00</td><td><span class="tg tr">STRUCTURING</span></td></tr>
                <tr class="sus"><td>2025-09-15</td><td>NBK-CORP-089</td><td>Mediterranean Trade Co.</td><td class="amt">9,870.00</td><td><span class="tg tr">STRUCTURING</span></td></tr>
                <tr class="sus"><td>2025-09-16</td><td>NBK-CORP-089</td><td>Mediterranean Trade Co.</td><td class="amt">9,990.00</td><td><span class="tg tr">STRUCTURING</span></td></tr>
            </tbody>
        </table>

        <h3 style="color:#ffaa00;font-size:.9rem;margin:1.5rem 0 .5rem">Layering: Offshore Wire Transfers</h3>
        <table class="et">
            <thead><tr><th>Date</th><th>From</th><th>To</th><th>Destination</th><th class="amt">Amount (EUR)</th><th>Flag</th></tr></thead>
            <tbody>
                <tr class="sus"><td>2025-10-02</td><td>NBK-CORP-044</td><td>CYP-8827134</td><td>Cyprus</td><td class="amt">245,000.00</td><td><span class="tg tr">OFFSHORE</span></td></tr>
                <tr class="sus"><td>2025-10-02</td><td>CYP-8827134</td><td>KYM-0044521</td><td>Cayman Islands</td><td class="amt">244,500.00</td><td><span class="tg tr">LAYERING</span></td></tr>
                <tr class="sus"><td>2025-10-03</td><td>KYM-0044521</td><td>SEY-3389012</td><td>Seychelles</td><td class="amt">243,800.00</td><td><span class="tg tr">LAYERING</span></td></tr>
                <tr class="sus"><td>2025-10-05</td><td>NBK-CORP-089</td><td>CYP-8827134</td><td>Cyprus</td><td class="amt">178,000.00</td><td><span class="tg tr">OFFSHORE</span></td></tr>
                <tr class="sus"><td>2025-10-08</td><td>NBK-CORP-112</td><td>PNM-5567823</td><td>Panama</td><td class="amt">520,000.00</td><td><span class="tg tr">OFFSHORE</span></td></tr>
            </tbody>
        </table>

        <h3 style="color:#ffaa00;font-size:.9rem;margin:1.5rem 0 .5rem">Shell Company Account Summary</h3>
        <table class="et">
            <thead><tr><th>Account ID</th><th>Entity Name</th><th>Registered</th><th class="amt">Total Outflow</th><th>Status</th></tr></thead>
            <tbody>
                <tr class="sus"><td>NBK-CORP-044</td><td>TechVenture Holdings LLC</td><td>Delaware, USA</td><td class="amt">14,240,000 TRY</td><td><span class="tg tr">SHELL COMPANY</span></td></tr>
                <tr class="sus"><td>NBK-CORP-089</td><td>Mediterranean Trade Co.</td><td>Malta</td><td class="amt">8,720,000 TRY</td><td><span class="tg tr">SHELL COMPANY</span></td></tr>
                <tr class="sus"><td>NBK-CORP-112</td><td>Apex Global Services</td><td>British Virgin Islands</td><td class="amt">22,400,000 TRY</td><td><span class="tg tr">SHELL COMPANY</span></td></tr>
            </tbody>
        </table>

        <div class="conc">
            <h3 style="color:#ff3333;font-size:1rem;margin-bottom:.75rem">INVESTIGATION CONCLUSION</h3>
            <p>Analysis of NovaBank's internal records reveals a <strong>systematic money laundering operation</strong>
            active since Q3 2025, involving at least three corporate shell accounts.</p><br>
            <p>Total estimated laundered funds: <strong>45+ million TRY</strong> through structuring, offshore layering,
            invoice fraud, and circular transfers. Evidence strongly suggests involvement of senior NovaBank management.</p><br>
            <p style="color:#ff3333;font-weight:bold">Recommendation: Immediate referral to MASAK and relevant law enforcement authorities.</p>
        </div>
    </div>

    <!-- LIVE CHAT SECTION -->
    <div class="sec" style="border-color:#333">
        <h2 style="color:#666">// Internal Communications - Live Intercept</h2>
        <p style="font-size:.82rem;color:#444;margin-bottom:.5rem">Intercepted channel: #vault-ops | Status: <span style="color:#00ff41">ACTIVE</span></p>
        <div class="chat-box" id="chatBox"></div>
        <div class="typing-ind" id="typingInd">
            <span class="typing-dot">.</span><span class="typing-dot" style="animation-delay:.2s">.</span><span class="typing-dot" style="animation-delay:.4s">.</span> kimsə yazır...
        </div>
    </div>

    <!-- DOCUMENT SEARCH SECTION -->
    <div class="sec">
        <h2>// Document Search Engine</h2>
        <p style="font-size:.82rem;color:#444;margin-bottom:.25rem">Search internal investigation documents and evidence files.</p>
        <div class="search-box">
            <input type="text" class="search-input" id="searchInput" placeholder="Enter search query...">
            <button class="search-btn" id="searchBtn" onclick="doSearch()">SEARCH</button>
        </div>
        <div class="search-result" id="searchResult"></div>
    </div>

    <div class="sec" style="border-color:#222">
        <h2 style="color:#444">// Session Info</h2>
        <div style="font-size:.8rem;color:#333">
            Access time: <?= date('Y-m-d H:i:s') ?> UTC | Case File: NB-INV-2026-0047 | Classification: TOP SECRET
        </div>
    </div>
</div>

<script>
var chatMessages = [
    {user:'K. Yildirim', role:'admin', cls:'cm-admin', color:'#ff4444', time:'14:22', msg:'Vəziyyəti araşdırdım. Offshore köçürmələrin həcmi gözləniləndən çox daha böyükdür.'},
    {user:'M. Demir', role:'manager', cls:'cm-mgr', color:'#00cc33', time:'14:23', msg:'MASAK hesabat həddinin altında qalmağa diqqət göstəriblər. Peşəkar bir əməliyyat.'},
    {user:'A. Kara', role:'finance', cls:'cm-fin', color:'#ffaa00', time:'14:24', msg:'Saxta faktura şirkəti Apex Consulting GmbH\'nin Almaniyada qeydiyyatı belə yoxdur. Tamamilə xəyali.'},
    {user:'K. Yildirim', role:'admin', cls:'cm-admin', color:'#ff4444', time:'14:25', msg:'Mediterranean Trade hesabındakı dövri köçürmələri gördüyünüzdə anlaşılar. Pul eyni nöqtəyə geri dönür.'},
    {user:'M. Demir', role:'manager', cls:'cm-mgr', color:'#00cc33', time:'14:27', msg:'Rəhbərliyin bundan xəbərsiz olması mümkün deyil. Hər əməliyyat CFO təsdiqlidir.'},
    {user:'A. Kara', role:'finance', cls:'cm-fin', color:'#ffaa00', time:'14:28', msg:'Ümumi həcm 45 milyon TRY-ni keçib. Son 6 ayda aydın bir sürətlənmə var.'},
    {user:'K. Yildirim', role:'admin', cls:'cm-admin', color:'#ff4444', time:'14:30', msg:'Məlumatların hamısı ehtiyat nüsxələnib. Sübut zənciri pozulmadan qorunacaq.'},
    {user:'K. Yildirim', role:'admin', cls:'cm-admin', color:'#ff4444', time:'14:31', msg:'Lazımi məlumatları saxladım və şifrələdim, bunu heç kimə ötürmə: 2f-2b-38-32-3b-57-0d-19-5e-1d-29-03-1c-5c-42-0a-31-5e-02-1c'},
    {user:'M. Demir', role:'manager', cls:'cm-mgr', color:'#00cc33', time:'14:32', msg:'Anlaşıldı. Öz tərəfimdəki tapıntıları da ayrı bir kanaldan ötürdüm.'},
    {user:'A. Kara', role:'finance', cls:'cm-fin', color:'#ffaa00', time:'14:33', msg:'MASAK-a rəsmi bildiriş üçün hazırlıq edirəm. Sabah səhərə qədər hazırlaya bilərəm.'},
    {user:'K. Yildirim', role:'admin', cls:'cm-admin', color:'#ff4444', time:'14:35', msg:'Çox yaxşı. Hamı diqqətli olsun. Bu səviyyədə bir əməliyyatı ifşa etmək asan olmayacaq.'},
    {user:'M. Demir', role:'manager', cls:'cm-mgr', color:'#00cc33', time:'14:36', msg:'Təhlükəsizlik komandası ilə koordinasiyalı irəliləyirik. Əlavə bir inkişaf olsa buradan paylaşacağam.'},
    {user:'K. Yildirim', role:'admin', cls:'cm-admin', color:'#ff4444', time:'14:38', msg:'Bu arada internal query API-sini istifadə edərək daha çox məlumat çəkə bilərsiniz. /nb-gql-svc-e8a1/ endpointi üzərindən əlçatandır. Giriş kodu: vlt_9k3mz_gql'},
    {user:'A. Kara', role:'finance', cls:'cm-fin', color:'#ffaa00', time:'14:39', msg:'Tamam, introspection ilə şemaları yoxlayacağam. Oradakı mutation-lar üzərindən əlavə qeydlərə çata bilərik.'}
];

var chatBox = document.getElementById('chatBox');
var typingInd = document.getElementById('typingInd');
var ci = 0;

function showTyping() {
    typingInd.classList.add('active');
    chatBox.scrollTop = chatBox.scrollHeight;
}
function hideTyping() {
    typingInd.classList.remove('active');
}

function addChatMsg() {
    if (ci >= chatMessages.length) { hideTyping(); return; }
    hideTyping();
    var m = chatMessages[ci];
    var div = document.createElement('div');
    div.className = 'chat-msg ' + m.cls;
    div.innerHTML = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.2rem">' +
        '<span class="cm-name" style="color:' + m.color + '">[' + m.role.toUpperCase() + '] ' + m.user + '</span>' +
        '<span class="cm-time">' + m.time + '</span></div>' +
        '<div class="cm-text">' + m.msg + '</div>';
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
    ci++;
    if (ci < chatMessages.length) {
        var delay = 1200 + Math.random() * 1800;
        setTimeout(showTyping, 400);
        setTimeout(addChatMsg, delay);
    }
}

setTimeout(addChatMsg, 800);

function doSearch() {
    var q = document.getElementById('searchInput').value.trim();
    var res = document.getElementById('searchResult');
    var btn = document.getElementById('searchBtn');
    if (!q) return;
    btn.disabled = true;
    btn.textContent = 'SEARCHING...';
    res.style.display = 'block';
    res.style.color = '#444';
    res.textContent = 'Searching documents...';

    var fd = new FormData();
    fd.append('q', q);

    fetch('/novabank-internal-7f2c9/vault/search.php', {method:'POST', body:fd})
    .then(function(r){return r.json()})
    .then(function(data){
        res.innerHTML = '<span style="color:#666">[DocSearch v1.2]</span> Query: "' +
            q.replace(/</g,'&lt;') + '"<br>' +
            '<span style="color:#ff4444">Results: 0 matches found.</span><br>' +
            '<span style="color:#333">Engine: NovaBank DocSearch | Filter: ' +
            (data.engine ? data.engine.filter_support : 'enabled') + '</span>';
        btn.disabled = false;
        btn.textContent = 'SEARCH';
    })
    .catch(function(){
        res.innerHTML = '<span style="color:#ff4444">Connection error.</span>';
        btn.disabled = false;
        btn.textContent = 'SEARCH';
    });
}

document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') doSearch();
});
</script>
</body>
</html>
