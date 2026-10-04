# Simple POS - Laporan Praktikum Pertemuan 6

Repositori ini digunakan untuk pengerjaan tugas kelompok mata kuliah Pemrograman Web Lanjut. Berikut adalah penjelasan dan analisis terkait keamanan manipulasi total transaksi dari masing-masing anggota kelompok (Langkah 6 Poin 5):

---

### Analisis Keamanan Manipulasi Total Transaksi

* **Dian Paramitha:**
  

* **Nafisah Aliyah Khumaini:**
  Tidak cukup, karena validasi numeric hanya memastikan bahwa nilai total berupa angka, bukan memastikan bahwa nilainya benar. User masih dapat memanipulasi nilai total melalui request. Oleh karena itu, server sebaiknya menghitung ulang total berdasarkan data yang dikirim, seperti harga produk dan jumlah barang, kemudian menggunakan hasil perhitungan server sebagai nilai yang dipercaya.

* **Agnes Titania Kinanti:**
  Tidak, validasi numeric saja belum cukup untuk mencegah manipulasi total transaksi. Validasi tersebut hanya memastikan nilai total yang dikirim berupa angka, tetapi pengguna masih bisa mengubah nilai tersebut sebelum dikirim ke server. Jadi, total transaksi tetap bisa dimanipulasi meskipun sudah divalidasi sebagai angka. Cara yang lebih aman adalah server menghitung ulang total berdasarkan harga produk yang tersimpan di database dan jumlah (qty) yang dikirim. Dengan begitu, total yang disimpan berasal dari perhitungan server dan tidak bergantung pada nilai total dari form.
  

* **Nety Sulistyorini:**
Hal tersebut belum cukup untuk mencegah manipulasi total, semua itu disebabkan karena aturan numeric cuma memvalidasi bahwa total berbentuk angka, bukan bahwa angka itu benar. Sebab form berasal dari sisi klien, pengguna tetap bisa mengubah total melalui DevTools atau mengirim POST langsung dengan Postman/curl, contohnya menurunkannya jadi 1. Server yang hanya bergantung pada validasi ini akan menyimpan angka palsu tersebut. Maka dari itu, server harus menghitung sendiri total berdasarkan harga produk di database, bukan mengikuti input klien. Intinya: data dari klien tidak boleh dipercaya untuk hal kritikal seperti harga atau total.
  

* **Pranata Putrandana:**
  Tidak, hal tersebut sama sekali tidak cukup untuk mencegah manipulasi. Validasi berbasis aturan numeric hanya berfungsi untuk memastikan bahwa format data yang dikirimkan berupa angka, bukan untuk menjamin keabsahan nominalnya.Data yang dikirimkan melalui form atau payload HTTP dari sisi klien sangat rentan direkayasa menggunakan browser developer tools atau alat eksekusi seperti cURL. Oleh karena itu, integritas sistem Point of Sale (POS) harus dijaga dengan cara mengabaikan total kiriman luar dan mewajibkan server untuk menghitung ulang seluruh subtotal serta total transaksi secara mutlak berdasarkan harga referensi yang valid di dalam database.
