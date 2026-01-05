<?php
require_once 'config/database.php';

// Fetch latest progress (sorted by newest first)
$query = "SELECT * FROM progres_pkl ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Laporan PKL</title>
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
                <a href="index.php" class="nav-link active">Beranda</a>
                <a href="upload-laporan.php" class="nav-link">Upload Laporan</a>
                <a href="admin/login.php" class="nav-link">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Selamat Datang di Sistem Laporan PKL</h1>
                <p class="hero-description">
                    Platform digital untuk mengelola laporan dan progres Praktik Kerja Lapangan (PKL) siswa. 
                    Upload laporan PKL Anda dan pantau progres kegiatan PKL secara real-time.
                </p>
                <div class="hero-buttons">
                    <a href="upload-laporan.php" class="btn btn-primary">Upload Laporan PKL</a>
                    <a href="#progres" class="btn btn-secondary">Lihat Progres</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Progress Section -->
    <section class="progress-section" id="progres">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Progres PKL Terbaru</h2>
                <p class="section-description">Pantau perkembangan dan kegiatan PKL yang sedang berlangsung</p>
            </div>

            <div class="progress-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="progress-card">
                            <div class="progress-header">
                                <span class="progress-badge"><?php echo htmlspecialchars($row['minggu_ke']); ?></span>
                                <span class="progress-date">
                                    <?php 
                                    $date = new DateTime($row['created_at']);
                                    echo $date->format('d M Y'); 
                                    ?>
                                </span>
                            </div>
                            <h3 class="progress-title"><?php echo htmlspecialchars($row['judul_progres']); ?></h3>
                            <p class="progress-description"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                            <?php if (!empty($row['file_foto'])): ?>
                                <div class="progress-image">
                                    <img src="uploads/progres/<?php echo htmlspecialchars($row['file_foto']); ?>" 
                                         alt="Foto Progres"
                                         onerror="this.style.display='none'">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3>Belum Ada Progres</h3>
                        <p>Progres PKL akan ditampilkan di sini setelah admin mengupload.</p>
                    </div>
                <?php endif; ?>
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