<?php
// Start session
session_start();

// Check if user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Include header (without navbar)
$no_navbar = true;
include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center mt-4 mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h2><i class="fas fa-user-plus"></i> Registrasi Akun SIPRAM</h2>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_SESSION['register_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['register_error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['register_error']); ?>
                    <?php endif; ?>
                    
                    <form action="process/proses_register.php" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="invalid-feedback">Nama lengkap harus diisi</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                            </div>
                            <div class="invalid-feedback">Username harus diisi</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                            </div>
                            <div class="invalid-feedback">Email harus diisi dan valid</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            </div>
                            <div class="form-text">Password minimal 8 karakter, mengandung huruf dan angka</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Masukkan konfirmasi password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Daftar</button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0">Sudah memiliki akun? <a href="login.php" class="text-decoration-none">Masuk sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    
    var forms = document.querySelectorAll('.needs-validation')
    
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
// Include footer
include 'includes/footer.php';
?>