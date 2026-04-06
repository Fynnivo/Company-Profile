<?php
// Guard: must be logged in
if (empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

$_admin_name = $_SESSION['admin_name'] ?? 'Admin';
$_cur_dir = basename(dirname($_SERVER['PHP_SELF']));
$_cur_file = basename($_SERVER['PHP_SELF']);
try {
    $_unread = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
} catch (PDOException $e) {
    $_unread = 0; // Table belum ada
}
function _nav(string $dir, string $file = '*'): string {
    global $_cur_dir, $_cur_file;
    $hit = ($_cur_dir === $dir) && ($file === '*' || $_cur_file === $file);
    return $hit
        ? 'bg-[#f1f5f9] text-[#111827] font-semibold'
        : 'text-[#64748b] hover:bg-[#f8fafc] hover:text-[#111827]';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? e($page_title).' — Admin' : 'Admin Panel' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <style>
    /* ── Reset ───────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    html, body { height: 100%; margin: 0; padding: 0; }
    body { font-family: 'Nunito', sans-serif; background: #f8fafc; color: #111827; }
    input, select, textarea { font-family: 'Nunito', sans-serif; }

    /* ── Shell layout ────────────────────────────────── */
    /*
     *  ┌─────────────────────────────────────────┐
     *  │  #shell  (flex row, full viewport)       │
     *  │  ┌────────┐  ┌──────────────────────┐   │
     *  │  │sidebar │  │  #main-col (flex col) │   │
     *  │  │(fixed  │  │  ┌────────────────┐  │   │
     *  │  │ on lg) │  │  │  topbar        │  │   │
     *  │  │        │  │  ├────────────────┤  │   │
     *  │  │        │  │  │  #page-content │  │   │
     *  │  │        │  │  │  (scrolls)     │  │   │
     *  │  └────────┘  └──────────────────────┘   │
     *  └─────────────────────────────────────────┘
     *
     *  Key rules:
     *  - #shell       : display:flex; height:100vh  (contains everything)
     *  - #sidebar     : flex-shrink:0; overflow-y:auto  (sidebar scrolls internally)
     *  - #main-col    : flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0
     *  - topbar       : flex-shrink:0  (never shrinks)
     *  - #page-content: flex:1; overflow-y:auto  (THE ONLY SCROLL CONTAINER)
     */
    #shell {
      display: flex;
      height: 100vh;
      overflow: hidden;  /* prevent body scroll — only #page-content scrolls */
    }
    #sidebar {
      width: 256px;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      background: #fff;
      border-right: 1px solid #e2e8f0;
      overflow-y: auto;  /* sidebar itself can scroll if nav is tall */
      transition: transform .2s ease;
    }
    #main-col {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-width: 0;
    }
    #topbar {
      height: 64px;
      flex-shrink: 0;
      background: #fff;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.5rem;
    }
    #page-content {
      flex: 1;
      overflow-y: auto;   /* ← THIS is the scroll container */
      overflow-x: hidden;
      padding: 1.5rem;
    }

    /* Mobile: sidebar off-canvas */
    @media (max-width: 1023px) {
      #sidebar {
        position: fixed;
        inset-y: 0;
        left: 0;
        z-index: 30;
        transform: translateX(-100%);
        width: 256px;
      }
      #sidebar.open {
        transform: translateX(0);
      }
    }
    #sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.3);
      z-index: 20;
    }
    #sidebar-overlay.show { display: block; }

    /* Flash autohide */
    [data-autohide] { transition: opacity .4s; }
  </style>
</head>
<body>
<div id="shell">

  <!-- ═══════════════════ SIDEBAR ═══════════════════ -->
  <aside id="sidebar">

    <!-- Logo -->
    <div style="height:64px;display:flex;align-items:center;gap:12px;padding:0 20px;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
      <div style="width:32px;height:32px;border-radius:8px;background:#111827;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <iconify-icon icon="solar:printer-2-bold" style="color:#fff;font-size:14px;"></iconify-icon>
      </div>
      <div style="min-width:0;">
        <p style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:-.02em;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
          <?= e(get_setting('site_name', $pdo) ?: 'Admin') ?>
        </p>
        <p style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;">Panel Admin</p>
      </div>
    </div>

    <!-- Nav -->
    <nav style="flex:1;overflow-y:auto;padding:16px 12px 8px;display:flex;flex-direction:column;gap:2px;">

      <p style="font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:#94a3b8;padding:8px 12px 4px;">Utama</p>

      <a href="<?= BASE_URL ?>/admin/index.php"
         class="<?= _nav('admin','index.php') ?>"
         style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;text-decoration:none;transition:all .15s;">
        <iconify-icon icon="solar:home-2-linear" style="font-size:18px;flex-shrink:0;"></iconify-icon>
        Dashboard
      </a>

      <a href="<?= BASE_URL ?>/admin/inbox/index.php"
         class="<?= _nav('inbox') ?>"
         style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;text-decoration:none;transition:all .15s;">
        <iconify-icon icon="solar:inbox-linear" style="font-size:18px;flex-shrink:0;"></iconify-icon>
        Inbox
        <?php if ($_unread > 0): ?>
        <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:10px;font-weight:700;border-radius:999px;padding:0 6px;line-height:20px;">
          <?= $_unread ?>
        </span>
        <?php endif; ?>
      </a>

      <p style="font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:#94a3b8;padding:16px 12px 4px;">Konten</p>

      <a href="<?= BASE_URL ?>/admin/articles/index.php"
         class="<?= _nav('articles','index.php') ?>"
         style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;text-decoration:none;transition:all .15s;">
        <iconify-icon icon="solar:document-text-linear" style="font-size:18px;flex-shrink:0;"></iconify-icon>
        Artikel
      </a>

      <a href="<?= BASE_URL ?>/admin/articles/create.php"
         class="<?= _nav('articles','create.php') ?>"
         style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;text-decoration:none;transition:all .15s;">
        <iconify-icon icon="solar:add-circle-linear" style="font-size:18px;flex-shrink:0;"></iconify-icon>
        Tambah Artikel
      </a>

      <a href="<?= BASE_URL ?>/admin/services/index.php"
         class="<?= _nav('services') ?>"
         style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;text-decoration:none;transition:all .15s;">
        <iconify-icon icon="solar:printer-2-linear" style="font-size:18px;flex-shrink:0;"></iconify-icon>
        Layanan
      </a>

      <p style="font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:#94a3b8;padding:16px 12px 4px;">Sistem</p>

      <a href="<?= BASE_URL ?>/admin/settings/index.php"
         class="<?= _nav('settings') ?>"
         style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;font-size:14px;text-decoration:none;transition:all .15s;">
        <iconify-icon icon="solar:settings-linear" style="font-size:18px;flex-shrink:0;"></iconify-icon>
        Pengaturan Situs
      </a>

    </nav>

    <!-- User footer -->
    <div style="padding:12px;border-top:1px solid #e2e8f0;flex-shrink:0;">
      <div style="display:flex;align-items:center;gap:10px;background:#f8fafc;border-radius:12px;padding:10px 12px;">
        <div style="width:32px;height:32px;border-radius:50%;background:#111827;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0;">
          <?= strtoupper(substr($_admin_name, 0, 1)) ?>
        </div>
        <div style="flex:1;min-width:0;">
          <p style="font-size:12px;font-weight:600;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($_admin_name) ?></p>
          <p style="font-size:10px;color:#94a3b8;">Administrator</p>
        </div>
        <a href="<?= BASE_URL ?>/admin/logout.php" title="Logout"
           style="color:#94a3b8;text-decoration:none;flex-shrink:0;transition:color .15s;"
           onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
          <iconify-icon icon="solar:logout-2-linear" style="font-size:18px;"></iconify-icon>
        </a>
      </div>
      <a href="<?= BASE_URL ?>/" target="_blank"
         style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:12px;font-size:12px;color:#64748b;text-decoration:none;margin-top:4px;transition:background .15s;"
         onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
        <iconify-icon icon="solar:arrow-right-up-linear" style="font-size:14px;"></iconify-icon>
        Lihat Website
      </a>
    </div>

  </aside>

  <!-- Mobile overlay -->
  <div id="sidebar-overlay" onclick="sidebarClose()"></div>

  <!-- ═══════════════════ MAIN AREA ═══════════════════ -->
  <div id="main-col">

    <!-- Topbar -->
    <div id="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button onclick="sidebarOpen()"
          style="display:none;background:none;border:none;cursor:pointer;color:#64748b;padding:4px;"
          id="hamburger">
          <iconify-icon icon="solar:hamburger-menu-linear" style="font-size:20px;"></iconify-icon>
        </button>
        <div>
          <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">
            <?= isset($page_title) ? e($page_title) : 'Dashboard' ?>
          </p>
          <?php if (isset($breadcrumb)): ?>
          <p style="font-size:12px;color:#94a3b8;margin:0;"><?= e($breadcrumb) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <?php if ($_unread > 0): ?>
        <a href="<?= BASE_URL ?>/admin/inbox/index.php"
           style="position:relative;color:#64748b;text-decoration:none;">
          <iconify-icon icon="solar:bell-linear" style="font-size:20px;"></iconify-icon>
          <span style="position:absolute;top:-4px;right:-4px;width:16px;height:16px;background:#ef4444;border-radius:50%;color:#fff;font-size:9px;display:flex;align-items:center;justify-content:center;font-weight:700;">
            <?= $_unread ?>
          </span>
        </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/admin/articles/create.php"
           style="display:flex;align-items:center;gap:6px;background:#111827;color:#fff;font-size:12px;text-transform:uppercase;letter-spacing:.08em;padding:8px 16px;border-radius:10px;text-decoration:none;transition:background .15s;"
           onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#111827'">
          <iconify-icon icon="solar:add-circle-linear" style="font-size:14px;"></iconify-icon>
          Artikel Baru
        </a>
      </div>
    </div>

    <!-- ↓ Page content — THIS is the scroll container -->
    <div id="page-content">