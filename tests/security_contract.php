<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$manifest = json_decode((string)file_get_contents($root . '/plugin.json'), true, 32, JSON_THROW_ON_ERROR);
$admin = (string)file_get_contents($root . '/admin/index.php');
$failures = [];
$check = static function (bool $condition, string $message) use (&$failures): void {
    echo ($condition ? 'PASS' : 'FAIL') . ' ' . $message . PHP_EOL;
    if (!$condition) $failures[] = $message;
};

$check(($manifest['name'] ?? null) === 'html-head-code'
    && ($manifest['version'] ?? null) === '1.0.1'
    && ($manifest['store']['url'] ?? null) === 'https://jyavani.com/plugin-store'
    && ($manifest['store']['slug'] ?? null) === 'html-head-code',
    'release identity and Store metadata are exact');
$iconInfo = getimagesize($root . '/icon.png');
$check(($manifest['icon'] ?? null) === 'icon.png'
    && is_array($iconInfo)
    && ($iconInfo[0] ?? null) === 128
    && ($iconInfo[1] ?? null) === 128
    && ($iconInfo[2] ?? null) === IMAGETYPE_PNG
    && ($iconInfo['mime'] ?? null) === 'image/png',
    'the declared Store icon is a packaged 128 by 128 PNG');
$page = $manifest['admin']['pages'][0] ?? [];
$nav = $manifest['admin']['nav'][0] ?? [];
$check(($page['site_owner'] ?? false) === true && ($nav['site_owner'] ?? false) === true
    && str_contains($admin, "defined('DASHBOARD_CONTEXT')")
    && str_contains($admin, 'adiwira_require_site_owner($pdo, false)'),
    'raw head-code management is restricted to the Site Owner in the manifest and endpoint');
$check(str_contains($admin, "!is_string(\$_POST['head_code'] ?? null)")
    && str_contains($admin, "strlen(\$_POST['head_code']) > 262144")
    && str_contains($admin, 'csrf_check($token)'),
    'head-code mutations require CSRF and bounded scalar input');

if ($failures !== []) exit(1);
echo "HTML Head Code security contract passed.\n";
