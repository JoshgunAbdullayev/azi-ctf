<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn() || !isset($_SESSION['hakan_auth']) || $_SESSION['hakan_auth'] !== true) {
    header('Location: /login.php');
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Priority Admin Panel | NovaBank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
    <style>
        .api-nav { display:flex; gap:0.5rem; margin-bottom:2rem; flex-wrap:wrap; }
        .api-nav a { padding:0.5rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:500; color:var(--gray-600); background:var(--gray-100); transition:all 0.15s; text-decoration:none; }
        .api-nav a:hover { background:var(--gray-200); }
        .api-nav a.active { background:var(--primary); color:white; }
        .code-block { background:#1F2937; color:#E5E7EB; padding:1.25rem; border-radius:8px; font-family:monospace; font-size:0.85rem; overflow-x:auto; margin:1rem 0; line-height:1.7; }
        .code-block .key { color:#34D399; }
        .code-block .str { color:#FBBF24; }
        .code-block .comment { color:#6B7280; }
        .endpoint-card { background:white; border:1px solid var(--gray-200); border-radius:12px; padding:1.25rem; margin-bottom:1rem; }
        .endpoint-card:hover { border-color:var(--gray-300); box-shadow:var(--shadow-xs); }
        .method-badge { display:inline-block; padding:0.15rem 0.5rem; border-radius:4px; font-size:0.72rem; font-weight:700; font-family:monospace; }
        .method-get { background:#DCFCE7; color:#166534; }
        .visibility { font-size:0.75rem; padding:0.2rem 0.5rem; border-radius:999px; }
        .vis-public { background:#DBEAFE; color:#1E40AF; }
        .vis-restricted { background:#FEE2E2; color:#991B1B; }
    </style>
</head>
<body style="background:var(--gray-50);">

    <div class="admin-header" style="background:#0F172A;">
        <div class="container">
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <span class="logo-icon" style="width:32px;height:32px;font-size:0.9rem;border-radius:6px;background:#F59E0B;">N</span>
                <span style="font-weight:600;">NovaBank Priority Admin</span>
                <span class="badge badge-yellow" style="margin-left:0.5rem;">PRIORITY</span>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;">
                <span style="color:var(--gray-400);font-size:0.85rem;">Hakan &#350;eker</span>
                <a href="/dashboard.php" style="color:var(--gray-400);font-size:0.85rem;">Back</a>
                <a href="/logout.php" style="color:var(--gray-400);font-size:0.85rem;">Logout</a>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top:2rem;padding-bottom:3rem;">

        <div class="api-nav">
            <a href="/hakan_panel.php" class="<?= $page === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/hakan_panel.php?page=api" class="<?= $page === 'api' ? 'active' : '' ?>">API Documentation</a>
            <a href="/hakan_panel.php?page=software" class="<?= $page === 'software' ? 'active' : '' ?>">Software Info</a>
        </div>

<?php if ($page === 'dashboard'): ?>

        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Priority Administration</h1>
        <p style="font-size:0.9rem;color:var(--gray-500);margin-bottom:2rem;">Welcome to the priority management interface. This panel provides access to internal API documentation and system configuration.</p>

        <div class="dashboard-grid">
            <div class="dash-card dash-card-accent" style="border-left-color:#F59E0B;">
                <div class="dash-card-label">Access Level</div>
                <div class="dash-card-value" style="font-size:1.2rem;">Priority Admin</div>
                <div class="dash-card-sub">Full API access granted</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">API Endpoints</div>
                <div class="dash-card-value">11</div>
                <div class="dash-card-sub">Active service endpoints</div>
            </div>
            <div class="dash-card">
                <div class="dash-card-label">System Status</div>
                <div class="dash-card-value" style="color:var(--green-600);">Healthy</div>
                <div class="dash-card-sub">All services operational</div>
            </div>
        </div>

        <div class="grid-2" style="margin-top:2rem;">
            <div class="card">
                <h3 class="card-title">API Documentation</h3>
                <p class="card-text" style="margin-bottom:1rem;">Complete reference for all internal banking service endpoints, authentication methods, and response schemas.</p>
                <a href="/hakan_panel.php?page=api" class="card-link">View Documentation &rarr;</a>
            </div>
            <div class="card">
                <h3 class="card-title">Software Information</h3>
                <p class="card-text" style="margin-bottom:1rem;">System version details, dependency list, build information, and deployment configuration.</p>
                <a href="/hakan_panel.php?page=software" class="card-link">View Details &rarr;</a>
            </div>
        </div>

<?php elseif ($page === 'api'): ?>

        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">API Documentation</h1>
        <p style="font-size:0.9rem;color:var(--gray-500);margin-bottom:2rem;">Internal service endpoints for NovaBank Core Banking Platform v3.2.1</p>

        <div class="card" style="margin-bottom:2rem;">
            <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Authentication &amp; Access</h3>
            <div class="code-block">
<span class="comment"># Base URL</span>
<span class="key">BASE_URL</span>: /nbs-core/api/service.php

<span class="comment"># Service access (public endpoints: id 1-10)</span>
<span class="key">Authentication</span>: Session-based (priority admin)

<span class="comment"># Example request</span>
GET /nbs-core/api/service.php?id=1

<span class="comment"># Response format: JSON</span>
{
  <span class="str">"service_id"</span>: 1,
  <span class="str">"name"</span>: "Account Management Service",
  <span class="str">"status"</span>: "active",
  <span class="str">"visibility"</span>: "public"
}
            </div>
        </div>

        <h2 style="font-size:1.15rem;font-weight:700;margin-bottom:1rem;">Available Service Endpoints</h2>

        <?php
        $services = [
            1 => ['name' => 'Account Management Service', 'desc' => 'Customer account CRUD operations, balance inquiries, and account status management.', 'vis' => 'public'],
            2 => ['name' => 'Transaction Processing Engine', 'desc' => 'Real-time transaction initiation, validation, and settlement processing.', 'vis' => 'public'],
            3 => ['name' => 'Notification Gateway', 'desc' => 'SMS, email, and push notification delivery for transaction alerts and security codes.', 'vis' => 'public'],
            4 => ['name' => 'Authentication Service', 'desc' => 'User authentication, session management, MFA token generation and validation.', 'vis' => 'public'],
            5 => ['name' => 'Logging & Audit Trail', 'desc' => 'Immutable audit logging for all system events, access logs, and compliance reporting.', 'vis' => 'public'],
            6 => ['name' => 'Report Generation Engine', 'desc' => 'Automated generation of financial reports, statements, and regulatory filings.', 'vis' => 'public'],
            7 => ['name' => 'Customer Support API', 'desc' => 'Ticket management, live chat integration, and support workflow automation.', 'vis' => 'public'],
            8 => ['name' => 'Mobile Banking Bridge', 'desc' => 'API bridge for iOS/Android mobile applications with biometric auth support.', 'vis' => 'public'],
            9 => ['name' => 'Payment Gateway Integration', 'desc' => 'SWIFT, EFT/RTGS, and instant payment rail integrations for domestic and international transfers.', 'vis' => 'public'],
            10 => ['name' => 'KYC/AML Verification', 'desc' => 'Know Your Customer and Anti-Money Laundering screening, sanctions list checks.', 'vis' => 'public'],
            11 => ['name' => 'Internal Admin Override', 'desc' => 'Administrative override functions for system configuration and emergency operations.', 'vis' => 'restricted'],
        ];
        foreach ($services as $id => $svc): ?>
        <div class="endpoint-card">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.5rem;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <span class="method-badge method-get">GET</span>
                    <code style="font-size:0.85rem;color:var(--gray-700);">/nbs-core/api/service.php?id=<?= $id ?></code>
                </div>
                <span class="visibility <?= $svc['vis'] === 'public' ? 'vis-public' : 'vis-restricted' ?>">
                    <?= $svc['vis'] === 'public' ? 'Visible to normal users' : 'Not visible to normal users - requires authorized approval' ?>
                </span>
            </div>
            <h4 style="font-size:0.95rem;font-weight:600;margin-bottom:0.25rem;"><?= $svc['name'] ?></h4>
            <p style="font-size:0.82rem;color:var(--gray-500);"><?= $svc['desc'] ?></p>
        </div>
        <?php endforeach; ?>

<?php elseif ($page === 'software'): ?>

        <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Software Information</h1>
        <p style="font-size:0.9rem;color:var(--gray-500);margin-bottom:2rem;">System configuration and build details</p>

        <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Build Information</h3>
            <div style="display:grid;gap:0.5rem;font-size:0.88rem;">
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Version</span><strong>3.2.1-beta</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Build Hash</span><strong>a4f8c2e19b</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Build Date</span><strong>2026-02-15</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Runtime</span><strong>PHP <?= phpversion() ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Database</span><strong>SQLite <?= $db->query("SELECT sqlite_version()")->fetchColumn() ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Environment</span><strong>staging</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-500);">Service Override Key</span><strong style="font-family:monospace;font-size:0.82rem;">priority_override</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.5rem 0;">
                    <span style="color:var(--gray-500);">Internal Comm Channel</span><strong>/nb-internal-comm-8f3a/</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Dependencies</h3>
            <div class="code-block">
<span class="key">php</span>: >=8.0
<span class="key">ext-sqlite3</span>: *
<span class="key">ext-openssl</span>: *
<span class="key">ext-mbstring</span>: *
<span class="key">ext-json</span>: *
<span class="key">novabank/core</span>: 3.2.1
<span class="key">novabank/auth</span>: 2.1.0
<span class="key">novabank/payment-rail</span>: 1.8.3
<span class="key">novabank/aml-engine</span>: 1.4.2
<span class="key">novabank/notification</span>: 1.2.0
            </div>
        </div>

<?php endif; ?>

    </div>

</body>
</html>
