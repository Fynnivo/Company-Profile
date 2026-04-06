<?php
require_once __DIR__ . '/../bootstrap.php';

$page_title = 'Tambah Artikel';
$breadcrumb = 'Buat artikel baru';
require_once __DIR__ . '/../includes/admin-header.php';

$errors = [];
$d = ['title'=>'','slug'=>'','category'=>'','excerpt'=>'','content'=>'',
      'meta_title'=>'','meta_description'=>'','status'=>'draft','thumbnail'=>null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d['title']            = trim($_POST['title']            ?? '');
    $d['slug']             = trim($_POST['slug']             ?? '');
    $d['category']         = trim($_POST['category']         ?? '');
    $d['excerpt']          = trim($_POST['excerpt']          ?? '');
    $d['content']          = $_POST['content']               ?? '';
    $d['meta_title']       = trim($_POST['meta_title']       ?? '');
    $d['meta_description'] = trim($_POST['meta_description'] ?? '');
    $d['status']           = ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft';

    if (!$d['title'])   $errors['title']   = 'Judul wajib diisi.';
    if (!$d['content']) $errors['content'] = 'Konten wajib diisi.';

    // Auto slug
    if (!$d['slug'] && $d['title']) $d['slug'] = make_slug($d['title']);

    // Unique slug check
    $chk = $pdo->prepare("SELECT id FROM articles WHERE slug=? LIMIT 1");
    $chk->execute([$d['slug']]);
    if ($chk->fetch()) $d['slug'] .= '-' . time();

    // Upload thumbnail
    if (!empty($_FILES['thumbnail']['name'])) {
        $path = upload_image($_FILES['thumbnail'], 'articles');
        if ($path) $d['thumbnail'] = $path;
        else $errors['thumbnail'] = 'Upload gagal. Gunakan JPG, PNG, atau WebP.';
    }

    if (empty($errors)) {
        $pdo->prepare("INSERT INTO articles
            (title,slug,category,excerpt,content,thumbnail,meta_title,meta_description,status,created_at)
            VALUES (?,?,?,?,?,?,?,?,?,NOW())")
            ->execute([$d['title'],$d['slug'],$d['category'],$d['excerpt'],$d['content'],
                       $d['thumbnail'],$d['meta_title'],$d['meta_description'],$d['status']]);
        flash('Artikel berhasil ditambahkan.');
        header('Location: ' . BASE_URL . '/admin/articles/index.php'); exit;
    }
}

// Existing categories for datalist
$cats = $pdo->query("SELECT DISTINCT category FROM articles WHERE category IS NOT NULL AND category!='' ORDER BY category")
            ->fetchAll(PDO::FETCH_COLUMN);
?>

<?php if ($errors): ?>
<div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
  <p class="text-xs font-semibold text-red-600 uppercase tracking-widest mb-1">Harap perbaiki:</p>
  <?php foreach ($errors as $err): ?>
  <p class="text-sm text-red-600">— <?= e($err) ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-6">

    <!-- ── LEFT: main content ── -->
    <div class="space-y-5">

      <!-- Title -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Judul Artikel <span class="text-red-400">*</span></label>
        <input type="text" name="title" id="inp-title" value="<?= e($d['title']) ?>"
          placeholder="Judul artikel..."
          class="w-full px-4 py-3 rounded-xl border <?= isset($errors['title']) ? 'border-red-400' : 'border-[#e2e8f0]' ?> bg-[#f8fafc] text-base font-semibold text-[#111827] outline-none focus:border-[#2563eb] focus:bg-white transition-all">
      </div>

      <!-- Slug -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Slug URL</label>
        <div class="flex items-center gap-2 px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc]">
          <span class="text-xs text-[#94a3b8] flex-shrink-0">/artikel/</span>
          <input type="text" name="slug" id="inp-slug" value="<?= e($d['slug']) ?>"
            placeholder="otomatis-dari-judul"
            class="flex-1 text-sm text-[#111827] bg-transparent outline-none min-w-0">
        </div>
        <p class="text-xs text-[#94a3b8] mt-1">Kosongkan untuk generate otomatis.</p>
      </div>

      <!-- Content (TinyMCE) -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-3">
          Konten <span class="text-red-400">*</span>
        </label>
        <textarea name="content" id="tinymce-content"><?= htmlspecialchars($d['content']) ?></textarea>
        <?php if (isset($errors['content'])): ?>
        <p class="text-xs text-red-500 mt-2"><?= e($errors['content']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Excerpt -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Ringkasan</label>
        <textarea name="excerpt" rows="3" placeholder="Ringkasan singkat (tampil di listing & SEO)..."
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#111827] placeholder:text-[#cbd5e1] outline-none focus:border-[#2563eb] transition-colors resize-none"><?= e($d['excerpt']) ?></textarea>
      </div>

      <!-- SEO -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#64748b] mb-4 flex items-center gap-2">
          <iconify-icon icon="solar:chart-2-linear" class="text-base"></iconify-icon> SEO
        </p>
        <div class="space-y-4">
          <div>
            <label class="block text-xs uppercase tracking-widest text-[#94a3b8] mb-2">Meta Title</label>
            <input type="text" name="meta_title" value="<?= e($d['meta_title']) ?>"
              placeholder="Kosongkan = pakai judul artikel"
              class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
          </div>
          <div>
            <label class="block text-xs uppercase tracking-widest text-[#94a3b8] mb-2">
              Meta Description
              <span id="meta-count" class="ml-2 text-[#94a3b8] normal-case tracking-normal font-normal">(0/160)</span>
            </label>
            <textarea name="meta_description" id="meta-desc" rows="2"
              placeholder="Deskripsi untuk Google (maks 160 karakter)..."
              class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors resize-none"><?= e($d['meta_description']) ?></textarea>
          </div>
        </div>
      </div>

    </div>

    <!-- ── RIGHT: sidebar ── -->
    <div class="space-y-5">

      <!-- Publish -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#64748b] mb-4">Publikasi</p>
        <div class="space-y-2 mb-5">
          <?php foreach (['draft'=>['Draft','solar:clock-circle-linear','#a16207'], 'published'=>['Publik','solar:check-circle-linear','#16a34a']] as $val=>[$lbl,$ico,$clr]): ?>
          <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all
            <?= $d['status']===$val ? 'border-[#2563eb] bg-[#eff6ff]' : 'border-[#e2e8f0] hover:bg-[#f8fafc]' ?>">
            <input type="radio" name="status" value="<?= $val ?>" <?= $d['status']===$val ? 'checked' : '' ?> class="accent-[#2563eb]">
            <iconify-icon icon="<?= $ico ?>" style="color:<?= $clr ?>" class="text-base"></iconify-icon>
            <span class="text-sm font-medium text-[#111827]"><?= $lbl ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <button type="submit"
          class="w-full bg-[#111827] text-white py-3 rounded-xl text-sm uppercase tracking-widest font-semibold hover:bg-[#2563eb] transition-all flex items-center justify-center gap-2">
          <iconify-icon icon="solar:diskette-linear" class="text-base"></iconify-icon>
          Tambah Artikel
        </button>
      </div>

      <!-- Thumbnail -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#64748b] mb-3">Thumbnail</p>
        <div id="thumb-wrap"
          class="mb-3 rounded-xl border-2 border-dashed border-[#e2e8f0] aspect-video flex items-center justify-center bg-[#f8fafc]">
          <div class="text-center pointer-events-none">
            <iconify-icon icon="solar:gallery-add-linear" class="text-3xl text-[#cbd5e1] mb-1 block"></iconify-icon>
            <p class="text-xs text-[#94a3b8]">Belum ada gambar</p>
          </div>
        </div>
        <label class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#475569] cursor-pointer hover:bg-[#f1f5f9] transition-colors">
          <iconify-icon icon="solar:upload-linear" class="text-base"></iconify-icon>
          Pilih Gambar
          <input type="file" name="thumbnail" accept="image/*" class="hidden" onchange="previewImg(this)">
        </label>
        <?php if (isset($errors['thumbnail'])): ?>
        <p class="text-xs text-red-500 mt-1"><?= e($errors['thumbnail']) ?></p>
        <?php endif; ?>
        <p class="text-xs text-[#94a3b8] mt-2">JPG, PNG, WebP. Maks 2MB.</p>
      </div>

      <!-- Category -->
      <div class="bg-white rounded-2xl border border-[#e2e8f0] p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#64748b] mb-3">Kategori</p>
        <input type="text" name="category" value="<?= e($d['category']) ?>"
          placeholder="cth: Panduan, Tips, Berita"
          list="cat-list"
          class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm outline-none focus:border-[#2563eb] transition-colors">
        <datalist id="cat-list">
          <?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?>
        </datalist>
      </div>

    </div>
  </div>
</form>

<!-- TinyMCE (free CDN — no API key needed for basic use) -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#tinymce-content',
  height: 420,
  menubar: false,
  promotion: false,
  branding: false,
  plugins: 'lists link image table code',
  toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | table | code',
  content_style: "body{font-family:'Nunito',sans-serif;font-size:15px;line-height:1.8;color:#374151;}",
  images_upload_url: '<?= BASE_URL ?>/admin/articles/upload-image.php',
  automatic_uploads: true,
});

// Auto-generate slug from title
const titleInput = document.getElementById('inp-title');
const slugInput  = document.getElementById('inp-slug');
let slugManual   = false;
slugInput.addEventListener('input',  () => slugManual = true);
titleInput.addEventListener('input', function() {
  if (!slugManual) {
    slugInput.value = this.value.toLowerCase()
      .replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-').trim('-');
  }
});

// Meta description counter
const metaDesc  = document.getElementById('meta-desc');
const metaCount = document.getElementById('meta-count');
function updateCount() {
  const n = metaDesc.value.length;
  metaCount.textContent = '(' + n + '/160)';
  metaCount.style.color = n > 160 ? '#ef4444' : n > 130 ? '#f59e0b' : '#94a3b8';
}
metaDesc.addEventListener('input', updateCount);
updateCount();

// Thumbnail preview
function previewImg(input) {
  if (!input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const wrap = document.getElementById('thumb-wrap');
    wrap.innerHTML = '<img src="'+e.target.result+'" class="w-full h-full object-cover rounded-xl">';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>