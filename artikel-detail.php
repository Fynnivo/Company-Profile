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
      <div class="relative max-w-[88rem] mx-auto pt-6 sm:pt-8 lg:pt-10 pb-8 sm:pb-10">

        <!-- Back link -->
        <div class="mb-6 sm:mb-8">
          <a href="<?= BASE_URL ?>/artikel" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-[#6b7280] hover:text-[#2563eb] transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
            Kembali ke Artikel
          </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[0.8fr_1.2fr] gap-6 sm:gap-8 lg:gap-10 items-end">

          <!-- Left: Meta + Title -->
          <div class="order-2 xl:order-1">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 border border-[#2563eb]/20 px-4 py-2 mb-5">
              <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($article['category'] ?? 'Artikel') ?></span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= $read_time ?> menit baca</span>
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight leading-[0.92] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
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
            <p class="mt-6 text-lg sm:text-xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              <?= e($article['excerpt']) ?>
            </p>
            <?php endif; ?>

            <!-- Article meta pills -->
            <div class="mt-8 flex flex-wrap items-center gap-3 sm:gap-4">
              <div class="rounded-full border border-[#111827]/10 bg-white/80 px-4 py-2">
                <p class="text-xs uppercase tracking-widest text-[#6b7280]">Tim Editorial</p>
              </div>
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
          <div class="order-1 xl:order-2">
            <div class="rounded-[1.75rem] overflow-hidden border border-[#111827]/8 bg-[#111827] shadow-[0_1.5rem_4rem_rgba(15,23,42,0.14)]">
              <?php if ($article['thumbnail']): ?>
              <div class="aspect-[16/10] overflow-hidden">
                <img src="<?= BASE_URL ?>/<?= e($article['thumbnail']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-full object-cover opacity-90 mix-blend-luminosity">
              </div>
              <?php else: ?>
              <div class="aspect-[16/10] flex items-center justify-center bg-[#1f2937]">
                <iconify-icon icon="solar:document-text-linear" class="text-7xl text-white/20"></iconify-icon>
              </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ===================== ARTICLE BODY ===================== -->
    <section class="mt-2">
      <div class="max-w-[88rem] mx-auto grid grid-cols-1 xl:grid-cols-[0.7fr_1.3fr] gap-6 sm:gap-8 lg:gap-10 items-start">

        <!-- Sidebar -->
        <aside class="xl:sticky xl:top-28 space-y-4">
          <!-- Table of contents (auto-generated from headings via JS) -->
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
            <div class="mb-6">
              <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Di halaman ini</p>
              <div id="toc" class="space-y-3">
                <p class="text-xs text-[#9ca3af] italic">Memuat daftar isi...</p>
              </div>
            </div>

            <div class="pt-6 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Info Artikel</p>
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-full border border-[#111827]/8 bg-[#f4f4f2] flex items-center justify-center text-[#2563eb]">
                    <iconify-icon icon="solar:document-text-linear" class="text-lg"></iconify-icon>
                  </div>
                  <div>
                    <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Kategori</p>
                    <p class="text-sm text-[#111827]"><?= e($article['category'] ?? '—') ?></p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-full border border-[#111827]/8 bg-[#f4f4f2] flex items-center justify-center text-[#2563eb]">
                    <iconify-icon icon="solar:clock-circle-linear" class="text-lg"></iconify-icon>
                  </div>
                  <div>
                    <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Estimasi Baca</p>
                    <p class="text-sm text-[#111827]">Sekitar <?= $read_time ?> menit</p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-full border border-[#111827]/8 bg-[#f4f4f2] flex items-center justify-center text-[#2563eb]">
                    <iconify-icon icon="solar:calendar-linear" class="text-lg"></iconify-icon>
                  </div>
                  <div>
                    <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Diperbarui</p>
                    <p class="text-sm text-[#111827]"><?= date('d M Y', strtotime($article['updated_at'])) ?></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Share -->
            <div class="pt-6 mt-2 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Bagikan</p>
              <div class="flex gap-3">
                <?php $share_url = urlencode(BASE_URL . '/artikel/' . $article['slug']); $share_title = urlencode($article['title']); ?>
                <a href="https://wa.me/?text=<?= $share_title ?>%20<?= $share_url ?>" target="_blank"
                  class="w-9 h-9 rounded-full border border-[#111827]/10 flex items-center justify-center text-[#111827]/50 hover:text-[#25D366] hover:border-[#25D366]/50 transition-all">
                  <iconify-icon icon="ic:baseline-whatsapp" class="text-lg"></iconify-icon>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?= $share_title ?>&url=<?= $share_url ?>" target="_blank"
                  class="w-9 h-9 rounded-full border border-[#111827]/10 flex items-center justify-center text-[#111827]/50 hover:text-[#2563eb] hover:border-[#2563eb]/50 transition-all">
                  <iconify-icon icon="simple-icons:x" class="text-lg"></iconify-icon>
                </a>
                <button onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<iconify-icon icon=\'solar:check-circle-linear\' class=\'text-lg\'></iconify-icon>'"
                  class="w-9 h-9 rounded-full border border-[#111827]/10 flex items-center justify-center text-[#111827]/50 hover:text-[#2563eb] hover:border-[#2563eb]/50 transition-all">
                  <iconify-icon icon="solar:copy-linear" class="text-lg"></iconify-icon>
                </button>
              </div>
            </div>
          </div>
        </aside>

        <!-- Article content -->
        <article class="rounded-[2rem] bg-white border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] overflow-hidden">
          <div class="px-6 sm:px-8 lg:px-12 py-8 sm:py-10 lg:py-12">
            <div class="max-w-3xl article-content">
              <?= $article['content'] ?>
            </div>

            <!-- Article footer: tags + back link -->
            <div class="max-w-3xl mt-12 pt-8 border-t border-[#111827]/8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <?php if ($article['category']): ?>
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs uppercase tracking-widest text-[#6b7280]">Kategori:</span>
                <a href="<?= BASE_URL ?>/artikel?kategori=<?= urlencode($article['category']) ?>"
                  class="rounded-full border border-[#2563eb]/20 bg-[#2563eb]/5 text-[#2563eb] text-xs uppercase tracking-widest px-3 py-1 hover:bg-[#2563eb] hover:text-white transition-all">
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
    <section class="bg-[#eef1f5] pt-16 sm:pt-20 lg:pt-24 px-4 sm:px-6 pb-16 sm:pb-20 lg:pb-24 rounded-[2rem] sm:rounded-[2.5rem] mt-6 sm:mt-8">
      <div class="max-w-[88rem] mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10 sm:mb-12">
          <div>
            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Lanjutkan Membaca</p>
            <h2 class="text-3xl sm:text-4xl md:text-5xl tracking-tight uppercase text-[#111827] leading-[0.95] font-semibold" style="font-family:'Playfair Display',serif;">Artikel Terkait</h2>
          </div>
          <p class="text-base md:text-lg text-[#4b5563] max-w-2xl">
            Bacaan tambahan seputar produksi cetak, persiapan file, dan alur kerja digital printing.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
          <article class="bg-[#111827] rounded-[1.5rem] overflow-hidden p-6 sm:p-8 flex flex-col justify-between">
            <div>
              <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-4">Butuh panduan cetak?</p>
              <h3 class="text-3xl tracking-tight uppercase text-white mb-4 font-semibold" style="font-family:'Playfair Display',serif;">Hubungi tim produksi kami</h3>
              <p class="text-base text-white/60 leading-relaxed">Dapatkan bantuan memilih bahan, finishing, dan pengaturan output untuk cetakan Anda berikutnya.</p>
            </div>
            <a href="<?= BASE_URL ?>/kontak" class="mt-8 inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#60a5fa] hover:text-white transition-colors">
              Hubungi kami
              <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
            </a>
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