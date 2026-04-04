<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Get slug — supports clean URL /layanan/{slug} and ?slug=
$slug = '';
if (isset($_GET['slug'])) {
    $slug = trim($_GET['slug']);
} else {
    $path  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    $slug  = end($parts);
}

if (!$slug) {
    header('Location: ' . BASE_URL . '/layanan');
    exit;
}

// Fetch service
$stmt = $pdo->prepare("SELECT * FROM services WHERE slug = ? AND is_active = 1 LIMIT 1");
$stmt->execute([$slug]);
$service = $stmt->fetch();

if (!$service) {
    http_response_code(404);
    include '404.php';
    exit;
}

// Other services (exclude current)
$others = $pdo->prepare("
    SELECT id, name, slug, description, icon, image
    FROM services
    WHERE is_active = 1 AND id != ?
    ORDER BY sort_order
    LIMIT 4
");
$others->execute([$service['id']]);
$other_services = $others->fetchAll();

// Related articles (by keyword match or just latest)
$related_articles = $pdo->query("
    SELECT id, title, slug, excerpt, thumbnail, category, created_at
    FROM articles
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 2
")->fetchAll();

$page_title       = $service['name'];
$meta_description = truncate(strip_tags($service['description'] ?? ''), 155);

require_once 'includes/header.php';

$bg_colors = ['bg-[#eef2ff]','bg-[#ecfeff]','bg-[#f5f3ff]','bg-[#eff6ff]'];
?>

<div class="max-w-[96rem] mx-auto w-full px-3 sm:px-4 md:px-6 lg:px-8 xl:px-10">
  <main class="pt-24 sm:pt-28 lg:pt-32 pb-8 sm:pb-10">

    <!-- ===================== SERVICE HERO ===================== -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0" style="background-image:linear-gradient(to right, rgba(31,41,55,0.04) 1px, transparent 1px),linear-gradient(to bottom, rgba(31,41,55,0.04) 1px, transparent 1px);background-size:3rem 3rem"></div>
      <div class="relative max-w-[88rem] mx-auto pt-6 sm:pt-8 lg:pt-10 pb-8 sm:pb-10">

        <!-- Back link -->
        <div class="mb-6 sm:mb-8">
          <a href="<?= BASE_URL ?>/layanan" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-[#6b7280] hover:text-[#2563eb] transition-colors">
            <iconify-icon icon="solar:arrow-left-linear" class="text-base"></iconify-icon>
            Kembali ke Layanan
          </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[0.8fr_1.2fr] gap-6 sm:gap-8 lg:gap-10 items-end">

          <!-- Left: Title & meta -->
          <div class="order-2 xl:order-1">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 border border-[#2563eb]/20 px-4 py-2 mb-5">
              <span class="text-xs uppercase tracking-widest text-[#2563eb]">Layanan</span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]">Cetak Digital</span>
            </div>
            <?php
            $words = explode(' ', $service['name']);
            $half  = ceil(count($words) / 2);
            $line1 = implode(' ', array_slice($words, 0, $half));
            $line2 = implode(' ', array_slice($words, $half));
            ?>
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight leading-[0.92] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
              <?= e($line1) ?>
              <?php if ($line2): ?>
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold"><?= e($line2) ?></span>
              <?php endif; ?>
            </h1>

            <?php if ($service['description']): ?>
            <p class="mt-6 text-lg sm:text-xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              <?= e($service['description']) ?>
            </p>
            <?php endif; ?>

            <!-- CTA buttons -->
            <div class="mt-8 flex flex-wrap gap-4">
              <a href="<?= BASE_URL ?>/kontak" class="rounded-full bg-[#111827] text-white px-8 py-4 text-sm uppercase tracking-widest hover:bg-[#2563eb] transition-all duration-300 font-semibold">
                Minta Penawaran
              </a>
              <a href="https://wa.me/<?= e(get_setting('whatsapp', $pdo)) ?>" target="_blank"
                class="rounded-full border border-[#111827]/15 text-[#111827] px-8 py-4 text-sm uppercase tracking-widest hover:border-[#2563eb]/40 hover:text-[#2563eb] transition-all duration-300 flex items-center gap-2">
                <iconify-icon icon="ic:baseline-whatsapp" class="text-lg text-[#25D366]"></iconify-icon>
                WhatsApp
              </a>
            </div>
          </div>

          <!-- Right: Image or icon -->
          <div class="order-1 xl:order-2">
            <div class="rounded-[1.75rem] overflow-hidden border border-[#111827]/8 shadow-[0_1.5rem_4rem_rgba(15,23,42,0.14)]">
              <?php if ($service['image']): ?>
              <div class="aspect-[16/10] overflow-hidden">
                <img src="<?= BASE_URL ?>/<?= e($service['image']) ?>" alt="<?= e($service['name']) ?>" class="w-full h-full object-cover">
              </div>
              <?php else: ?>
              <div class="aspect-[16/10] bg-gradient-to-br from-[#1e3a5f] to-[#111827] flex items-center justify-center">
                <div class="text-center">
                  <iconify-icon icon="<?= e($service['icon'] ?? 'solar:print-linear') ?>" class="text-[8rem] text-[#2563eb]/60"></iconify-icon>
                  <p class="text-white/30 text-xs uppercase tracking-widest mt-4"><?= e($service['name']) ?></p>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ===================== SERVICE DETAIL BODY ===================== -->
    <section class="mt-2">
      <div class="max-w-[88rem] mx-auto grid grid-cols-1 xl:grid-cols-[0.7fr_1.3fr] gap-6 sm:gap-8 lg:gap-10 items-start">

        <!-- Sidebar -->
        <aside class="xl:sticky xl:top-28 space-y-4">
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">

            <!-- Other services nav -->
            <div class="mb-6">
              <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Layanan Lainnya</p>
              <div class="space-y-3">
                <?php foreach ($other_services as $os): ?>
                <a href="<?= BASE_URL ?>/layanan/<?= e($os['slug']) ?>"
                  class="flex items-center gap-3 text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors group">
                  <iconify-icon icon="<?= e($os['icon'] ?? 'solar:print-linear') ?>" class="text-base text-[#6b7280] group-hover:text-[#2563eb] transition-colors"></iconify-icon>
                  <?= e($os['name']) ?>
                </a>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="pt-6 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Info Layanan</p>
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-full border border-[#111827]/8 bg-[#f4f4f2] flex items-center justify-center text-[#2563eb]">
                    <iconify-icon icon="solar:verified-check-linear" class="text-lg"></iconify-icon>
                  </div>
                  <div>
                    <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Status</p>
                    <p class="text-sm text-[#111827]">Tersedia & Aktif</p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-full border border-[#111827]/8 bg-[#f4f4f2] flex items-center justify-center text-[#2563eb]">
                    <iconify-icon icon="solar:clock-circle-linear" class="text-lg"></iconify-icon>
                  </div>
                  <div>
                    <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Pengerjaan</p>
                    <p class="text-sm text-[#111827]">Cepat & Terukur</p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 rounded-full border border-[#111827]/8 bg-[#f4f4f2] flex items-center justify-center text-[#2563eb]">
                    <iconify-icon icon="solar:chat-round-dots-linear" class="text-lg"></iconify-icon>
                  </div>
                  <div>
                    <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Konsultasi</p>
                    <p class="text-sm text-[#111827]">Gratis & Langsung</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- WhatsApp CTA -->
            <div class="pt-6 mt-2 border-t border-[#111827]/8">
              <a href="https://wa.me/<?= e(get_setting('whatsapp', $pdo)) ?>?text=Halo, saya ingin bertanya tentang layanan <?= urlencode($service['name']) ?>"
                target="_blank"
                class="flex items-center gap-3 rounded-[1.25rem] bg-[#25D366] p-4 hover:bg-[#128c7e] transition-all">
                <iconify-icon icon="ic:baseline-whatsapp" class="text-2xl text-white"></iconify-icon>
                <div>
                  <p class="text-xs text-white/80 uppercase tracking-widest">Chat Langsung</p>
                  <p class="text-sm text-white font-semibold">Tanya via WhatsApp</p>
                </div>
              </a>
            </div>
          </div>
        </aside>

        <!-- Main content -->
        <div class="space-y-6">

          <!-- Service description card -->
          <div class="rounded-[2rem] bg-white border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] overflow-hidden">
            <div class="px-6 sm:px-8 lg:px-12 py-8 sm:py-10 lg:py-12">
              <div class="max-w-3xl">

                <!-- Long description (if you add it to DB later, render here) -->
                <div class="article-content">
                  <p class="text-base sm:text-lg text-[#4b5563] leading-8">
                    <?= e($service['description']) ?>
                  </p>

                  <div class="pt-10">
                    <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Mengapa Memilih Layanan Ini?</p>
                    <h2 class="text-3xl sm:text-4xl tracking-tight uppercase text-[#111827] mb-5 font-semibold" style="font-family:'Playfair Display',serif;">Kualitas yang Konsisten</h2>
                    <p class="text-base sm:text-lg text-[#4b5563] leading-8 mb-5">
                      Kami menggunakan peralatan cetak digital terkini untuk memastikan setiap hasil cetakan memenuhi standar kualitas tertinggi. Dari proofing hingga pengiriman akhir, setiap tahap dikontrol dengan ketat.
                    </p>
                  </div>

                  <!-- How it works steps -->
                  <div class="pt-10">
                    <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Proses Kami</p>
                    <h2 class="text-3xl sm:text-4xl tracking-tight uppercase text-[#111827] mb-6 font-semibold" style="font-family:'Playfair Display',serif;">Langkah Mudah</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <?php
                      $steps = [
                        ['num'=>'01','title'=>'Konsultasi','desc'=>'Diskusikan kebutuhan cetak Anda dengan tim kami secara gratis.'],
                        ['num'=>'02','title'=>'Produksi','desc'=>'File Anda diproses dan dicetak dengan mesin berkualitas tinggi.'],
                        ['num'=>'03','title'=>'Pengiriman','desc'=>'Hasil cetakan dikemas dengan aman dan dikirim ke lokasi Anda.'],
                      ];
                      foreach ($steps as $step):
                      ?>
                      <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3"><?= $step['num'] ?></p>
                        <p class="text-base font-semibold text-[#111827] uppercase mb-2"><?= $step['title'] ?></p>
                        <p class="text-sm text-[#6b7280] leading-6"><?= $step['desc'] ?></p>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <!-- Final CTA box -->
                  <div class="pt-10">
                    <div class="rounded-[1.5rem] bg-[#111827] p-6 sm:p-8">
                      <p class="text-xs uppercase tracking-[0.25em] !text-[#60a5fa] mb-4">Siap Memulai?</p>
                      <p class="text-lg sm:text-xl !text-white leading-relaxed font-semibold">
                        Hubungi kami sekarang dan dapatkan penawaran terbaik untuk kebutuhan cetak <?= e($service['name']) ?> Anda.
                      </p>
                      <div class="flex flex-wrap gap-4 mt-6">
                        <a href="<?= BASE_URL ?>/kontak" class="rounded-full bg-[#2563eb] !text-white px-6 py-3 text-sm uppercase tracking-widest hover:bg-[#1c52c9] hover:text-[#111827] transition-all">
                          Hubungi Kami
                        </a>
                        <a href="https://wa.me/<?= e(get_setting('whatsapp', $pdo)) ?>" target="_blank"
                          class="rounded-full border border-white/20 !text-white px-6 py-3 text-sm uppercase tracking-widest hover:border-[#25D366] hover:text-[#25D366] transition-all flex items-center gap-2">
                          <iconify-icon icon="ic:baseline-whatsapp" class="text-lg"></iconify-icon>
                          WhatsApp
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ===================== OTHER SERVICES ===================== -->
    <?php if (!empty($other_services)): ?>
    <section class="bg-[#eef1f5] pt-16 sm:pt-20 px-4 sm:px-6 pb-16 sm:pb-20 rounded-[2rem] sm:rounded-[2.5rem] mt-6 sm:mt-8">
      <div class="max-w-[88rem] mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-10">
          <div>
            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Eksplorasi Lebih</p>
            <h2 class="text-3xl sm:text-4xl md:text-5xl tracking-tight uppercase text-[#111827] leading-[0.95] font-semibold" style="font-family:'Playfair Display',serif;">Layanan Lainnya</h2>
          </div>
          <a href="<?= BASE_URL ?>/layanan" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
            Lihat Semua Layanan
            <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <?php foreach (array_slice($other_services, 0, 3) as $i => $os): ?>
          <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group">
            <div class="aspect-[4/3] overflow-hidden <?= $bg_colors[$i % count($bg_colors)] ?> flex items-center justify-center">
              <?php if ($os['image']): ?>
              <img src="<?= BASE_URL ?>/<?= e($os['image']) ?>" alt="<?= e($os['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
              <?php else: ?>
              <iconify-icon icon="<?= e($os['icon'] ?? 'solar:print-linear') ?>" class="text-6xl text-[#2563eb] group-hover:scale-110 transition-transform duration-500"></iconify-icon>
              <?php endif; ?>
            </div>
            <div class="p-6">
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase tracking-widest text-[#2563eb]">Layanan</span>
              </div>
              <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($os['name']) ?></h3>
              <p class="text-base text-[#4b5563] leading-relaxed mb-6"><?= e(truncate($os['description'] ?? '', 110)) ?></p>
              <a href="<?= BASE_URL ?>/layanan/<?= e($os['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                Selengkapnya
                <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
              </a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Related Articles -->
    <?php if (!empty($related_articles)): ?>
    <section class="mt-6 sm:mt-8 max-w-[88rem] mx-auto">
      <div class="flex items-end justify-between mb-8">
        <div>
          <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Dari Blog Kami</p>
          <h2 class="text-3xl tracking-tight uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">Artikel Terkait</h2>
        </div>
        <a href="<?= BASE_URL ?>/artikel" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#6b7280] hover:text-[#2563eb] transition-colors">
          Semua Artikel
          <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
        </a>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($related_articles as $art): ?>
        <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group flex gap-0">
          <div class="w-32 sm:w-44 flex-shrink-0 overflow-hidden bg-[#f4f4f2]">
            <?php if ($art['thumbnail']): ?>
            <img src="<?= BASE_URL ?>/<?= e($art['thumbnail']) ?>" alt="<?= e($art['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
              <iconify-icon icon="solar:document-text-linear" class="text-3xl text-[#d1d5db]"></iconify-icon>
            </div>
            <?php endif; ?>
          </div>
          <div class="p-5 flex flex-col justify-between">
            <div>
              <span class="text-xs uppercase tracking-widest text-[#2563eb] mb-2 block"><?= e($art['category'] ?? 'Artikel') ?></span>
              <h3 class="text-lg tracking-tight uppercase text-[#111827] mb-2 font-semibold leading-tight" style="font-family:'Playfair Display',serif;"><?= e($art['title']) ?></h3>
              <p class="text-sm text-[#6b7280] leading-relaxed"><?= e(truncate($art['excerpt'] ?? '', 80)) ?></p>
            </div>
            <a href="<?= BASE_URL ?>/artikel/<?= e($art['slug']) ?>" class="inline-flex items-center gap-1 text-xs uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors mt-3">
              Baca
              <iconify-icon icon="solar:arrow-right-linear" class="text-sm"></iconify-icon>
            </a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  </main>
</div>

<?php require_once 'includes/footer.php'; ?>