</div><!-- end #page-content -->
  </div><!-- end #main-col -->
</div><!-- end #shell -->

<script>
// ── Sidebar toggle (mobile) ───────────────────────────────────
function sidebarOpen() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebar-overlay').classList.add('show');
}
function sidebarClose() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('show');
}

// Show hamburger on small screens
(function() {
  var btn = document.getElementById('hamburger');
  function check() { btn.style.display = window.innerWidth < 1024 ? 'block' : 'none'; }
  check();
  window.addEventListener('resize', check);
})();

// ── Auto-hide flash messages after 3.5 s ─────────────────────
document.querySelectorAll('[data-autohide]').forEach(function(el) {
  setTimeout(function() {
    el.style.opacity = '0';
    setTimeout(function() { el.remove(); }, 400);
  }, 3500);
});
</script>
</body>
</html>