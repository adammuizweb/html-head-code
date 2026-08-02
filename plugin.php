<?php
// /plugins/html-head-code/plugin.php
declare(strict_types=1);

if (!defined('PLUGIN_SYSTEM_LOADED')) {
    return;
}

const HHC_PLUGIN_NAME = 'html-head-code';
const HHC_HEAD_CODE_KEY = 'html_head_code';

function hhc_setting(PDO $pdo, string $key, string $default = ''): string {
    if (!function_exists('settings_get')) {
        return $default;
    }
    $val = settings_get($pdo, $key, $default);
    return is_string($val) ? $val : $default;
}

function hhc_e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

add_action('jy_head', function (): void {
    $pdo = $GLOBALS['pdo'] ?? null;
    if (!($pdo instanceof PDO)) {
        return;
    }

    $code = hhc_setting($pdo, HHC_HEAD_CODE_KEY, '');
    if ($code === '') {
        return;
    }

    echo "<!-- HTML Head Code plugin -->\n";
    echo $code . "\n";
    echo "<!-- /HTML Head Code plugin -->\n";
});

add_action('plugin_uninstall', function (string $name): void {
    if ($name !== HHC_PLUGIN_NAME) {
        return;
    }

    $pdo = $GLOBALS['pdo'] ?? null;
    if (!($pdo instanceof PDO) || !function_exists('settings_set')) {
        return;
    }

    settings_set($pdo, HHC_HEAD_CODE_KEY, '', 1);
});
