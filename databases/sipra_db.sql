-- Create database if not exists
CREATE DATABASE IF NOT EXISTS sipra_db;
USE sipra_db;

-- Table for users
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for mata kuliah (courses)
CREATE TABLE IF NOT EXISTS mata_kuliah (
    mk_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    kode_mk VARCHAR(20) NOT NULL,
    nama_mk VARCHAR(100) NOT NULL,
    sks INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table for tugas (assignments)
CREATE TABLE IF NOT EXISTS tugas (
    tugas_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    mk_id INT NOT NULL,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    deadline DATETIME NOT NULL,
    kategori ENUM('high', 'medium', 'easy') NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (mk_id) REFERENCES mata_kuliah(mk_id) ON DELETE CASCADE
);

-- Table for jadwal kuliah (class schedules)
CREATE TABLE IF NOT EXISTS jadwal_kuliah (
    jadwal_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    mk_id INT NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    lokasi VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (mk_id) REFERENCES mata_kuliah(mk_id) ON DELETE CASCADE
);

-- Table for pengaturan (settings)
CREATE TABLE IF NOT EXISTS pengaturan (
    pengaturan_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    tema ENUM('light', 'dark') DEFAULT 'light',
    bahasa ENUM('id', 'en') DEFAULT 'id',
    notifikasi_email BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert sample user for testing
INSERT INTO users (username, password, email, full_name) VALUES
('mahasiswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa1@example.com', 'Mahasiswa Satu');

-- Insert sample mata kuliah for the user
INSERT INTO mata_kuliah (user_id, kode_mk, nama_mk, sks, semester) VALUES
(1, 'CS101', 'Pemrograman Dasar', 3, 'Ganjil'),
(1, 'CS102', 'Algoritma dan Struktur Data', 4, 'Ganjil'),
(1, 'CS201', 'Basis Data', 3, 'Genap'),
(1, 'CS202', 'Pemrograman Web', 3, 'Genap');

-- Insert sample tugas
INSERT INTO tugas (user_id, mk_id, judul, deskripsi, deadline, kategori) VALUES
(1, 1, 'Tugas 1 Pemrograman', 'Membuat program sederhana dengan bahasa C', '2025-05-10 23:59:59', 'medium'),
(1, 2, 'Implementasi Linked List', 'Implementasi single linked list dengan Java', '2025-05-15 23:59:59', 'high'),
(1, 3, 'ERD Sistem Akademik', 'Membuat ERD untuk sistem akademik', '2025-05-20 23:59:59', 'medium'),
(1, 4, 'Website Portofolio', 'Membuat website portofolio pribadi dengan HTML, CSS, dan JS', '2025-05-25 23:59:59', 'high');

-- Insert sample jadwal kuliah
INSERT INTO jadwal_kuliah (user_id, mk_id, hari, jam_mulai, jam_selesai, lokasi) VALUES
(1, 1, 'Senin', '08:00:00', '09:40:00', 'Ruang 101'),
(1, 2, 'Selasa', '10:00:00', '11:40:00', 'Ruang 202'),
(1, 3, 'Rabu', '13:00:00', '14:40:00', 'Lab Komputer 1'),
(1, 4, 'Kamis', '15:00:00', '16:40:00', 'Lab Komputer 2');

-- Insert setting for user
INSERT INTO pengaturan (user_id, tema, bahasa, notifikasi_email) VALUES
(1, 'light', 'id', TRUE);