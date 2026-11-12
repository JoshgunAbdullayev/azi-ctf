<?php
header('Content-Type: application/json; charset=utf-8');

class UserPreference {
    public $theme = 'light';
    public $language = 'az';
    public $notifications = true;

    public function __toString() {
        return json_encode(['theme' => $this->theme, 'language' => $this->language]);
    }
}

class LogEntry {
    public $message = '';
    public $file = '';

    public function __destruct() {
        if (!empty($this->file) && !empty($this->message)) {
            @file_put_contents($this->file, $this->message . "\n", FILE_APPEND);
        }
    }
}

class SecretVault {
    public $unlocked = false;
    private $flag = 'ADNSU26{php_deserialization_gadget_chain_9e4f}';

    public function __wakeup() {
        if ($this->unlocked === true) {
            echo json_encode([
                'status' => 'vault_unlocked',
                'flag' => $this->flag,
                'note' => 'PHP deserialization allowed bypassing the unlock check'
            ]);
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['prefs'])) {
    echo json_encode([
        'service' => 'Session Preferences API',
        'version' => '2.0.1',
        'usage' => [
            'save' => 'POST with JSON: {"theme": "dark", "language": "en"}',
            'load' => 'GET ?prefs=<serialized_preferences>',
        ],
        'note' => 'Preferences are serialized for efficient storage',
        'supported_themes' => ['light', 'dark', 'banking-classic'],
        'supported_languages' => ['az', 'tr', 'en']
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $pref = new UserPreference();
    $pref->theme = $input['theme'] ?? 'light';
    $pref->language = $input['language'] ?? 'az';
    $pref->notifications = $input['notifications'] ?? true;

    $serialized = serialize($pref);
    $encoded = base64_encode($serialized);

    echo json_encode([
        'success' => true,
        'preferences_token' => $encoded,
        'note' => 'Use this token with GET ?prefs= to restore preferences',
        'debug_raw' => $serialized
    ], JSON_PRETTY_PRINT);
    exit;
}

if (isset($_GET['prefs'])) {
    $decoded = @base64_decode($_GET['prefs']);
    if ($decoded === false) {
        echo json_encode(['error' => 'Invalid base64 encoding']);
        exit;
    }

    $obj = @unserialize($decoded);

    if ($obj === false) {
        echo json_encode(['error' => 'Deserialization failed', 'hint' => 'Expected serialized PHP object']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'preferences' => [
            'theme' => $obj->theme ?? 'unknown',
            'language' => $obj->language ?? 'unknown'
        ]
    ], JSON_PRETTY_PRINT);
}
