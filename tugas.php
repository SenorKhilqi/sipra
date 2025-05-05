<?php
// Include database configuration
require_once 'config/db_config.php';

// For demonstration, we're using user_id = 1 
// In a real application, you would get this from the session after user login
$user_id = 1;

// Get filter from URL parameter, default to 'all'
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Prepare base SQL query
$sql = "SELECT t.*, mk.nama_mk 
        FROM tugas t 
        JOIN mata_kuliah mk ON t.mk_id = mk.mk_id 
        WHERE t.user_id = ?";

// Add filter condition if not 'all'
if ($filter !== 'all') {
    $sql .= " AND t.kategori = ?";
}

// Add order by deadline
$sql .= " ORDER BY t.deadline ASC";

// Prepare and execute query
$stmt = $conn->prepare($sql);

if ($filter !== 'all') {
    $stmt->bind_param("is", $user_id, $filter);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

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
            <h2><i class="fas fa-tasks"></i> Daftar Tugas</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTugasModal">
                <i class="fas fa-plus"></i> Tambah Tugas
            </button>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($filter === 'all') ? 'active' : ''; ?>" 
                           href="tugas.php?filter=all">Semua</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($filter === 'high') ? 'active' : ''; ?> text-danger" 
                           href="tugas.php?filter=high">High Priority</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($filter === 'medium') ? 'active' : ''; ?> text-warning" 
                           href="tugas.php?filter=medium">Medium Priority</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($filter === 'easy') ? 'active' : ''; ?> text-success" 
                           href="tugas.php?filter=easy">Easy Priority</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-info">
                                <tr>
                                    <th>Judul</th>
                                    <th>Mata Kuliah</th>
                                    <th>Deskripsi</th>
                                    <th>Deadline</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_mk']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($row['deskripsi'], 0, 50)) . (strlen($row['deskripsi']) > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <?php 
                                            $deadline = new DateTime($row['deadline']);
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
                                            <span class="badge bg-<?php echo ($row['kategori'] == 'high') ? 'danger' : (($row['kategori'] == 'medium') ? 'warning' : 'success'); ?>">
                                                <?php echo ucfirst($row['kategori']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($row['status'] == 'pending') ? 'secondary' : 'primary'; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info edit-btn" 
                                                        data-tugas-id="<?php echo $row['tugas_id']; ?>"
                                                        data-judul="<?php echo htmlspecialchars($row['judul']); ?>"
                                                        data-mk-id="<?php echo $row['mk_id']; ?>"
                                                        data-deskripsi="<?php echo htmlspecialchars($row['deskripsi']); ?>"
                                                        data-deadline="<?php echo date('Y-m-d\TH:i', strtotime($row['deadline'])); ?>"
                                                        data-kategori="<?php echo $row['kategori']; ?>"
                                                        data-bs-toggle="modal" data-bs-target="#editTugasModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-tugas-id="<?php echo $row['tugas_id']; ?>"
                                                        data-judul="<?php echo htmlspecialchars($row['judul']); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#deleteTugasModal">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                                <a href="process/proses_tugas.php?action=toggle_status&tugas_id=<?php echo $row['tugas_id']; ?>" 
                                                  class="btn btn-sm btn-<?php echo ($row['status'] == 'pending') ? 'success' : 'secondary'; ?>">
                                                    <i class="fas <?php echo ($row['status'] == 'pending') ? 'fa-check' : 'fa-undo'; ?>"></i>
                                                    <?php echo ($row['status'] == 'pending') ? 'Complete' : 'Undo'; ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada tugas yang ditambahkan atau tidak ada tugas dalam kategori yang dipilih.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Tugas Modal -->
<div class="modal fade" id="addTugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTugasForm" action="process/proses_tugas.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Tugas</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
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
                        <label for="deskripsi" class="form-label">Deskripsi Tugas</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="deadline" class="form-label">Deadline</label>
                            <input type="datetime-local" class="form-control" id="deadline" name="deadline" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="high">High Priority</option>
                                <option value="medium">Medium Priority</option>
                                <option value="easy">Easy Priority</option>
                            </select>
                        </div>
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

<!-- Edit Tugas Modal -->
<div class="modal fade" id="editTugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTugasForm" action="process/proses_tugas.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_judul" class="form-label">Judul Tugas</label>
                        <input type="text" class="form-control" id="edit_judul" name="judul" required>
                    </div>
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
                        <label for="edit_deskripsi" class="form-label">Deskripsi Tugas</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_deadline" class="form-label">Deadline</label>
                            <input type="datetime-local" class="form-control" id="edit_deadline" name="deadline" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="edit_kategori" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="high">High Priority</option>
                                <option value="medium">Medium Priority</option>
                                <option value="easy">Easy Priority</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="edit_tugas_id" name="tugas_id">
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

<!-- Delete Tugas Modal -->
<div class="modal fade" id="deleteTugasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Hapus Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tugas <strong id="delete_tugas_name"></strong>?</p>
                <p class="text-danger">Perhatian: Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <form id="deleteTugasForm" action="process/proses_tugas.php" method="post">
                <input type="hidden" id="delete_tugas_id" name="tugas_id">
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
            document.getElementById('edit_tugas_id').value = this.getAttribute('data-tugas-id');
            document.getElementById('edit_judul').value = this.getAttribute('data-judul');
            document.getElementById('edit_mk_id').value = this.getAttribute('data-mk-id');
            document.getElementById('edit_deskripsi').value = this.getAttribute('data-deskripsi');
            document.getElementById('edit_deadline').value = this.getAttribute('data-deadline');
            document.getElementById('edit_kategori').value = this.getAttribute('data-kategori');
        });
    });

    // Set data for delete modal
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_tugas_id').value = this.getAttribute('data-tugas-id');
            document.getElementById('delete_tugas_name').textContent = this.getAttribute('data-judul');
        });
    });
    
    // Set default deadline to tomorrow
    document.addEventListener('DOMContentLoaded', function() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(23, 59);
        
        const tomorrowStr = tomorrow.toISOString().slice(0, 16);
        document.getElementById('deadline').value = tomorrowStr;
    });
</script>

<?php
// Close the statement and connection
$stmt->close();
$stmt_mk->close();
// Include footer
include 'includes/footer.php';
?>