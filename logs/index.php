<?php
header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['view'])) {
    $file = basename($_GET['view']);
    $filepath = __DIR__ . '/' . $file;
    if (file_exists($filepath) && is_file($filepath)) {
        header('Content-Type: text/plain; charset=utf-8');
        readfile($filepath);
        exit;
    }
}

$dir = __DIR__;
$files = array_diff(scandir($dir), ['.', '..', 'index.php', '.htaccess']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Index of /logs/</title>
    <style>
        body { font-family: monospace; font-size: 14px; padding: 20px; background: #fff; color: #333; }
        h1 { font-size: 18px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: 4px 15px; }
        th { border-bottom: 2px solid #ccc; }
        td { border-bottom: 1px solid #eee; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .size { color: #666; }
        .date { color: #888; }
        .footer { font-size: 12px; color: #999; margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <h1>Index of /logs/</h1>
    <table>
        <thead>
            <tr><th>Name</th><th>Last modified</th><th>Size</th><th>Description</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="/">Parent Directory</a></td>
                <td class="date">-</td><td class="size">-</td><td>-</td>
            </tr>
            <?php foreach ($files as $file): ?>
            <?php
                $filepath = $dir . DIRECTORY_SEPARATOR . $file;
                $mod_time = date('Y-m-d H:i', filemtime($filepath));
                $size = is_file($filepath) ? filesize($filepath) : '-';
                if (is_numeric($size) && $size > 1024) $size = round($size / 1024, 1) . 'K';
            ?>
            <tr>
                <td><a href="/logs/index.php?view=<?= urlencode($file) ?>"><?= htmlspecialchars($file) ?></a></td>
                <td class="date"><?= $mod_time ?></td>
                <td class="size"><?= $size ?></td>
                <td>-</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="footer">Apache/2.4.54 (Ubuntu) Server at novabank.com.tr Port 443</div>
</body>
</html>
