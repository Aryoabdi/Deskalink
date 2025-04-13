<aside class="hidden md:block fixed top-0 left-0 h-full w-64 bg-gray-800 text-white shadow-lg z-40">
    <div class="p-6 text-center border-b border-gray-700">
        <h1 class="text-2xl font-bold text-green-400">DeskaLink Admin</h1>
        <p class="text-sm text-gray-400 mt-1">Panel Admin</p>
    </div>
    <nav class="mt-4">
        <a href="index.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-gray-700' : '' ?>">
            Dashboard
        </a>
        <a href="manage_users.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'bg-gray-700' : '' ?>">
            Manajemen Pengguna
        </a>
        <a href="manage_portfolios.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'manage_portfolios.php' ? 'bg-gray-700' : '' ?>">
            Manajemen Portofolio
        </a>
        <a href="manage_contents.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'manage_contents.php' ? 'bg-gray-700' : '' ?>">
            Manajemen Jasa & Desain
        </a>
        <a href="transactions.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'bg-gray-700' : '' ?>">
            Transaksi
        </a>
        <a href="reports.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-gray-700' : '' ?>">
            Laporan & Pengaduan
        </a>
        <a href="settings.php" class="block px-6 py-3 hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-gray-700' : '' ?>">
            Pengaturan
        </a>
        <a href="../users/logout.php" class="block px-6 py-3 hover:bg-gray-700 border-t border-gray-700 mt-4">
            Keluar
        </a>
    </nav>
</aside>
