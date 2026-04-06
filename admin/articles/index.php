<?php
require_once __DIR__ . '/../bootstrap.php';

$page_title = 'Artikel';
$breadcrumb = 'Kelola semua artikel';
require_once __DIR__ . '/../includes/admin-header.php';

// ── Actions ──────────────────────────────────────────────────
// Delete
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $row = $pdo->prepare("SELECT thumbnail FROM articles WHERE id=?");
    $row->execute([(int)$_GET['delete']]);
    $art = $row->fetch();
    if ($art && $art['thumbnail']) delete_file($art['thumbnail']);
    $pdo->prepare("DELETE FROM articles WHERE id=?")->execute([(int)$_GET['delete']]);
    flash('Artikel berhasil dihapus.');
    header('Location: ' . BASE_URL . '/admin/articles/index.php'); exit;
}

// Toggle published/draft
if (isset($_GET['toggle']) && ctype_digit($_GET['toggle'])) {
    $pdo->prepare("UPDATE articles SET status=IF(status='published','draft','published') WHERE id=?")
        ->execute([(int)$_GET['toggle']]);
    header('Location: ' . BASE_URL . '/admin/articles/index.php'); exit;
}

// ── Query ────────────────────────────────────────────────────
$filter = in_array($_GET['status'] ?? '', ['published','draft']) ? $_GET['status'] : '';
$search = trim($_GET['q'] ?? '');

$where  = []; $params = [];
if ($filter) { $where[] = "status=?";               $params[] = $filter; }
if ($search) { $where[] = "(title LIKE ? OR category LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$sql  = "SELECT * FROM articles" . ($where ? " WHERE ".implode(" AND ",$where) : "") . " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

$qs = fn($extra=[]) => http_build_query(array_filter(array_merge(['status'=>$filter,'q'=>$search], $extra)));
?>

<?= render_flash() ?>

<!-- Toolbar -->
<div class="flex flex-wrap gap-3 items-center justify-between mb-5">

  <!-- Search -->
  <form class="flex gap-2 flex-1 max-w-xs">
    <input type="hidden" name="status" value="<?= e($filter) ?>">
    <div class="relative flex-1">
      <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-sm pointer-events-none"></iconify-icon>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari artikel..."
        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
    </div>
    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#f1f5f9] text-sm text-[#475569] hover:bg-[#e2e8f0] transition-colors">Cari</button>
  </form>

  <!-- Filters + add -->
  <div class="flex items-center gap-2 flex-wrap">
    <a href="?<?= $qs(['status'=>'']) ?>"
       class="text-xs uppercase tracking-widest px-3 py-2 rounded-xl transition-all <?= !$filter ? 'bg-[#111827] text-white' : 'bg-[#f1f5f9] text-[#475569] hover:bg-[#e2e8f0]' ?>">Semua</a>
    <a href="?<?= $qs(['status'=>'published']) ?>"
       class="text-xs uppercase tracking-widest px-3 py-2 rounded-xl transition-all <?= $filter==='published' ? 'bg-[#111827] text-white' : 'bg-[#f1f5f9] text-[#475569] hover:bg-[#e2e8f0]' ?>">Publik</a>
    <a href="?<?= $qs(['status'=>'draft']) ?>"
       class="text-xs uppercase tracking-widest px-3 py-2 rounded-xl transition-all <?= $filter==='draft' ? 'bg-[#111827] text-white' : 'bg-[#f1f5f9] text-[#475569] hover:bg-[#e2e8f0]' ?>">Draft</a>
    <a href="<?= BASE_URL ?>/admin/articles/create.php"
       class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#111827] text-white text-xs uppercase tracking-widest hover:bg-[#2563eb] transition-all">
      <iconify-icon icon="solar:add-circle-linear" class="text-sm"></iconify-icon>Tambah
    </a>
  </div>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden">
  <?php if ($articles): ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-[#e2e8f0] bg-[#f8fafc] text-xs uppercase tracking-widest text-[#64748b]">
          <th class="text-left px-5 py-3 font-semibold">Judul</th>
          <th class="text-left px-4 py-3 font-semibold hidden md:table-cell">Kategori</th>
          <th class="text-left px-4 py-3 font-semibold hidden lg:table-cell">Tanggal</th>
          <th class="text-left px-4 py-3 font-semibold">Status</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#f1f5f9]">
        <?php foreach ($articles as $a): ?>
        <tr class="hover:bg-[#f8fafc] transition-colors">
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-3">
              <?php if ($a['thumbnail']): ?>
              <img src="<?= BASE_URL ?>/<?= e($a['thumbnail']) ?>"
                   class="w-10 h-10 rounded-lg object-cover border border-[#e2e8f0] flex-shrink-0">
              <?php else: ?>
              <div class="w-10 h-10 rounded-lg bg-[#f1f5f9] flex items-center justify-center flex-shrink-0">
                <iconify-icon icon="solar:document-text-linear" class="text-[#94a3b8]"></iconify-icon>
              </div>
              <?php endif; ?>
              <div class="min-w-0">
                <p class="font-medium text-[#111827] truncate max-w-[200px]"><?= e($a['title']) ?></p>
                <p class="text-xs text-[#94a3b8] truncate max-w-[200px]">/artikel/<?= e($a['slug']) ?></p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3.5 hidden md:table-cell">
            <span class="text-xs bg-[#f1f5f9] text-[#475569] px-2 py-1 rounded-lg"><?= e($a['category'] ?: '—') ?></span>
          </td>
          <td class="px-4 py-3.5 text-xs text-[#94a3b8] hidden lg:table-cell whitespace-nowrap">
            <?= date('d M Y', strtotime($a['created_at'])) ?>
          </td>
          <td class="px-4 py-3.5">
            <a href="?<?= $qs(['toggle'=>$a['id'],'status'=>$filter,'q'=>$search]) ?>"
               class="inline-flex items-center gap-1 text-[10px] uppercase tracking-widest px-2.5 py-1.5 rounded-lg font-semibold transition-all
               <?= $a['status']==='published' ? 'bg-[#f0fdf4] text-[#16a34a] hover:bg-[#dcfce7]' : 'bg-[#fefce8] text-[#a16207] hover:bg-[#fef9c3]' ?>">
              <span class="w-1.5 h-1.5 rounded-full <?= $a['status']==='published' ? 'bg-[#22c55e]' : 'bg-[#f59e0b]' ?>"></span>
              <?= $a['status']==='published' ? 'Publik' : 'Draft' ?>
            </a>
          </td>
          <td class="px-4 py-3.5">
            <div class="flex items-center justify-end gap-2">
              <a href="<?= BASE_URL ?>/artikel/<?= e($a['slug']) ?>" target="_blank"
                 class="text-[#94a3b8] hover:text-[#2563eb] transition-colors" title="Lihat">
                <iconify-icon icon="solar:eye-linear" class="text-base"></iconify-icon>
              </a>
              <a href="<?= BASE_URL ?>/admin/articles/edit.php?id=<?= $a['id'] ?>"
                 class="text-[#94a3b8] hover:text-[#2563eb] transition-colors" title="Edit">
                <iconify-icon icon="solar:pen-2-linear" class="text-base"></iconify-icon>
              </a>
              <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Hapus artikel ini?')"
                 class="text-[#94a3b8] hover:text-[#ef4444] transition-colors" title="Hapus">
                <iconify-icon icon="solar:trash-bin-2-linear" class="text-base"></iconify-icon>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="py-16 text-center">
    <iconify-icon icon="solar:document-text-linear" class="text-4xl text-[#cbd5e1] mb-3 block"></iconify-icon>
    <p class="text-sm text-[#94a3b8]">Belum ada artikel<?= $search ? ' yang cocok.' : '.' ?></p>
    <a href="<?= BASE_URL ?>/admin/articles/create.php"
       class="mt-4 inline-flex items-center gap-1.5 text-sm text-[#2563eb] hover:underline">
      <iconify-icon icon="solar:add-circle-linear" class="text-base"></iconify-icon> Tambah artikel pertama
    </a>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>