<?php
$page_title       = 'Kontak';
$meta_description = 'Hubungi kami untuk konsultasi gratis, permintaan penawaran, atau pertanyaan seputar layanan digital printing kami.';
require_once 'includes/header.php';

// Pull contact info from settings
$phone     = get_setting('phone', $pdo);
$whatsapp  = get_setting('whatsapp', $pdo);
$email     = get_setting('email', $pdo);
$address   = get_setting('address', $pdo);
$maps      = get_setting('maps_embed', $pdo);
$instagram = get_setting('instagram', $pdo);

// Fetch services for dropdown
$services_list = $pdo->query("SELECT name FROM services WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_COLUMN);

// ── Form submission ──────────────────────────────────────────────
$success = false;
$errors  = [];
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = [
        'name'    => trim($_POST['name']    ?? ''),
        'email'   => trim($_POST['email']   ?? ''),
        'phone'   => trim($_POST['phone']   ?? ''),
        'service' => trim($_POST['service'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
    ];

    // Validate
    if (!$old['name'])                          $errors['name']    = 'Nama wajib diisi.';
    if (!$old['message'])                       $errors['message'] = 'Pesan wajib diisi.';
    if ($old['email'] && !filter_var($old['email'], FILTER_VALIDATE_EMAIL))
                                                $errors['email']   = 'Format email tidak valid.';
    if (!$old['email'] && !$old['phone'])       $errors['contact'] = 'Isi email atau nomor telepon.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (name, email, phone, message, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $full_message = ($old['service'] ? '[Layanan: ' . $old['service'] . "]\n" : '') . $old['message'];
        $stmt->execute([$old['name'], $old['email'], $old['phone'], $full_message]);
        $success = true;
        $old = [];
    }
}
?>

<div class="max-w-[96rem] mx-auto w-full px-3 sm:px-4 md:px-6 lg:px-8 xl:px-10">
  <main class="pt-24 sm:pt-28 lg:pt-32 pb-8 sm:pb-10">

    <!-- ===================== HERO ===================== -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0" style="background-image:linear-gradient(to right, rgba(31,41,55,0.04) 1px, transparent 1px),linear-gradient(to bottom, rgba(31,41,55,0.04) 1px, transparent 1px);background-size:3rem 3rem"></div>
      <div class="relative max-w-[88rem] mx-auto pt-6 sm:pt-8 lg:pt-10 pb-8 sm:pb-10">

        <div class="grid grid-cols-1 xl:grid-cols-[0.95fr_1.05fr] gap-6 sm:gap-8 lg:gap-10 items-end">
          <div>
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 border border-[#2563eb]/20 px-4 py-2 mb-5">
            <span class="relative flex h-2 w-2">
              <span class="absolute inline-flex h-full w-full rounded-full bg-[#2563eb] opacity-60 animate-ping"></span>
              <span class="relative inline-flex h-2 w-2 rounded-full bg-[#2563eb]"></span>
            </span>  
            <span class="text-xs uppercase tracking-widest text-[#2563eb]">Kontak</span>
              <span class="text-xs uppercase tracking-widest text-[#6b7280]">Mulai berbicara</span>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl tracking-tight leading-[0.92] uppercase text-[#111827] font-semibold" style="font-family:'Playfair Display',serif;">
              Mulai
              <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#111827] to-[#2563eb] font-semibold">Percakapan yang Tepat</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-[#4b5563] leading-relaxed max-w-2xl font-semibold" style="font-family:'Playfair Display',serif;">
              Butuh penawaran, konsultasi, atau bantuan memilih solusi cetak terbaik? Kami siap membantu dengan cara yang jelas, langsung, dan benar-benar berguna.
            </p>
          </div>

          <!-- Stats -->
          <div>
            <div class="rounded-[1.75rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm shadow-[0_1.5rem_4rem_rgba(15,23,42,0.08)] p-5 sm:p-6">
              <div class="grid grid-cols-3 gap-4">
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Respons</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Cepat</p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Dukungan</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Praktis</p>
                </div>
                <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-2">Pendekatan</p>
                  <p class="text-2xl tracking-tight text-[#111827] font-semibold">Personal</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick action pills -->
        <div class="mt-8 sm:mt-10 rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-4 sm:p-5 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">
          <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2">
              <a href="#form" class="rounded-full bg-[#111827] text-white text-xs uppercase tracking-widest px-4 py-2 hover:bg-[#2563eb] transition-all">Kirim Pesan</a>
              <?php if ($phone): ?>
              <a href="tel:<?= e($phone) ?>" class="rounded-full border border-[#111827]/10 bg-white text-[#111827] text-xs uppercase tracking-widest px-4 py-2 hover:border-[#2563eb]/40 hover:text-[#2563eb] transition-all">Telepon</a>
              <?php endif; ?>
              <?php if ($email): ?>
              <a href="mailto:<?= e($email) ?>" class="rounded-full border border-[#111827]/10 bg-white text-[#111827] text-xs uppercase tracking-widest px-4 py-2 hover:border-[#2563eb]/40 hover:text-[#2563eb] transition-all">Email</a>
              <?php endif; ?>
              <?php if ($whatsapp): ?>
              <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank" class="rounded-full border border-[#25D366]/30 bg-[#25D366]/5 text-[#16a34a] text-xs uppercase tracking-widest px-4 py-2 hover:bg-[#25D366] hover:text-white transition-all flex items-center gap-1.5">
                <iconify-icon icon="ic:baseline-whatsapp" class="text-sm"></iconify-icon>
                WhatsApp
              </a>
              <?php endif; ?>
            </div>
            <div class="flex items-center gap-2 text-sm text-[#6b7280]">
              <iconify-icon icon="solar:chat-round-dots-linear" class="text-base"></iconify-icon>
              Respons nyata, bukan balasan otomatis
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== CONTACT BODY ===================== -->
    <section class="mt-2">
      <div class="max-w-[88rem] mx-auto grid grid-cols-1 xl:grid-cols-[0.72fr_1.28fr] gap-6 sm:gap-8 lg:gap-10 items-start">

        <!-- Sidebar -->
        <aside class="xl:sticky xl:top-28">
          <div class="rounded-[1.5rem] border border-[#111827]/8 bg-white/80 backdrop-blur-sm p-6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.04)]">

            <p class="text-xs uppercase tracking-[0.25em] text-[#2563eb] mb-4">Hubungi Kami</p>
            <div class="space-y-3">

              <?php if ($whatsapp): ?>
              <a href="https://wa.me/<?= e($whatsapp) ?>" target="_blank"
                class="flex items-start gap-3 rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4 hover:border-[#25D366]/40 hover:bg-[#f0fdf4] transition-all group">
                <div class="w-10 h-10 rounded-full bg-white border border-[#111827]/8 flex items-center justify-center text-[#25D366] flex-shrink-0">
                  <iconify-icon icon="ic:baseline-whatsapp" class="text-lg"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">WhatsApp</p>
                  <p class="text-sm text-[#111827] group-hover:text-[#16a34a] transition-colors">+<?= e($whatsapp) ?></p>
                </div>
              </a>
              <?php endif; ?>

              <?php if ($phone): ?>
              <a href="tel:<?= e($phone) ?>"
                class="flex items-start gap-3 rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4 hover:border-[#2563eb]/40 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white border border-[#111827]/8 flex items-center justify-center text-[#2563eb] flex-shrink-0">
                  <iconify-icon icon="solar:phone-linear" class="text-lg"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Telepon</p>
                  <p class="text-sm text-[#111827] group-hover:text-[#2563eb] transition-colors"><?= e($phone) ?></p>
                </div>
              </a>
              <?php endif; ?>

              <?php if ($email): ?>
              <a href="mailto:<?= e($email) ?>"
                class="flex items-start gap-3 rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4 hover:border-[#2563eb]/40 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white border border-[#111827]/8 flex items-center justify-center text-[#2563eb] flex-shrink-0">
                  <iconify-icon icon="solar:letter-linear" class="text-lg"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Email</p>
                  <p class="text-sm text-[#111827] group-hover:text-[#2563eb] transition-colors"><?= e($email) ?></p>
                </div>
              </a>
              <?php endif; ?>

              <?php if ($address): ?>
              <div class="flex items-start gap-3 rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4">
                <div class="w-10 h-10 rounded-full bg-white border border-[#111827]/8 flex items-center justify-center text-[#2563eb] flex-shrink-0">
                  <iconify-icon icon="solar:map-point-linear" class="text-lg"></iconify-icon>
                </div>
                <div>
                  <p class="text-xs uppercase tracking-widest text-[#6b7280] mb-1">Alamat</p>
                  <p class="text-sm text-[#111827] leading-6"><?= nl2br(e($address)) ?></p>
                </div>
              </div>
              <?php endif; ?>

            </div>

            <!-- Maps embed -->
            <?php if ($maps): ?>
            <div class="mt-5 rounded-[1.25rem] overflow-hidden border border-[#111827]/8 aspect-[4/3]">
              <iframe src="<?= e($maps) ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <?php endif; ?>

            <!-- What to include tip -->
            <div class="pt-6 mt-4 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-4">Tips Pesan yang Baik</p>
              <div class="rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-5 space-y-3">
                <?php
                $tips = [
                  ['icon'=>'solar:file-text-linear',   'text'=>'Sebutkan jenis layanan yang dibutuhkan'],
                  ['icon'=>'solar:layers-linear',       'text'=>'Sertakan jumlah & ukuran jika ada'],
                  ['icon'=>'solar:calendar-linear',     'text'=>'Beritahu deadline Anda'],
                  ['icon'=>'solar:palette-linear',      'text'=>'Tanyakan finishing jika belum yakin'],
                ];
                foreach ($tips as $tip):
                ?>
                <div class="flex items-center gap-3">
                  <iconify-icon icon="<?= $tip['icon'] ?>" class="text-base text-[#2563eb] flex-shrink-0"></iconify-icon>
                  <p class="text-xs text-[#4b5563] leading-5"><?= $tip['text'] ?></p>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Social links -->
            <?php if ($instagram): ?>
            <div class="pt-6 mt-4 border-t border-[#111827]/8">
              <p class="text-xs uppercase tracking-[0.25em] text-[#6b7280] mb-3">Ikuti Kami</p>
              <a href="<?= e($instagram) ?>" target="_blank"
                class="flex items-center gap-3 rounded-[1.25rem] border border-[#111827]/8 bg-[#f8f8f6] p-4 hover:border-[#e1306c]/30 hover:bg-[#fff5f8] transition-all group">
                <iconify-icon icon="solar:instagram-linear" class="text-xl text-[#e1306c]"></iconify-icon>
                <span class="text-sm text-[#111827] group-hover:text-[#e1306c] transition-colors">Instagram</span>
              </a>
            </div>
            <?php endif; ?>

          </div>
        </aside>

        <!-- Main: Form + info cards -->
        <div class="space-y-6" id="form">

          <!-- Success message -->
          <?php if ($success): ?>
          <div class="rounded-[1.5rem] bg-[#f0fdf4] border border-[#86efac] p-6 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-[#22c55e] flex items-center justify-center flex-shrink-0">
              <iconify-icon icon="solar:check-circle-bold" class="text-xl text-white"></iconify-icon>
            </div>
            <div>
              <p class="text-sm font-semibold text-[#15803d] uppercase tracking-widest mb-1">Pesan Terkirim!</p>
              <p class="text-sm text-[#166534] leading-6">Terima kasih telah menghubungi kami. Tim kami akan merespons Anda secepatnya, biasanya dalam 1 hari kerja.</p>
            </div>
          </div>
          <?php endif; ?>

          <!-- Contact form card -->
          <article class="rounded-[2rem] bg-white border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_0.95fr]">

              <!-- Form side -->
              <div class="px-6 sm:px-8 py-6 sm:py-8 flex flex-col justify-center">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                  <span class="text-xs uppercase tracking-widest text-[#2563eb]">Kirim Pesan</span>
                  <span class="text-xs uppercase tracking-widest text-[#6b7280]">Brief baru disambut baik</span>
                </div>
                <h2 class="text-3xl sm:text-4xl tracking-tight uppercase text-[#111827] mb-4 font-semibold" style="font-family:'Playfair Display',serif;">Ceritakan Kebutuhan Cetak Anda</h2>
                <p class="text-base sm:text-lg text-[#4b5563] leading-8 mb-6">
                  Bagikan detail proyek Anda dan kami akan merespons dengan langkah selanjutnya yang jelas dan saran yang praktis.
                </p>

                <form method="POST" action="<?= BASE_URL ?>/kontak#form" class="space-y-4">

                  <!-- Global error -->
                  <?php if (!empty($errors)): ?>
                  <div class="rounded-[1rem] bg-red-50 border border-red-200 px-4 py-3">
                    <p class="text-xs text-red-600 uppercase tracking-widest font-semibold mb-1">Harap perbaiki:</p>
                    <?php foreach ($errors as $err): ?>
                    <p class="text-sm text-red-600">— <?= e($err) ?></p>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs uppercase tracking-widest text-[#6b7280] mb-2">Nama <span class="text-red-400">*</span></label>
                      <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>"
                        placeholder="Nama Anda"
                        class="w-full rounded-[1rem] border <?= isset($errors['name']) ? 'border-red-400' : 'border-[#111827]/10' ?> bg-[#f8f8f6] text-sm text-[#111827] placeholder:text-[#9ca3af] px-4 py-3.5 outline-none focus:border-[#2563eb] transition-colors">
                    </div>
                    <div>
                      <label class="block text-xs uppercase tracking-widest text-[#6b7280] mb-2">Email</label>
                      <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>"
                        placeholder="email@perusahaan.com"
                        class="w-full rounded-[1rem] border <?= isset($errors['email']) ? 'border-red-400' : 'border-[#111827]/10' ?> bg-[#f8f8f6] text-sm text-[#111827] placeholder:text-[#9ca3af] px-4 py-3.5 outline-none focus:border-[#2563eb] transition-colors">
                    </div>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-xs uppercase tracking-widest text-[#6b7280] mb-2">
                        Telepon / WhatsApp
                        <?php if (!isset($errors['email'])): ?>
                        <span class="text-[#9ca3af] normal-case tracking-normal">atau email</span>
                        <?php endif; ?>
                      </label>
                      <input type="text" name="phone" value="<?= e($old['phone'] ?? '') ?>"
                        placeholder="08xxxxxxxxxx"
                        class="w-full rounded-[1rem] border <?= isset($errors['contact']) ? 'border-red-400' : 'border-[#111827]/10' ?> bg-[#f8f8f6] text-sm text-[#111827] placeholder:text-[#9ca3af] px-4 py-3.5 outline-none focus:border-[#2563eb] transition-colors">
                    </div>
                    <div>
                      <label class="block text-xs uppercase tracking-widest text-[#6b7280] mb-2">Jenis Layanan</label>
                      <div class="relative">
                        <select name="service"
                          class="appearance-none w-full rounded-[1rem] border border-[#111827]/10 bg-[#f8f8f6] text-sm text-[#111827] px-4 py-3.5 pr-10 outline-none focus:border-[#2563eb] transition-colors">
                          <option value="">Pilih layanan...</option>
                          <?php foreach ($services_list as $svc): ?>
                          <option value="<?= e($svc) ?>" <?= ($old['service'] ?? '') === $svc ? 'selected' : '' ?>><?= e($svc) ?></option>
                          <?php endforeach; ?>
                          <option value="Konsultasi Umum" <?= ($old['service'] ?? '') === 'Konsultasi Umum' ? 'selected' : '' ?>>Konsultasi Umum</option>
                          <option value="Lainnya" <?= ($old['service'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                        <iconify-icon icon="solar:alt-arrow-down-linear" class="text-base text-[#6b7280] absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"></iconify-icon>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-xs uppercase tracking-widest text-[#6b7280] mb-2">Pesan <span class="text-red-400">*</span></label>
                    <textarea name="message" rows="6"
                      placeholder="Ceritakan kebutuhan cetak Anda — jumlah, ukuran, bahan, deadline, atau pertanyaan lainnya."
                      class="w-full rounded-[1rem] border <?= isset($errors['message']) ? 'border-red-400' : 'border-[#111827]/10' ?> bg-[#f8f8f6] text-sm text-[#111827] placeholder:text-[#9ca3af] px-4 py-4 outline-none focus:border-[#2563eb] transition-colors resize-none"><?= e($old['message'] ?? '') ?></textarea>
                  </div>

                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <p class="text-xs uppercase tracking-widest text-[#6b7280]">Biasanya dibalas dalam 1 hari kerja</p>
                    <button type="submit"
                      class="text-sm uppercase hover:bg-[#2563eb] transition-all duration-300 inline-flex items-center justify-center gap-2 text-white tracking-widest font-semibold bg-[#111827] rounded-full px-6 py-3.5">
                      Kirim Pesan
                      <iconify-icon icon="solar:arrow-right-linear" class="text-base"></iconify-icon>
                    </button>
                  </div>
                </form>
              </div>

              <!-- Info side -->
              <div class="bg-[#eef4ff] border-t lg:border-t-0 lg:border-l border-[#111827]/6 p-6 sm:p-8 flex flex-col justify-between">
                <div>
                  <div class="inline-flex items-center gap-2 rounded-full bg-white border border-[#2563eb]/15 px-4 py-2 mb-5">
                    <iconify-icon icon="solar:cup-hot-linear" class="text-base text-[#2563eb]"></iconify-icon>
                    <span class="text-xs uppercase tracking-widest text-[#2563eb]">Pendekatan personal</span>
                  </div>
                  <h3 class="text-2xl sm:text-3xl tracking-tight uppercase text-[#111827] mb-4 font-semibold" style="font-family:'Playfair Display',serif;">Saran Jelas Sebelum Produksi Dimulai</h3>
                  <p class="text-base text-[#4b5563] leading-8 mb-8">
                    Kami percaya kontak yang baik adalah awal dari hubungan kerja yang nyata. Jika ada yang belum jelas, kami bantu figur out dulu sebelum apapun bergerak maju.
                  </p>

                  <div class="space-y-4">
                    <?php
                    $cards = [
                      ['icon'=>'solar:file-text-linear',   'title'=>'Butuh penawaran harga?',    'desc'=>'Kirim spesifikasi, jumlah, finishing, dan deadline jika sudah ada.'],
                      ['icon'=>'solar:palette-linear',     'title'=>'Butuh panduan?',             'desc'=>'Kami bantu pilih bahan, finishing, format, dan metode produksi terbaik.'],
                      ['icon'=>'solar:calendar-linear',    'title'=>'Ada deadline mendesak?',     'desc'=>'Kabari kami lebih awal dan kami akan sarankan jalur paling realistis.'],
                    ];
                    foreach ($cards as $card):
                    ?>
                    <div class="rounded-[1.25rem] bg-white border border-[#111827]/8 p-4">
                      <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#f8f8f6] border border-[#111827]/8 flex items-center justify-center text-[#2563eb] flex-shrink-0">
                          <iconify-icon icon="<?= $card['icon'] ?>" class="text-lg"></iconify-icon>
                        </div>
                        <div>
                          <p class="text-sm text-[#111827] mb-1 font-semibold"><?= $card['title'] ?></p>
                          <p class="text-sm text-[#4b5563] leading-6"><?= $card['desc'] ?></p>
                        </div>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <?php if ($whatsapp): ?>
                <a href="https://wa.me/<?= e($whatsapp) ?>?text=Halo, saya ingin konsultasi tentang layanan cetak Anda"
                  target="_blank"
                  class="mt-8 flex items-center justify-between gap-4 rounded-[1.25rem] bg-[#25D366] px-5 py-4 hover:bg-[#128c7e] transition-all">
                  <div class="flex items-center gap-3">
                    <iconify-icon icon="ic:baseline-whatsapp" class="text-2xl text-white"></iconify-icon>
                    <div>
                      <p class="text-xs text-white/70 uppercase tracking-widest">Chat Langsung</p>
                      <p class="text-sm text-white font-semibold">Hubungi via WhatsApp</p>
                    </div>
                  </div>
                  <iconify-icon icon="solar:arrow-right-linear" class="text-base text-white/70"></iconify-icon>
                </a>
                <?php endif; ?>
              </div>

            </div>
          </article>

          <!-- 3 info cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] p-6">
              <div class="w-12 h-12 rounded-full bg-[#eef2ff] border border-[#111827]/8 flex items-center justify-center text-[#2563eb] mb-5">
                <iconify-icon icon="solar:clock-circle-linear" class="text-xl"></iconify-icon>
              </div>
              <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;">Respons Cepat</h3>
              <p class="text-base text-[#4b5563] leading-relaxed">Kami menjaga komunikasi tetap bergerak dengan balasan tepat waktu dan komunikasi yang lugas.</p>
            </article>

            <article class="bg-white rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] p-6">
              <div class="w-12 h-12 rounded-full bg-[#ecfeff] border border-[#111827]/8 flex items-center justify-center text-[#2563eb] mb-5">
                <iconify-icon icon="solar:headphones-round-linear" class="text-xl"></iconify-icon>
              </div>
              <h3 class="text-2xl tracking-tight uppercase text-[#111827] mb-3 font-semibold" style="font-family:'Playfair Display',serif;">Dukungan Nyata</h3>
              <p class="text-base text-[#4b5563] leading-relaxed">Balasan kami dibangun atas dasar bantuan praktis, bukan bahasa sales yang samar atau jawaban generik.</p>
            </article>

            <article class="bg-[#111827] rounded-[1.5rem] overflow-hidden border border-[#111827]/6 shadow-[0_0.5rem_2rem_rgba(15,23,42,0.05)] p-6">
              <div class="w-12 h-12 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-[#60a5fa] mb-5">
                <iconify-icon icon="solar:bolt-circle-linear" class="text-xl"></iconify-icon>
              </div>
              <h3 class="text-2xl tracking-tight uppercase text-white mb-3 font-semibold" style="font-family:'Playfair Display',serif;">Momentum Nyata</h3>
              <p class="text-base text-white/60 leading-relaxed">Pesan pertama yang baik menciptakan kejelasan dan momentum — itulah yang selalu kami berikan kembali.</p>
            </article>
          </div>

        </div>
      </div>
    </section>

  </main>
</div>

<?php require_once 'includes/footer.php'; ?>