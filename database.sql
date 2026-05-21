-- ============================================================
-- DATABASE: tb_sepatu
-- Jalankan file ini di phpMyAdmin atau MySQL Laragon
-- ============================================================

CREATE DATABASE IF NOT EXISTS `tb_sepatu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tb_sepatu`;

-- ----------------------------------------------------------
-- Tabel: kategori
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kategori` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100) NOT NULL,
  `deskripsi`  TEXT DEFAULT NULL,
  `dibuat_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`nama`, `deskripsi`) VALUES
  ('Basketball', 'Sepatu khusus olahraga basket'),
  ('Running',    'Sepatu lari performa tinggi'),
  ('Tenis',      'Sepatu untuk olahraga tenis'),
  ('Voley',      'Sepatu voli dengan grip kuat'),
  ('Style',      'Sepatu kasual dan lifestyle');

-- ----------------------------------------------------------
-- Tabel: produk
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produk` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`         VARCHAR(255) NOT NULL,
  `merek`        VARCHAR(100) NOT NULL,
  `deskripsi`    TEXT DEFAULT NULL,
  `harga`        DECIMAL(12,2) NOT NULL DEFAULT 0,
  `stok`         INT(11) NOT NULL DEFAULT 0,
  `gambar`       VARCHAR(255) DEFAULT NULL,
  `id_kategori`  INT(11) UNSIGNED DEFAULT NULL,
  `ukuran`       VARCHAR(255) DEFAULT NULL COMMENT 'Contoh: 38,39,40,41,42',
  `dibuat_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diubah_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_kategori` (`id_kategori`),
  CONSTRAINT `fk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk` (`nama`, `merek`, `deskripsi`, `harga`, `stok`, `gambar`, `id_kategori`, `ukuran`) VALUES
  ('Speedcat OG Unisex Lifestyle Shoes - Black',         'PUMA',    'Sepatu lifestyle ikonik dengan desain ramping.',          1899000, 25, 'Rectangle 18 (4).png', 5, '38,39,40,41,42'),
  ('Palermo Premium Unisex Lifestyle Shoes - Beige',     'PUMA',    'Sneaker klasik Palermo dengan bahan premium.',             1699000, 18, 'Rectangle 18 (5).png', 5, '38,39,40,41,42'),
  ('MB.03 Lo TMNT Krang Unisex Basketball Shoes',        'PUMA',    'Terinspirasi karakter TMNT Krang, desain futuristik.',     1999000, 15, 'Rectangle 18 (6).png', 1, '39,40,41,42,43'),
  ('MagMax NITRO Men Running Shoes',                     'PUMA',    'Sepatu lari dengan teknologi NITRO foam.',                 2699000, 12, 'Rectangle 18 (7).png', 2, '39,40,41,42,43'),
  ('V2K Run Women Sneakers Shoes - Photon Dust',         'NIKE',    'Desain retro futuristik untuk wanita aktif.',              1145400, 20, 'Rectangle 18 (8).png', 2, '36,37,38,39,40'),
  ('Old Skool Unisex Sneakers Shoes - BLACK',            'VANS',    'Ikon skate style dengan stripe samping khas Vans.',        999000,  30, 'Rectangle 18 (9).png', 5, '38,39,40,41,42'),
  ('Vomero 5 Boys Grade School Sneakers Shoes',          'NIKE',    'Sneaker chunky dengan bantalan Zoom Air.',                 1499000, 10, 'Rectangle 18 (10).png', 5, '35,36,37,38,39'),
  ('Samba OG Kids Play Sneakers - Preloved Brown',       'ADIDAS',  'Sepatu kasual anak dengan sol karet dan detail suede.',    850000,  22, 'Rectangle 18 (11).png', 5, '30,31,32,33,34');

-- ----------------------------------------------------------
-- Tabel: admin (login)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin` (
  `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- password: admin123 (bcrypt)
INSERT INTO `admin` (`username`, `password`) VALUES
  ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Catatan: password default = "password"
-- Ganti dengan: password_hash('admin123', PASSWORD_BCRYPT)
