<?php
require_once '../config/database.php';

$error_message = '';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error_message = 'Username dan password wajib diisi!';
    } else {
        // Check admin credentials
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Set session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = 'Username atau password salah!';
            }
        } else {
            $error_message = 'Username atau password salah!';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Laporan PKL</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                <a href="../index.php" class="nav-link">Beranda</a>
                <a href="../upload-laporan.php" class="nav-link">Upload Laporan</a>
                <a href="login.php" class="nav-link active">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="login-section">
        <div class="container">
            <div class="login-container">
                <div class="login-header">
                    <div class="login-icon">🔐</div>
                    <h1 class="login-title">Login Admin</h1>
                    <p class="login-description">Masuk ke dashboard admin untuk mengelola sistem PKL</p>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">✕</span>
                        <span><?php echo $error_message; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-input" 
                               placeholder="Masukkan username" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" 
                               placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large btn-block">
                        <span>Login</span>
                        <span class="btn-icon">→</span>
                    </button>
                </form>

                <div class="login-footer">
                    <p class="login-hint">💡 Default: username = <strong>admin</strong>, password = <strong>admin123</strong></p>
                </div>
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