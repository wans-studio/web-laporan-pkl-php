<?php
require_once '../config/database.php';

$error_message = '';
$mode = (isset($_GET['mode']) && $_GET['mode'] === 'register') ? 'register' : 'login';
// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? 'login';
    if ($form_type === 'register') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        if (empty($username) || empty($password) || empty($confirm_password)) {
            $error_message = 'Semua field wajib diisi!';
            $mode = 'register';
        } elseif (strlen($password) < 6) {
            $error_message = 'Password minimal 6 karakter!';
            $mode = 'register';
        } elseif ($password !== $confirm_password) {
            $error_message = 'Konfirmasi password tidak cocok!';
            $mode = 'register';
        } else {
            $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error_message = 'Username sudah digunakan!';
                $stmt->close();
                $mode = 'register';
            } else {
                $stmt->close();
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt2 = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
                $stmt2->bind_param("ss", $username, $hashed);
                if ($stmt2->execute()) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $stmt2->insert_id;
                    $_SESSION['admin_username'] = $username;
                    $stmt2->close();
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error_message = 'Terjadi kesalahan saat mendaftar. Coba lagi.';
                    $stmt2->close();
                    $mode = 'register';
                }
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if (empty($username) || empty($password)) {
            $error_message = 'Username dan password wajib diisi!';
        } else {
            $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
                        if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                if (password_verify($password, $admin['password'])) {
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
                    <h1 class="login-title"><?php echo $mode === 'register' ? 'Daftar Admin' : 'Login Admin'; ?></h1>
                    <p class="login-description"><?php echo $mode === 'register' ? 'Buat akun admin untuk mengakses sistem PKL' : 'Masuk ke dashboard admin untuk mengelola sistem PKL'; ?></p>
                </div>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">✕</span>
                        <span><?php echo $error_message; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($mode === 'register'): ?>
                    <form method="POST" class="login-form">
                        <input type="hidden" name="form_type" value="register">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-input" 
                                   placeholder="Pilih username" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-input" 
                                   placeholder="Buat password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                                   placeholder="Ulangi password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-large btn-block">
                            <span>Daftar</span>
                            <span class="btn-icon">→</span>
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" class="login-form">
                        <input type="hidden" name="form_type" value="login">
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
                <?php endif; ?>

                <div class="login-footer">
                    <?php if ($mode === 'register'): ?>
                        <p class="login-hint">Sudah punya akun? <a href="login.php?mode=login">Masuk</a></p>
                    <?php else: ?>
                        <p class="login-hint">Belum punya akun? <a href="login.php?mode=register">Daftar</a></p>
                        <p class="login-hint">💡 Default: username = <strong>admin</strong>, password = <strong>admin123</strong></p>
                    <?php endif; ?>
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
