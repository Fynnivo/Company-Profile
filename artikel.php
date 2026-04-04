<?php
$per_page = 6;
$current_page = isset($_GET['halaman']) ? max(1, (int)$_GET['halaman']) : 1;
$offset = ($current_page - 1) * $per_page;
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

require_once 'includes/header.php';

// Build query with optional category filter
$where = "WHERE status = 'published'";
$params = [];
if ($kategori) {
    $where .= " AND category = ?";
    $params[] = $kategori;
}

// Total count for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM articles $where");
$count_stmt->execute($params);
$total = (int)$count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// Fetch articles
$stmt = $pdo->prepare("SELECT id, title, slug, excerpt, thumbnail, category, created_at FROM articles $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Featured (first article)
$featured = !empty($articles) ? array_shift($articles) : null;

// Get all distinct categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM articles WHERE status = 'published' AND category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Stats
$total_articles = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();
$total_cats     = (int)$pdo->query("SELECT COUNT(DISTINCT category) FROM articles WHERE status='published'")->fetchColumn();

$page_title       = 'Artikel';
$meta_description = 'Baca artikel, panduan, dan tips seputar digital printing dari tim kami.';
?>

<div class="max-w-[96rem] mx-auto w-full px-3 sm:px-4 md:px-6 lg:px-8 xl:px-10">

  <main class="pt-24 sm:pt-28 lg:pt-32 pb-8 sm:pb-10">

    <!-- ===================== HERO ===================== -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0" style="background-image:linear-gradient(to right, rgba(31,41,55,0.04) 1px, transparent 1px),linear-gradient(to bottom, rgba(31,41,55,0.04) 1px, transparent 1px);background-size:3rem 3rem"></div>
      <div class="relative max-w-[88rem] mx-auto pt-6 sm:pt-8 lg:pt-10 pb-8 sm:pb-10">

        <div class="grid grid-cols-1 xl:grid-cols-[0.9fr_1.1fr] gap-6 sm:gap-8 lg:gap-10 items-end">
          <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 border border-[#2563eb]/20 px-4 py-2 mb-5">
              <span class="relative flex h-2 w-2">
              <span class="absolute inline-flex h-full w-full rounded-full bg-[#2563eb] opacity-60 animate-ping"></span>
              <span class="relative inline-flex h-2 w-2 rounded-full bg-[#2563eb]"></span>
            </span>
              <span class="text-xs uppercase tracking-widest text-[#2563eb]">Jurnal</span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]">Wawasan Terbaru</span>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight leading-[0.92] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
              Artikel tentang Cetak,
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold">Produksi, dan Branding</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              Panduan praktis, catatan produksi, dan wawasan editorial untuk tim yang ingin menghasilkan materi cetak berkualitas.
            </p>
          </div>

          <!-- Stats card -->
          <div>
            <div class="rounded-[1.75rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm shadow-[0_1.5rem_4rem_rgba(15,23,42,0.08)] p-5 sm:p-6">
              <div class="grid grid-cols-3 gap-4">
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Topik</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold"><?= $total_cats ?></p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Artikel</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold"><?= $total_articles ?></p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Update</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Rutin</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Category filter bar -->
        <div class="mt-8 sm:mt-10 rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-4 sm:p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
          <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
              <a href="<?= BASE_URL ?>/artikel"
                class="rounded-full text-xs uppercase tracking-widest px-4 py-2 <?= !$kategori ? 'bg-[#111827] text-white' : 'border border-[#111827]/10 bg-white text-[#111827] hover:bg-[#111827]/5' ?> transition-all">
                Semua
              </a>
              <?php foreach ($categories as $cat): ?>
              <a href="<?= BASE_URL ?>/artikel?kategori=<?= urlencode($cat) ?>"
                class="rounded-full text-xs uppercase tracking-widest px-4 py-2 <?= $kategori === $cat ? 'bg-[#111827] text-white' : 'border border-[#111827]/10 bg-white text-[#111827] hover:bg-[#111827]/5' ?> transition-all">
                <?= e($cat) ?>
              </a>
              <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-2 text-sm text-[#6b7280]">
              <iconify-icon icon="solar:sort-linear" class="text-base"></iconify-icon>
              Terbaru
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== ARTICLES GRID ===================== -->
    <section class="mt-2">
      <div class="max-w-[88rem] mx-auto grid grid-cols-1 xl:grid-cols-[0.7fr_1.3fr] gap-6 sm:gap-8 lg:gap-10 items-start">

        <!-- Sidebar -->
        <aside class="xl:sticky xl:top-28">
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
            <div class="mb-6">
              <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Jelajahi</p>
              <div class="space-y-3">
                <a href="<?= BASE_URL ?>/artikel" class="block text-sm <?= !$kategori ? 'text-[#2563eb]' : 'text-[#111827]' ?> hover:text-[#2563eb] transition-colors">Semua artikel</a>
                <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>/artikel?kategori=<?= urlencode($cat) ?>"
                  class="block text-sm <?= $kategori === $cat ? 'text-[#2563eb]' : 'text-[#111827]/70' ?> hover:text-[#2563eb] transition-colors capitalize">
                  <?= e($cat) ?>
                </a>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="pt-6 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Topik Unggulan</p>
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-5">
                <p class="text-xs uppercase tracking-widest text-[#2563eb] mb-3">Tips Cetak</p>
                <p class="text-sm text-[#111827] leading-7">Panduan memilih bahan, finishing, dan pengaturan output untuk kebutuhan cetak Anda.</p>
              </div>
            </div>

            <!-- Contact CTA in sidebar -->
            <div class="pt-6 mt-6 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Butuh Bantuan?</p>
              <a href="<?= BASE_URL ?>/kontak" class="flex items-center gap-3 rounded-[1.25rem] bg-[#111827] p-4 hover:bg-[#2563eb] transition-all group">
                <iconify-icon icon="solar:chat-round-dots-linear" class="text-2xl text-[#60a5fa]"></iconify-icon>
                <div>
                  <p class="text-xs text-white/60 uppercase tracking-widest">Konsultasi Gratis</p>
                  <p class="text-sm text-white font-semibold">Hubungi Tim Kami</p>
                </div>
              </a>
            </div>
          </div>
        </aside>

        <!-- Articles list -->
        <div class="space-y-6">

          <?php if ($featured): ?>
          <!-- Featured / first article (big card) -->
          <article class="rounded-[2rem] bg-white border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr]">
              <div class="aspect-[16/11] lg:aspect-auto overflow-hidden bg-[#eef1f5]">
                <?php if ($featured['thumbnail']): ?>
                <img src="<?= BASE_URL ?>/<?= e($featured['thumbnail']) ?>" alt="<?= e($featured['title']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                <div class="w-full h-full min-h-[16rem] flex items-center justify-center">
                  <iconify-icon icon="solar:document-text-linear" class="text-6xl text-[#d1d5db]"></iconify-icon>
                </div>
                <?php endif; ?>
              </div>
              <div class="px-6 sm:px-8 py-6 sm:py-8 flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($featured['category'] ?? 'Artikel') ?></span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= date('d M Y', strtotime($featured['created_at'])) ?></span>
                </div>
                <h2 class="text-3xl sm:text-4xl tracking-tight uppercase text-[#111827] mb-4 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($featured['title']) ?></h2>
                <p class="text-base sm:text-lg text-[#4b5563] leading-8 mb-6"><?= e(truncate($featured['excerpt'] ?? '', 160)) ?></p>
                <div class="flex items-center justify-between gap-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280]"><?= date('d M Y', strtotime($featured['created_at'])) ?></p>
                  <a href="<?= BASE_URL ?>/artikel/<?= e($featured['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                    Baca artikel
                    <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
                  </a>
                </div>
              </div>
            </div>
          </article>
          <?php endif; ?>

          <!-- Article grid (rest) -->
          <?php if (!empty($articles)): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($articles as $article): ?>
            <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group">
              <div class="aspect-[4/3] overflow-hidden bg-[#eef1f5]">
                <?php if ($article['thumbnail']): ?>
                <img src="<?= BASE_URL ?>/<?= e($article['thumbnail']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                  <iconify-icon icon="solar:document-text-linear" class="text-5xl text-[#d1d5db]"></iconify-icon>
                </div>
                <?php endif; ?>
              </div>
              <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($article['category'] ?? 'Artikel') ?></span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= date('d M Y', strtotime($article['created_at'])) ?></span>
                </div>
                <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($article['title']) ?></h3>
                <p class="text-base text-[#4b5563] leading-relaxed mb-6"><?= e(truncate($article['excerpt'] ?? '', 110)) ?></p>
                <a href="<?= BASE_URL ?>/artikel/<?= e($article['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                  Baca artikel
                  <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
                </a>
              </div>
            </article>
            <?php endforeach; ?>

            <!-- CTA card -->
            <article class="bg-[#111827] rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] p-6 sm:p-8 flex flex-col justify-between">
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
          <?php elseif (!$featured): ?>
          <div class="rounded-[2rem] bg-white border border-[#111827]/6 p-16 text-center">
            <iconify-icon icon="solar:document-text-linear" class="text-5xl text-[#d1d5db] mb-4 block"></iconify-icon>
            <p class="text-[#6b7280] uppercase tracking-widest text-sm">Belum ada artikel<?= $kategori ? ' dalam kategori ini' : '' ?>.</p>
          </div>
          <?php endif; ?>

          <!-- Pagination -->
          <?php if ($total_pages > 1): ?>
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-4 sm:p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
              <p class="text-xs uppercase tracking-[0.2em] text-[#6b7280]">
                Menampilkan <?= min($offset + $per_page, $total) ?> dari <?= $total ?> artikel
              </p>
              <div class="flex items-center gap-2">
                <?php if ($current_page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['halaman' => $current_page - 1])) ?>"
                  class="rounded-full border border-[#111827]/10 bg-white text-[#111827] text-xs uppercase tracking-widest px-4 py-2 hover:bg-[#111827] hover:text-white transition-all">
                  Sebelumnya
                </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['halaman' => $i])) ?>"
                  class="rounded-full text-xs uppercase tracking-widest px-4 py-2 <?= $i === $current_page ? 'bg-[#111827] text-white' : 'border border-[#111827]/10 bg-white text-[#111827] hover:bg-[#111827]/5' ?> transition-all">
                  <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['halaman' => $current_page + 1])) ?>"
                  class="rounded-full border border-[#111827]/10 bg-white text-[#111827] text-xs uppercase tracking-widest px-4 py-2 hover:bg-[#111827] hover:text-white transition-all">
                  Berikutnya
                </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </section>

  </main>
</div>

<?php require_once 'includes/footer.php'; ?>