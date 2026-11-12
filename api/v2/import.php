<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/auth.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'service' => 'NovaBank Data Import',
        'version' => '1.0.0',
        'method' => 'POST',
        'content_type' => 'application/xml',
        'description' => 'Import transaction data in XML format',
        'example' => '<?xml version="1.0"?><import><transaction><amount>1000</amount><description>Test</description></transaction></import>',
        'note' => 'Send XML data via POST request'
    ], JSON_PRETTY_PRINT);
    exit;
}

$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
$input = @file_get_contents('php://input');

if (empty($input)) {
    http_response_code(400);
    echo json_encode(['error' => 'Empty request body', 'expected' => 'XML data']);
    exit;
}

// API v2 is beta with relaxed validation: external entity declarations are
// NOT filtered here (classic XXE). Only block the obviously destructive
// expect:// wrapper to avoid command execution on the host.
$xxe_patterns = [
    '/expect:\/\//i',
];

$xxe_blocked = false;
foreach ($xxe_patterns as $p) {
    if (preg_match($p, $input)) {
        $xxe_blocked = true;
        break;
    }
}

if ($xxe_blocked) {
    http_response_code(403);
    echo json_encode([
        'error' => 'XML Security Violation',
        'message' => 'External entity declarations are blocked by security policy.',
        'code' => 'NB-XXE-GUARD-1.3',
        'hint' => 'Direct ENTITY+SYSTEM/PUBLIC declarations and file:// protocol are filtered.'
    ], JSON_PRETTY_PRINT);
    exit;
}

libxml_disable_entity_loader(false);
$prev = libxml_use_internal_errors(true);

$dom = new DOMDocument();
$dom->loadXML($input, LIBXML_NOENT | LIBXML_DTDLOAD);

$errors = libxml_get_errors();
libxml_clear_errors();
libxml_use_internal_errors($prev);

if (!empty($errors)) {
    $error_msgs = [];
    foreach ($errors as $e) {
        $error_msgs[] = trim($e->message);
    }
    http_response_code(400);
    echo json_encode([
        'error' => 'XML parsing error',
        'details' => $error_msgs,
        'hint' => 'Ensure your XML is well-formed'
    ], JSON_PRETTY_PRINT);
    exit;
}

$result = [];
$transactions = $dom->getElementsByTagName('transaction');
$imported = 0;

foreach ($transactions as $tx) {
    $amount = '';
    $description = '';
    $recipient = '';

    foreach ($tx->childNodes as $node) {
        if ($node->nodeName === 'amount') $amount = $node->textContent;
        if ($node->nodeName === 'description') $description = $node->textContent;
        if ($node->nodeName === 'recipient') $recipient = $node->textContent;
    }

    $result[] = [
        'amount' => $amount,
        'description' => $description,
        'recipient' => $recipient,
        'status' => 'parsed'
    ];
    $imported++;
}

$root_content = '';
if ($dom->documentElement) {
    $root_content = $dom->documentElement->textContent;
}

$response = [
    'status' => 'success',
    'imported' => $imported,
    'transactions' => $result,
    'raw_content' => $root_content
];

if (stripos($root_content, 'ADNSU') !== false || stripos($root_content, 'root:') !== false || strlen($root_content) > 500) {
    $response['flag'] = 'ADNSU14{xxe_external_entity_injection_9b1c}';
    $response['warning'] = 'Sensitive data detected in parsed XML content';
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
