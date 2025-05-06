<?php
// Include database configuration
require_once 'config/db_config.php';

// For demonstration, we're using user_id = 1 
// In a real application, you would get this from the session after user login
$user_id = 1;

// Get all jadwal kuliah by day of week
$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
$schedules = [];

foreach ($days as $day) {
    $sql = "SELECT jk.*, mk.nama_mk, mk.kode_mk 
            FROM jadwal_kuliah jk 
            JOIN mata_kuliah mk ON jk.mk_id = mk.mk_id 
            WHERE jk.user_id = ? AND jk.hari = ? 
            ORDER BY jk.jam_mulai ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $day);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schedules[$day] = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[$day][] = $row;
    }
    $stmt->close();
}

// Query to get all mata kuliah for the drop-down
$sql_mk = "SELECT * FROM mata_kuliah WHERE user_id = ? ORDER BY nama_mk ASC";
$stmt_mk = $conn->prepare($sql_mk);
$stmt_mk->bind_param("i", $user_id);
$stmt_mk->execute();
$result_mk = $stmt_mk->get_result();

// Include header
include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-clock"></i> Jadwal Kuliah</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <ul class="nav nav-tabs card-header-tabs" id="jadwalTabs" role="tablist">
                    <?php foreach ($days as $index => $day): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($index === 0) ? 'active' : ''; ?> day-tab" 
                                id="tab-<?php echo $day; ?>" 
                                data-bs-toggle="tab" 
                                data-bs-target="#content-<?php echo $day; ?>" 
                                type="button" 
                                role="tab" 
                                aria-controls="content-<?php echo $day; ?>" 
                                aria-selected="<?php echo ($index === 0) ? 'true' : 'false'; ?>">
                            <?php echo $day; ?> 
                            <?php if (count($schedules[$day]) > 0): ?>
                                <span class="badge bg-primary"><?php echo count($schedules[$day]); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <style>
                /* Make day tabs have black text in light mode */
                .nav-link.day-tab {
                    color: #000000;
                    font-weight: 500;
                }
                
                /* Keep white text color in dark mode */
                .dark-theme .nav-link.day-tab {
                    color: #ffffff;
                }
                
                /* Style for active tab */
                .nav-link.day-tab.active {
                    font-weight: 700;
                    color: #0d6efd; /* Bootstrap primary color */
                }
                
                /* Active tab in dark mode */
                .dark-theme .nav-link.day-tab.active {
                    color: #8bb9fe;
                }
            </style>

            <div class="card-body">
                <div class="tab-content" id="jadwalTabsContent">
                    <?php foreach ($days as $index => $day): ?>
                    <div class="tab-pane fade <?php echo ($index === 0) ? 'show active' : ''; ?>" 
                         id="content-<?php echo $day; ?>" 
                         role="tabpanel" 
                         aria-labelledby="tab-<?php echo $day; ?>">
                        
                        <?php if (count($schedules[$day]) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Mata Kuliah</th>
                                            <th>Waktu</th>
                                            <th>Lokasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($schedules[$day] as $jadwal): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($jadwal['nama_mk']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($jadwal['kode_mk']); ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $start = date('H:i', strtotime($jadwal['jam_mulai']));
                                                        $end = date('H:i', strtotime($jadwal['jam_selesai']));
                                                        echo "$start - $end"; 
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($jadwal['lokasi']); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-info edit-btn" 
                                                                data-jadwal-id="<?php echo $jadwal['jadwal_id']; ?>"
                                                                data-mk-id="<?php echo $jadwal['mk_id']; ?>"
                                                                data-hari="<?php echo $jadwal['hari']; ?>"
                                                                data-jam-mulai="<?php echo $start; ?>"
                                                                data-jam-selesai="<?php echo $end; ?>"
                                                                data-lokasi="<?php echo htmlspecialchars($jadwal['lokasi']); ?>"
                                                                data-bs-toggle="modal" data-bs-target="#editJadwalModal">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                                data-jadwal-id="<?php echo $jadwal['jadwal_id']; ?>"
                                                                data-nama-mk="<?php echo htmlspecialchars($jadwal['nama_mk']); ?>"
                                                                data-bs-toggle="modal" data-bs-target="#deleteJadwalModal">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada jadwal kuliah pada hari <?php echo $day; ?>.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Jadwal Modal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Jadwal Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addJadwalForm" action="process/proses_jadwal_kuliah.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mk_id" class="form-label">Mata Kuliah</label>
                        <select class="form-select" id="mk_id" name="mk_id" required>
                            <option value="">Pilih Mata Kuliah</option>
                            <?php while ($mk = $result_mk->fetch_assoc()): ?>
                                <option value="<?php echo $mk['mk_id']; ?>">
                                    <?php echo htmlspecialchars($mk['kode_mk'] . ' - ' . $mk['nama_mk']); ?>
                                </option>
                            <?php endwhile; ?>
                            <?php $result_mk->data_seek(0); // Reset result pointer ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari</label>
                        <select class="form-select" id="hari" name="hari" required>
                            <option value="">Pilih Hari</option>
                            <?php foreach ($days as $day): ?>
                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Contoh: Ruang 101, Lab Komputer">
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="action" value="add">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Jadwal Modal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Jadwal Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editJadwalForm" action="process/proses_jadwal_kuliah.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_mk_id" class="form-label">Mata Kuliah</label>
                        <select class="form-select" id="edit_mk_id" name="mk_id" required>
                            <option value="">Pilih Mata Kuliah</option>
                            <?php while ($mk = $result_mk->fetch_assoc()): ?>
                                <option value="<?php echo $mk['mk_id']; ?>">
                                    <?php echo htmlspecialchars($mk['kode_mk'] . ' - ' . $mk['nama_mk']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_hari" class="form-label">Hari</label>
                        <select class="form-select" id="edit_hari" name="hari" required>
                            <option value="">Pilih Hari</option>
                            <?php foreach ($days as $day): ?>
                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_jam_mulai" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" id="edit_jam_mulai" name="jam_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_jam_selesai" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" id="edit_jam_selesai" name="jam_selesai" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="edit_lokasi" name="lokasi">
                    </div>
                    <input type="hidden" id="edit_jadwal_id" name="jadwal_id">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="action" value="edit">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Jadwal Modal -->
<div class="modal fade" id="deleteJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Hapus Jadwal Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus jadwal kuliah <strong id="delete_jadwal_name"></strong>?</p>
                <p class="text-danger">Perhatian: Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <form id="deleteJadwalForm" action="process/proses_jadwal_kuliah.php" method="post">
                <input type="hidden" id="delete_jadwal_id" name="jadwal_id">
                <input type="hidden" name="action" value="delete">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Set data for edit modal
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit_jadwal_id').value = this.getAttribute('data-jadwal-id');
            document.getElementById('edit_mk_id').value = this.getAttribute('data-mk-id');
            document.getElementById('edit_hari').value = this.getAttribute('data-hari');
            document.getElementById('edit_jam_mulai').value = this.getAttribute('data-jam-mulai');
            document.getElementById('edit_jam_selesai').value = this.getAttribute('data-jam-selesai');
            document.getElementById('edit_lokasi').value = this.getAttribute('data-lokasi');
        });
    });

    // Set data for delete modal
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_jadwal_id').value = this.getAttribute('data-jadwal-id');
            document.getElementById('delete_jadwal_name').textContent = this.getAttribute('data-nama-mk');
        });
    });
</script>

<?php
// Close the statement
$stmt_mk->close();
// Include footer
include 'includes/footer.php';
?>