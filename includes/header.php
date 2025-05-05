<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPRAM - Sistem Pengingat Tugas Mahasiswa</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 60px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            min-height: calc(100vh - 60px);
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .content {
            padding: 20px;
            flex-grow: 1;
        }
        .nav-link {
            color: #ffffff;
        }
        .nav-link:hover, .nav-link.active {
            color: #000000;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        footer {
            margin-top: auto;
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px 0;
            border-top: 1px solid #e9ecef;
        }

        /* Font size classes */
        .font-size-small {
            font-size: 0.9rem;
        }

        .font-size-medium {
            font-size: 1rem;
        }

        .font-size-large {
            font-size: 1.2rem;
        }

        /* Dark theme */
        .dark-theme {
            background-color: #222;
            color: #f8f9fa;
        }

        .dark-theme .card {
            background-color: #333;
            border-color: #444;
        }

        .dark-theme .card-header {
            background-color: #444;
            border-color: #555;
        }

        .dark-theme .navbar {
            background-color: #333 !important;
            border-color: #444;
        }

        .dark-theme .list-group-item {
            background-color: #333;
            border-color: #444;
            color: #f8f9fa;
        }

        .dark-theme .table {
            color: #f8f9fa;
        }

        .dark-theme .bg-light {
            background-color: #444 !important;
        }

        .dark-theme .text-muted {
            color: #aaa !important;
        }
        
        /* Dark theme footer */
        .dark-theme footer {
            background-color: #222;
            color: #f8f9fa;
            border-top: 1px solid #444;
        }

        /* Additional text color fixes for dark mode */
        .dark-theme .form-check-label,
        .dark-theme .card-body h4,
        .dark-theme .card-body h5,
        .dark-theme .card-body p,
        .dark-theme .card-body label,
        .dark-theme .alert,
        .dark-theme .bg-white,
        .dark-theme input,
        .dark-theme select,
        .dark-theme .bg-light {
            color: #f8f9fa;
        }
        
        .dark-theme .alert-info,
        .dark-theme .alert-warning {
            background-color: #3c4c5c !important;
            border-color: #4a5f73;
            color: #f8f9fa !important;
        }

        .dark-theme .bg-white {
            background-color: #333 !important;
        }

        .dark-theme .border {
            border-color: #444 !important;
        }
        
        .dark-theme .card-header.bg-light h5 {
            color: #f8f9fa;
        }
        
        .dark-theme .text-danger {
            color: #ff6b6b !important;
        }
        
        .dark-theme .sample-text {
            color: #f8f9fa !important;
        }
    </style>

    <!-- Script to apply user preferences from localStorage -->
    <script>
        // Apply saved preferences when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Apply theme preference
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
            }
            
            // Apply font size preference
            const savedFontSize = localStorage.getItem('fontSize') || 'medium';
            document.body.classList.add(`font-size-${savedFontSize}`);
            
            // Language preference is handled during user interaction
            // Calendar start day preference is applied on the calendar page
        });
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">SIPRAM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kalender.php"><i class="fas fa-calendar"></i> Kalender</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mata_kuliah.php"><i class="fas fa-book"></i> Mata Kuliah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jadwal_kuliah.php"><i class="fas fa-clock"></i> Jadwal Kuliah</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <!-- Content will be placed here -->