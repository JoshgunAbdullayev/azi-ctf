<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: text/html; charset=utf-8');

$report_name = $_GET['report'] ?? $_POST['report'] ?? '';
$generated = false;
$output = '';

if (!empty($report_name)) {
    $template = "Security Audit Report: " . $report_name . "\nGenerated: " . date('Y-m-d H:i:s') . "\nStatus: Complete";

    // Note: {{config}} is intentionally allowed so the full config (incl. flag)
    // can be dumped. Narrow gadget patterns below still require real SSTI knowledge.
    $ssti_blocked_patterns = [
        '/\{\{\s*self\s*\}\}/i',
        '/\{\{\s*request\s*\}\}/i',
        '/\{\{.*?__class__/i',
        '/\{\{.*?__mro__/i',
        '/\{\{.*?__subclasses__/i',
        '/\{\{.*?__globals__/i',
        '/\{\{.*?__builtins__/i',
        '/\{\{.*?\bimport\b/i',
        '/\{\{.*?\bos\b\./i',
        '/\{\{.*?\bpopen\b/i',
    ];

    $ssti_blocked = false;
    foreach ($ssti_blocked_patterns as $p) {
        if (preg_match($p, $report_name)) {
            $ssti_blocked = true;
            break;
        }
    }

    if ($ssti_blocked) {
        $output = "[WAF] Template injection attempt blocked by NovaBank SSTI Guard v2.0\n[INFO] Patterns config, self, request, __class__, __mro__, __subclasses__, import, os, popen are restricted.\n[HINT] Template expressions like {{7*7}} are allowed for calculations.";
        $generated = true;
    } elseif (preg_match('/\{\{(.+?)\}\}/', $report_name, $matches)) {
        $expr = trim($matches[1]);

        $safe_results = [
            '7*7' => '49',
            '7*191' => '1337',
            'config.secret_key' => 'adnsu7floor',
            'config.jwt_secret' => 'n0v4b4nk_jwt_s3cr3t_k3y_2026',
            'config.api_key' => 'nb_internal_api_8f3k2m9x',
            'self.__class__' => "<class 'jinja2.environment.TemplateContext'>",
            'config' => json_encode([
                'DEBUG' => true,
                'SECRET_KEY' => 'adnsu7floor',
                'DATABASE' => 'sqlite:///var/data/novabank.db',
                'FLAG' => 'ADNSU18{ssti_template_injection_code_exec_6d4f}'
            ], JSON_PRETTY_PRINT),
            "config['FLAG']" => 'ADNSU18{ssti_template_injection_code_exec_6d4f}',
            'config.FLAG' => 'ADNSU18{ssti_template_injection_code_exec_6d4f}',
            "config|attr('FLAG')" => 'ADNSU18{ssti_template_injection_code_exec_6d4f}',
            'lipsum.__globals__' => json_encode(['os' => '<module os>', 'sys' => '<module sys>'], JSON_PRETTY_PRINT),
            "cycler.__init__.__globals__" => json_encode(['os' => '<module os>'], JSON_PRETTY_PRINT),
            "config.get('FLAG')" => 'ADNSU18{ssti_template_injection_code_exec_6d4f}',
        ];

        if (isset($safe_results[$expr])) {
            $output = str_replace($matches[0], $safe_results[$expr], $template);
        } else {
            $output = str_replace($matches[0], '[EVALUATED: ' . htmlspecialchars($expr) . ']', $template);
        }
        $generated = true;
    } else {
        $output = $template;
        $generated = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Audit Portal - NovaBank</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Courier New',monospace;background:#0a0a1a;color:#00ff41;min-height:100vh}
        .container{max-width:800px;margin:0 auto;padding:2rem}
        .header{text-align:center;padding:2rem 0;border-bottom:1px solid #1a3a1a;margin-bottom:2rem}
        h1{font-size:1.3rem;letter-spacing:0.2rem;text-shadow:0 0 10px #00ff41;margin-bottom:0.5rem}
        .sub{color:#006b1a;font-size:0.82rem}
        .card{background:#0d1a0d;border:1px solid #1a3a1a;border-radius:4px;padding:1.5rem;margin-bottom:1.5rem}
        .card h2{color:#00cc33;font-size:0.95rem;margin-bottom:1rem;border-bottom:1px solid #1a3a1a;padding-bottom:0.5rem}
        label{display:block;font-size:0.8rem;color:#006b1a;margin-bottom:0.4rem;text-transform:uppercase}
        input[type="text"]{width:100%;padding:0.7rem;background:#000;border:1px solid #1a3a1a;color:#00ff41;font-family:inherit;font-size:0.85rem;border-radius:4px;outline:none}
        input:focus{border-color:#00ff41;box-shadow:0 0 5px rgba(0,255,65,0.2)}
        .btn{padding:0.6rem 1.5rem;background:#003300;color:#00ff41;border:1px solid #00ff41;font-family:inherit;font-size:0.85rem;cursor:pointer;border-radius:4px;margin-top:0.75rem}
        .btn:hover{background:#004400}
        .output{background:#000;border:1px solid #333;border-radius:4px;padding:1.25rem;margin-top:1rem;font-size:0.82rem;white-space:pre-wrap;line-height:1.6;color:#aaa}
        .hint{color:#333;font-size:0.75rem;margin-top:1rem}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Security Audit Portal</h1>
        <div class="sub">[ NovaBank Internal Security Division ]</div>
    </div>

    <div class="card">
        <h2>Report Generator</h2>
        <p style="font-size:0.82rem;color:#006b1a;margin-bottom:1rem;">
            Generate security audit reports by entering the report name.
            The template engine will process your input and generate the report.
        </p>
        <form method="GET">
            <label>Report Name</label>
            <input type="text" name="report" placeholder="Enter report name (e.g., Q1-2026-Audit)" value="<?= htmlspecialchars($report_name) ?>">
            <button type="submit" class="btn">Generate Report</button>
        </form>

        <?php if ($generated): ?>
        <div class="output"><?= htmlspecialchars($output) ?></div>
        <?php endif; ?>

        <div class="hint">
            Report Engine v2.4 | Template-based generation
        </div>
    </div>

    <div class="card" style="border-color:#222;">
        <h2 style="color:#666;">Recent Audit Reports</h2>
        <div style="font-size:0.8rem;color:#444;line-height:1.8;">
            <div>[2026-02-28] Network Penetration Test - Status: COMPLETED</div>
            <div>[2026-02-20] Application Security Review - Status: COMPLETED</div>
            <div>[2026-02-15] Infrastructure Vulnerability Scan - Status: COMPLETED</div>
            <div>[2026-02-01] Compliance Audit (KVKK/GDPR) - Status: IN PROGRESS</div>
        </div>
    </div>
</div>
</body>
</html>
