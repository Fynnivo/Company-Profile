<?php
require_once __DIR__ . '/../bootstrap.php';

$page_title = 'Inbox';
$breadcrumb = 'Lihat Pesan';
require_once __DIR__ . '/../includes/admin-header.php';

// ── Get Message ID ────────────────────────────────────────────
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: ' . BASE_URL . '/admin/inbox/index.php');
    exit;
}

$msg_id = (int)$_GET['id'];

// ── Delete Action ─────────────────────────────────────────────
if (isset($_POST['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$msg_id]);
    flash('Pesan dihapus.');
    header('Location: ' . BASE_URL . '/admin/inbox/index.php');
    exit;
}

// ── Fetch Message ─────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id=? LIMIT 1");
$stmt->execute([$msg_id]);
$msg = $stmt->fetch();

if (!$msg) {
    http_response_code(404);
    ?>
    <div class="bg-[#fee2e2] border border-[#fecaca] text-[#991b1b] px-4 py-3 rounded-lg mb-5">
      Pesan tidak ditemukan.
    </div>
    <a href="<?= BASE_URL ?>/admin/inbox/index.php" class="text-[#2563eb] hover:underline">← Kembali ke Inbox</a>
    <?php
    require_once __DIR__ . '/../includes/admin-footer.php';
    exit;
}

// ── Mark as Read ──────────────────────────────────────────────
if (!$msg['is_read']) {
    $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$msg_id]);
}

?>

<?= render_flash() ?>

<!-- Back link -->
<div class="mb-5">
  <a href="<?= BASE_URL ?>/admin/inbox/index.php" class="inline-flex items-center gap-2 text-[#2563eb] hover:text-[#1c52c9] transition-colors">
    <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
    Kembali ke Inbox
  </a>
</div>

<!-- Message card -->
<div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden">
  <!-- Header -->
  <div class="border-b border-[#e2e8f0] px-6 py-5 bg-[#f8fafc]">
    <div class="flex items-start justify-between gap-4">
      <div class="flex-1">
        <h2 class="text-2xl font-semibold text-[#111827] mb-3"><?= e($msg['name']) ?></h2>
        
        <!-- Contact info -->
        <div class="space-y-1 text-sm text-[#475569]">
          <?php if ($msg['email']): ?>
          <p>
            <span class="text-[#6b7280]">Email:</span>
            <a href="mailto:<?= e($msg['email']) ?>" class="text-[#2563eb] hover:underline"><?= e($msg['email']) ?></a>
          </p>
          <?php endif; ?>
          
          <?php if ($msg['phone']): ?>
          <p>
            <span class="text-[#6b7280]">Telepon:</span>
            <a href="tel:<?= e($msg['phone']) ?>" class="text-[#2563eb] hover:underline"><?= e($msg['phone']) ?></a>
          </p>
          <?php endif; ?>
          
          <p>
            <span class="text-[#6b7280]">Tanggal:</span>
            <?= date('d M Y H:i', strtotime($msg['created_at'])) ?>
          </p>
        </div>
      </div>

      <!-- Delete button -->
      <form method="POST" style="display:inline;">
        <button type="submit" name="delete" value="1" 
                onclick="return confirm('Hapus pesan ini secara permanen?')"
                class="text-[#ef4444] hover:bg-[#fee2e2] px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
          <iconify-icon icon="solar:trash-bin-2-linear" class="text-base"></iconify-icon>
          <span class="text-xs uppercase tracking-widest">Hapus</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Message body -->
  <div class="px-6 py-6">
    <div class="prose prose-sm max-w-none">
      <p class="text-base leading-7 text-[#111827] whitespace-pre-wrap">
        <?= e($msg['message']) ?>
      </p>
    </div>
  </div>

  <!-- Footer -->
  <div class="border-t border-[#e2e8f0] px-6 py-4 bg-[#f8fafc] flex items-center justify-between">
    <div class="text-xs text-[#94a3b8]">
      ID Pesan: #<?= $msg['id'] ?>
    </div>
    <div class="flex gap-2">
      <a href="<?php
        $email = urlencode($msg['email']);
        $subject = urlencode('RE: ' . ($msg['subject'] ?? 'Pesan dari NamaPrint'));
        echo "mailto:{$msg['email']}?subject={$subject}";
      ?>" class="bg-[#2563eb] text-white text-xs uppercase tracking-widest px-4 py-2 rounded-lg hover:bg-[#1c52c9] transition-all">
        Balas Email
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>