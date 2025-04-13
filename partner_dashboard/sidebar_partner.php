<?php
// sidebar_partner.php
?>
<div class="hidden md:block fixed top-0 left-0 h-full w-64 bg-gray-800 ...">
    <div class="p-6 text-center border-b border-gray-700">
        <h1 class="text-2xl font-bold text-green-400">Partner Panel</h1>
        <p class="text-sm text-gray-400 mt-1">DeskaLink</p>
    </div>
    <nav class="mt-4">
        <a href="dashboard_partner.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'dashboard_partner.php' ? 'bg-gray-700' : '' ?>">
            Dashboard
        </a>
        <a href="my_services.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'my_services.php' ? 'bg-gray-700' : '' ?>">
            Jasa Saya
        </a>
        <a href="my_designs.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'my_designs.php' ? 'bg-gray-700' : '' ?>">
            Desain Digital Saya
        </a>
        <a href="portfolio.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'bg-gray-700' : '' ?>">
            Portofolio
        </a>
        <a href="account_settings.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'account_settings.php' ? 'bg-gray-700' : '' ?>">
            Pengaturan Akun
        </a>
        <a href="../users/logout.php" class="block px-6 py-3 hover:bg-gray-700 border-t border-gray-700 mt-4">
            Keluar
        </a>
    </nav>
</div>
