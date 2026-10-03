# Simple POS - Laporan Praktikum Pertemuan 6

Repositori ini digunakan untuk pengerjaan tugas kelompok mata kuliah Pemrograman Web Lanjut. Berikut adalah penjelasan dan analisis terkait keamanan manipulasi total transaksi dari masing-masing anggota kelompok (Langkah 6 Poin 5):

---

### Analisis Keamanan Manipulasi Total Transaksi

* **Dian Paramitha:**
  

* **Nafisah Aliyah Khumaini:**
  

* **Agnes Titania Kinanti:**
  Tidak, validasi numeric saja belum cukup untuk mencegah manipulasi total transaksi. Validasi tersebut hanya memastikan nilai total yang dikirim berupa angka, tetapi pengguna masih bisa mengubah nilai tersebut sebelum dikirim ke server. Jadi, total transaksi tetap bisa dimanipulasi meskipun sudah divalidasi sebagai angka. Cara yang lebih aman adalah server menghitung ulang total berdasarkan harga produk yang tersimpan di database dan jumlah (qty) yang dikirim. Dengan begitu, total yang disimpan berasal dari perhitungan server dan tidak bergantung pada nilai total dari form.
  

* **Nety Sulistyorini:**
  

* **Pranata Putrandana:**
  Tidak, hal tersebut sama sekali tidak cukup untuk mencegah manipulasi. Validasi berbasis aturan numeric hanya berfungsi untuk memastikan bahwa format data yang dikirimkan berupa angka, bukan untuk menjamin keabsahan nominalnya.Data yang dikirimkan melalui form atau payload HTTP dari sisi klien sangat rentan direkayasa menggunakan browser developer tools atau alat eksekusi seperti cURL. Oleh karena itu, integritas sistem Point of Sale (POS) harus dijaga dengan cara mengabaikan total kiriman luar dan mewajibkan server untuk menghitung ulang seluruh subtotal serta total transaksi secara mutlak berdasarkan harga referensi yang valid di dalam database.