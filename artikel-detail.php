<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Get slug from URL — supports both /artikel/slug (htaccess) and ?slug=
$slug = '';
if (isset($_GET['slug'])) {
    $slug = trim($_GET['slug']);
} else {
    // Support clean URL: /artikel/{slug}
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    $slug = end($parts);
}

if (!$slug) {
    header('Location: ' . BASE_URL . '/artikel');
    exit;
}

// Fetch article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    include '404.php';
    exit;
}

// Fetch related articles (same category, exclude current)
$related = $pdo->prepare("
    SELECT id, title, slug, excerpt, thumbnail, category, created_at
    FROM articles
    WHERE status = 'published' AND id != ? AND category = ?
    ORDER BY created_at DESC
    LIMIT 2
");
$related->execute([$article['id'], $article['category']]);
$related_articles = $related->fetchAll();

// If not enough related by category, fill with recent
if (count($related_articles) < 2) {
    $existing_ids = array_merge([$article['id']], array_column($related_articles, 'id'));
    $placeholders = implode(',', array_fill(0, count($existing_ids), '?'));
    $fill = $pdo->prepare("
        SELECT id, title, slug, excerpt, thumbnail, category, created_at
        FROM articles
        WHERE status = 'published' AND id NOT IN ($placeholders)
        ORDER BY created_at DESC
        LIMIT 2
    ");
    $fill->execute($existing_ids);
    $related_articles = array_merge($related_articles, $fill->fetchAll());
    $related_articles = array_slice($related_articles, 0, 2);
}

// Estimate reading time (avg 200 words/min)
$word_count = str_word_count(strip_tags($article['content']));
$read_time  = max(1, round($word_count / 200));

// SEO
$page_title       = $article['meta_title'] ?: $article['title'];
$meta_description = $article['meta_description'] ?: truncate(strip_tags($article['excerpt'] ?? $article['content']), 155);
$og_image         = $article['thumbnail'] ?? '';

require_once 'includes/header.php';
?>

<div class="max-w-[96rem] mx-auto w-full px-3 sm:px-4 md:px-6 lg:px-8 xl:px-10">
  <main class="pt-24 sm:pt-28 lg:pt-32 pb-8 sm:pb-10">

    <!-- ===================== ARTICLE HERO ===================== -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0" style="background-image:linear-gradient(to right, rgba(31,41,55,0.04) 1px, transparent 1px),linear-gradient(to bottom, rgba(31,41,55,0.04) 1px, transparent 1px);background-size:3rem 3rem"></div>
      <div class="relative max-w-[1400px] mx-auto pt-8 sm:pt-12 lg:pt-16 pb-12 sm:pb-16 lg:pb-20 px-4 sm:px-6">

        <!-- Back link -->
        <div class="mb-8 sm:mb-10">
          <a href="<?= BASE_URL ?>/artikel" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-[#6b7280] hover:text-[#2563eb] transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
            Kembali ke Artikel
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

          <!-- Left: Meta + Title + Excerpt -->
          <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 border border-[#2563eb]/20 px-4 py-2 mb-6">
              <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($article['category'] ?? 'Artikel') ?></span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= $read_time ?> menit baca</span>
            </div>

            <h1 class="text-3xl sm:text-4xl md:text-5xl tracking-tight leading-[1.05] uppercase text-[#111827] font-semibold mb-6" style="font-family:'Playfair Display',serif;">
              <?php
              // Split title at midpoint for gradient effect
              $words = explode(' ', $article['title']);
              $half  = ceil(count($words) / 2);
              $line1 = implode(' ', array_slice($words, 0, $half));
              $line2 = implode(' ', array_slice($words, $half));
              echo e($line1);
              if ($line2):
              ?>
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold"><?= e($line2) ?></span>
              <?php endif; ?>
            </h1>

            <?php if ($article['excerpt']): ?>
            <p class="text-lg text-[#4b5563] leading-relaxed font-normal mb-8" style="font-family:'Nunito',sans-serif;">
              <?= e($article['excerpt']) ?>
            </p>
            <?php endif; ?>

            <!-- Article meta pills -->
            <div class="flex flex-wrap items-center gap-3">

              <div class="rounded-full border border-[#111827]/10 bg-white/80 px-4 py-2">
                <p class="text-xs uppercase tracking-widest text-[#6b7280]"><?= date('d M Y', strtotime($article['created_at'])) ?></p>
              </div>
              <?php if ($article['category']): ?>
              <div class="rounded-full border border-[#111827]/10 bg-white/80 px-4 py-2">
                <p class="text-xs uppercase tracking-widest text-[#6b7280]"><?= e($article['category']) ?></p>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Right: Thumbnail -->
          <div>
            <div class="rounded-2xl overflow-hidden border border-[#111827]/8 bg-[#111827] shadow-lg">
              <?php if ($article['thumbnail']): ?>
              <div class="aspect-[4/3] overflow-hidden">
                <img src="<?= BASE_URL ?>/<?= e($article['thumbnail']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-full object-cover">
              </div>
              <?php else: ?>
              <div class="aspect-[4/3] flex items-center justify-center bg-[#1f2937]">
                <iconify-icon icon="solar:document-text-linear" class="text-8xl text-white/20"></iconify-icon>
              </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ===================== ARTICLE BODY ===================== -->
    <section class="mt-2">
      <div class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-8 lg:gap-12 items-start px-4 sm:px-6">

        <!-- Sidebar -->
        <aside class="lg:sticky lg:top-28 space-y-4">
          <!-- Table of contents (auto-generated from headings via JS) -->
          <div class="rounded-xl border border-[#111827]/8 bg-white/60 backdrop-blur-sm p-4 shadow-sm text-sm">
            <div class="mb-4">
              <p class="text-xs uppercase tracking-[0.15em] text-[#2563eb] font-semibold mb-2">Daftar Isi</p>
              <div id="toc" class="space-y-2">
                <p class="text-xs text-[#9ca3af] italic">Memuat...</p>
              </div>
            </div>

            <div class="pt-4 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.15em] text-[#6b7280] font-semibold mb-3">Info</p>
              <div class="space-y-3 text-xs">
                <div>
                  <p class="text-[#6b7280] tracking-wide uppercase font-medium">Kategori</p>
                  <p class="text-[#111827] mt-0.5"><?= e($article['category'] ?? '—') ?></p>
                </div>
                <div>
                  <p class="text-[#6b7280] tracking-wide uppercase font-medium">Baca</p>
                  <p class="text-[#111827] mt-0.5">~<?= $read_time ?> menit</p>
                </div>
                <div>
                  <p class="text-[#6b7280] tracking-wide uppercase font-medium">Update</p>
                  <p class="text-[#111827] mt-0.5"><?= date('d M Y', strtotime($article['updated_at'])) ?></p>
                </div>
              </div>
            </div>

            <!-- Share -->
            <div class="pt-4 mt-2 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.15em] text-[#6b7280] font-semibold mb-3">Bagikan</p>
              <div class="flex gap-2">
                <?php $share_url = urlencode(BASE_URL . '/artikel/' . $article['slug']); $share_title = urlencode($article['title']); ?>
                <a href="https://wa.me/?text=<?= $share_title ?>%20<?= $share_url ?>" target="_blank"
                  class="w-8 h-8 rounded-full border border-[#111827]/10 flex items-center justify-center text-[#111827]/50 hover:text-[#25D366] hover:border-[#25D366]/50 transition-all text-sm">
                  <iconify-icon icon="ic:baseline-whatsapp"></iconify-icon>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?= $share_title ?>&url=<?= $share_url ?>" target="_blank"
                  class="w-8 h-8 rounded-full border border-[#111827]/10 flex items-center justify-center text-[#111827]/50 hover:text-[#2563eb] hover:border-[#2563eb]/50 transition-all text-sm">
                  <iconify-icon icon="simple-icons:x"></iconify-icon>
                </a>
                <button onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<iconify-icon icon=\'solar:check-circle-linear\'></iconify-icon>'"
                  class="w-8 h-8 rounded-full border border-[#111827]/10 flex items-center justify-center text-[#111827]/50 hover:text-[#2563eb] hover:border-[#2563eb]/50 transition-all text-sm">
                  <iconify-icon icon="solar:copy-linear"></iconify-icon>
                </button>
              </div>
            </div>
          </div>
        </aside>

        <!-- Article content -->
        <article class="max-w-[720px] mx-auto w-full">
          <div class="py-2">
            <div class="article-content">
              <?= display_content($article['content']) ?>
            </div>

            <!-- Article footer: tags + back link -->
            <div class="mt-12 pt-8 border-t border-[#111827]/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <?php if ($article['category']): ?>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs uppercase tracking-widest text-[#6b7280]">Kategori:</span>
                <a href="<?= BASE_URL ?>/artikel?kategori=<?= urlencode($article['category']) ?>"
                  class="rounded-full border border-[#2563eb]/20 bg-[#2563eb]/5 text-[#2563eb] text-xs uppercase tracking-widest px-3 py-1.5 hover:bg-[#2563eb] hover:text-white transition-all">
                  <?= e($article['category']) ?>
                </a>
              </div>
              <?php endif; ?>
              <a href="<?= BASE_URL ?>/artikel" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#6b7280] hover:text-[#2563eb] transition-colors">
                <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
                Semua artikel
              </a>
            </div>
          </div>
        </article>

      </div>
    </section>

    <!-- ===================== RELATED ARTICLES ===================== -->
    <?php if (!empty($related_articles)): ?>
    <section class="mt-24 sm:mt-32 lg:mt-40 px-4 sm:px-6">
      <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10 sm:mb-12">
          <div>
            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Lanjutkan Membaca</p>
            <h2 class="text-3xl sm:text-4xl md:text-5xl tracking-tight uppercase text-[#111827] leading-[0.95] font-semibold" style="font-family:'Playfair Display',serif;">Artikel Terkait</h2>
          </div>
          <p class="text-base md:text-lg text-[#4b5563] max-w-2xl">
            Bacaan tambahan seputar produksi cetak, persiapan file, dan alur kerja digital printing.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">
          <?php foreach ($related_articles as $rel): ?>
          <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group">
            <div class="aspect-[4/3] overflow-hidden bg-[#f4f4f2]">
              <?php if ($rel['thumbnail']): ?>
              <img src="<?= BASE_URL ?>/<?= e($rel['thumbnail']) ?>" alt="<?= e($rel['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
              <?php else: ?>
              <div class="w-full h-full flex items-center justify-center">
                <iconify-icon icon="solar:document-text-linear" class="text-5xl text-[#d1d5db]"></iconify-icon>
              </div>
              <?php endif; ?>
            </div>
            <div class="p-6">
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($rel['category'] ?? 'Artikel') ?></span>
                <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= date('d M Y', strtotime($rel['created_at'])) ?></span>
              </div>
              <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($rel['title']) ?></h3>
              <p class="text-base text-[#4b5563] leading-relaxed mb-6"><?= e(truncate($rel['excerpt'] ?? '', 110)) ?></p>
              <a href="<?= BASE_URL ?>/artikel/<?= e($rel['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                Baca artikel
                <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
              </a>
            </div>
          </article>
          <?php endforeach; ?>

          <!-- CTA card -->
          <article class="md:col-span-2 bg-[#1f2937] rounded-[1.5rem] overflow-hidden p-8 sm:p-10 lg:p-12 flex flex-col justify-center items-center text-center">
            <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-4">Butuh panduan cetak?</p>
            <h3 class="text-3xl sm:text-4xl md:text-5xl tracking-tight uppercase text-white mb-6 font-semibold max-w-2xl" style="font-family:'Playfair Display',serif;">Hubungi tim produksi kami</h3>
            <p class="text-base text-white/70 leading-relaxed max-w-xl mb-8">Dapatkan bantuan memilih bahan, finishing, dan pengaturan output untuk cetakan Anda berikutnya.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <a href="<?= BASE_URL ?>/kontak" class="rounded-full bg-[#2563eb] !text-white px-8 py-3 text-sm uppercase tracking-widest font-semibold hover:bg-[#1c52c9] transition-all">
                Hubungi kami
              </a>
              <a href="https://wa.me/<?= e(get_setting('whatsapp', $pdo)) ?>" target="_blank" class="rounded-full border border-[#2563eb] text-[#2563eb] px-8 py-3 text-sm uppercase tracking-widest font-semibold hover:bg-[#2563eb] hover:text-white transition-all flex items-center gap-2">
                <iconify-icon icon="ic:baseline-whatsapp" class="text-lg"></iconify-icon>
                WhatsApp
              </a>
            </div>
          </article>
        </div>
      </div>
    </section>
    <?php endif; ?>

  </main>
</div>

<!-- Auto Table of Contents script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const content = document.querySelector('.article-content');
  const toc     = document.getElementById('toc');
  if (!content || !toc) return;

  const headings = content.querySelectorAll('h2, h3');
  if (!headings.length) { toc.innerHTML = '<p class="text-xs text-[#9ca3af] italic">Tidak ada daftar isi.</p>'; return; }

  toc.innerHTML = '';
  headings.forEach((h, i) => {
    const id = 'section-' + i;
    h.id = id;
    const a = document.createElement('a');
    a.href = '#' + id;
    a.textContent = h.textContent;
    a.className = h.tagName === 'H2'
      ? 'block text-sm text-[#111827] hover:text-[#2563eb] transition-colors font-medium'
      : 'block text-sm text-[#111827]/60 hover:text-[#2563eb] transition-colors pl-3 border-l border-[#111827]/10';
    toc.appendChild(a);
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>