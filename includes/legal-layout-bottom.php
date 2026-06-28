</main>

<footer class="ai-footer">
  <div class="ai-footer-brand"><img src="<?php echo e(url('assets/img/logo.png')); ?>" alt="Access Infra"></div>
  <nav class="ai-footer-links">
    <a href="<?php echo e(url('index.php')); ?>">Home</a>
    <a href="<?php echo e(url('about.php')); ?>">About Us</a>
    <a href="<?php echo e(url('government-departments.php')); ?>">Government Departments</a>
    <a href="<?php echo e(url('contact.php')); ?>">Contact</a>
    <a href="<?php echo e(url('privacy-policy.php')); ?>">Privacy Policy</a>
    <a href="<?php echo e(url('cookie-policy.php')); ?>">Cookie Policy</a>
  </nav>
  <p class="ai-footer-copy">&copy; <?php echo date('Y'); ?> Access Infra Consulting. All rights reserved.</p>
  <p class="ai-footer-credit">Developed and maintained by <a href="https://aqualeo.co" target="_blank" rel="noopener noreferrer">Aqualeo Digecom</a></p>
</footer>

<script>
(function() {
  var toggle = document.getElementById('navToggle');
  var links  = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function() { links.classList.toggle('open'); });
  }
})();
</script>
</body>
</html>
