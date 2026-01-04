</div> <!-- Close container from header -->

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">
                <i class="fas fa-graduation-cap"></i> Student Assessment Quiz System &copy; <?php echo date('Y'); ?>
            </p>
            <p class="mb-0">
                <small>Built with PHP, MySQL & Bootstrap</small>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS (if needed) -->
    <?php if (isset($include_timer) && $include_timer): ?>
        <script src="../assets/js/timer.js"></script>
    <?php endif; ?>
</body>
</html>