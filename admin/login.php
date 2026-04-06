<?php
require_once __DIR__ . '/bootstrap.php';

// Already logged in → go to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['name'];
        header('Location: ' . BASE_URL . '/admin/index.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <style>body{font-family:'Nunito',sans-serif;}</style>
</head>
<body class="min-h-screen bg-[#f8fafc] flex items-center justify-center p-4">

  <div class="fixed inset-0 pointer-events-none"
    style="background-image:linear-gradient(rgba(31,41,55,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(31,41,55,.03) 1px,transparent 1px);background-size:2rem 2rem;">
  </div>

  <div class="w-full max-w-sm relative z-10">

    <!-- Logo -->
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-[#111827] flex items-center justify-center mx-auto mb-4 shadow-lg">
        <iconify-icon icon="solar:printer-2-bold" class="text-white text-2xl"></iconify-icon>
      </div>
      <h1 class="text-xl font-bold text-[#111827] uppercase tracking-tight">Admin Panel</h1>
      <p class="text-sm text-[#94a3b8] mt-1">Masuk untuk mengelola konten</p>
    </div>

    <div class="bg-white rounded-2xl border border-[#e2e8f0] shadow-lg p-8">

      <?php if ($error): ?>
      <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 flex items-center gap-3">
        <iconify-icon icon="solar:danger-circle-linear" class="text-red-500 text-lg flex-shrink-0"></iconify-icon>
        <p class="text-sm text-red-600"><?= e($error) ?></p>
      </div>
      <?php endif; ?>

      <form method="POST" class="space-y-4">

        <div>
          <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Username</label>
          <div class="relative">
            <iconify-icon icon="solar:user-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94a3b8] text-lg pointer-events-none"></iconify-icon>
            <input type="text" name="username" autofocus autocomplete="username"
              value="<?= e($_POST['username'] ?? '') ?>"
              placeholder="Masukkan username"
              class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#111827] placeholder:text-[#cbd5e1] outline-none focus:border-[#2563eb] focus:bg-white transition-all">
          </div>
        </div>

        <div>
          <label class="block text-xs uppercase tracking-widest text-[#64748b] mb-2">Password</label>
          <div class="relative">
            <iconify-icon icon="solar:lock-password-linear" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94a3b8] text-lg pointer-events-none"></iconify-icon>
            <input type="password" name="password" id="pw" autocomplete="current-password"
              placeholder="••••••••"
              class="w-full pl-10 pr-11 py-3 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-sm text-[#111827] placeholder:text-[#cbd5e1] outline-none focus:border-[#2563eb] focus:bg-white transition-all">
            <button type="button" id="pw-toggle"
              class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#94a3b8] hover:text-[#64748b] transition-colors">
              <iconify-icon icon="solar:eye-linear" class="text-lg"></iconify-icon>
            </button>
          </div>
        </div>

        <button type="submit"
          class="w-full mt-2 bg-[#111827] text-white py-3 rounded-xl text-sm uppercase tracking-widest font-semibold hover:bg-[#2563eb] transition-all flex items-center justify-center gap-2">
          <iconify-icon icon="solar:login-2-linear" class="text-base"></iconify-icon>
          Masuk
        </button>
      </form>
    </div>

    <p class="text-center text-xs text-[#94a3b8] mt-6">
      <a href="<?= BASE_URL ?>/" class="hover:text-[#2563eb] transition-colors">← Kembali ke Website</a>
    </p>
  </div>

  <script>
  document.getElementById('pw-toggle').addEventListener('click', function() {
    const pw  = document.getElementById('pw');
    const ico = this.querySelector('iconify-icon');
    const show = pw.type === 'password';
    pw.type = show ? 'text' : 'password';
    ico.setAttribute('icon', show ? 'solar:eye-closed-linear' : 'solar:eye-linear');
  });
  </script>
</body>
</html>