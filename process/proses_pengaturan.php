<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include database configuration
require_once '../config/db_config.php';

// Get current user ID
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action type
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process based on action type
    switch ($action) {
        case 'update_profile':
            // Update user profile
            updateProfile($conn, $user_id);
            break;
            
        case 'update_appearance':
            // Update appearance settings
            updateAppearance($conn, $user_id);
            break;
            
        case 'update_notifications':
            // Update notification settings
            updateNotifications($conn, $user_id);
            break;
            
        case 'change_password':
            // Change user password
            changePassword($conn, $user_id);
            break;
            
        default:
            // Invalid action
            $_SESSION['settings_error'] = "Invalid action.";
            header("Location: ../pengaturan.php");
            exit;
    }
    
    // Redirect back to settings page
    header("Location: ../pengaturan.php");
    exit;
}

// Function to update user profile
function updateProfile($conn, $user_id) {
    // Get form data
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    // Validate input
    if (empty($full_name) || empty($email)) {
        $_SESSION['settings_error'] = "Nama lengkap dan email tidak boleh kosong.";
        return;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['settings_error'] = "Format email tidak valid.";
        return;
    }
    
    // Check if email already exists (excluding current user)
    $check_email_sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
    $check_stmt = $conn->prepare($check_email_sql);
    $check_stmt->bind_param("si", $email, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['settings_error'] = "Email sudah digunakan oleh pengguna lain.";
        $check_stmt->close();
        return;
    }
    $check_stmt->close();
    
    // Update user profile
    $update_sql = "UPDATE users SET full_name = ?, email = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $full_name, $email, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['settings_success'] = "Profil berhasil diperbarui.";
        // Update session variable
        $_SESSION['full_name'] = $full_name;
    } else {
        $_SESSION['settings_error'] = "Gagal memperbarui profil.";
    }
    
    $update_stmt->close();
}

// Function to update appearance settings
function updateAppearance($conn, $user_id) {
    // Get form data
    $tema = isset($_POST['tema']) ? $_POST['tema'] : 'light';
    $bahasa = isset($_POST['bahasa']) ? $_POST['bahasa'] : 'id';
    
    // Validate tema
    if ($tema != 'light' && $tema != 'dark') {
        $tema = 'light';
    }
    
    // Validate bahasa
    if ($bahasa != 'id' && $bahasa != 'en') {
        $bahasa = 'id';
    }
    
    // Update user settings
    $update_sql = "UPDATE pengaturan SET tema = ?, bahasa = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $tema, $bahasa, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['settings_success'] = "Pengaturan tampilan berhasil diperbarui.";
    } else {
        $_SESSION['settings_error'] = "Gagal memperbarui pengaturan tampilan.";
    }
    
    $update_stmt->close();
}

// Function to update notification settings
function updateNotifications($conn, $user_id) {
    // Get form data
    $notifikasi_email = isset($_POST['notifikasi_email']) ? 1 : 0;
    
    // Update user settings
    $update_sql = "UPDATE pengaturan SET notifikasi_email = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $notifikasi_email, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['settings_success'] = "Pengaturan notifikasi berhasil diperbarui.";
    } else {
        $_SESSION['settings_error'] = "Gagal memperbarui pengaturan notifikasi.";
    }
    
    $update_stmt->close();
}

// Function to change user password
function changePassword($conn, $user_id) {
    // Get form data
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['settings_error'] = "Semua field password harus diisi.";
        return;
    }
    
    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['settings_error'] = "Password baru tidak cocok dengan konfirmasi password.";
        return;
    }
    
    // Validate password strength
    if (strlen($new_password) < 8 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $_SESSION['settings_error'] = "Password baru harus minimal 8 karakter dan mengandung huruf dan angka.";
        return;
    }
    
    // Get current user's password hash
    $sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['settings_success'] = "Password berhasil diubah.";
            } else {
                $_SESSION['settings_error'] = "Gagal mengubah password.";
            }
            
            $update_stmt->close();
        } else {
            $_SESSION['settings_error'] = "Password saat ini tidak valid.";
        }
    } else {
        $_SESSION['settings_error'] = "User tidak ditemukan.";
    }
    
    $stmt->close();
}

// Close database connection
$conn->close();
?>