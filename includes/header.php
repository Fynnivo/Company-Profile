<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

// Get site settings
$site_name    = get_setting('site_name', $pdo);
$site_tagline = get_setting('site_tagline', $pdo);
$meta_desc    = isset($meta_description) ? $meta_description : get_setting('meta_description', $pdo);
$page_title   = isset($page_title) ? $page_title . ' | ' . $site_name : $site_name . ' | ' . $site_tagline;
$current_url  = BASE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title) ?></title>
  <meta name="description" content="<?= e($meta_desc) ?>">

  <!-- Open Graph -->
  <meta property="og:title" content="<?= e($page_title) ?>">
  <meta property="og:description" content="<?= e($meta_desc) ?>">
  <meta property="og:url" content="<?= e($current_url) ?>">
  <meta property="og:type" content="website">
  <?php if (isset($og_image)): ?>
  <meta property="og:image" content="<?= e(BASE_URL . '/' . $og_image) ?>">
  <?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Iconify -->
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-[#f4f4f2] text-[#1f2937] antialiased overflow-x-hidden selection:bg-[#2563eb] selection:text-white" style="font-family:'Nunito',sans-serif;">

  <!-- Noise overlay -->
  <div class="fixed inset-0 pointer-events-none z-[100] opacity-[0.025] mix-blend-multiply"
    style="background-image:url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.85%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');">
  </div>

  <!-- Scroll progress bar -->
  <div id="scroll-progress" class="fixed top-0 left-0 h-1 bg-[#2563eb] z-[120] origin-left" style="transform:scaleX(0);"></div>

  <!-- Navbar -->
  <div class="fixed top-5 left-0 right-0 z-50 flex justify-center px-4">
    <header class="w-full max-w-[78rem] rounded-full border border-[#1f2937]/10 bg-white/80 backdrop-blur-xl shadow-[0_0.5rem_2rem_rgba(15,23,42,0.06)] px-5 md:px-7 h-16 flex items-center justify-between">
      <a href="<?= BASE_URL ?>" class="text-lg md:text-xl tracking-tight uppercase text-[#111827] font-semibold"><?= e($site_name) ?></a>
      <nav class="hidden md:flex items-center gap-8">
        <a href="<?= BASE_URL ?>/tentang" class="text-sm text-[#1f2937]/65 hover:text-[#2563eb] uppercase tracking-widest transition-colors">Tentang</a>
        <a href="<?= BASE_URL ?>/layanan" class="text-sm text-[#1f2937]/65 hover:text-[#2563eb] uppercase tracking-widest transition-colors">Layanan</a>
        <a href="<?= BASE_URL ?>/artikel" class="text-sm text-[#1f2937]/65 hover:text-[#2563eb] uppercase tracking-widest transition-colors">Artikel</a>
        <a href="<?= BASE_URL ?>/kontak" class="text-sm text-[#1f2937]/65 hover:text-[#2563eb] uppercase tracking-widest transition-colors">Kontak</a>
      </nav>
      <div class="flex items-center gap-3">
        <!-- Mobile menu button -->
        <button id="mobile-menu-btn" class="md:hidden flex items-center justify-center text-[#1f2937]">
          <iconify-icon icon="solar:hamburger-menu-linear" class="text-2xl"></iconify-icon>
        </button>
        <a href="<?= BASE_URL ?>/kontak" class="hidden md:flex md:text-sm uppercase hover:bg-[#2563eb] transition-all duration-300 items-center gap-2 text-xs text-white tracking-widest font-semibold bg-[#111827] rounded-full pt-2.5 pr-5 pb-2.5 pl-5">Hubungi Kami</a>
      </div>
    </header>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden fixed inset-0 z-40 bg-[#111827]/95 backdrop-blur-xl flex-col items-center justify-center gap-8">
    <button id="mobile-menu-close" class="absolute top-6 right-6 text-white">
      <iconify-icon icon="solar:close-circle-linear" class="text-3xl"></iconify-icon>
    </button>
    <a href="<?= BASE_URL ?>/tentang" class="text-2xl text-white uppercase tracking-widest hover:text-[#60a5fa] transition-colors">Tentang</a>
    <a href="<?= BASE_URL ?>/layanan" class="text-2xl text-white uppercase tracking-widest hover:text-[#60a5fa] transition-colors">Layanan</a>
    <a href="<?= BASE_URL ?>/artikel" class="text-2xl text-white uppercase tracking-widest hover:text-[#60a5fa] transition-colors">Artikel</a>
    <a href="<?= BASE_URL ?>/kontak" class="text-2xl text-white uppercase tracking-widest hover:text-[#60a5fa] transition-colors">Kontak</a>
    <a href="<?= BASE_URL ?>/kontak" class="mt-4 text-sm uppercase tracking-widest bg-[#2563eb] text-white rounded-full px-8 py-4 hover:bg-white hover:text-[#111827] transition-all">Hubungi Kami</a>
  </div>

  <main></main>