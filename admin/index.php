<?php
// /plugins/html-head-code/admin/index.php
declare(strict_types=1);

if (!defined('PLUGIN_SYSTEM_LOADED') || !defined('DASHBOARD_CONTEXT')) return;

$pdo = $GLOBALS['pdo'] ?? null;
if (!($pdo instanceof PDO)) {
    if (function_exists('adiwira_render_404')) adiwira_render_404();
    return;
}
if (function_exists('adiwira_require_site_owner')) adiwira_require_site_owner($pdo, false);

const HHC_HEAD_CODE_KEY = 'html_head_code';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');
    if (function_exists('csrf_check') && !csrf_check($token)) {
        $error = 'Token keamanan tidak valid. Silakan muat ulang halaman.';
    } elseif (!is_string($_POST['head_code'] ?? null)) {
        $error = 'Head code tidak valid.';
    } elseif (strlen($_POST['head_code']) > 262144) {
        $error = 'Head code terlalu panjang.';
    } elseif (function_exists('settings_set')) {
        $code = $_POST['head_code'];
        settings_set($pdo, HHC_HEAD_CODE_KEY, $code, 1);
        $success = true;
    } else {
        $error = 'Settings helper tidak tersedia.';
    }
}

function hhc_admin_e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$code = '';
if (function_exists('settings_get')) {
    $val = settings_get($pdo, HHC_HEAD_CODE_KEY, '');
    $code = is_string($val) ? $val : '';
}
?>

<section class="adam-card" style="max-width:900px">
  <header class="adam-card-header">
    <h2 class="adam-card-title">HTML Head Code</h2>
    <p class="adam-card-subtitle">Tambahkan kode HTML, script, atau meta tag ke dalam &lt;head&gt; halaman publik.</p>
  </header>

  <?php if ($success): ?>
    <div class="adam-alert adam-alert-success" role="status">Head code berhasil disimpan.</div>
  <?php elseif ($error !== ''): ?>
    <div class="adam-alert adam-alert-error" role="alert"><?= hhc_admin_e($error) ?></div>
  <?php endif; ?>

  <form method="post" action="" class="adam-form" id="hhc-form">
    <input type="hidden" name="csrf_token" value="<?= hhc_admin_e(function_exists('csrf_token') ? csrf_token() : '') ?>">

    <div class="adam-form-group">
      <label class="adam-form-label" for="hhc-textarea">Head Code</label>
      <textarea id="hhc-textarea" name="head_code" class="inpud" rows="12" placeholder="&lt;!-- paste Meta Pixel, verification meta, atau custom script di sini --&gt;"><?= hhc_admin_e($code) ?></textarea>
      <p class="adam-form-help">Kode akan disisipkan persis sebelum penutup &lt;/head&gt;.</p>
    </div>

    <div class="adam-form-actions">
      <button type="submit" class="adam-button">Simpan</button>
    </div>
  </form>
</section>

<script>
(function () {
  var ta = document.getElementById('hhc-textarea');
  if (!ta || typeof CodeMirror === 'undefined') {
    return;
  }

  var editor = CodeMirror.fromTextArea(ta, {
    mode: 'htmlmixed',
    theme: 'dracula',
    lineNumbers: true,
    lineWrapping: true,
    autoCloseTags: true,
    autoCloseBrackets: true,
    foldGutter: true,
    gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter']
  });

  var form = document.getElementById('hhc-form');
  if (form) {
    form.addEventListener('submit', function () {
      editor.save();
    });
  }
})();
</script>
