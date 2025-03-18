<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Selamat datang, <?php echo $_SESSION['username']; ?>!</h2>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-gray-900 to-gray-800 text-white text-center font-montserrat">

    <div class="bg-white bg-opacity-10 p-10 rounded-lg shadow-lg w-1/2 mx-auto mt-10 transition-all duration-300">
        <h2 class="text-3xl font-bold text-yellow-400">Tambahkan Produk</h2>

        <input type="text" id="nama" class="w-full p-3 my-3 rounded-md text-lg text-gray-900" placeholder="Nama Produk" required>
        <input type="number" id="harga" class="w-full p-3 my-3 rounded-md text-lg text-gray-900" placeholder="Harga" required>

        <button id="btnTambah" class="bg-green-600 hover:bg-green-700 text-white font-semibold text-lg py-3 px-5 rounded-lg transition-all duration-300 transform hover:scale-110">
            Tambah Produk
        </button>
        <button id="btnSimpan" class="bg-green-500 text-white py-2 px-4 rounded-md" style="display: none;">Simpan</button>

        <h3 class="text-2xl font-semibold mt-5 text-yellow-300">Daftar Produk</h3>

        <table class="w-full mt-5 bg-white bg-opacity-10 rounded-lg">
            <thead>
                <tr class="bg-yellow-500 text-black">
                    <th class="py-3">Nama Produk</th>
                    <th class="py-3">Harga</th>
                    <th class="py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelProduk"></tbody>
        </table>
    </div>

    <script>
        function loadProduk() {
            fetch("../Manajemenbrg/read.php")
                .then(response => response.json())
                .then(data => {
                    console.log("Data produk yang diterima:", data); // Debugging

                    let rows = "";
                    data.forEach((produk) => {
                        rows += `
                            <tr id="produk-${produk.id}" class="border-b border-white">
                                <td class="py-3">${produk.nama}</td>
                                <td class="py-3">${produk.harga}</td>
                                <td class="py-3">
                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-md transition-all" 
                                        onclick="editProduk(${produk.id}, '${produk.nama}', ${produk.harga})">
                                        Edit
                                    </button>
                                    <button class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition-all" 
                                        onclick="hapusProduk(${produk.id})">
                                        Hapus
                                    </button>
                                </td>
                            </tr>`;
                    });

                    document.getElementById("tabelProduk").innerHTML = rows;
                })
                .catch(error => console.error("Gagal memuat produk:", error));
        }

        // Tombol Tambah Produk
        document.getElementById("btnTambah").addEventListener("click", function() {
            let nama = document.getElementById("nama").value.trim();
            let harga = document.getElementById("harga").value.trim();

            if (nama === "" || harga === "") {
                alert("Nama produk dan harga harus diisi!");
                return;
            }

            fetch("../Manajemenbrg/create.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `nama=${encodeURIComponent(nama)}&harga=${encodeURIComponent(harga)}`
            })
            .then(response => response.text())
            .then(result => {
                console.log("Response dari server:", result);
                alert(result);
                loadProduk();
                resetForm(); // Reset form setelah input
            })
            .catch(error => console.error("Gagal mengirim data:", error));
        });

        // Tombol Simpan (Untuk Update)
        document.getElementById("btnSimpan").addEventListener("click", function() {
            let nama = document.getElementById("nama").value.trim();
            let harga = document.getElementById("harga").value.trim();
            let idProduk = document.getElementById("btnSimpan").getAttribute("data-id");

            if (nama === "" || harga === "") {
                alert("Nama produk dan harga harus diisi!");
                return;
            }

            fetch("../Manajemenbrg/update.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${idProduk}&nama=${encodeURIComponent(nama)}&harga=${encodeURIComponent(harga)}`
            })
            .then(response => response.text())
            .then(result => {
                console.log("Response dari server:", result);
                alert(result);
                loadProduk();
                resetForm(); // Reset form setelah edit
            })
            .catch(error => console.error("Gagal mengirim data:", error));
        });

        // Fungsi Edit: Isi form dan tampilkan tombol Simpan
        function editProduk(id, nama, harga) {
            console.log("Edit produk:", id, nama, harga);

            document.getElementById("nama").value = nama;
            document.getElementById("harga").value = harga;
            document.getElementById("btnSimpan").setAttribute("data-id", id);

            document.getElementById("btnTambah").style.display = "none"; // Sembunyikan tombol Tambah
            document.getElementById("btnSimpan").style.display = "inline-block"; // Munculkan tombol Simpan
        }

        // Fungsi Reset Form
        function resetForm() {
            document.getElementById("nama").value = "";
            document.getElementById("harga").value = "";

            document.getElementById("btnSimpan").removeAttribute("data-id");
            document.getElementById("btnSimpan").style.display = "none"; // Sembunyikan tombol Simpan
            document.getElementById("btnTambah").style.display = "inline-block"; // Tampilkan kembali tombol Tambah
        }

        function hapusProduk(id) {
            if (confirm("Yakin ingin menghapus produk ini?")) {
                fetch("../Manajemenbrg/delete.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id=${id}`
                }).then(response => response.text())
                  .then(alert)
                  .then(loadProduk);
            }
        }

        function editProduk(id, nama, harga) {
            console.log("Edit produk:", id, nama, harga); // Debugging

            // Set nilai input utama dengan data produk yang dipilih
            document.getElementById("nama").value = nama;
            document.getElementById("harga").value = harga;

            // Simpan ID produk yang sedang diedit agar bisa disimpan nanti
            document.getElementById("btnTambah").setAttribute("onclick", `simpanEdit(${id})`);
        }

        function simpanEdit(id) {
            let namaBaru = document.getElementById("nama").value;
            let hargaBaru = document.getElementById("harga").value;

            console.log("Simpan perubahan:", id, namaBaru, hargaBaru); // Debugging

            fetch("../Manajemenbrg/update.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${id}&nama=${encodeURIComponent(namaBaru)}&harga=${encodeURIComponent(hargaBaru)}`
            }).then(response => response.text())
            .then(response => {
                console.log("Respon dari server:", response);
                alert(response);
                loadProduk(); // Reload daftar produk
                document.getElementById("nama").value = ""; // Reset form
                document.getElementById("harga").value = "";
                document.getElementById("btnTambah").setAttribute("onclick", "tambahProduk()"); // Balik fungsi tambah
            })
            .catch(error => console.error("Gagal menyimpan produk:", error));
        }

        document.addEventListener("DOMContentLoaded", loadProduk);
    </script>
    <div class="flex justify-end p-5">
    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white py-2 px-5 rounded-lg transition-all">
        Logout
    </a>
</div>
</body>
</html>
