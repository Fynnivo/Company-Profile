<?php
$page_title       = 'Layanan';
$meta_description = 'Layanan digital printing profesional kami — cetak digital, large format, finishing, dan pengiriman untuk kebutuhan bisnis Anda.';
require_once 'includes/header.php';

// Fetch all active services
$services = $pdo->query("
    SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC
")->fetchAll();

// Featured = first service
$featured = !empty($services) ? array_shift($services) : null;

// Stats
$total_services = (int)$pdo->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn();

// Icon bg color map for variety
$bg_colors = [
    'bg-[#eef2ff]', 'bg-[#ecfeff]', 'bg-[#f5f3ff]',
    'bg-[#eff6ff]', 'bg-[#f9fafb]', 'bg-[#fef9c3]'
];
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
            <span class="text-xs uppercase tracking-widest text-[#2563eb]">Layanan</span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]">Solusi cetak profesional</span>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight leading-[0.92] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
              Layanan Cetak
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold">Komersial untuk Tim Modern</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              Dari materi promosi sehari-hari hingga dukungan produksi terkelola, kami membantu brand Anda merencanakan, memproduksi, dan mengirimkan hasil cetak dengan presisi.
            </p>
          </div>

          <!-- Stats card -->
          <div>
            <div class="rounded-[1.75rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm shadow-[0_1.5rem_4rem_rgba(15,23,42,0.08)] p-5 sm:p-6">
              <div class="grid grid-cols-3 gap-4">
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Layanan</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold"><?= str_pad($total_services, 2, '0', STR_PAD_LEFT) ?></p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Pengerjaan</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Cepat</p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Dukungan</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Penuh</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter/info bar -->
        <div class="mt-8 sm:mt-10 rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-4 sm:p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
          <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
              <span class="rounded-full bg-[#111827] text-white text-xs uppercase tracking-widest px-4 py-2">Semua Layanan</span>
              <?php
              $tags = ['Cetak Digital', 'Large Format', 'Finishing', 'Pengiriman'];
              foreach ($tags as $tag):
              ?>
              <span class="rounded-full border border-[#111827]/10 bg-white text-[#111827] text-xs uppercase tracking-widest px-4 py-2"><?= e($tag) ?></span>
              <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-2 text-sm text-[#6b7280]">
              <iconify-icon icon="solar:verified-check-linear" class="text-base"></iconify-icon>
              Siap produksi
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== SERVICES GRID ===================== -->
    <section class="mt-2">
      <div class="max-w-[88rem] mx-auto grid grid-cols-1 xl:grid-cols-[0.7fr_1.3fr] gap-6 sm:gap-8 lg:gap-10 items-start">

        <!-- Sidebar -->
        <aside class="xl:sticky xl:top-28">
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
            <div class="mb-6">
              <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Yang Kami Kerjakan</p>
              <div class="space-y-3">
                <?php
                // Re-fetch for sidebar (since we shifted the array)
                $sidebar_services = $pdo->query("SELECT name, slug FROM services WHERE is_active = 1 ORDER BY sort_order LIMIT 6")->fetchAll();
                foreach ($sidebar_services as $i => $s):
                ?>
                <a href="<?= BASE_URL ?>/layanan/<?= e($s['slug']) ?>"
                  class="block text-sm <?= $i === 0 ? 'text-[#111827]' : 'text-[#111827]/70' ?> hover:text-[#2563eb] transition-colors">
                  <?= e($s['name']) ?>
                </a>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="pt-6 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Cocok Untuk</p>
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-5">
                <p class="text-xs uppercase tracking-widest text-[#2563eb] mb-3">Tim Brand & Bisnis</p>
                <p class="text-sm text-[#111827] leading-7">Ideal untuk bisnis yang membutuhkan output cetak andal, revisi cepat, dan dukungan produksi berkelanjutan.</p>
              </div>
            </div>

            <!-- Consult CTA -->
            <div class="pt-6 mt-6 border-t border-[#111827]/8">
              <a href="<?= BASE_URL ?>/kontak" class="flex items-center gap-3 rounded-[1.25rem] bg-[#111827] p-4 hover:bg-[#2563eb] transition-all group">
                <iconify-icon icon="solar:chat-round-dots-linear" class="text-2xl text-[#60a5fa]"></iconify-icon>
                <div>
                  <p class="text-xs text-white/60 uppercase tracking-widest">Konsultasi Gratis</p>
                  <p class="text-sm text-white font-semibold">Minta Penawaran</p>
                </div>
              </a>
            </div>
          </div>
        </aside>

        <!-- Main content -->
        <div class="space-y-6">

          <?php if ($featured): ?>
          <!-- Featured service (big card) -->
          <article class="rounded-[2rem] bg-white border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr]">
              <!-- Image or icon placeholder -->
              <div class="aspect-[16/11] lg:aspect-auto overflow-hidden <?= $featured['image'] ? '' : 'bg-[#e5e7eb] flex items-center justify-center' ?>">
                <?php if ($featured['image']): ?>
                <img src="<?= BASE_URL ?>/<?= e($featured['image']) ?>" alt="<?= e($featured['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                <iconify-icon icon="<?= e($featured['icon'] ?? 'solar:printer-2-linear') ?>" class="text-7xl text-[#2563eb]"></iconify-icon>
                <?php endif; ?>
              </div>
              <div class="px-6 sm:px-8 py-6 sm:py-8 flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]">Layanan Unggulan</span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]">Cetak Profesional</span>
                </div>
                <h2 class="text-3xl sm:text-4xl tracking-tight uppercase text-[#111827] mb-4 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($featured['name']) ?></h2>
                <p class="text-base sm:text-lg text-[#4b5563] leading-8 mb-6"><?= e($featured['description']) ?></p>
                <div class="flex items-center justify-between gap-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280]">Cocok untuk kebutuhan bisnis harian</p>
                  <a href="<?= BASE_URL ?>/layanan/<?= e($featured['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                    Selengkapnya
                    <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
                  </a>
                </div>
              </div>
            </div>
          </article>
          <?php endif; ?>

          <!-- Services grid -->
          <?php if (!empty($services)): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($services as $i => $service): ?>
            <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group">
              <div class="aspect-[4/3] overflow-hidden <?= $bg_colors[$i % count($bg_colors)] ?> flex items-center justify-center">
                <?php if ($service['image']): ?>
                <img src="<?= BASE_URL ?>/<?= e($service['image']) ?>" alt="<?= e($service['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <?php else: ?>
                <iconify-icon icon="<?= e($service['icon'] ?? 'solar:print-linear') ?>" class="text-6xl text-[#2563eb] group-hover:scale-110 transition-transform duration-500"></iconify-icon>
                <?php endif; ?>
              </div>
              <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]">Layanan</span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]">Cetak Digital</span>
                </div>
                <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($service['name']) ?></h3>
                <p class="text-base text-[#4b5563] leading-relaxed mb-6"><?= e(truncate($service['description'] ?? '', 130)) ?></p>
                <a href="<?= BASE_URL ?>/layanan/<?= e($service['slug']) ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                  Selengkapnya
                  <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
                </a>
              </div>
            </article>
            <?php endforeach; ?>

            <!-- CTA dark card -->
            <article class="bg-[#111827] rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] p-6 sm:p-8 flex flex-col justify-between">
              <div>
                <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-4">Butuh solusi khusus?</p>
                <h3 class="text-3xl tracking-tight uppercase text-white mb-4 font-semibold" style="font-family:'Playfair Display',serif;">Bicara dengan tim produksi kami</h3>
                <p class="text-base text-white/60 leading-relaxed">Ceritakan apa yang ingin Anda cetak, ke mana dikirim, dan kapan dibutuhkan.</p>
              </div>
              <a href="<?= BASE_URL ?>/kontak" class="mt-8 inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#60a5fa] hover:text-white transition-colors">
                Minta penawaran
                <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
              </a>
            </article>
          </div>
          <?php elseif (!$featured): ?>
          <div class="rounded-[2rem] bg-white border border-[#111827]/6 p-16 text-center">
            <iconify-icon icon="solar:print-linear" class="text-5xl text-[#d1d5db] mb-4 block"></iconify-icon>
            <p class="text-[#6b7280] uppercase tracking-widest text-sm">Belum ada layanan. Tambahkan melalui admin.</p>
          </div>
          <?php endif; ?>

          <!-- How it works -->
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 sm:p-8 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-6">Cara Kerja</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3">Langkah 01</p>
                <h4 class="text-xl uppercase text-[#111827] mb-2 font-semibold">Kirim Brief Anda</h4>
                <p class="text-sm text-[#4b5563] leading-7">Kirimkan file artwork, spesifikasi, jumlah, dan kebutuhan pengiriman Anda.</p>
              </div>
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3">Langkah 02</p>
                <h4 class="text-xl uppercase text-[#111827] mb-2 font-semibold">Konfirmasi Produksi</h4>
                <p class="text-sm text-[#4b5563] leading-7">Kami review file, memberikan saran setup, dan mengkonfirmasi jadwal produksi.</p>
              </div>
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3">Langkah 03</p>
                <h4 class="text-xl uppercase text-[#111827] mb-2 font-semibold">Cetak & Kirim</h4>
                <p class="text-sm text-[#4b5563] leading-7">Materi Anda diproduksi, dikemas, dan dikirimkan tepat waktu.</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
</div>

<?php require_once 'includes/footer.php'; ?>