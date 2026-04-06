<?php
require_once __DIR__ . '/bootstrap.php';

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/admin-header.php';

// ── Stats ────────────────────────────────────────────────────
$total_articles  = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$pub_articles    = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();
$draft_articles  = $total_articles - $pub_articles;
$total_services  = (int)$pdo->query("SELECT COUNT(*) FROM services WHERE is_active=1")->fetchColumn();
$total_messages  = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$unread_messages = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();

// ── Recent data ──────────────────────────────────────────────
$recent_articles = $pdo->query("SELECT id,title,status,created_at FROM articles ORDER BY created_at DESC LIMIT 6")->fetchAll();
$recent_messages = $pdo->query("SELECT id,name,message,is_read,created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<?= render_flash() ?>

<!-- Stat cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <?php
  $cards = [
    ['label'=>'Total Artikel',  'value'=>$total_articles,  'icon'=>'solar:document-text-linear',  'bg'=>'bg-[#eff6ff]', 'ic'=>'text-[#2563eb]'],
    ['label'=>'Dipublikasi',    'value'=>$pub_articles,    'icon'=>'solar:check-circle-linear',   'bg'=>'bg-[#f0fdf4]', 'ic'=>'text-[#16a34a]'],
    ['label'=>'Layanan Aktif',  'value'=>$total_services,  'icon'=>'solar:printer-2-linear',      'bg'=>'bg-[#f5f3ff]', 'ic'=>'text-[#7c3aed]'],
    ['label'=>'Pesan Masuk',    'value'=>$total_messages,  'icon'=>'solar:inbox-linear',          'bg'=>'bg-[#fff7ed]', 'ic'=>'text-[#ea580c]',
     'badge'=>$unread_messages > 0 ? $unread_messages.' baru' : null],
  ];
  foreach ($cards as $c):
  ?>
  <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
    <div class="flex items-start justify-between mb-3">
      <div class="w-10 h-10 rounded-xl <?= $c['bg'] ?> flex items-center justify-center">
        <iconify-icon icon="<?= $c['icon'] ?>" class="text-xl <?= $c['ic'] ?>"></iconify-icon>
      </div>
      <?php if (!empty($c['badge'])): ?>
      <span class="text-[10px] bg-[#ef4444] text-white rounded-full px-2 py-0.5 font-bold"><?= $c['badge'] ?></span>
      <?php endif; ?>
    </div>
    <p class="text-2xl font-bold text-[#111827]"><?= $c['value'] ?></p>
    <p class="text-xs text-[#94a3b8] uppercase tracking-widest mt-1"><?= $c['label'] ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- Article status bar -->
<?php if ($total_articles > 0): ?>
<div class="bg-white rounded-2xl border border-[#e2e8f0] p-5 mb-6">
  <div class="flex items-center justify-between mb-3">
    <p class="text-xs font-semibold uppercase tracking-widest text-[#64748b]">Status Artikel</p>
    <div class="flex gap-4 text-xs text-[#64748b]">
      <span class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-[#22c55e] inline-block"></span><?= $pub_articles ?> Publik
      </span>
      <span class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-[#f59e0b] inline-block"></span><?= $draft_articles ?> Draft
      </span>
    </div>
  </div>
  <div class="h-2 rounded-full bg-[#f1f5f9] overflow-hidden">
    <div class="h-full rounded-full bg-[#22c55e] transition-all"
         style="width:<?= round($pub_articles / $total_articles * 100) ?>%"></div>
  </div>
</div>
<?php endif; ?>

<!-- Two-column: recent articles + recent messages -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  <!-- Recent articles -->
  <div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0]">
      <p class="text-sm font-semibold text-[#111827]">Artikel Terbaru</p>
      <a href="<?= BASE_URL ?>/admin/articles/index.php" class="text-xs text-[#2563eb] hover:underline">Lihat semua →</a>
    </div>
    <div class="divide-y divide-[#f1f5f9]">
      <?php if ($recent_articles): ?>
        <?php foreach ($recent_articles as $a): ?>
        <div class="flex items-center gap-3 px-5 py-3">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-[#111827] truncate"><?= e($a['title']) ?></p>
            <p class="text-xs text-[#94a3b8] mt-0.5"><?= date('d M Y', strtotime($a['created_at'])) ?></p>
          </div>
          <span class="flex-shrink-0 text-[10px] uppercase tracking-widest px-2 py-1 rounded-lg font-semibold
            <?= $a['status']==='published' ? 'bg-[#f0fdf4] text-[#16a34a]' : 'bg-[#fefce8] text-[#a16207]' ?>">
            <?= $a['status']==='published' ? 'Publik' : 'Draft' ?>
          </span>
          <a href="<?= BASE_URL ?>/admin/articles/edit.php?id=<?= $a['id'] ?>"
             class="flex-shrink-0 text-[#94a3b8] hover:text-[#2563eb] transition-colors">
            <iconify-icon icon="solar:pen-2-linear" class="text-base"></iconify-icon>
          </a>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="px-5 py-8 text-sm text-[#94a3b8] text-center">Belum ada artikel.</p>
      <?php endif; ?>
    </div>
    <div class="px-5 py-3 border-t border-[#f1f5f9]">
      <a href="<?= BASE_URL ?>/admin/articles/create.php"
         class="text-xs text-[#2563eb] hover:underline flex items-center gap-1">
        <iconify-icon icon="solar:add-circle-linear" class="text-sm"></iconify-icon>
        Tambah artikel baru
      </a>
    </div>
  </div>

  <!-- Recent messages -->
  <div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0]">
      <p class="text-sm font-semibold text-[#111827]">Pesan Terbaru</p>
      <a href="<?= BASE_URL ?>/admin/inbox/index.php" class="text-xs text-[#2563eb] hover:underline">Lihat semua →</a>
    </div>
    <div class="divide-y divide-[#f1f5f9]">
      <?php if ($recent_messages): ?>
        <?php foreach ($recent_messages as $m): ?>
        <div class="flex items-start gap-3 px-5 py-3 <?= !$m['is_read'] ? 'bg-[#eff6ff]/40' : '' ?>">
          <div class="w-8 h-8 rounded-full bg-[#f1f5f9] flex items-center justify-center text-xs font-bold text-[#475569] flex-shrink-0 mt-0.5">
            <?= strtoupper(substr($m['name'], 0, 1)) ?>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <p class="text-sm font-medium text-[#111827] truncate"><?= e($m['name']) ?></p>
              <?php if (!$m['is_read']): ?>
              <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb] flex-shrink-0"></span>
              <?php endif; ?>
            </div>
            <p class="text-xs text-[#94a3b8] truncate"><?= e(truncate($m['message'], 55)) ?></p>
          </div>
          <a href="<?= BASE_URL ?>/admin/inbox/view.php?id=<?= $m['id'] ?>"
             class="flex-shrink-0 text-[#94a3b8] hover:text-[#2563eb] transition-colors mt-1">
            <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
          </a>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="px-5 py-8 text-sm text-[#94a3b8] text-center">Belum ada pesan.</p>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>