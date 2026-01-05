<?php
require_once 'config/database.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $nis = trim($_POST['nis'] ?? '');
    $kelas_jurusan = trim($_POST['kelas_jurusan'] ?? '');
    $nama_sekolah = trim($_POST['nama_sekolah'] ?? '');
    $tempat_pkl = trim($_POST['tempat_pkl'] ?? '');
    $tanggal_pkl = trim($_POST['tanggal_pkl'] ?? '');

    // Validate all fields are filled
    if (empty($nama_lengkap) || empty($nis) || empty($kelas_jurusan) || 
        empty($nama_sekolah) || empty($tempat_pkl) || empty($tanggal_pkl)) {
        $error_message = 'Semua field wajib diisi!';
    } else {
        // Handle file upload
        if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file_laporan'];
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_tmp = $file['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validate file type
            $allowed_extensions = ['pdf', 'docx', 'doc'];
            if (!in_array($file_ext, $allowed_extensions)) {
                $error_message = 'Hanya file PDF atau DOCX yang diperbolehkan!';
            } elseif ($file_size > 10485760) { // 10MB
                $error_message = 'Ukuran file maksimal 10MB!';
            } else {
                // Create upload directory if not exists
                $upload_dir = 'uploads/laporan/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Generate unique filename
                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
                $upload_path = $upload_dir . $new_filename;

                // Move uploaded file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO laporan_pkl (nama_lengkap, nis, kelas_jurusan, nama_sekolah, tempat_pkl, tanggal_pkl, file_laporan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssss", $nama_lengkap, $nis, $kelas_jurusan, $nama_sekolah, $tempat_pkl, $tanggal_pkl, $new_filename);

                    if ($stmt->execute()) {
                        $success_message = 'Laporan PKL berhasil diupload!';
                        // Clear form
                        $_POST = array();
                    } else {
                        $error_message = 'Gagal menyimpan data ke database!';
                    }
                    $stmt->close();
                } else {
                    $error_message = 'Gagal mengupload file!';
                }
            }
        } else {
            $error_message = 'File laporan wajib diupload!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Laporan PKL - Sistem Laporan PKL</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h2>📚 Sistem Laporan PKL</h2>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="upload-laporan.php" class="nav-link active">Upload Laporan</a>
                <a href="admin/login.php" class="nav-link">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Form Section -->
    <section class="form-section">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h1 class="form-title">Upload Laporan PKL</h1>
                    <p class="form-description">Lengkapi formulir di bawah ini untuk mengupload laporan PKL Anda</p>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <span><?php echo $success_message; ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">✕</span>
                        <span><?php echo $error_message; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="upload-form">
                    <div class="form-group">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-input" 
                               value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nis" class="form-label">NIS / NISN <span class="required">*</span></label>
                            <input type="text" id="nis" name="nis" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['nis'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="kelas_jurusan" class="form-label">Kelas & Jurusan <span class="required">*</span></label>
                            <input type="text" id="kelas_jurusan" name="kelas_jurusan" class="form-input" 
                                   placeholder="Contoh: XII RPL 1"
                                   value="<?php echo htmlspecialchars($_POST['kelas_jurusan'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama_sekolah" class="form-label">Nama Sekolah <span class="required">*</span></label>
                        <input type="text" id="nama_sekolah" name="nama_sekolah" class="form-input" 
                               value="<?php echo htmlspecialchars($_POST['nama_sekolah'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tempat_pkl" class="form-label">Tempat PKL <span class="required">*</span></label>
                            <input type="text" id="tempat_pkl" name="tempat_pkl" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['tempat_pkl'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_pkl" class="form-label">Tanggal PKL <span class="required">*</span></label>
                            <input type="date" id="tanggal_pkl" name="tanggal_pkl" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['tanggal_pkl'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="file_laporan" class="form-label">Upload File Laporan <span class="required">*</span></label>
                        <div class="file-input-wrapper">
                            <input type="file" id="file_laporan" name="file_laporan" class="file-input" 
                                   accept=".pdf,.doc,.docx" required>
                            <label for="file_laporan" class="file-input-label">
                                <span class="file-icon">📄</span>
                                <span class="file-text">Pilih file PDF atau DOCX (Max 10MB)</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <span>Upload Laporan</span>
                            <span class="btn-icon">→</span>
                        </button>
                        <a href="index.php" class="btn btn-secondary btn-large">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Sistem Laporan PKL. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>