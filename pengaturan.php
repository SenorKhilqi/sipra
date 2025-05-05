<?php
// Include header
include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="fas fa-cog"></i> Pengaturan Tampilan</h2>
        <p class="text-muted">Sesuaikan tampilan aplikasi sesuai preferensi Anda. Pengaturan disimpan di browser Anda.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Menu Pengaturan</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="#section-theme" class="list-group-item list-group-item-action active" id="theme-tab" data-bs-toggle="list">
                    <i class="fas fa-moon"></i> Mode Tampilan
                </a>
                <a href="#section-font-size" class="list-group-item list-group-item-action" id="font-size-tab" data-bs-toggle="list">
                    <i class="fas fa-font"></i> Ukuran Font
                </a>
                <a href="#section-calendar" class="list-group-item list-group-item-action" id="calendar-tab" data-bs-toggle="list">
                    <i class="fas fa-calendar"></i> Hari Awal Kalender
                </a>
                <a href="#" class="list-group-item list-group-item-action text-danger" id="reset-settings">
                    <i class="fas fa-undo"></i> Reset Ke Default
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="tab-content">
            <!-- Theme Section -->
            <div class="tab-pane fade show active" id="section-theme">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-moon"></i> Mode Tampilan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-auto">
                                <h4 class="mb-1">Mode Gelap</h4>
                                <p class="text-muted mb-0">Mengaktifkan tampilan dengan latar belakang gelap</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="darkModeSwitch" style="width: 3rem; height: 1.5rem;">
                                <label class="form-check-label ms-2" for="darkModeSwitch" id="darkModeLabel">
                                    Mode Gelap
                                </label>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card theme-preview theme-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">Mode Terang</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-3 bg-white border rounded mb-2">
                                            <div class="bg-primary text-white p-2 rounded mb-2">Header</div>
                                            <div class="bg-light p-2 rounded mb-2">Konten</div>
                                            <div class="p-2 rounded border">Data</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card theme-preview theme-dark">
                                    <div class="card-header">
                                        <h6 class="mb-0">Mode Gelap</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="p-3 bg-dark border rounded mb-2">
                                            <div class="bg-primary text-white p-2 rounded mb-2">Header</div>
                                            <div class="bg-secondary p-2 rounded mb-2 text-white">Konten</div>
                                            <div class="p-2 rounded border border-secondary text-white">Data</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Perubahan tema akan otomatis disimpan dan diterapkan.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Font Size Section -->
            <div class="tab-pane fade" id="section-font-size">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-font"></i> Ukuran Font</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card font-option" data-font-size="small">
                                    <div class="card-body text-center">
                                        <i class="fas fa-text-height fa-2x mb-3"></i>
                                        <h5>Font Kecil</h5>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="fontSize" id="fontSmall" value="small">
                                            <label class="form-check-label" for="fontSmall">
                                                Ukuran Kecil
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card font-option" data-font-size="medium">
                                    <div class="card-body text-center">
                                        <i class="fas fa-text-height fa-3x mb-3"></i>
                                        <h5>Font Sedang</h5>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="fontSize" id="fontMedium" value="medium">
                                            <label class="form-check-label" for="fontMedium">
                                                Ukuran Sedang
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card font-option" data-font-size="large">
                                    <div class="card-body text-center">
                                        <i class="fas fa-text-height fa-4x mb-3"></i>
                                        <h5>Font Besar</h5>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="fontSize" id="fontLarge" value="large">
                                            <label class="form-check-label" for="fontLarge">
                                                Ukuran Besar
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="sample-text">Ini adalah contoh tampilan teks dengan ukuran font yang dipilih.</p>
                            <p class="sample-text text-muted">Perubahan ukuran font akan mempengaruhi seluruh teks di aplikasi.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Calendar Start Day Section -->
            <div class="tab-pane fade" id="section-calendar">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-calendar"></i> Hari Awal Kalender</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card calendar-option" data-start-day="monday">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-4x mb-3 text-primary"></i>
                                        <h4>Mulai dari Senin</h4>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="calendarStartDay" id="startMonday" value="monday">
                                            <label class="form-check-label" for="startMonday">
                                                Kalender dimulai dari Senin
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card calendar-option" data-start-day="sunday">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-4x mb-3 text-success"></i>
                                        <h4>Mulai dari Minggu</h4>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="calendarStartDay" id="startSunday" value="sunday">
                                            <label class="form-check-label" for="startSunday">
                                                Kalender dimulai dari Minggu
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="calendar-preview mt-4">
                            <h5>Pratinjau Kalender:</h5>
                            <table class="table table-bordered calendar-preview-table">
                                <thead id="calendar-preview-header">
                                    <!-- Diisi oleh JavaScript -->
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>4</td>
                                        <td>5</td>
                                        <td>6</td>
                                        <td>7</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Style untuk pilihan tema */
.theme-option, .font-option, .calendar-option {
    cursor: pointer;
    transition: all 0.3s ease;
    height: 100%;
}

.theme-option:hover, .font-option:hover, .calendar-option:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.theme-option.active, .font-option.active, .calendar-option.active {
    border: 2px solid #007bff;
}

.theme-preview {
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.theme-preview:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.theme-dark .card-header, .theme-dark .card-body {
    background-color: #333;
    color: #fff;
}

/* Form switch styling */
.form-switch .form-check-input {
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-switch .form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

/* Calendar preview */
.calendar-preview-table {
    text-align: center;
}

.calendar-preview-table th, .calendar-preview-table td {
    width: 14.28%;
    height: 40px;
}

/* Dark theme footer */
.dark-theme footer {
    background-color: #222;
    color: #f8f9fa;
    border-top: 1px solid #444;
    margin-top: 2rem;
    padding: 1rem 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize settings from localStorage
    initializeSettings();
    
    // Add event listeners for settings changes
    setupEventListeners();
    
    // Initialize tabs
    initializeTabs();
    
    // Initialize calendar preview
    updateCalendarPreview();
});

// Initialize settings from localStorage
function initializeSettings() {
    // Theme setting
    const theme = localStorage.getItem('theme') || 'light';
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const darkModeLabel = document.getElementById('darkModeLabel');
    
    if (theme === 'dark') {
        darkModeSwitch.checked = true;
        darkModeLabel.textContent = 'Mode Terang';
    } else {
        darkModeSwitch.checked = false;
        darkModeLabel.textContent = 'Mode Gelap';
    }
    
    applyTheme(theme);
    
    // Font size setting
    const fontSize = localStorage.getItem('fontSize') || 'medium';
    document.querySelector(`input[name="fontSize"][value="${fontSize}"]`).checked = true;
    document.querySelector(`.font-option[data-font-size="${fontSize}"]`).classList.add('active');
    applyFontSize(fontSize);
    
    // Calendar start day setting
    const startDay = localStorage.getItem('startDay') || 'monday';
    document.querySelector(`input[name="calendarStartDay"][value="${startDay}"]`).checked = true;
    document.querySelector(`.calendar-option[data-start-day="${startDay}"]`).classList.add('active');
}

// Set up event listeners for settings changes
function setupEventListeners() {
    // Dark mode toggle
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const darkModeLabel = document.getElementById('darkModeLabel');
    
    darkModeSwitch.addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        applyTheme(theme);
        
        // Update label
        darkModeLabel.textContent = this.checked ? 'Mode Terang' : 'Mode Gelap';
    });
    
    // Font size setting listeners
    document.querySelectorAll('input[name="fontSize"]').forEach(input => {
        input.addEventListener('change', function() {
            const fontSize = this.value;
            localStorage.setItem('fontSize', fontSize);
            applyFontSize(fontSize);
            
            // Update active state on cards
            document.querySelectorAll('.font-option').forEach(card => {
                card.classList.remove('active');
            });
            document.querySelector(`.font-option[data-font-size="${fontSize}"]`).classList.add('active');
        });
    });
    
    // Calendar start day setting listeners
    document.querySelectorAll('input[name="calendarStartDay"]').forEach(input => {
        input.addEventListener('change', function() {
            const startDay = this.value;
            localStorage.setItem('startDay', startDay);
            updateCalendarPreview();
            
            // Update active state on cards
            document.querySelectorAll('.calendar-option').forEach(card => {
                card.classList.remove('active');
            });
            document.querySelector(`.calendar-option[data-start-day="${startDay}"]`).classList.add('active');
        });
    });
    
    // Reset settings listener
    document.getElementById('reset-settings').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin ingin mengembalikan semua pengaturan ke default?')) {
            localStorage.removeItem('theme');
            localStorage.removeItem('fontSize');
            localStorage.removeItem('startDay');
            
            // Reload the page to apply default settings
            location.reload();
        }
    });
    
    // Card click event listeners
    document.querySelectorAll('.font-option').forEach(card => {
        card.addEventListener('click', function() {
            const fontSize = this.dataset.fontSize;
            document.querySelector(`input[name="fontSize"][value="${fontSize}"]`).click();
        });
    });
    
    document.querySelectorAll('.calendar-option').forEach(card => {
        card.addEventListener('click', function() {
            const startDay = this.dataset.startDay;
            document.querySelector(`input[name="calendarStartDay"][value="${startDay}"]`).click();
        });
    });
}

// Initialize tab functionality
function initializeTabs() {
    document.querySelectorAll('a[data-bs-toggle="list"]').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs and panes
            document.querySelectorAll('a[data-bs-toggle="list"]').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show', 'active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show the corresponding tab content
            const target = document.querySelector(this.getAttribute('href'));
            target.classList.add('show', 'active');
        });
    });
}

// Apply theme to the page
function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
}

// Apply font size to the page
function applyFontSize(fontSize) {
    document.body.classList.remove('font-size-small', 'font-size-medium', 'font-size-large');
    document.body.classList.add(`font-size-${fontSize}`);
}

// Update calendar preview based on start day setting
function updateCalendarPreview() {
    const startDay = localStorage.getItem('startDay') || 'monday';
    const headerRow = document.getElementById('calendar-preview-header');
    
    let dayNames = startDay === 'monday' ? 
        ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] : 
        ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    
    // Create header row with day names
    headerRow.innerHTML = '<tr>' + dayNames.map(day => `<th>${day}</th>`).join('') + '</tr>';
}
</script>

<?php
// Include footer
include 'includes/footer.php';
?>