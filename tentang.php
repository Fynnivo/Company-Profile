<?php
$page_title       = 'Tentang Kami';
$meta_description = 'Kenali lebih dekat siapa kami, visi misi, dan nilai-nilai yang menjadi landasan layanan digital printing kami.';
require_once 'includes/header.php';

// Pull settings for authentic content
$site_name    = get_setting('site_name', $pdo);
$site_tagline = get_setting('site_tagline', $pdo);
$phone        = get_setting('phone', $pdo);
$whatsapp     = get_setting('whatsapp', $pdo);
$email        = get_setting('email', $pdo);
$address      = get_setting('address', $pdo);
$instagram    = get_setting('instagram', $pdo);

// Stats from DB
$total_services  = (int)$pdo->query("SELECT COUNT(*) FROM services WHERE is_active=1")->fetchColumn();
$total_articles  = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();

// About content sections — edit these to match the real company story
$about_sections = [
  [
    'icon'    => 'solar:users-group-rounded-linear',
    'bg'      => 'bg-[#e5e7eb]',
    'tag1'    => 'Siapa Kami',
    'tag2'    => 'Independen · Profesional · Berkualitas',
    'title'   => 'Mitra Cetak yang Fokus pada Hasil Terbaik',
    'body'    => get_setting('about_who', $pdo) ?: $site_name . ' hadir untuk membuat cetak komersial terasa lebih mudah dan andal. Kami bekerja bersama bisnis, tim pemasaran, dan organisasi yang membutuhkan output berkualitas, panduan praktis, dan mitra yang benar-benar memahami detail.',
    'cta'     => ['label' => 'Hubungi Kami', 'href' => BASE_URL . '/kontak'],
    'featured'=> true,
  ],
  [
    'icon'    => 'solar:book-bookmark-linear',
    'bg'      => 'bg-[#eef2ff]',
    'tag1'    => 'Cerita',
    'tag2'    => 'Bagaimana kami dimulai',
    'title'   => 'Dibangun dari Pengalaman Produksi Nyata',
    'body'    => get_setting('about_story', $pdo) ?: 'Fondasi kami berasal dari pengetahuan cetak langsung dan keyakinan bahwa layanan yang baik harus jelas, responsif, dan dapat diandalkan dari awal hingga akhir.',
    'cta'     => null,
  ],
  [
    'icon'    => 'solar:target-linear',
    'bg'      => 'bg-[#ecfeff]',
    'tag1'    => 'Misi',
    'tag2'    => 'Apa yang mendorong kami',
    'title'   => 'Membuat Cetak Lebih Sederhana dan Lebih Kuat',
    'body'    => get_setting('about_mission', $pdo) ?: 'Kami bertujuan menghilangkan kerumitan dari produksi cetak sambil membantu klien menghasilkan karya yang terlihat profesional, terasa matang, dan tiba sesuai harapan.',
    'cta'     => null,
  ],
  [
    'icon'    => 'solar:heart-angle-linear',
    'bg'      => 'bg-[#f5f3ff]',
    'tag1'    => 'Nilai',
    'tag2'    => 'Kepedulian · Presisi',
    'title'   => 'Kami Menjunjung Detail, Layanan, dan Kepercayaan',
    'body'    => get_setting('about_values', $pdo) ?: 'Pekerjaan kami dibentuk oleh eksekusi yang cermat, komunikasi yang jujur, dan komitmen untuk membangun hubungan terpercaya melalui layanan yang konsisten.',
    'cta'     => null,
  ],
  [
    'icon'    => 'solar:settings-linear',
    'bg'      => 'bg-[#eff6ff]',
    'tag1'    => 'Proses',
    'tag2'    => 'Cara kami bekerja',
    'title'   => 'Gaya Kerja yang Jelas dan Praktis',
    'body'    => get_setting('about_process', $pdo) ?: 'Dari pertanyaan hingga pengiriman, kami fokus pada komunikasi yang jelas, produksi yang terkelola dengan baik, dan jadwal yang andal untuk mendukung kebutuhan bisnis nyata.',
    'cta'     => null,
  ],
  [
    'icon'    => 'solar:user-id-linear',
    'bg'      => 'bg-[#f9fafb]',
    'tag1'    => 'Tim',
    'tag2'    => 'Pola pikir tim kami',
    'title'   => 'Tim yang Memahami Cetak dalam Praktik',
    'body'    => get_setting('about_team', $pdo) ?: 'Kami menyatukan pengalaman praktis, kesadaran produksi, dan pola pikir mengutamakan layanan untuk membantu klien bergerak dengan percaya diri dari ide ke pengiriman.',
    'cta'     => null,
  ],
];

$featured    = array_shift($about_sections);
$grid_items  = $about_sections;
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
            <span class="text-xs uppercase tracking-widest text-[#2563eb]">Tentang Kami</span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]">Siapa kami</span>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight leading-[0.92] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
              Keahlian Cetak
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold">Dibangun atas Kejujuran dan Kualitas</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              <?= e($site_tagline ?: 'Kami membantu bisnis mewujudkan komunikasi cetak melalui produksi andal, dukungan praktis, dan pemahaman mendalam tentang kualitas.') ?>
            </p>
          </div>

          <!-- Live stats from DB -->
          <div>
            <div class="rounded-[1.75rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm shadow-[0_1.5rem_4rem_rgba(15,23,42,0.08)] p-5 sm:p-6">
              <div class="grid grid-cols-3 gap-4">
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Layanan Aktif</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold"><?= str_pad($total_services, 2, '0', STR_PAD_LEFT) ?></p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Pendekatan</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Personal</p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Standar</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Konsisten</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section anchor pills -->
        <div class="mt-8 sm:mt-10 rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-4 sm:p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
          <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
              <?php
              $pills = [
                ['label'=>'Cerita Kami',  'href'=>'#cerita'],
                ['label'=>'Misi',         'href'=>'#misi'],
                ['label'=>'Nilai',        'href'=>'#nilai'],
                ['label'=>'Tim',          'href'=>'#tim'],
                ['label'=>'Proses',       'href'=>'#proses'],
              ];
              foreach ($pills as $i => $pill):
              ?>
              <a href="<?= $pill['href'] ?>"
                class="rounded-full text-xs uppercase tracking-widest px-4 py-2 <?= $i === 0 ? 'bg-[#111827] text-white' : 'border border-[#111827]/10 bg-white text-[#111827] hover:bg-[#111827]/5' ?> transition-all">
                <?= e($pill['label']) ?>
              </a>
              <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-2 text-sm text-[#6b7280]">
              <iconify-icon icon="solar:verified-check-linear" class="text-base"></iconify-icon>
              Mitra cetak terpercaya
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== ABOUT CONTENT ===================== -->
    <section class="mt-2" id="cerita">
      <div class="max-w-[88rem] mx-auto grid grid-cols-1 xl:grid-cols-[0.7fr_1.3fr] gap-6 sm:gap-8 lg:gap-10 items-start">

        <!-- Sticky Sidebar -->
        <aside class="xl:sticky xl:top-28">
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">

            <div class="mb-6">
              <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-3">Di Dalam <?= e($site_name) ?></p>
              <div class="space-y-3">
                <a href="#cerita"  class="block text-sm text-[#111827] hover:text-[#2563eb] transition-colors">Cerita kami</a>
                <a href="#misi"    class="block text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors">Misi kami</a>
                <a href="#nilai"   class="block text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors">Nilai yang kami pegang</a>
                <a href="#proses"  class="block text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors">Cara kami bekerja</a>
                <a href="#tim"     class="block text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors">Tim kami</a>
              </div>
            </div>

            <div class="pt-6 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Yang Mendefinisikan Kami</p>
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-5">
                <p class="text-xs uppercase tracking-widest text-[#2563eb] mb-3">Produksi yang Thoughtful</p>
                <p class="text-sm text-[#111827] leading-7">Kami menggabungkan pengetahuan praktis, layanan responsif, dan pola pikir produksi yang cermat untuk mendukung brand yang menghargai konsistensi.</p>
              </div>
            </div>

            <!-- Contact info from settings -->
            <?php if ($phone || $email || $address): ?>
            <div class="pt-6 mt-4 border-t border-[#111827]/8 space-y-3">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-3">Kontak Kami</p>
              <?php if ($phone): ?>
              <a href="tel:<?= e($phone) ?>" class="flex items-center gap-3 text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors">
                <iconify-icon icon="solar:phone-linear" class="text-base text-[#2563eb]"></iconify-icon>
                <?= e($phone) ?>
              </a>
              <?php endif; ?>
              <?php if ($email): ?>
              <a href="mailto:<?= e($email) ?>" class="flex items-center gap-3 text-sm text-[#111827]/70 hover:text-[#2563eb] transition-colors">
                <iconify-icon icon="solar:letter-linear" class="text-base text-[#2563eb]"></iconify-icon>
                <?= e($email) ?>
              </a>
              <?php endif; ?>
              <?php if ($address): ?>
              <div class="flex items-start gap-3 text-sm text-[#111827]/70">
                <iconify-icon icon="solar:map-point-linear" class="text-base text-[#2563eb] mt-0.5 flex-shrink-0"></iconify-icon>
                <?= e($address) ?>
              </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- CTA -->
            <div class="pt-6 mt-4 border-t border-[#111827]/8">
              <a href="<?= BASE_URL ?>/kontak"
                class="flex items-center gap-3 rounded-[1.25rem] bg-[#111827] p-4 hover:bg-[#2563eb] transition-all">
                <iconify-icon icon="solar:chat-round-dots-linear" class="text-2xl text-[#60a5fa]"></iconify-icon>
                <div>
                  <p class="text-xs text-white/60 uppercase tracking-widest">Mulai Kolaborasi</p>
                  <p class="text-sm text-white font-semibold">Hubungi Kami</p>
                </div>
              </a>
            </div>
          </div>
        </aside>

        <!-- Main cards -->
        <div class="space-y-6">

          <!-- Featured "Who We Are" large card -->
          <article id="cerita" class="rounded-[2rem] bg-white border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr]">
              <div class="aspect-[16/11] lg:aspect-auto overflow-hidden <?= $featured['bg'] ?> flex items-center justify-center relative">
                <iconify-icon icon="<?= e($featured['icon']) ?>" class="text-[6rem] text-[#2563eb]/40"></iconify-icon>
                <!-- Company name watermark -->
                <div class="absolute bottom-4 left-4">
                  <span class="text-xs uppercase tracking-[0.3em] text-[#6b7280]/60"><?= e($site_name) ?></span>
                </div>
              </div>
              <div class="px-6 sm:px-8 py-6 sm:py-8 flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($featured['tag1']) ?></span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= e($featured['tag2']) ?></span>
                </div>
                <h2 class="text-3xl sm:text-4xl tracking-tight uppercase text-[#111827] mb-4 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($featured['title']) ?></h2>
                <p class="text-base sm:text-lg text-[#4b5563] leading-8 mb-6"><?= e($featured['body']) ?></p>
                <div class="flex items-center justify-between gap-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280]">Dibangun untuk kolaborasi jangka panjang</p>
                  <a href="<?= $featured['cta']['href'] ?>" class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#111827] hover:text-[#2563eb] transition-colors">
                    <?= e($featured['cta']['label']) ?>
                    <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
                  </a>
                </div>
              </div>
            </div>
          </article>

          <!-- Grid: Story, Mission, Values, Process, Team, CTA -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <?php
            $anchors = ['misi', 'nilai', 'proses', 'tim'];
            foreach ($grid_items as $idx => $item):
              $anchor = $anchors[$idx] ?? '';
            ?>
            <article id="<?= $anchor ?>" class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] group">
              <div class="aspect-[4/3] overflow-hidden <?= $item['bg'] ?> flex items-center justify-center relative">
                <iconify-icon icon="<?= e($item['icon']) ?>" class="text-[5rem] text-[#2563eb]/50 group-hover:scale-110 transition-transform duration-500"></iconify-icon>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/10"></div>
              </div>
              <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]"><?= e($item['tag1']) ?></span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]"><?= e($item['tag2']) ?></span>
                </div>
                <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($item['title']) ?></h3>
                <p class="text-base text-[#4b5563] leading-relaxed mb-6"><?= e($item['body']) ?></p>
              </div>
            </article>
            <?php endforeach; ?>

            <!-- Dark CTA card -->
            <article class="bg-[#111827] rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] p-6 sm:p-8 flex flex-col justify-between">
              <div>
                <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-4">Ayo Terhubung</p>
                <h3 class="text-3xl tracking-tight uppercase text-white mb-4 font-semibold" style="font-family:'Playfair Display',serif;">Kami ingin mendengar proyek Anda berikutnya</h3>
                <p class="text-base text-white/60 leading-relaxed">Apakah Anda butuh dukungan, panduan, atau mitra produksi yang andal — kami siap membantu Anda melangkah maju.</p>
                <?php if ($whatsapp): ?>
                <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank"
                  class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/15 text-white/70 px-5 py-2.5 text-xs uppercase tracking-widest hover:border-[#25D366] hover:text-[#25D366] transition-all">
                  <iconify-icon icon="ic:baseline-whatsapp" class="text-base"></iconify-icon>
                  <?= e($whatsapp) ?>
                </a>
                <?php endif; ?>
              </div>
              <a href="<?= BASE_URL ?>/kontak" class="mt-8 inline-flex items-center gap-2 text-sm uppercase tracking-widest text-[#60a5fa] hover:text-white transition-colors">
                Hubungi kami
                <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
              </a>
            </article>
          </div>

          <!-- How we work (3-steps) -->
          <div id="proses" class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 sm:p-8 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-6">Filosofi Kerja Kami</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3">Langkah 01</p>
                <h4 class="text-xl uppercase text-[#111827] mb-2 font-semibold">Dengarkan Dulu</h4>
                <p class="text-sm text-[#4b5563] leading-7">Kami mulai dengan memahami apa yang Anda butuhkan dan apa yang paling penting bagi Anda.</p>
              </div>
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3">Langkah 02</p>
                <h4 class="text-xl uppercase text-[#111827] mb-2 font-semibold">Rencanakan dengan Jelas</h4>
                <p class="text-sm text-[#4b5563] leading-7">Kami merancang jalur produksi yang tepat dengan saran yang praktis dan lugas.</p>
              </div>
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-[#2563eb] mb-3">Langkah 03</p>
                <h4 class="text-xl uppercase text-[#111827] mb-2 font-semibold">Hasilkan yang Terbaik</h4>
                <p class="text-sm text-[#4b5563] leading-7">Kami fokus pada konsistensi, kualitas, dan pengalaman yang bisa Anda andalkan setiap saat.</p>
              </div>
            </div>
          </div>

          <!-- Company info bar from settings -->
          <?php if ($address || $phone || $email): ?>
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-[#111827] p-6 sm:p-8 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.1)]">
            <p class="text-xs uppercase tracking-[0.25em] text-[#60a5fa] mb-6">Informasi Perusahaan</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <?php if ($address): ?>
              <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                  <iconify-icon icon="solar:map-point-linear" class="text-[#60a5fa]"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-white/40 mb-1">Alamat</p>
                  <p class="text-sm text-white leading-6"><?= e($address) ?></p>
                </div>
              </div>
              <?php endif; ?>
              <?php if ($phone): ?>
              <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                  <iconify-icon icon="solar:phone-linear" class="text-[#60a5fa]"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-white/40 mb-1">Telepon</p>
                  <a href="tel:<?= e($phone) ?>" class="text-sm text-white hover:text-[#60a5fa] transition-colors"><?= e($phone) ?></a>
                </div>
              </div>
              <?php endif; ?>
              <?php if ($email): ?>
              <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                  <iconify-icon icon="solar:letter-linear" class="text-[#60a5fa]"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-white/40 mb-1">Email</p>
                  <a href="mailto:<?= e($email) ?>" class="text-sm text-white hover:text-[#60a5fa] transition-colors"><?= e($email) ?></a>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </section>

  </main>
</div>

<?php require_once 'includes/footer.php'; ?>