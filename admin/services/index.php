<?php

ob_start();

require_once __DIR__ . '/../bootstrap.php';

$page_title = 'Layanan';
$breadcrumb = 'Kelola layanan';
require_once __DIR__ . '/../includes/admin-header.php';

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Reorder (AJAX from SortableJS) ──
    if ($action === 'reorder' && !empty($_POST['order'])) {
        $ids = json_decode($_POST['order'], true);
        if (is_array($ids)) {
            foreach ($ids as $i => $sid) {
                $pdo->prepare("UPDATE services SET sort_order=? WHERE id=?")->execute([$i+1, (int)$sid]);
            }
        }
        echo 'ok'; exit;
    }

    // ── Save (insert or update) ──
    if ($action === 'save') {
        $id   = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: make_slug($name);
        $desc = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'solar:printer-2-linear');
        $active = isset($_POST['is_active']) ? 1 : 0;

        // Unique slug
        $chk = $pdo->prepare("SELECT id FROM services WHERE slug=? AND id!=? LIMIT 1");
        $chk->execute([$slug, $id]);
        if ($chk->fetch()) $slug .= '-' . time();

        // Image upload
        $current_image = $_POST['current_image'] ?? null;
        if (!empty($_FILES['image']['name'])) {
            $path = upload_image($_FILES['image'], 'services');
            if ($path) {
                delete_file($current_image);
                $current_image = $path;
            }
        }

        if ($id) {
            $pdo->prepare("UPDATE services SET name=?,slug=?,description=?,icon=?,image=?,is_active=? WHERE id=?")
                ->execute([$name,$slug,$desc,$icon,$current_image,$active,$id]);
            flash('Layanan berhasil diperbarui.');
        } else {
            $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM services")->fetchColumn();
            $pdo->prepare("INSERT INTO services (name,slug,description,icon,image,is_active,sort_order) VALUES (?,?,?,?,?,?,?)")
                ->execute([$name,$slug,$desc,$icon,$current_image,$active,$max+1]);
            flash('Layanan berhasil ditambahkan.');
        }
        header('Location: ' . BASE_URL . '/admin/services/index.php'); exit;
    }
}

// ── Handle GET actions ───────────────────────────────────────
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $row = $pdo->prepare("SELECT image FROM services WHERE id=?");
    $row->execute([(int)$_GET['delete']]);
    $s = $row->fetch();
    if ($s) delete_file($s['image']);
    $pdo->prepare("DELETE FROM services WHERE id=?")->execute([(int)$_GET['delete']]);
    flash('Layanan berhasil dihapus.');
    header('Location: ' . BASE_URL . '/admin/services/index.php'); exit;
}

if (isset($_GET['toggle']) && ctype_digit($_GET['toggle'])) {
    $pdo->prepare("UPDATE services SET is_active=IF(is_active=1,0,1) WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: ' . BASE_URL . '/admin/services/index.php'); exit;
}

// Load edit target
$editing = null;
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();

// Icon options (Solar icons)
$icon_opts = [
    'solar:printer-2-linear','solar:image-linear','solar:document-text-linear',
    'solar:id-card-linear','solar:sticky-note-linear','solar:box-linear',
    'solar:gallery-wide-linear','solar:scissors-linear','solar:palette-linear',
    'solar:t-shirt-linear','solar:tag-linear','solar:flag-linear',
    'solar:gift-linear','solar:star-linear','solar:shield-check-linear',
];
?>

<?= render_flash() ?>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">

  <!-- ── LIST ── -->
  <div class="bg-white rounded-2xl border border-[#e2e8f0] overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#e2e8f0]">
      <p class="text-sm font-semibold text-[#111827]">Daftar Layanan</p>
      <p class="text-xs text-[#94a3b8]">Drag baris untuk mengurutkan</p>
    </div>

    <?php if ($services): ?>
    <ul id="sortable" class="divide-y divide-[#f1f5f9]">
      <?php foreach ($services as $s): ?>
      <li data-id="<?= $s['id'] ?>"
          class="flex items-center gap-3 px-5 py-3.5 hover:bg-[#f8fafc] transition-colors cursor-grab">

        <!-- Drag handle -->
        <iconify-icon icon="solar:menu-dots-linear" class="text-[#cbd5e1] text-lg rotate-90 flex-shrink-0"></iconify-icon>

        <!-- Icon preview -->
        <div class="w-9 h-9 rounded-xl bg-[#f1f5f9] flex items-center justify-center flex-shrink-0">
          <iconify-icon icon="<?= e($s['icon'] ?? 'solar:printer-2-linear') ?>" class="text-[#2563eb]"></iconify-icon>
        </div>

        <!-- Image preview (if any) -->
        <?php if ($s['image']): ?>
        <img src="<?= BASE_URL ?>/<?= e($s['image']) ?>"
             class="w-10 h-10 rounded-lg object-cover border border-[#e2e8f0] flex-shrink-0">
        <?php endif; ?>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-[#111827] truncate"><?= e($s['name']) ?></p>
          <p class="text-xs text-[#94a3b8] truncate">/layanan/<?= e($s['slug']) ?></p>
        </div>

        <!-- Active badge -->
        <a href="?toggle=<?= $s['id'] ?>"
           class="flex-shrink-0 text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-lg font-semibold
           <?= $s['is_active'] ? 'bg-[#f0fdf4] text-[#16a34a]' : 'bg-[#f1f5f9] text-[#94a3b8]' ?>">
          <?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?>
        </a>

        <!-- Actions -->
        <div class="flex items-center gap-2 flex-shrink-0">
          <a href="?edit=<?= $s['id'] ?>" class="text-[#94a3b8] hover:text-[#2563eb] transition-colors">
            <iconify-icon icon="solar:pen-2-linear" class="text-base"></iconify-icon>
          </a>
          <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Hapus layanan ini?')"
             class="text-[#94a3b8] hover:text-[#ef4444] transition-colors">
            <iconify-icon icon="solar:trash-bin-2-linear" class="text-base"></iconify-icon>
          </a>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p class="px-5 py-12 text-sm text-[#94a3b8] text-center">Belum ada layanan. Tambahkan di form sebelah kanan.</p>
    <?php endif; ?>
  </div>

  <!-- ── FORM ── -->
  <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5 h-fit">
    <p class="text-sm font-semibold text-[#111827] mb-5">
      <?= $editing ? 'Edit Layanan' : 'Tambah Layanan Baru' ?>
    </p>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="action" value="save">
      <?php if ($editing): ?>
      <input type="hidden" name="id" value="<?= $editing['id'] ?>">
      <input type="hidden" name="current_image" value="<?= e($editing['image'] ?? '') ?>">
      <?php endif; ?>

      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Nama Layanan *</label>
        <input type="text" name="name" required value="<?= e($editing['name'] ?? '') ?>"
          placeholder="cth: Cetak Banner"
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
      </div>

      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Slug</label>
        <input type="text" name="slug" value="<?= e($editing['slug'] ?? '') ?>"
          placeholder="otomatis dari nama"
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
      </div>

      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Deskripsi</label>
        <textarea name="description" rows="3"
          placeholder="Deskripsi singkat layanan..."
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors resize-none"><?= e($editing['description'] ?? '') ?></textarea>
      </div>

      <!-- Icon picker -->
      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Icon</label>
        <div class="grid grid-cols-5 gap-2 p-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] mb-2">
          <?php foreach ($icon_opts as $ico): ?>
          <label class="cursor-pointer">
            <input type="radio" name="icon" value="<?= $ico ?>"
              <?= ($editing['icon'] ?? 'solar:printer-2-linear') === $ico ? 'checked' : '' ?>
              class="sr-only peer">
            <div class="w-full aspect-square rounded-lg flex items-center justify-center bg-white border border-[#e2e8f0]
                         peer-checked:border-[#2563eb] peer-checked:bg-[#eff6ff] hover:bg-[#f1f5f9] transition-all">
              <iconify-icon icon="<?= $ico ?>" class="text-xl text-[#64748b] peer-checked:text-[#2563eb]"></iconify-icon>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        <input type="text" name="icon" value="<?= e($editing['icon'] ?? 'solar:printer-2-linear') ?>"
          placeholder="Atau ketik nama icon Solar..."
          class="w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
      </div>

      <!-- Image upload -->
      <div>
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Gambar (opsional)</label>
        <?php if (!empty($editing['image'])): ?>
        <div class="mb-2 rounded-xl overflow-hidden border border-[#e2e8f0] aspect-video">
          <img src="<?= BASE_URL ?>/<?= e($editing['image']) ?>" class="w-full h-full object-cover">
        </div>
        <?php endif; ?>
        <label class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569] cursor-pointer hover:bg-[#f1f5f9] transition-colors">
          <iconify-icon icon="solar:upload-linear" class="text-base"></iconify-icon>
          <?= !empty($editing['image']) ? 'Ganti Gambar' : 'Upload Gambar' ?>
          <input type="file" name="image" accept="image/*" class="hidden">
        </label>
      </div>

      <!-- Active toggle -->
      <label class="flex items-center gap-3 cursor-pointer">
        <div class="relative w-10 h-6 flex-shrink-0">
          <input type="checkbox" name="is_active" id="toggle-active" class="sr-only peer"
            <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
          <div class="w-full h-full rounded-full bg-[#e2e8f0] peer-checked:bg-[#2563eb] transition-colors"></div>
          <div class="absolute top-1 left-1 w-4 h-4 rounded-full bg-white shadow peer-checked:translate-x-4 transition-transform"></div>
        </div>
        <span class="text-sm text-[#475569]">Tampilkan di website</span>
      </label>

      <div class="flex gap-3 pt-1">
        <button type="submit"
          class="flex-1 bg-[#111827] text-white py-3 rounded-xl text-sm uppercase tracking-widest font-semibold hover:bg-[#2563eb] transition-all">
          <?= $editing ? 'Simpan' : 'Tambah' ?>
        </button>
        <?php if ($editing): ?>
        <a href="<?= BASE_URL ?>/admin/services/index.php"
           class="px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm text-[#475569] hover:bg-[#f1f5f9] transition-colors text-center">
          Batal
        </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

</div>

<!-- SortableJS drag-to-reorder -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const sortable = document.getElementById('sortable');
if (sortable) {
  Sortable.create(sortable, {
    animation: 150,
    onEnd: function() {
      const order = Array.from(sortable.querySelectorAll('li')).map(li => li.dataset.id);
      fetch('', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=reorder&order=' + encodeURIComponent(JSON.stringify(order))
      });
    }
  });
}
</script>
<?php 
ob_end_flush();
require_once __DIR__ . '/../includes/admin-footer.php'; ?>