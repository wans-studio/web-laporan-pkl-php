<?php
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get statistics
$total_laporan = $conn->query("SELECT COUNT(*) as count FROM laporan_pkl")->fetch_assoc()['count'];
$total_progres = $conn->query("SELECT COUNT(*) as count FROM progres_pkl")->fetch_assoc()['count'];

// Get recent reports
$recent_reports = $conn->query("SELECT * FROM laporan_pkl ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Laporan PKL</title>
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
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="upload-progres.php" class="nav-link">Upload Progres</a>
                <a href="../index.php" class="nav-link">Lihat Website</a>
                <a href="logout.php" class="nav-link nav-link-logout">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <section class="dashboard-section">
        <div class="container">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
                <p class="dashboard-description">Kelola sistem laporan PKL dari dashboard ini</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-card-blue">
                    <div class="stat-icon">📄</div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $total_laporan; ?></h3>
                        <p class="stat-label">Total Laporan PKL</p>
                    </div>
                </div>

                <div class="stat-card stat-card-green">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3 class="stat-value"><?php echo $total_progres; ?></h3>
                        <p class="stat-label">Total Progres PKL</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2 class="section-title">Menu Cepat</h2>
                <div class="action-grid">
                    <a href="upload-progres.php" class="action-card">
                        <div class="action-icon">➕</div>
                        <h3 class="action-title">Upload Progres PKL</h3>
                        <p class="action-description">Tambahkan progres PKL terbaru</p>
                    </a>

                    <a href="#laporan" class="action-card">
                        <div class="action-icon">📋</div>
                        <h3 class="action-title">Lihat Laporan Siswa</h3>
                        <p class="action-description">Kelola laporan yang masuk</p>
                    </a>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="recent-reports" id="laporan">
                <h2 class="section-title">Laporan Terbaru</h2>
                
                <?php if ($recent_reports && $recent_reports->num_rows > 0): ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Tempat PKL</th>
                                    <th>Tanggal Upload</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while($row = $recent_reports->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kelas_jurusan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tempat_pkl']); ?></td>
                                        <td>
                                            <?php 
                                            $date = new DateTime($row['created_at']);
                                            echo $date->format('d M Y H:i'); 
                                            ?>
                                        </td>
                                        <td>
                                            <a href="../uploads/laporan/<?php echo htmlspecialchars($row['file_laporan']); ?>" 
                                               class="btn btn-small btn-secondary" target="_blank">Download</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3>Belum Ada Laporan</h3>
                        <p>Laporan siswa akan ditampilkan di sini setelah mereka mengupload.</p>
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

    <script src="../assets/js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>
