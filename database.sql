-- Database: pkl_system
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS pkl_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pkl_system;

-- Table: admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (username: admin, password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Table: laporan_pkl
CREATE TABLE IF NOT EXISTS laporan_pkl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    nis VARCHAR(20) NOT NULL,
    kelas_jurusan VARCHAR(50) NOT NULL,
    nama_sekolah VARCHAR(100) NOT NULL,
    tempat_pkl VARCHAR(100) NOT NULL,
    tanggal_pkl DATE NOT NULL,
    file_laporan VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: progres_pkl
CREATE TABLE IF NOT EXISTS progres_pkl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul_progres VARCHAR(200) NOT NULL,
    minggu_ke VARCHAR(20) NOT NULL,
    deskripsi TEXT NOT NULL,
    file_foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample progress data
INSERT INTO progres_pkl (judul_progres, minggu_ke, deskripsi) VALUES 
('Orientasi dan Pengenalan Lingkungan Kerja', 'Minggu ke-1', 'Siswa melakukan orientasi di tempat PKL, mengenal lingkungan kerja, dan bertemu dengan pembimbing lapangan.'),
('Pelatihan Dasar dan Pengenalan Tugas', 'Minggu ke-2', 'Siswa mendapatkan pelatihan dasar terkait pekerjaan yang akan dilakukan selama PKL.');