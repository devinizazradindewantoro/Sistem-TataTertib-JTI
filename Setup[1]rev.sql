-- Create database
CREATE DATABASE pelapor;
GO

USE pelapor;
GO

---------------------------------------------------------------------
-- Create Schemas
---------------------------------------------------------------------

CREATE SCHEMA Civitas AUTHORIZATION dbo;
GO
CREATE SCHEMA Tatib AUTHORIZATION dbo;
GO

---------------------------------------------------------------------
-- Create Tables
---------------------------------------------------------------------

-- Create table Civitas.Mahasiswa
CREATE TABLE Civitas.Mahasiswa
(
  id_mahasiswa   INT          NOT NULL IDENTITY,
  nim			 NVARCHAR(20) NOT NULL,
  password       NVARCHAR(10) NOT NULL,
  nama           NVARCHAR(100) NOT NULL,
  prodi			 NVARCHAR(25) NOT NULL,
  kelas			 NVARCHAR(10) NOT NULL,
  email          NVARCHAR(60) NOT NULL,
  no_hp_ortu     NVARCHAR(15) NOT NULL,
  alamat         NVARCHAR(100) NOT NULL,
  CONSTRAINT PK_Mahasiswa PRIMARY KEY(id_mahasiswa)
);

CREATE NONCLUSTERED INDEX idx_nc_prodi ON Civitas.Mahasiswa(prodi);
CREATE NONCLUSTERED INDEX idx_nc_kelas ON Civitas.Mahasiswa(kelas);

-- Create table Civitas.Dosen
CREATE TABLE Civitas.Dosen
(
  id_dosen	   INT          NOT NULL IDENTITY,
  nip		   NVARCHAR(20) NOT NULL,
  password     NVARCHAR(10) NOT NULL,
  nama         NVARCHAR(100) NOT NULL,
  email        NVARCHAR(60) NOT NULL,
  no_hp	       NVARCHAR(15) NOT NULL,
  CONSTRAINT PK_Dosen PRIMARY KEY(id_dosen)
);

CREATE NONCLUSTERED INDEX idx_nc_nama ON Civitas.Dosen(nama);

-- Create table Civitas.Dpa
CREATE TABLE Civitas.Dpa
(
  id_dpa   INT           NOT NULL IDENTITY,
  nama     NVARCHAR(100) NOT NULL,
  prodi	   NVARCHAR(25) NOT NULL,
  kelas	   NVARCHAR(10) NOT NULL,
  CONSTRAINT PK_Dpa PRIMARY KEY(id_dpa)
);

CREATE NONCLUSTERED INDEX idx_nc_prodi ON Civitas.Dpa(prodi);

-- Create table Civitas.Admin
CREATE TABLE Civitas.Admin
(
  id_admin   INT           NOT NULL IDENTITY,
  nama			NVARCHAR(100) NOT NULL,
  email			NVARCHAR(60) NOT NULL,
  password	    NVARCHAR(10) NOT NULL,
  status		NVARCHAR(60) NOT NULL,
  CONSTRAINT PK_Admin PRIMARY KEY(id_admin)
);

CREATE NONCLUSTERED INDEX idx_nc_status ON Civitas.Admin(status);

-- Create table Civitas.Komdis
CREATE TABLE Civitas.Komdis
(
  id_komdis VARCHAR(10) PRIMARY KEY,
  tingkat	VARCHAR(255),
  nama		VARCHAR(255)
);

-- Create table Tatib.Pelanggaran
CREATE TABLE Tatib.Pelanggaran
(
  id_pelanggaran VARCHAR(10) PRIMARY KEY,
  tingkat_pelanggaran INT,
    deskripsi TEXT
);

-- Tabel baru untuk laporan sementara dari dosen ke admin
CREATE TABLE Tatib.Pelaporan_Admin (
    id_pelaporan_admin INT IDENTITY(1,1) PRIMARY KEY,
    nim NVARCHAR(20), -- NIM mahasiswa yang dilaporkan
    nama_mahasiswa NVARCHAR(255), -- Nama mahasiswa yang dilaporkan
    tanggal DATE NOT NULL, -- Tanggal pelaporan
	deskripsi_pelanggaran NVARCHAR(250),
    tingkat_pelanggaran INT, -- Tingkat pelanggaran
    bukti NVARCHAR(255), -- Bukti pelanggaran
    id_dosen INT NOT NULL, -- ID dosen yang melaporkan
    status NVARCHAR(50) DEFAULT 'Pending', -- Status laporan (Pending/Approved/Rejected)
    id_admin INT, -- ID admin yang menangani
    CONSTRAINT FK_Pelaporan_Admin_Dosen FOREIGN KEY (id_dosen) REFERENCES Civitas.Dosen(id_dosen),
    CONSTRAINT FK_Pelaporan_Admin_Admin FOREIGN KEY (id_admin) REFERENCES Civitas.Admin(id_admin)
);

-- Modifikasi tabel Pelaporan agar hanya menyimpan laporan yang disetujui admin
CREATE TABLE Tatib.Pelaporan (
    id_laporan INT IDENTITY(1,1) PRIMARY KEY,
    nim NVARCHAR(20) NOT NULL, -- NIM mahasiswa
    nama_mahasiswa NVARCHAR(255) NOT NULL, -- Nama mahasiswa
    tanggal DATE NOT NULL, -- Tanggal laporan
	deskripsi_pelanggaran NVARCHAR(250),
    tingkat_pelanggaran INT NOT NULL, -- Tingkat pelanggaran
    bukti NVARCHAR(255) NOT NULL, -- Bukti pelanggaran
    id_dosen INT NOT NULL, -- ID dosen yang melaporkan
    id_admin INT NOT NULL, -- ID admin yang menyetujui
    CONSTRAINT FK_Pelaporan_Dosen FOREIGN KEY (id_dosen) REFERENCES Civitas.Dosen(id_dosen),
    CONSTRAINT FK_Pelaporan_Admin FOREIGN KEY (id_admin) REFERENCES Civitas.Admin(id_admin)
);

-- Modifikasi tabel Sanksi agar tetap relevan dengan perubahan
CREATE TABLE Tatib.Sanksi (
    id_sanksi NVARCHAR(10) PRIMARY KEY,
    id_laporan INT NOT NULL, -- ID laporan yang sudah disetujui
    tanggal DATE NOT NULL, -- Tanggal sanksi diberikan
    status NVARCHAR(50) NOT NULL, -- Status sanksi (e.g., "Diberikan", "Selesai")
    CONSTRAINT FK_Sanksi_Pelaporan FOREIGN KEY (id_laporan) REFERENCES Tatib.Pelaporan(id_laporan) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Riwayat Pelanggaran tetap, dengan data dari laporan yang sudah disetujui
CREATE TABLE Tatib.Riwayat_Pelanggaran (
    id_riwayat INT IDENTITY PRIMARY KEY,
    nim NVARCHAR(20) NOT NULL,
    nama_mahasiswa NVARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
	deskripsi_pelanggaran NVARCHAR(250),
    tingkat_pelanggaran NVARCHAR(50) NOT NULL,
    bukti NVARCHAR(255) NOT NULL,
    id_dosen INT NOT NULL, -- ID dosen yang melaporkan
    CONSTRAINT FK_Riwayat_Dosen FOREIGN KEY (id_dosen) REFERENCES Civitas.Dosen(id_dosen)
);

---------------------------------------------------------------------
-- Populate Tables
---------------------------------------------------------------------

SET NOCOUNT ON;

-- Populate table Civitas.Mahasiswa
SET IDENTITY_INSERT Civitas.Mahasiswa ON;
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(1, N'2341760050', '2341760050', N'Aditya Yuhanda Putra', N'Sistem Informasi Bisnis', N'2A', N'2341760050@polinema.ac.id', '089606775727', N'Bekasi');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(2, N'2341760146', '2341760146' , N'Aldo Febriansyah', N'Sistem Informasi Bisnis', N'2A', N'2341760146@polinema.ac.id', '081585712737', N'Tangerang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(3, N'2341760091', '2341760091', N'Aldo Khrisna Wijaya', N'Sistem Informasi Bisnis', N'2A', N'2341760091@polinema.ac.id', '089503593601', N'Tangerang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(4, N'2341760022', '2341760022' , N'Aqila Nur Azza',  N'Sistem Informasi Bisnis', N'2A', N'2341760022@polinema.ac.id' , '081230382286', N'Kediri');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(5, N'2341760016', '2341760016', N'Arimbi Putri Hapsari',  N'Sistem Informasi Bisnis', N'2A', N'2341760016@polinema.ac.id', '085812121478', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(6, N'2341760061', '2341760061', N'Athallah Ayudya Paramesti', N'Sistem Informasi Bisnis', N'2A', N'2341760061@polinema.ac.id', '085236798880', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(7, N'2341760073', '2341760073', N'Bayu Triwibowo', N'Sistem Informasi Bisnis', N'2A', N'2341760073@polinema.ac.id', '087850048885', N'Mojokerto');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(8, N'2341760008', '2341760008', N'Claudya Destine Julia Handoko', N'Sistem Informasi Bisnis', N'2A', N'2341760008@polinema.ac.id', '085732821808 ', N'Trenggalek');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(9, N'2341760023', '2341760023', N'Dahniar Davina', N'Sistem Informasi Bisnis', N'2A', N'2341760023@polinema.ac.id', '081252749394 ', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(10, N'2341760034', '2341760034', N'Devin I’zaz Radin Dewantoro', N'Sistem Informasi Bisnis', N'2A', N'2341760034@polinema.ac.id', '081335729933', N'Pasuruan');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(11, N'2341760070', '2341760070', N'Diajeng Sekar Arum', N'Sistem Informasi Bisnis', N'2A', N'2341760070@polinema.ac.id', '087851944523', N'Tulungagung');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(12, N'2341760105', '2341760105', N'Faiza Anathasya Eka Falen', N'Sistem Informasi Bisnis', N'2A', N'2341760105@polinema.ac.id', '081230948205', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(13, N'2341760140', '2341760140', N'Gegas Anughrah Derajat', N'Sistem Informasi Bisnis', N'2A', N'2341760140@polinema.ac.id', '081231808302', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(14, N'2341760154', '2341760154', N'Husein Fadhlullah', N'Sistem Informasi Bisnis', N'2A', N'2341760154@polinema.ac.id', '081333347104', N'Tulungagung');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(15, N'2341760118', '2341760118', N'Kanaya Abdielaramadhani hidayat', N'Sistem Informasi Bisnis', N'2A', N'2341760118@polinema.ac.id', '081335729933', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(16, N'2341760042', '2341760042', N'Karina Ika Indasa', N'Sistem Informasi Bisnis', N'2A', N'2341760042@polinema.ac.id', '082250042722', N'Blitar');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(17, N'2341760078', '2341760078', N'Khuzaima Filla Januartha', N'Sistem Informasi Bisnis', N'2A', N'2341760078@polinema.ac.id', '082132517964', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(18, N'2341760117', '2341760117', N'Louise Nazaroza', N'Sistem Informasi Bisnis', N'2A', N'23417600117@polinema.ac.id', '082334431621', N'Nganjuk');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(19, N'2341760013', '2341760013', N'Lyra Faiqah Bilqis', N'Sistem Informasi Bisnis', N'2A', N'2341760013@polinema.ac.id', '085655896780', N'Tulungagung');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(20, N'2341760094', '2341760094', N'Mochammad Audric Andhika Hidayatulloh', N'Sistem Informasi Bisnis', N'2A', N'2341760094@polinema.ac.id', '085812121478', N'Blitar');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(21, N'2341760190', '2341760190', N'Muhammad Hamdan Ubaidillah', N'Sistem Informasi Bisnis', N'2A', N'2341760190@polinema.ac.id', '089515954829', N'Kediri');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(22, N'2341760115', '2341760115', N'Muhammad Ircham Daffansyah Ismail', N'Sistem Informasi Bisnis', N'2A', N'2341760115@polinema.ac.id', '082145343418', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(23, N'2341760012', '2341760012', N'Muhammad Reishi Fauzi Auguri', N'Sistem Informasi Bisnis', N'2A', N'2341760012@polinema.ac.id', '085773071834', N'Bekasi');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(24, N'2341760063', '2341760063', N'Paudra Akbar Buana', N'Sistem Informasi Bisnis', N'2A', N'2341760063@polinema.ac.id', '089529017372', N'Kediri');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(25, N'2341760035', '2341760061', N'Qusnul Diah Mawanti', N'Sistem Informasi Bisnis', N'2A', N'2341760035@polinema.ac.id', '085234572917', N'Bojonegoro');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(26, N'2341760015', '2341760061', N'Reza Angelina Febrianti', N'Sistem Informasi Bisnis', N'2A', N'2341760015@polinema.ac.id', '081240749138', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(27, N'2341760042', '2341760042', N'Satria Rakhmadani', N'Sistem Informasi Bisnis', N'2A', N'2341760042@polinema.ac.id', '085335510121', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(28, N'2341760082', '2341760082', N'Vita Eka Saraswati', N'Sistem Informasi Bisnis', N'2A', N'2341760082@polinema.ac.id', '085730127441', N'Malang');
INSERT INTO Civitas.Mahasiswa(id_mahasiswa, nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES(29, N'2341760184', '2341760184', N'Yonanada Mayla Rusdiaty', N'Sistem Informasi Bisnis', N'2A', N'2341760184@polinema.ac.id', '082139631334', N'Jombang');
SET IDENTITY_INSERT Civitas.Mahasiswa OFF;

-- Populate table Civitas.Dosen
SET IDENTITY_INSERT Civitas.Dosen ON;
INSERT INTO Civitas.Dosen(id_dosen, nip, password, nama, email, no_hp)
  VALUES
  (1, '199006192019031017', 'loremipsum', N'Bagas Satya Dian Nugraha', N'bagasnugraha@polinema.ac.id', '08113344323'),
  (2, '198807112015042005', 'loremipsum', N'Eka Larasti Amelia', N'eka.larasti@polinema.ac.id', '081259668854'),
  (3, '199204122019031013', 'loremipsum', N'Habibie Ed Dien', N'habibie@polinema.ac.id', '08113200670'),
  (4, '198704242019032017', 'loremipsum', N'Meyti Eka Apriliyani', N'meytieka@polinema.ac.id', '081220096263');
SET IDENTITY_INSERT Civitas.Dosen OFF;

-- Populate table Civitas.Dpa
SET IDENTITY_INSERT Civitas.Dpa ON;
INSERT INTO Civitas.Dpa(id_dpa, nama, prodi, kelas)
  VALUES
  (1, N'Moch Zawawuddin Abdullah', N'Sistem Informsi Bisnis', '2A');
SET IDENTITY_INSERT Civitas.Dpa OFF;

-- Populate table Civitas.Admin
SET IDENTITY_INSERT Civitas.Admin ON;
INSERT INTO Civitas.Admin(id_admin, nama, email, password, status)
VALUES
  (1, N'Lailatul Qodriayah', N'lailatulQodriayah@gmail.com', 'loremipsum', N'Admin Akademik'),
  (2, N'Titis Octary Satrio', N'titisOctary@gmail.com', 'loremipsum', N'Admin Program Studi');
SET IDENTITY_INSERT Civitas.Admin OFF;

-- Populate table Civitas.Komdis
INSERT INTO Civitas.Komdis(id_komdis, tingkat, nama)
VALUES
('K001', 'ketua jurusan teknologi infoemasi', 'Bapak Rudy Ariyanto, ST, M.Cs.'),
('K002', 'sekertaris jurusan teknologi informasi', 'DR. Eng. Rosa Andrie Asmara, ST, MT.'),
('K003', 'ketua program studi sistem informasi bisnis', 'Hendra Pradibta, SE., M.Sc,');

-- Populate table Tatib.Pelanggaran
INSERT INTO Tatib.Pelanggaran(id_pelanggaran, tingkat_pelanggaran, deskripsi)
VALUES
('A01', 5, 'Berkomunikasi dengan tidak sopan, baik tertulis atau tidak tertulis kepada mahasiswa, dosen, karyawan, atau orang lain'),
('A02', 4, 'Berbusana tidak sopan dan tidak rapi. Yaitu antara lain adalah: berpakaian ketat, transparan, memakai t-shirt (baju kaos tidak berkerah), tank top, hipster, you can see, rok mini, backless, celana pendek, celana tiga per empat, legging, model celana atau baju koyak, sandal, sepatu sandal di lingkungan kampus'),
('A03', 4, 'Mahasiswa laki-laki berambut tidak rapi, gondrong yaitu panjang rambutnya melewati batas alis mata di bagian depan, telinga di bagian samping atau menyentuh kerah baju di bagian leher'),
('A04', 4, 'Mahasiswa berambut dengan model punk, dicat selain hitam dan/atau skinned.'),
('A05', 4, 'Makan, atau minum di dalam ruang kuliah/ laboratorium/ bengkel.'),
('A06', 3, 'Melanggar peraturan/ ketentuan yang berlaku di Polinema baik di Jurusan/ Program Studi'),
('A07', 3, 'Tidak menjaga kebersihan di seluruh area Polinema'),
('A08', 3, 'Membuat kegaduhan yang mengganggu pelaksanaan perkuliahan atau praktikum yang sedang berlangsung.'),
('A09', 3, 'Merokok di luar area kawasan merokok'),
('A10', 3, 'Bermain kartu, game online di area kampus'),
('A11', 3, 'Mengotori atau mencoret-coret meja, kursi, tembok, dan lain-lain di lingkungan Polinema'),
('A12', 3, 'Bertingkah laku kasar atau tidak sopan kepada mahasiswa, dosen, dan/atau karyawan'),
('A13', 2, 'Merusak sarana dan prasarana yang ada di area Polinema'),
('A14', 2, 'Tidak menjaga ketertiban dan keamanan di seluruh area Polinema (misalnya: parkir tidak pada tempatnya, konvoi selebrasi wisuda dll)'),
('A15', 2, 'Melakukan pengotoran/ pengrusakan barang milik orang lain termasuk milik Politeknik Negeri Malang'),
('A16', 2, 'Mengakses materi pornografi di kelas atau area kampus'),
('A17', 2, 'Membawa dan/atau menggunakan senjata tajam dan/atau senjata api untuk hal kriminal'),
('A18', 2, 'Melakukan perkelahian, serta membentuk geng/ kelompok yang bertujuan negatif'),
('A19', 2, 'Melakukan kegiatan politik praktis di dalam kampus'),
('A20', 2, 'Melakukan tindakan kekerasan atau perkelahian di dalam kampus.'),
('A21', 2, 'Melakukan penyalahgunaan identitas untuk perbuatan negatif'),
('A22', 2, 'Mengancam, baik tertulis atau tidak tertulis kepada mahasiswa, dosen, dan/atau karyawan'),
('A23', 2, 'Mencuri dalam bentuk apapun'),
('A24', 1, 'Melakukan kecurangan dalam bidang akademik, administratif, dan keuangan'),
('A25', 1, 'Melakukan pemerasan dan/atau penipuan'),
('A26', 1, 'Melakukan pelecehan dan/atau tindakan asusila dalam segala bentuk di dalam dan di luar kampus'),
('A27', 1, 'Berjudi, mengkonsumsi minum-minuman keras, dan/ atau bermabuk-mabukan di lingkungan dan di luar lingkungan Kampus Polinema'),
('A28', 1, 'Mengikuti organisasi dan atau menyebarkan faham-faham yang dilarang oleh Pemerintah.'),
('A29', 1, 'Melakukan pemalsuan data / dokumen / tanda tangan.'),
('A30', 1, 'Melakukan plagiasi(copy paste) dalam tugas-tugas atau karya ilmiah'),
('A31', 1, 'Tidak menjaga nama baik Polinema di masyarakat dan/ atau mencemarkan nama baik Polinema melalui media apapun'),
('A32', 1, 'Melakukan kegiatan atau sejenisnya yang dapat menurunkan kehormatan atau martabat Negara, Bangsa dan Polinema.'),
('A33', 1, 'Menggunakan barang-barang psikotropika dan/ atau zat-zat Adiktif lainnya'),
('A34', 1, 'Mengedarkan serta menjual barang-barang psikotropika dan/ atau zat-zat Adiktif lainnya'),
('A35', 1, 'Terlibat dalam tindakan kriminal dan dinyatakan bersalah oleh Pengadilan');

INSERT INTO Tatib.Pelaporan_Admin 
    (nim, nama_mahasiswa, tanggal, deskripsi_pelanggaran, tingkat_pelanggaran, bukti, id_dosen, status)
VALUES
    ('2341760070', 'Diajeng Sekar Arum', '2023-02-26', 'Deskripsi pelanggaran 1', 5, 'foto', 1, 'Pending'),
    ('2341760061', 'Athallah Ayudya Paramesti', '2023-03-24', 'Deskripsi pelanggaran 2', 4, 'video', 3, 'Pending'),
    ('2341760063', 'Paudra Akbar Buana', '2023-11-06', 'Deskripsi pelanggaran 3', 4, 'foto', 2, 'Pending'),
    ('2341760034', 'Devin I’zaz Radin Dewantoro', '2023-05-09', 'Deskripsi pelanggaran 4', 4, 'video', 3, 'Pending'),
    ('2341760117', 'Louise Nazaroza', '2023-11-06', 'Deskripsi pelanggaran 5', 4, 'foto', 1, 'Pending');


-- Insert data ke dalam tabel Tatib.Pelaporan tanpa menyertakan kolom id_laporan
INSERT INTO Tatib.Pelaporan(nim, nama_mahasiswa, tanggal, tingkat_pelanggaran, bukti, id_dosen)
VALUES
('2341760070', 'Diajeng Sekar Arum', '2023-02-26', 5, 'foto', '1'),
('2341760061', 'Athallah Ayudya Paramesti', '2023-03-24', 4, 'video', '3'),
('2341760063', 'Paudra Akbar Buana', '2023-11-06', 4, 'foto', '2'),
('2341760034', 'Devin I’zaz Radin Dewantoro', '2023-05-09', 4, 'video', '3'),
('2341760117', 'Louise Nazaroza', '2023-11-06', 4, 'foto', '1');


-- Insert data ke dalam tabel Tatib.Sanksi
INSERT INTO Tatib.Sanksi(id_sanksi, id_laporan, nim, nama_mahasiswa, tanggal, tingkat_pelanggaran, bukti, status)
VALUES
('B01', 1, '2341760070', 'Diajeng Sekar Arum', '2023-02-26', 5, 'foto', 'telah dikerjakan'),
('B002', 2, '2341760061', 'Athallah Ayudya Paramesti', '2023-03-24', 4, 'video', 'belum dikerjakan'),
('B003', 3, '2341760063', 'Paudra Akbar Buana', '2023-11-06', 4, 'foto', 'telah dikerjakan'),
('B004', 4, '2341760034', 'Devin I’zaz Radin Dewantoro', '2023-05-09', 4, 'video', 'belum dikerjakan'),
('B005', 5, '2341760117', 'Louise Nazaroza', '2023-11-06', 5, 'foto', 'telah dikerjakan');

-- Populate table Tatib.Riwayat_Pelanggaran

-- Mengaktifkan IDENTITY_INSERT untuk tabel yang benar
SET IDENTITY_INSERT Tatib.Riwayat_Pelanggaran ON;

-- Menyisipkan data ke tabel riwayat

INSERT INTO Tatib.Riwayat_Pelanggaran(nim, nama_mahasiswa, tanggal, deskripsi_pelanggaran, tingkat_pelanggaran, bukti, id_dosen)
VALUES
('2341760070', 'Diajeng Sekar Arum', '2023-02-26', 'Berkomunikasi dengan tidak sopan', '5', 'foto', 1),
('2341760061', 'Athallah Ayudya Paramesti', '2023-03-24', 'Berbusana tidak sopan dan tidak rapi', '4', 'video', 3),
('2341760063', 'Paudra Akbar Buana', '2023-11-06', 'Berbusana tidak sopan dan tidak rapi', '4', 'foto', 2),
('2341760034', 'Devin I’zaz Radin Dewantoro', '2023-07-02', 'Berbusana tidak sopan dan tidak rapi', '4', 'video', 3),
('2341760117', 'Louise Nazaroza', '2023-11-06', 'Berkomunikasi dengan tidak sopan', '5', 'foto', 1);


-- Menonaktifkan IDENTITY_INSERT setelah selesai
SET IDENTITY_INSERT Tatib.Riwayat_Pelanggaran OFF;


---------------------------------------------------------------------
-- menampilkan setiap tabel
---------------------------------------------------------------------
select * from Civitas.Komdis;
select * from Civitas.Admin;
select * from Civitas.Dosen;
select * from Civitas.Mahasiswa;
select * from Tatib.Pelanggaran;
select * from Tatib.Pelaporan_Admin;
select * from Tatib.Sanksi;
select * from Tatib.Pelaporan;
select * from Tatib.Riwayat_Pelanggaran;



---------------------------------------------------------------------
-- Create VIEW
---------------------------------------------------------------------

--view menampilkan data mahasiswa secara lengkap
CREATE VIEW v_TabelMahasiswa AS
SELECT 
    id_mahasiswa,
    nim,
    nama,
    prodi,
    kelas,
    email,
    no_hp_ortu,
    alamat
FROM Civitas.Mahasiswa;

--Menampilkan data dosen secara lengkap
CREATE VIEW v_TabelDosen AS
SELECT 
    id_dosen,
    nip,
    nama,
    email,
    no_hp
FROM Civitas.Dosen;

--Menampilkan data admin
CREATE VIEW v_TabelAdmin AS
SELECT 
    id_admin,
	nama,
	email,
	password,
	status
FROM Civitas.Admin;

--Menampilkan data DPA 
CREATE OR ALTER VIEW v_Dpa AS
SELECT 
    id_dpa,
    nama AS NamaDPA,
    prodi AS ProgramStudi,
    kelas AS KelasDPA
FROM Civitas.Dpa;

--Menampilkan data komdis 
CREATE VIEW v_KomdisCivitas AS
SELECT 
    id_komdis,
    tingkat,
    nama
FROM Civitas.Komdis;

--Menampilkan Pelanggaran
CREATE VIEW v_DataPelanggaran AS
SELECT 
    id_pelanggaran,
    tingkat_pelanggaran,
    deskripsi
FROM Tatib.Pelanggaran;

--Menampilkan pelaporan
CREATE VIEW v_PelaporanMahasiswa AS
SELECT 
    p.id_laporan,
    p.nim,
    m.nama AS nama_mahasiswa,
    p.tanggal,
    p.tingkat_pelanggaran,
    p.bukti
FROM Tatib.Pelaporan p
LEFT JOIN Civitas.Mahasiswa m ON p.nim = m.nim;


--Menampilkan sanksi
CREATE VIEW v_SanksiMahasiswa AS
SELECT 
    s.id_sanksi,
    s.id_laporan,
    p.nim,
    m.nama AS nama_mahasiswa,
    s.tanggal,
    s.tingkat_pelanggaran,
    s.bukti,
    s.status
FROM Tatib.Sanksi s
LEFT JOIN Tatib.Pelaporan p ON s.id_laporan = p.id_laporan
LEFT JOIN Civitas.Mahasiswa m ON p.nim = m.nim;

--Menampilkan riwayat pelaporan
CREATE VIEW v_RiwayatPelanggaran AS
SELECT 
    rp.id_riwayat,
    rp.nim,
    m.nama AS nama_mahasiswa,
    rp.tanggal,
    rp.jenis_pelanggaran,
    rp.frekuensi,
    rp.status
FROM Tatib.Riwayat_Pelanggaran rp
LEFT JOIN Civitas.Mahasiswa m ON rp.nim = m.nim;

--Menampilkan mahasiswa berdasarkan prodi dan kelas
CREATE VIEW v_MahasiswaProdiKelas AS
SELECT 
    prodi,
    kelas,
    COUNT(id_mahasiswa) AS jumlah_mahasiswa
FROM Civitas.Mahasiswa
GROUP BY prodi, kelas;

---------------------------------------------------------------------
-- Menampilkan hasil VIEW
---------------------------------------------------------------------
SELECT * FROM v_TabelMahasiswa;
SELECT * FROM v_TabelDosen;
SELECT * FROM v_Dpa;
SELECT * FROM v_TabelAdmin;
SELECT * FROM v_KomdisCivitas;
SELECT * FROM v_DataPelanggaran;
SELECT * FROM v_SanksiMahasiswa;
SELECT * FROM v_PelaporanMahasiswa;
SELECT * FROM v_RiwayatPelanggaran;
SELECT * FROM v_MahasiswaProdiKelas;

---------------------------------------------------------------------
-- Membuat TRIGGER
---------------------------------------------------------------------
CREATE TRIGGER SettingAdminId
ON Tatib.Pelaporan
AFTER INSERT
AS
BEGIN
    DECLARE @id_dosen INT;
    DECLARE @id_admin INT;

    -- Set admin id tetap ke 1
    SET @id_admin = 1; -- Admin tunggal yang menangani laporan

    -- Mengambil nilai id_dosen dari laporan yang baru saja dimasukkan
    SELECT @id_dosen = id_dosen FROM inserted;

    -- Memperbarui tabel Pelaporan dengan id_admin yang sesuai
    UPDATE Tatib.Pelaporan
    SET id_admin = @id_admin
    WHERE id_admin IS NULL;
END;

CREATE TRIGGER trg_CheckTingkatPelanggaran
ON Tatib.Pelanggaran
AFTER INSERT
AS
BEGIN
    IF EXISTS (
        SELECT 1
        FROM inserted
        WHERE tingkat_pelanggaran > 5
    )
    BEGIN
        RAISERROR ('Tingkat pelanggaran tidak boleh lebih dari 5.', 16, 1);
        ROLLBACK TRANSACTION;
    END
END;

CREATE TRIGGER trg_ApprovePelaporanAdmin
ON Tatib.Pelaporan_Admin
AFTER UPDATE
AS
BEGIN
    INSERT INTO Tatib.Pelaporan (nim, nama_mahasiswa, tanggal, tingkat_pelanggaran, bukti, id_dosen, id_admin)
    SELECT nim, nama_mahasiswa, tanggal, tingkat_pelanggaran, bukti, id_dosen, id_admin
    FROM inserted
    WHERE status = 'Approved';
    
    DELETE FROM Tatib.Pelaporan_Admin
    WHERE id_pelaporan_admin IN (SELECT id_pelaporan_admin FROM inserted WHERE status = 'Approved');
END;

---------------------------------------------------------------------
-- Menampilkan TRIGGER
---------------------------------------------------------------------
USE pelapor;
GO
SELECT *
FROM sys.objects
WHERE name = 'SettingAdminId';

USE pelapor;
GO
SELECT *
FROM sys.objects
WHERE name = 'trg_CheckTingkatPelanggaran';

USE pelapor;
GO
SELECT *
FROM sys.objects
WHERE name = 'trg_ApprovePelaporanAdmin';

--melihat seluruh trigger
SELECT name
FROM sys.triggers;


---------------------------------------------------------------------
-- Membuat STORED PROCEDURE
---------------------------------------------------------------------
--Stored Procedure untuk Menambahkan Mahasiswa
CREATE PROCEDURE sp_InsertMahasiswa
  @nim NVARCHAR(20),
  @password NVARCHAR(10),
  @nama NVARCHAR(100),
  @prodi NVARCHAR(25),
  @kelas NVARCHAR(10),
  @email NVARCHAR(60),
  @no_hp_ortu NVARCHAR(15),
  @alamat NVARCHAR(100)
AS
BEGIN
  INSERT INTO Civitas.Mahasiswa (nim, password, nama, prodi, kelas, email, no_hp_ortu, alamat)
  VALUES (@nim, @password, @nama, @prodi, @kelas, @email, @no_hp_ortu, @alamat);
END;

--Stored Procedure untuk Memperbarui Data Mahasiswa
CREATE PROCEDURE sp_UpdateMahasiswa
  @id_mahasiswa INT,
  @nim NVARCHAR(20),
  @password NVARCHAR(10),
  @nama NVARCHAR(100),
  @prodi NVARCHAR(25),
  @kelas NVARCHAR(10),
  @email NVARCHAR(60),
  @no_hp_ortu NVARCHAR(15),
  @alamat NVARCHAR(100)
AS
BEGIN
  UPDATE Civitas.Mahasiswa
  SET nim = @nim,
      password = @password,
      nama = @nama,
      prodi = @prodi,
      kelas = @kelas,
      email = @email,
      no_hp_ortu = @no_hp_ortu,
      alamat = @alamat
  WHERE id_mahasiswa = @id_mahasiswa;
END;

--menghapus data mahasiswa
CREATE PROCEDURE sp_DeleteMahasiswa
  @id_mahasiswa INT
AS
BEGIN
  DELETE FROM Civitas.Mahasiswa
  WHERE id_mahasiswa = @id_mahasiswa;
END;

--melihat riwayat laporan berdasarkan nim
CREATE PROCEDURE sp_GetRiwayatPelanggaranByNIM
  @nim NVARCHAR(20)
AS
BEGIN
  SELECT id_riwayat, nim, nama_mahasiswa, tanggal, tingkat_pelanggaran, bukti, id_dosen
  FROM Tatib.Riwayat_Pelanggaran
  WHERE nim = @nim;
END;

--menambahkan dosen
CREATE PROCEDURE sp_InsertDosen
  @nip NVARCHAR(20),
  @password NVARCHAR(10),
  @nama NVARCHAR(100),
  @email NVARCHAR(60),
  @no_hp NVARCHAR(15)
AS
BEGIN
  INSERT INTO Civitas.Dosen (nip, password, nama, email, no_hp)
  VALUES (@nip, @password, @nama, @email, @no_hp);
END;

---------------------------------------------------------------------
-- Menampilkan STORED PROCEDURE
---------------------------------------------------------------------
EXEC sp_GetRiwayatPelanggaranByNIM
  @nim = '2341760070';

--menampilkan seluruh stored procedure
SELECT name
FROM sys.objects
WHERE type = 'P';

DROP TABLE Civitas.Mahasiswa;
DROP TABLE Civitas.Dosen;
DROP TABLE Civitas.Dpa;
DROP TABLE Civitas.Admin;
DROP TABLE Civitas.Komdis;
DROP TABLE Tatib.Pelanggaran;
DROP TABLE Tatib.Pelaporan_Admin;
DROP TABLE Tatib.Pelaporan;
DROP TABLE Tatib.Sanksi;
DROP TABLE Tatib.Riwayat_Pelanggaran;






