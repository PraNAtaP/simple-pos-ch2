# Pengukuran Memori: get() vs lazyById()

Dataset: sekitar 52.500 row pada tabel `transactions`.

| Cara baca data | Peak memory (MB) |
|---|---:|
| `Transaction::lazyById()` | 70.0 |
| `Transaction::get()` | 128.3 |

**Kesimpulan kelompok:**

Berdasarkan hasil pengukuran terhadap 52.501 row, metode `lazyById()` menggunakan peak memory sebesar 70.0 MB, sedangkan metode `get()` menggunakan 128.3 MB. Metode `lazyById()` menghemat penggunaan peak memory sebesar 58.3 MB atau sekitar 45.4% dibandingkan metode `get()`. Hal ini karena `lazyById()` membaca data secara bertahap, sedangkan `get()` memuat seluruh hasil query ke dalam memori sekaligus. Dengan demikian, `lazyById()` lebih hemat memori untuk membaca data transaksi dalam jumlah besar pada pengujian ini.