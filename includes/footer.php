</main>

  <!-- FOOTER -->
  <footer class="text-white bg-[#111827] border-white/6 border-t pt-16 pb-10">
    <div class="max-w-[88rem] mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-16">

        <div class="lg:col-span-2">
          <h3 class="text-3xl tracking-tight uppercase text-white mb-4 font-semibold" style="font-family:'Playfair Display',serif;"><?= e($site_name) ?></h3>
          <p class="text-base text-white/50 max-w-sm leading-relaxed mb-8"><?= e($site_tagline) ?></p>
          <div class="flex gap-4">
            <?php $instagram = get_setting('instagram', $pdo); if ($instagram): ?>
            <a href="<?= e($instagram) ?>" target="_blank" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white/40 hover:text-[#60a5fa] hover:border-[#60a5fa]/50 transition-all">
              <iconify-icon icon="solar:instagram-linear" class="text-lg"></iconify-icon>
            </a>
            <?php endif; ?>
            <?php $facebook = get_setting('facebook', $pdo); if ($facebook): ?>
            <a href="<?= e($facebook) ?>" target="_blank" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white/40 hover:text-[#60a5fa] hover:border-[#60a5fa]/50 transition-all">
              <iconify-icon icon="solar:brand-facebook-linear" class="text-lg"></iconify-icon>
            </a>
            <?php endif; ?>
            <?php $wa = get_setting('whatsapp', $pdo); if ($wa): ?>
            <a href="https://wa.me/<?= e($wa) ?>" target="_blank" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white/40 hover:text-[#60a5fa] hover:border-[#60a5fa]/50 transition-all">
              <iconify-icon icon="solar:phone-linear" class="text-lg"></iconify-icon>
            </a>
            <?php endif; ?>
          </div>
        </div>

        <div>
          <h4 class="text-xs uppercase tracking-[0.2em] text-[#60a5fa] mb-6">Layanan</h4>
          <ul class="space-y-3">
          <?php
            $services_footer = $pdo->query("SELECT name, slug FROM services WHERE is_active = 1 ORDER BY sort_order LIMIT 5")->fetchAll();
            foreach ($services_footer as $s):
          ?>
            <li><a href="<?= BASE_URL ?>/layanan-detail?slug=<?= e($s['slug']) ?>" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors"><?= e($s['name']) ?></a></li>
          <?php endforeach; ?>
          </ul>
        </div>

        <div>
          <h4 class="text-xs uppercase tracking-[0.2em] text-[#60a5fa] mb-6">Artikel</h4>
          <ul class="space-y-3">
            <li><a href="<?= BASE_URL ?>/artikel" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Semua Artikel</a></li>
            <li><a href="<?= BASE_URL ?>/artikel?kategori=panduan" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Panduan</a></li>
            <li><a href="<?= BASE_URL ?>/artikel?kategori=tips" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Tips & Trik</a></li>
            <li><a href="<?= BASE_URL ?>/artikel?kategori=berita" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Berita</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-xs uppercase tracking-[0.2em] text-[#60a5fa] mb-6">Perusahaan</h4>
          <ul class="space-y-3">
            <li><a href="<?= BASE_URL ?>/tentang" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Tentang Kami</a></li>
            <li><a href="<?= BASE_URL ?>/layanan" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Layanan</a></li>
            <li><a href="<?= BASE_URL ?>/kontak" class="text-base text-white/50 hover:text-[#60a5fa] transition-colors">Kontak</a></li>
          </ul>
        </div>
      </div>

      <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
        <p class="text-xs uppercase tracking-[0.2em] text-white/30">© <?= date('Y') ?> <?= e($site_name) ?>. All rights reserved.</p>
        <div class="flex gap-6">
          <span class="text-xs uppercase tracking-widest text-white/30"><?= e(get_setting('address', $pdo)) ?></span>
        </div>
      </div>
    </div>
  </footer>

  <!-- WhatsApp Float Button -->
  <?php $wa_float = get_setting('whatsapp', $pdo); if ($wa_float): ?>
  <a href="https://wa.me/<?= e($wa_float) ?>" target="_blank"
    class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-[#25D366] rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
    <iconify-icon icon="ic:baseline-whatsapp" class="text-white text-3xl"></iconify-icon>
  </a>
  <?php endif; ?>

  <!-- Main JS -->
  <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>