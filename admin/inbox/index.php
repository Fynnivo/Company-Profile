<?php
require_once __DIR__ . '/../bootstrap.php';

$page_title = 'Inbox';
$breadcrumb = 'Pesan dari pengunjung';
require_once __DIR__ . '/../includes/admin-header.php';

// ── Actions ──────────────────────────────────────────────────
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_GET['delete']]);
    flash('Pesan dihapus.');
    header('Location: ' . BASE_URL . '/admin/inbox/index.php'); exit;
}

if (isset($_GET['markall'])) {
    $pdo->query("UPDATE contact_messages SET is_read=1");
    header('Location: ' . BASE_URL . '/admin/inbox/index.php'); exit;
}

// ── Query ─────────────────────────────────────────────────────
$filter  = ($_GET['status'] ?? '') === 'unread' ? 'unread' : '';
$where   = $filter === 'unread' ? 'WHERE is_read=0' : '';
$msgs    = $pdo->query("SELECT * FROM contact_messages $where ORDER BY created_at DESC")->fetchAll();
$unread  = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
?>

<?= render_flash() ?>

<!-- Toolbar -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <div class="flex gap-2">
    <a href="?" class="text-xs uppercase tracking-widest px-3 py-2 rounded-xl transition-all
      <?= !$filter ? 'bg-[#111827] text-white' : 'bg-[#f1f5f9] text-[#475569] hover:bg-[#e2e8f0]' ?>">
      Semua (<?= count($msgs) ?>)
    </a>
    <a href="?status=unread" class="flex items-center gap-2 text-xs uppercase tracking-widest px-3 py-2 rounded-xl transition-all
      <?= $filter ? 'bg-[#111827] text-white' : 'bg-[#f1f5f9] text-[#475569] hover:bg-[#e2e8f0]' ?>">
      Belum Dibaca
      <?php if ($unread > 0): ?>
      <span class="bg-[#ef4444] text-white text-[10px] font-bold rounded-full px-1.5 leading-5"><?= $unread ?></span>
      <?php endif; ?>
    </a>
  </div>
  <?php if ($unread > 0): ?>
  <a href="?markall=1"
     class="flex items-center gap-1.5 text-xs text-[#64748b] hover:text-[#2563eb] transition-colors">
    <iconify-icon icon="solar:check-read-linear" class="text-sm"></iconify-icon>
    Tandai semua dibaca
  </a>
  <?php endif; ?>
</div>

<!-- Messages list -->
<div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden">
  <?php if ($msgs): ?>
  <div class="divide-y divide-[#f1f5f9]">
    <?php foreach ($msgs as $m): ?>
    <div class="flex items-start gap-4 px-5 py-4 hover:bg-[#f8fafc] transition-colors
                <?= !$m['is_read'] ? 'bg-[#eff6ff]/30' : '' ?>">

      <div class="w-10 h-10 rounded-full bg-[#f1f5f9] flex items-center justify-center text-sm font-bold text-[#475569] flex-shrink-0 mt-0.5">
        <?= strtoupper(substr($m['name'], 0, 1)) ?>
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-0.5">
          <p class="text-sm font-semibold text-[#111827]"><?= e($m['name']) ?></p>
          <?php if (!$m['is_read']): ?>
          <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb] flex-shrink-0"></span>
          <?php endif; ?>
          <span class="text-xs text-[#94a3b8]">· <?= date('d M Y H:i', strtotime($m['created_at'])) ?></span>
        </div>
        <?php if ($m['email'] || $m['phone']): ?>
        <p class="text-xs text-[#94a3b8] mb-1">
          <?= $m['email'] ? e($m['email']) : '' ?>
          <?= ($m['email'] && $m['phone']) ? ' · ' : '' ?>
          <?= $m['phone'] ? e($m['phone']) : '' ?>
        </p>
        <?php endif; ?>
        <p class="text-sm text-[#475569] leading-6 line-clamp-2"><?= e(truncate($m['message'], 100)) ?></p>
      </div>

      <div class="flex items-center gap-3 flex-shrink-0 mt-1">
        <a href="<?= BASE_URL ?>/admin/inbox/view.php?id=<?= $m['id'] ?>"
           class="text-xs uppercase tracking-widest bg-[#f1f5f9] text-[#475569] px-3 py-2 rounded-xl hover:bg-[#111827] hover:text-white transition-all">
          Buka
        </a>
        <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Hapus pesan ini?')"
           class="text-[#94a3b8] hover:text-[#ef4444] transition-colors">
          <iconify-icon icon="solar:trash-bin-2-linear" class="text-base"></iconify-icon>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="py-16 text-center">
    <iconify-icon icon="solar:inbox-linear" class="text-4xl text-[#cbd5e1] mb-3 block"></iconify-icon>
    <p class="text-sm text-[#94a3b8]">Belum ada pesan masuk.</p>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>