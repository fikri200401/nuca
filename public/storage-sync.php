<?php
/**
 * storage-sync.php
 * ─────────────────────────────────────────────────────────────
 * Akses via browser: https://yourdomain.com/storage-sync.php?key=GANTI_KEY_INI
 *
 * Script ini menyalin isi storage/app/public → public/storage
 * tanpa perlu terminal / SSH.
 *
 * ⚠️  PENTING: Ganti SECRET_KEY di bawah sebelum upload ke hosting!
 * ⚠️  Setelah berhasil, hapus file ini dari hosting untuk keamanan.
 * ─────────────────────────────────────────────────────────────
 */

define('SECRET_KEY', 'nuca-storage-sync-2026');   // ← GANTI INI

// ── Auth ─────────────────────────────────────────────────────
if (($_GET['key'] ?? '') !== SECRET_KEY) {
    http_response_code(403);
    die('<h2 style="color:red;font-family:sans-serif">403 Forbidden — wrong key</h2>');
}

// ── Paths ────────────────────────────────────────────────────
$publicDir  = __DIR__;                                         // = public/
$src        = dirname(__DIR__) . '/storage/app/public';       // storage/app/public
$dest       = $publicDir . '/storage';                        // public/storage

$force      = isset($_GET['force']);
$dryRun     = isset($_GET['dry']);

// ── Helpers ──────────────────────────────────────────────────
function rCopy(string $src, string $dest, bool $force, bool $dryRun): array
{
    $log = [];

    if (!is_dir($src)) {
        return [['error', "Source not found: $src"]];
    }

    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iter as $item) {
        $relative = ltrim(str_replace($src, '', $item->getRealPath()), DIRECTORY_SEPARATOR . '/');
        $destPath = $dest . DIRECTORY_SEPARATOR . $relative;

        if ($item->isDir()) {
            if (!is_dir($destPath) && !$dryRun) {
                mkdir($destPath, 0755, true);
            }
            continue;
        }

        $needsCopy = $force || !file_exists($destPath) || filemtime($item->getRealPath()) > filemtime($destPath);

        if ($needsCopy) {
            if (!$dryRun) {
                $dir = dirname($destPath);
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                copy($item->getRealPath(), $destPath);
            }
            $log[] = ['copied', $relative];
        } else {
            $log[] = ['skip', $relative];
        }
    }

    return $log;
}

// ── Run ──────────────────────────────────────────────────────
// Remove symlink if exists
if (is_link($dest)) {
    unlink($dest);
}
if (!is_dir($dest) && !$dryRun) {
    mkdir($dest, 0755, true);
}

$results  = rCopy($src, $dest, $force, $dryRun);
$copied   = array_filter($results, fn($r) => $r[0] === 'copied');
$skipped  = array_filter($results, fn($r) => $r[0] === 'skip');
$errors   = array_filter($results, fn($r) => $r[0] === 'error');

// ── Output ───────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Storage Sync — Nuca</title>
<style>
  body { font-family: 'Segoe UI', sans-serif; background: #f9fafb; color: #111; margin: 0; padding: 32px; }
  h1   { font-size: 1.5rem; margin-bottom: 4px; }
  .sub { color: #6b7280; font-size: .875rem; margin-bottom: 24px; }
  .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px 24px; margin-bottom: 16px; }
  .stat { display: flex; gap: 24px; flex-wrap: wrap; }
  .stat div { text-align: center; }
  .stat .num { font-size: 2rem; font-weight: 700; }
  .green  { color: #16a34a; }
  .gray   { color: #6b7280; }
  .red    { color: #dc2626; }
  .orange { color: #d97706; }
  table { width: 100%; border-collapse: collapse; font-size: .8125rem; }
  th    { text-align: left; padding: 6px 10px; background: #f3f4f6; border-bottom: 1px solid #e5e7eb; }
  td    { padding: 5px 10px; border-bottom: 1px solid #f3f4f6; font-family: monospace; }
  .tag  { display: inline-block; border-radius: 4px; padding: 1px 7px; font-size: .75rem; font-weight: 600; }
  .tag-copied { background: #dcfce7; color: #16a34a; }
  .tag-skip   { background: #f3f4f6; color: #6b7280; }
  .tag-error  { background: #fee2e2; color: #dc2626; }
  .btn { display: inline-block; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: .875rem; margin-right: 8px; }
  .btn-primary { background: #6366f1; color: #fff; }
  .btn-danger  { background: #ef4444; color: #fff; }
  .btn-gray    { background: #e5e7eb; color: #374151; }
  .notice { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 12px 16px; font-size: .875rem; color: #92400e; margin-top: 16px; }
</style>
</head>
<body>

<h1>🗂️ Storage Sync</h1>
<p class="sub">Menyalin <code>storage/app/public</code> → <code>public/storage</code></p>

<div class="card">
    <div class="stat">
        <div><div class="num green"><?= count($copied) ?></div><div>File disalin</div></div>
        <div><div class="num gray"><?= count($skipped) ?></div><div>Dilewati (sama)</div></div>
        <div><div class="num red"><?= count($errors) ?></div><div>Error</div></div>
    </div>
</div>

<?php if ($dryRun): ?>
<div class="card" style="border-color:#fcd34d">
    <strong class="orange">⚠️ Dry Run Mode — tidak ada file yang benar-benar disalin.</strong>
    <a class="btn btn-primary" style="margin-left:12px" href="?key=<?= htmlspecialchars(SECRET_KEY) ?>">Jalankan sungguhan</a>
</div>
<?php endif; ?>

<div class="card">
    <p style="margin:0 0 12px"><strong>Aksi:</strong></p>
    <a class="btn btn-primary" href="?key=<?= htmlspecialchars(SECRET_KEY) ?>">🔄 Sync (skip yg sudah ada)</a>
    <a class="btn btn-danger"  href="?key=<?= htmlspecialchars(SECRET_KEY) ?>&force=1"
       onclick="return confirm('Overwrite semua file?')">⚡ Force Sync (overwrite semua)</a>
    <a class="btn btn-gray"    href="?key=<?= htmlspecialchars(SECRET_KEY) ?>&dry=1">👁️ Dry Run (preview saja)</a>
</div>

<?php if (!empty($errors)): ?>
<div class="card">
    <strong class="red">Errors:</strong>
    <table><tr><th>Pesan</th></tr>
    <?php foreach ($errors as $r): ?>
    <tr><td class="red"><?= htmlspecialchars($r[1]) ?></td></tr>
    <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($results)): ?>
<div class="card">
    <strong>Detail file (<?= count($results) ?> total):</strong>
    <table style="margin-top:10px">
        <tr><th>Status</th><th>File</th></tr>
        <?php foreach ($results as $r): ?>
        <tr>
            <td><span class="tag tag-<?= $r[0] ?>"><?= $r[0] ?></span></td>
            <td><?= htmlspecialchars($r[1]) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<div class="notice">
    ⚠️ <strong>Keamanan:</strong> Hapus file <code>storage-sync.php</code> dari hosting setelah selesai digunakan,
    atau setidaknya ganti <code>SECRET_KEY</code>-nya.
</div>

</body>
</html>
