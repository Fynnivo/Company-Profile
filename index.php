<?php
$page_title       = null; // Will use default: SiteName | Tagline
$meta_description = 'Jasa digital printing profesional — banner, brosur, kartu nama, stiker, dan lebih banyak lagi.';
require_once 'includes/header.php';

// Fetch latest 3 published articles
$articles = $pdo->query("
  SELECT id, title, slug, excerpt, thumbnail, category, created_at
  FROM articles
  WHERE status = 'published'
  ORDER BY created_at DESC
  LIMIT 3
")->fetchAll();

// Fetch active services
$services = $pdo->query("
  SELECT id, name, slug, description, icon
  FROM services
  WHERE is_active = 1
  ORDER BY sort_order
  LIMIT 4
")->fetchAll();
?>

<!-- ===================== HERO ===================== -->
<div class="mx-5">
  <section class="relative min-h-screen flex items-center pt-28 pb-20 overflow-hidden">
    <!-- Grid background -->
    <div class="absolute inset-0"
      style="background-image:linear-gradient(to right, rgba(31,41,55,0.04) 1px, transparent 1px),linear-gradient(to bottom, rgba(31,41,55,0.04) 1px, transparent 1px);background-size:3rem 3rem;">
    </div>

    <div class="z-10 w-full max-w-[88rem] mx-auto px-6 relative">
      <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-10 lg:gap-16 items-start">

        <!-- LEFT: Text content -->
        <div class="pt-8 lg:pt-16">
          <!-- Badge -->
          <div class="inline-flex gap-2 uppercase text-xs text-[#374151] tracking-widest bg-white/70 border-[#2563eb]/20 border rounded-full mb-7 px-4 py-2 items-center">
            <span class="relative flex h-2 w-2">
              <span class="absolute inline-flex h-full w-full rounded-full bg-[#2563eb] opacity-60 animate-ping"></span>
              <span class="relative inline-flex h-2 w-2 rounded-full bg-[#2563eb]"></span>
            </span>
            Digital Printing Profesional
          </div>

          <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-start">
            <h1 class="text-5xl md:text-7xl lg:text-8xl tracking-tight leading-[0.9] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
              Digital Print
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold">Solusi Cetak Terpercaya</span>
            </h1>
            <!-- Stat cards -->
            <div class="hidden lg:flex flex-col gap-3 pt-4">
              <div class="rounded-[1.25rem] bg-white/80 border border-[#111827]/8 p-4 w-44 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
                <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Output</p>
                <p class="text-3xl tracking-tight text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">24h</p>
              </div>
              <div class="rounded-[1.25rem] bg-[#111827] border border-[#111827]/10 p-4 w-44 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.08)] rotate-[-4deg]">
                <p class="text-xs uppercase tracking-widest text-white/45 mb-2">Cetak</p>
                <p class="text-3xl tracking-tight text-white font-semibold" style="font-family:'Playfair Display',serif;">Kecil + Besar</p>
              </div>
            </div>
          </div>

          <div class="mt-10 grid md:grid-cols-[1.05fr_0.95fr] gap-8 items-end">
            <p class="text-xl md:text-2xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              Solusi cetak digital modern untuk kebutuhan branding, promosi, dan identitas bisnis Anda.
            </p>

            <!-- Focus Area cards -->
            <div class="rounded-[1.5rem] bg-white/70 border border-[#111827]/8 p-5 backdrop-blur-sm">
              <div class="flex items-center justify-between mb-5">
                <span class="text-xs uppercase tracking-[0.25em] text-[#6b7280]">Kategori</span>
                <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= date('Y') ?></span>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <?php
                $focus = [
                  ['label' => 'Banner', 'desc' => 'Cetak besar'],
                  ['label' => 'Brosur', 'desc' => 'Lipat & cetak'],
                  ['label' => 'Stiker', 'desc' => 'Custom ukuran'],
                  ['label' => 'Kartu Nama', 'desc' => 'Eksklusif'],
                ];
                foreach ($focus as $i => $f):
                  $isBlue = ($i === 3);
                ?>
                <div class="rounded-xl <?= $isBlue ? 'bg-[#2563eb] text-white' : 'bg-[#f4f4f2] border border-[#111827]/6' ?> p-4">
                  <p class="text-xs uppercase tracking-widest <?= $isBlue ? 'text-white/70' : 'text-[#6b7280]' ?> mb-2"><?= e($f['label']) ?></p>
                  <p class="text-sm <?= $isBlue ? '' : 'text-[#111827]' ?>"><?= e($f['desc']) ?></p>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <!-- CTA Buttons -->
          <div class="mt-10 flex flex-col sm:flex-row gap-4">
            <a href="<?= BASE_URL ?>/layanan" class="rounded-full bg-[#111827] text-white px-8 py-4 text-sm uppercase tracking-widest hover:bg-[#2563eb] transition-all duration-300 text-center font-semibold">Lihat Layanan</a>
            <a href="<?= BASE_URL ?>/artikel" class="rounded-full border border-[#111827]/15 text-[#111827] px-8 py-4 text-sm uppercase tracking-widest hover:border-[#2563eb]/40 hover:text-[#2563eb] transition-all duration-300 text-center flex items-center justify-center gap-2">
              <iconify-icon icon="solar:document-text-linear" class="text-lg"></iconify-icon>
              Baca Artikel
            </a>
          </div>
        </div>

        <!-- RIGHT: Hero card -->
        <div class="relative lg:pt-10">
          <div class="absolute -inset-3 rounded-[2rem] blur-2xl bg-gradient-to-b from-[#2563eb]/20 via-[#2563eb]/8 to-transparent"></div>
          <div class="relative grid gap-4">
            <div class="rounded-[1.75rem] overflow-hidden border border-[#111827]/10 bg-[#0f172a] shadow-[0_1.5rem_4rem_rgba(15,23,42,0.22)]">
              <div class="grid sm:grid-cols-[0.9fr_1.1fr] min-h-[32rem]">
                <div class="flex flex-col bg-[#111827] bg-cover bg-center p-8 relative justify-between"
                  style="background-image:url('https://images.unsplash.com/photo-1586717799252-bd134ad00e26?w=800&q=80');">
                  <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 px-3 py-1 text-xs uppercase tracking-widest text-white/75 mb-5 backdrop-blur-md">
                      Koleksi Unggulan
                    </div>
                    <h2 class="leading-tight text-3xl font-semibold text-white tracking-tight" style="font-family:'Playfair Display',serif;">
                      Sistem Cetak untuk Banner, Signage &amp; Branding
                    </h2>
                  </div>
                  <div class="flex gap-3">
                    <div class="bg-white/5 border-white/10 border rounded-xl p-4">
                      <p class="uppercase text-xs text-white/45 tracking-widest mb-2">Kategori</p>
                      <p class="text-base text-white">Digital Print</p>
                    </div>
                    <div class="bg-white/5 border-white/10 border rounded-xl px-5 py-3">
                      <p class="uppercase text-xs text-white/45 tracking-widest mb-2">Untuk</p>
                      <p class="text-sm text-white">Bisnis &amp; Brand</p>
                    </div>
                  </div>
                </div>
                <div class="relative min-h-[20rem]">
                  <img src="https://images.unsplash.com/photo-1586717799252-bd134ad00e26?auto=format&fit=crop&q=80" alt="Produksi digital print" class="opacity-85 mix-blend-luminosity w-full h-full object-cover">
                  <div class="bg-gradient-to-t from-[#0f172a]/65 via-[#0f172a]/10 to-transparent absolute inset-0"></div>
                </div>
              </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-white p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
                <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-3">Proofing</p>
                <p class="text-xl tracking-tight text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">Review File</p>
              </div>
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-white p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)] sm:translate-y-6">
                <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-3">Produksi</p>
                <p class="text-xl tracking-tight text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">Satuan &amp; Massal</p>
              </div>
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#2563eb] p-5 shadow-[0_0.5rem_2rem_rgba(37,99,235,0.16)]">
                <p class="text-xs uppercase tracking-widest text-white/70 mb-3">Pengiriman</p>
                <p class="text-xl tracking-tight text-white font-semibold" style="font-family:'Playfair Display',serif;">Prioritas</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ===================== ARTICLES ===================== -->
  <section class="bg-[#eef1f5] pt-28 px-6 pb-28" id="artikel">
    <div class="max-w-[88rem] mx-auto">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
        <div>
          <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Jurnal Editorial</p>
          <h2 class="text-4xl md:text-6xl tracking-tight uppercase text-[#111827] leading-[0.95] font-semibold" style="font-family:'Playfair Display',serif;">Artikel Terbaru Digital Print</h2>
        </div>
        <p class="text-base md:text-lg text-[#4b5563] max-w-2xl">
          Panduan, tips, dan wawasan seputar dunia digital printing untuk bisnis Anda.
        </p>
      </div>

      <?php if ($articles): ?>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php foreach ($articles as $article): ?>
        <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group">
          <div class="aspect-[4/3] overflow-hidden bg-[#f4f4f2]">
            <?php if ($article['thumbnail']): ?>
            <img src="<?= BASE_URL ?>/<?= e($article['thumbnail']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-[#eef1f5]">
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
            <p class="text-base text-[#4b5563] leading-relaxed mb-6"><?= e(truncate($article['excerpt'] ?? '', 120)) ?></p>
            <a href="<?= BASE_URL ?>/artikel/<?= e($article['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
              Baca artikel
              <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
            </a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="text-center mt-12">
        <a href="<?= BASE_URL ?>/artikel" class="inline-flex items-center gap-2 rounded-full border border-[#111827]/15 text-[#111827] px-8 py-4 text-sm uppercase tracking-widest hover:border-[#2563eb]/40 hover:text-[#2563eb] transition-all duration-300">
          Lihat Semua Artikel
          <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
        </a>
      </div>
      <?php else: ?>
      <div class="text-center py-20 text-[#6b7280]">
        <iconify-icon icon="solar:document-text-linear" class="text-5xl mb-4 block"></iconify-icon>
        <p class="uppercase tracking-widest text-sm">Belum ada artikel. Segera hadir!</p>
      </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<!-- ===================== SERVICES BAND ===================== -->
<section class="relative bg-[#111827] py-28 rounded-t-[3rem] -mt-8 overflow-hidden">
  <div class="absolute inset-0 opacity-25"
    style="background-image:linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);background-size:4rem 4rem;mask-image:radial-gradient(ellipse at center, black, transparent 72%);">
  </div>
  <div class="max-w-[82rem] mx-auto px-6 relative z-10">
    <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-10 items-center">
      <div>
        <div class="flex items-center gap-4 mb-8">
          <span class="h-px w-10 bg-[#2563eb]"></span>
          <span class="text-xs uppercase tracking-[0.3em] text-[#60a5fa]">Standar Cetak</span>
        </div>
        <h2 class="text-5xl md:text-6xl lg:text-7xl tracking-tight uppercase text-white leading-[0.92] font-semibold" style="font-family:'Playfair Display',serif;">
          Dibangun untuk
          <span class="text-[#60a5fa] font-semibold">Presisi</span>,
          Kualitas &amp; Ketepatan
        </h2>
      </div>
      <div class="grid sm:grid-cols-3 gap-4">
        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-6">
          <p class="text-xs uppercase tracking-widest text-white/45 mb-3">Warna</p>
          <p class="text-lg text-white">Output konsisten di setiap cetakan</p>
        </div>
        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-6 sm:translate-y-6">
          <p class="text-xs uppercase tracking-widest text-white/45 mb-3">Format</p>
          <p class="text-lg text-white">Fleksibel untuk berbagai kebutuhan</p>
        </div>
        <div class="rounded-[1.5rem] border border-[#60a5fa]/30 bg-[#2563eb]/10 p-6">
          <p class="text-xs uppercase tracking-widest text-[#93c5fd] mb-3">Waktu</p>
          <p class="text-lg text-white">Pengerjaan tepat waktu &amp; terjamin</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== SERVICES GRID ===================== -->
<section id="layanan" class="bg-[#111827] px-6 pb-28">
  <div class="max-w-[88rem] mx-auto">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-10">
      <div>
        <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-3">Katalog Layanan</p>
        <h2 class="text-4xl md:text-6xl tracking-tight uppercase text-white leading-[0.95] font-semibold" style="font-family:'Playfair Display',serif;">Layanan Cetak Unggulan</h2>
      </div>
      <p class="text-base md:text-lg text-white/55 max-w-2xl">Pilihan layanan cetak profesional untuk berbagai kebutuhan branding dan promosi bisnis Anda.</p>
    </div>

    <?php if ($services): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
      <?php
      $colSpans = ['lg:col-span-5', 'lg:col-span-3', 'lg:col-span-4'];
      foreach ($services as $i => $service):
        $span = $colSpans[$i % 3] ?? 'lg:col-span-4';
      ?>
      <article class="group rounded-2xl overflow-hidden border border-white/8 bg-[#1f2937] relative <?= $span ?> min-h-[28rem]">
        <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-[#111827]/55 to-transparent"></div>
        <div class="relative p-8 h-full flex flex-col justify-end">
          <div class="w-10 h-10 rounded-full border border-[#60a5fa]/50 bg-[#2563eb]/15 text-[#60a5fa] flex items-center justify-center mb-6">
            <iconify-icon icon="<?= e($service['icon'] ?? 'solar:print-linear') ?>" class="text-xl"></iconify-icon>
          </div>
          <h3 class="text-3xl tracking-tight uppercase text-white mb-3 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($service['name']) ?></h3>
          <p class="text-white/60 text-base leading-relaxed mb-6"><?= e(truncate($service['description'] ?? '', 120)) ?></p>
          <a href="<?= BASE_URL ?>/layanan-detail?slug=<?= e($service['slug']) ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-[#60a5fa] hover:text-white transition-colors">
            Selengkapnya
            <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
          </a>
        </div>
      </article>
      <?php endforeach; ?>

      <!-- Info card -->
      <div class="rounded-2xl border border-white/8 bg-white/5 p-6 lg:col-span-4 flex flex-col justify-between min-h-[24rem]">
        <div>
          <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-4">Konsultasi Gratis</p>
          <h3 class="text-3xl tracking-tight uppercase text-white mb-4 font-semibold" style="font-family:'Playfair Display',serif;">Tidak yakin pilih layanan apa?</h3>
          <p class="text-white/60 text-base leading-relaxed">Tim kami siap membantu Anda memilih solusi cetak yang paling tepat sesuai kebutuhan dan anggaran Anda.</p>
        </div>
        <a href="<?= BASE_URL ?>/kontak" class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#2563eb] text-white px-6 py-3 text-sm uppercase tracking-widest hover:bg-white hover:text-[#111827] transition-all">
          Hubungi Kami
          <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
        </a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="bg-[#111827] border-y border-white/6">
  <div class="lg:py-28 grid lg:grid-cols-[0.9fr_1.1fr] max-w-[88rem] mx-auto pt-20 px-6 pb-20 gap-14 items-center">
    <div>
      <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-widest text-white/70 mb-6">
        Mulai Sekarang
      </div>
      <h2 class="text-5xl md:text-6xl tracking-tight uppercase text-white leading-[0.92] font-semibold" style="font-family:'Playfair Display',serif;">
        Siap untuk
        <span class="block text-[#60a5fa] font-semibold">Proyek Cetak Anda?</span>
      </h2>
      <p class="mt-7 text-xl text-white/60 leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
        Hubungi kami sekarang dan dapatkan konsultasi gratis serta estimasi harga terbaik.
      </p>
      <a href="<?= BASE_URL ?>/kontak" class="inline-flex items-center gap-2 mt-8 text-[#60a5fa] text-sm uppercase tracking-widest hover:text-white transition-colors">
        Mulai Konsultasi
        <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
      </a>
    </div>

    <div class="rounded-[1.75rem] border border-white/10 bg-[#1f2937]/80 backdrop-blur-md p-8 lg:p-10 shadow-[0_1.5rem_4rem_rgba(0,0,0,0.35)]">
      <div class="grid sm:grid-cols-3 gap-4">
        <div class="rounded-[1.5rem] border border-white/8 bg-white/5 p-6">
          <p class="text-xs uppercase tracking-widest text-white/45 mb-3">Warna</p>
          <p class="text-lg text-white">Konsisten di setiap cetakan</p>
        </div>
        <div class="rounded-[1.5rem] border border-white/8 bg-white/5 p-6 sm:translate-y-4">
          <p class="text-xs uppercase tracking-widest text-white/45 mb-3">Format</p>
          <p class="text-lg text-white">Semua ukuran tersedia</p>
        </div>
        <div class="rounded-[1.5rem] border border-[#60a5fa]/30 bg-[#2563eb]/10 p-6">
          <p class="text-xs uppercase tracking-widest text-[#93c5fd] mb-3">Waktu</p>
          <p class="text-lg text-white">Pengiriman tepat waktu</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>