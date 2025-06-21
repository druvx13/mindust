</main> <!-- Closes <main class="admin-container"> from admin_header_inc.php -->

<footer class="text-center py-6 mt-8 text-sm text-gray-500 border-t border-slate-700">
    Mindust Content Management System &copy; <?= date("Y") ?>.
    All rights reserved (not really, it's GPL-3.0!).
    <p class="text-xs mt-1">Powered by PHP, Insomnia, and Existential Dread.</p>
</footer>

<?php // Pathing for assets/js/main.js needs to be context-aware if used from different depths ?>
<?php // Example: if admin files are in root, path is 'assets/js/main.js' ?>
<?php // If admin files were in admin/, path would be '../assets/js/main.js' ?>
<?php $basePath = (strpos($_SERVER['PHP_SELF'], 'admin_') === false ? '../' : ''); // Heuristic for path ?>
<script src="<?= $basePath . 'assets/js/main.js'; ?>"></script>
<?php // Add admin-specific JS file if needed: <script src="<?= $basePath . 'assets/js/admin_main.js'; ?>"></script> ?>

</body>
</html>
