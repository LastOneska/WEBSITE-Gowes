<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-about">
            <a class="brand brand-light" href="<?= h($frontendBase) ?>/index.php">
                <span class="brand-mark"><i class="bi bi-bicycle" aria-hidden="true"></i></span>
                <span>gowes<span class="brand-dot">.</span>id</span>
            </a>
            <p>Perjalanan dekat di Bandung jadi lebih sederhana. Ambil kendaraan, lalu nikmati perjalananmu.</p>
        </div>
        <div>
            <h2 class="footer-title">Jelajahi</h2>
            <a href="<?= h($frontendBase) ?>/kendaraan.php">Semua kendaraan</a>
            <a href="<?= h($frontendBase) ?>/index.php#cara-sewa">Cara menyewa</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="<?= h($frontendBase) ?>/riwayat.php">Riwayat sewa</a>
            <?php endif; ?>
        </div>
        <div>
            <h2 class="footer-title">Temukan kami</h2>
            <p>Bandung, Jawa Barat</p>
            <a href="mailto:sapa@gowes.id">sapa@gowes.id</a>
            <p>Setiap hari · 07.00–21.00 WIB</p>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>&copy; <?= date('Y') ?> Gowes.id</span>
        <span>Dibuat untuk perjalanan yang lebih dekat.</span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= h($frontendBase) ?>/js/script.js"></script>
</body>
</html>
