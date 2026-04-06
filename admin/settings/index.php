<?php
require_once __DIR__ . '/../bootstrap.php';

$page_title = 'Pengaturan Situs';
$breadcrumb = 'Kelola informasi perusahaan';
require_once __DIR__ . '/../includes/admin-header.php';

// ── Save ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['s'] ?? [] as $key => $value) {
        $pdo->prepare("INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?")
            ->execute([trim($key), trim($value), trim($value)]);
    }
    flash('Pengaturan berhasil disimpan.');
    header('Location: ' . BASE_URL . '/admin/settings/index.php?tab=' . ($_GET['tab'] ?? 'general')); exit;
}

// ── Load all settings ─────────────────────────────────────────
$rows = $pdo->query("SELECT `key`,`value` FROM settings")->fetchAll();
$cfg  = array_column($rows, 'value', 'key');
$g    = fn(string $k): string => $cfg[$k] ?? '';

$tab  = $_GET['tab'] ?? 'general';
$tabs = [
    'general' => ['Umum',         'solar:info-circle-linear'],
    'contact' => ['Kontak',        'solar:phone-linear'],
    'social'  => ['Sosial Media',  'solar:share-linear'],
    'about'   => ['Tentang',       'solar:document-text-linear'],
];
?>

<?= render_flash() ?>

<!-- Tab bar -->
<div class="flex gap-1 mb-6 bg-white rounded-2xl border border-[#e2e8f0] p-1.5 w-fit flex-wrap">
  <?php foreach ($tabs as $key => [$label, $icon]): ?>
  <a href="?tab=<?= $key ?>"
     class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm transition-all
     <?= $tab===$key ? 'bg-[#111827] text-white font-semibold' : 'text-[#64748b] hover:bg-[#f1f5f9]' ?>">
    <iconify-icon icon="<?= $icon ?>" class="text-base"></iconify-icon>
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>

<form method="POST">
  <input type="hidden" name="_tab" value="<?= e($tab) ?>">
  <div class="bg-white rounded-2xl border border-[#e2e8f0] p-6 max-w-2xl">

    <?php
    // ── Field renderer helpers ──────────────────────────────
    $text = function(string $key, string $label, string $hint='') use ($g): void { ?>
      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2"><?= $label ?></label>
        <input type="text" name="s[<?= $key ?>]" value="<?= e($g($key)) ?>"
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
        <?php if ($hint): ?><p class="text-xs text-[#94a3b8] mt-1"><?= $hint ?></p><?php endif; ?>
      </div>
    <?php };

    $textarea = function(string $key, string $label, int $rows=3, string $hint='') use ($g): void { ?>
      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2"><?= $label ?></label>
        <textarea name="s[<?= $key ?>]" rows="<?= $rows ?>"
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors resize-none"><?= e($g($key)) ?></textarea>
        <?php if ($hint): ?><p class="text-xs text-[#94a3b8] mt-1"><?= $hint ?></p><?php endif; ?>
      </div>
    <?php };
    ?>

    <div class="space-y-5">

      <?php if ($tab === 'general'): ?>
        <?php $text('site_name',        'Nama Perusahaan', 'Tampil di navbar dan footer.'); ?>
        <?php $text('site_tagline',     'Tagline / Slogan'); ?>
        <?php $textarea('meta_description', 'Meta Description (SEO)', 3, 'Maks 160 karakter. Tampil di hasil pencarian Google.'); ?>

      <?php elseif ($tab === 'contact'): ?>
        <?php $text('phone',    'Nomor Telepon',   'cth: 021-1234567'); ?>
        <?php $text('whatsapp', 'Nomor WhatsApp',  'Format: 628xxxxxxxxx (tanpa + atau spasi)'); ?>
        <?php $text('email',    'Email',           'cth: info@perusahaan.com'); ?>
        <?php $textarea('address', 'Alamat Lengkap', 3, 'Tampil di halaman kontak dan footer.'); ?>
        <?php $text('maps_embed', 'Google Maps Embed URL', 'Google Maps → Share → Embed a map → salin nilai src="..."'); ?>

      <?php elseif ($tab === 'social'): ?>
        <?php
        foreach ([
            ['instagram', 'Instagram',  'solar:instagram-linear'],
            ['facebook',  'Facebook',   'solar:brand-facebook-linear'],
            ['tiktok',    'TikTok',     'simple-icons:tiktok'],
            ['youtube',   'YouTube',    'solar:play-circle-linear'],
        ] as [$key, $label, $icon]): ?>
        <div>
          <label class="flex items-center gap-2 text-xs uppercase tracking-widest text-[#64748b] mb-2">
            <iconify-icon icon="<?= $icon ?>" class="text-base text-[#475569]"></iconify-icon>
            <?= $label ?>
          </label>
          <input type="url" name="s[<?= $key ?>]" value="<?= e($g($key)) ?>"
            placeholder="https://..."
            class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
        </div>
        <?php endforeach; ?>

      <?php elseif ($tab === 'about'): ?>
        <?php
        foreach ([
            ['about_who',     'Siapa Kami',    4],
            ['about_story',   'Cerita Kami',   4],
            ['about_mission', 'Misi',          3],
            ['about_values',  'Nilai-nilai',   3],
            ['about_process', 'Proses Kami',   3],
            ['about_team',    'Tentang Tim',   3],
        ] as [$key, $label, $rows]): ?>
        <?php $textarea($key, $label, $rows); ?>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>

    <!-- Save -->
    <div class="mt-8 pt-6 border-t border-[#f1f5f9] flex items-center gap-4">
      <button type="submit"
        class="flex items-center gap-2 bg-[#111827] text-white px-6 py-3 rounded-xl text-sm uppercase tracking-widest font-semibold hover:bg-[#2563eb] transition-all">
        <iconify-icon icon="solar:diskette-linear" class="text-base"></iconify-icon>
        Simpan
      </button>
      <p class="text-xs text-[#94a3b8]">Perubahan langsung tampil di website.</p>
    </div>

  </div>
</form>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>