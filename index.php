<?php
// Include database configuration
require_once 'config/db_config.php';

// For demonstration, we're using user_id = 1 
// In a real application, you would get this from the session after user login
$user_id = 1;

// Get current date for deadline calculation
$current_date = date('Y-m-d');
$deadline_date = date('Y-m-d', strtotime('+7 days'));

// Query to get mata kuliah count
$sql_mk_count = "SELECT COUNT(*) as total_mk FROM mata_kuliah WHERE user_id = ?";
$stmt_mk_count = $conn->prepare($sql_mk_count);
$stmt_mk_count->bind_param("i", $user_id);
$stmt_mk_count->execute();
$result_mk_count = $stmt_mk_count->get_result();
$mata_kuliah_count = $result_mk_count->fetch_assoc()['total_mk'];
$stmt_mk_count->close();

// Query to get upcoming tasks with deadline within 7 days
$sql_upcoming = "SELECT t.*, mk.nama_mk 
                 FROM tugas t 
                 JOIN mata_kuliah mk ON t.mk_id = mk.mk_id 
                 WHERE t.user_id = ? 
                 AND t.deadline BETWEEN ? AND ? 
                 AND t.status = 'pending'
                 ORDER BY t.deadline ASC";
$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param("iss", $user_id, $current_date, $deadline_date);
$stmt_upcoming->execute();
$result_upcoming = $stmt_upcoming->get_result();

// Query to get all pending tasks ordered by deadline
$sql_all_tasks = "SELECT t.*, mk.nama_mk 
                 FROM tugas t 
                 JOIN mata_kuliah mk ON t.mk_id = mk.mk_id 
                 WHERE t.user_id = ? 
                 AND t.status = 'pending'
                 ORDER BY t.deadline ASC
                 LIMIT 5";
$stmt_all_tasks = $conn->prepare($sql_all_tasks);
$stmt_all_tasks->bind_param("i", $user_id);
$stmt_all_tasks->execute();
$result_all_tasks = $stmt_all_tasks->get_result();

// Include header
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-5">Dashboard</h1>
        <p class="lead text-muted">Selamat datang di SIPRAM - Sistem Pengingat Tugas Mahasiswa</p>
    </div>
</div>

<div class="row mb-4">
    <!-- Mata Kuliah Card -->
    <div class="col-md-4">
        <div class="card h-100 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="fas fa-book"></i> Total Mata Kuliah</h5>
            </div>
            <div class="card-body text-center">
                <h1 class="display-1 text-primary"><?php echo $mata_kuliah_count; ?></h1>
                <p class="card-text">Mata kuliah terdaftar semester ini</p>
                <a href="mata_kuliah.php" class="btn btn-outline-primary">Lihat Mata Kuliah</a>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Tasks Card -->
    <div class="col-md-4">
        <div class="card h-100 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle"></i> Tugas Mendesak (H-7)</h5>
            </div>
            <div class="card-body">
                <?php if ($result_upcoming->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($task = $result_upcoming->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($task['judul']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($task['nama_mk']); ?></small>
                                </div>
                                <span class="badge bg-<?php echo ($task['kategori'] == 'high') ? 'danger' : (($task['kategori'] == 'medium') ? 'warning' : 'success'); ?> rounded-pill">
                                    <?php 
                                    $deadline = new DateTime($task['deadline']);
                                    $now = new DateTime();
                                    $interval = $now->diff($deadline);
                                    echo "H-" . $interval->format('%a'); 
                                    ?>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <p>Tidak ada tugas mendesak dalam 7 hari ke depan</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light">
                <a href="tugas.php" class="btn btn-sm btn-warning w-100">Lihat Semua Tugas</a>
            </div>
        </div>
    </div>
    
    <!-- Schedule Today Card -->
    <div class="col-md-4">
        <div class="card h-100 border-success">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-day"></i> Jadwal Hari Ini</h5>
            </div>
            <div class="card-body">
                <?php
                $today = date('l');
                $hari_mapping = [
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu'
                ];
                $today_indo = $hari_mapping[$today];
                
                $sql_schedule = "SELECT jk.*, mk.nama_mk 
                               FROM jadwal_kuliah jk 
                               JOIN mata_kuliah mk ON jk.mk_id = mk.mk_id 
                               WHERE jk.user_id = ? AND jk.hari = ?
                               ORDER BY jk.jam_mulai ASC";
                $stmt_schedule = $conn->prepare($sql_schedule);
                $stmt_schedule->bind_param("is", $user_id, $today_indo);
                $stmt_schedule->execute();
                $result_schedule = $stmt_schedule->get_result();
                ?>
                
                <?php if ($result_schedule->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($schedule = $result_schedule->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($schedule['nama_mk']); ?></strong>
                                    <span class="badge bg-success">
                                        <?php 
                                        $start = date('H:i', strtotime($schedule['jam_mulai']));
                                        $end = date('H:i', strtotime($schedule['jam_selesai']));
                                        echo "$start - $end"; 
                                        ?>
                                    </span>
                                </div>
                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($schedule['lokasi']); ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-coffee text-success fa-4x mb-3"></i>
                        <p>Tidak ada jadwal kuliah hari ini</p>
                    </div>
                <?php endif; ?>
                <?php $stmt_schedule->close(); ?>
            </div>
            <div class="card-footer bg-light">
                <a href="jadwal_kuliah.php" class="btn btn-sm btn-success w-100">Lihat Semua Jadwal</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tasks"></i> Daftar Tugas Mendatang</h5>
                <a href="tugas.php" class="btn btn-sm btn-light">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tugas</th>
                                <th>Mata Kuliah</th>
                                <th>Deadline</th>
                                <th>Kategori</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_all_tasks->num_rows > 0): ?>
                                <?php while ($task = $result_all_tasks->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($task['nama_mk']); ?></td>
                                        <td>
                                            <?php 
                                            $deadline = new DateTime($task['deadline']);
                                            echo $deadline->format('d M Y H:i'); 
                                            
                                            $now = new DateTime();
                                            $interval = $now->diff($deadline);
                                            $days_left = $interval->format('%r%a');
                                            
                                            if ($days_left < 0) {
                                                echo ' <span class="badge bg-danger">Terlambat</span>';
                                            } elseif ($days_left <= 3) {
                                                echo ' <span class="badge bg-warning">Segera</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($task['kategori'] == 'high') ? 'danger' : (($task['kategori'] == 'medium') ? 'warning' : 'success'); ?>">
                                                <?php echo ucfirst($task['kategori']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="process/proses_tugas.php?action=toggle_status&tugas_id=<?php echo $task['tugas_id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="far fa-square"></i> Mark Complete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3">Tidak ada tugas mendatang</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Close statements
$stmt_upcoming->close();
$stmt_all_tasks->close();

// Include footer
include 'includes/footer.php';
?>