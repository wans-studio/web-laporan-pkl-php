<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul_progres = trim($_POST['judul_progres'] ?? '');
    $minggu_ke = trim($_POST['minggu_ke'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    // Validate required fields
    if (empty($judul_progres) || empty($minggu_ke) || empty($deskripsi)) {
        $error_message = 'Judul, minggu, dan deskripsi wajib diisi!';
    } else {
        $file_foto = null;

        // Handle optional file upload
        if (isset($_FILES['file_foto']) && $_FILES['file_foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file_foto'];
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_tmp = $file['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validate file type
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'doc'];
            if (!in_array($file_ext, $allowed_extensions)) {
                $error_message = 'Format file tidak didukung!';
            } elseif ($file_size > 10485760) { // 10MB
                $error_message = 'Ukuran file maksimal 10MB!';
            } else {
                // Create upload directory if not exists
                $upload_dir = '../uploads/progres/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Generate unique filename
                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $file_foto = $new_filename;
                } else {
                    $error_message = 'Gagal mengupload file!';
                }
            }
        }

        // Insert into database if no errors
        if (empty($error_message)) {
            $stmt = $conn->prepare("INSERT INTO progres_pkl (judul_progres, minggu_ke, deskripsi, file_foto) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $judul_progres, $minggu_ke, $deskripsi, $file_foto);

            if ($stmt->execute()) {
                $success_message = 'Progres PKL berhasil diupload! Progres akan muncul di halaman utama.';
                // Clear form
                $_POST = array();
            } else {
                $error_message = 'Gagal menyimpan data ke database!';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Progres PKL - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-admin">
        <div class="container">
            <div class="nav-brand">
                <h2>📚 Admin Dashboard</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="upload-progres.php" class="nav-link active">Upload Progres</a>
                <a href="../index.php" class="nav-link">Lihat Website</a>
                <a href="logout.php" class="nav-link nav-link-logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Form Section -->
    <section class="form-section">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h1 class="form-title">Upload Progres PKL</h1>
                    <p class="form-description">Tambahkan progres PKL terbaru yang akan ditampilkan di halaman utama</p>
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
                        <label for="judul_progres" class="form-label">Judul Progres <span class="required">*</span></label>
                        <input type="text" id="judul_progres" name="judul_progres" class="form-input" 
                               placeholder="Contoh: Orientasi dan Pengenalan Lingkungan Kerja"
                               value="<?php echo htmlspecialchars($_POST['judul_progres'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="minggu_ke" class="form-label">Minggu Ke- <span class="required">*</span></label>
                        <input type="text" id="minggu_ke" name="minggu_ke" class="form-input" 
                               placeholder="Contoh: Minggu ke-1"
                               value="<?php echo htmlspecialchars($_POST['minggu_ke'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi Progres <span class="required">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" class="form-textarea" rows="6" 
                                  placeholder="Jelaskan detail progres PKL yang dilakukan..."
                                  required><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="file_foto" class="form-label">Upload Foto / Dokumen (Opsional)</label>
                        <div class="file-input-wrapper">
                            <input type="file" id="file_foto" name="file_foto" class="file-input" 
                                   accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                            <label for="file_foto" class="file-input-label">
                                <span class="file-icon">📷</span>
                                <span class="file-text">Pilih foto atau dokumen (Max 10MB)</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <span>Upload Progres</span>
                            <span class="btn-icon">→</span>
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary btn-large">Kembali</a>
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

    <script src="../assets/js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>