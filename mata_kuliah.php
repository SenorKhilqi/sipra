<?php
// Include database configuration
require_once 'config/db_config.php';

// For demonstration, we're using user_id = 1 
// In a real application, you would get this from the session after user login
$user_id = 1;

// Prepare and execute query to get all mata kuliah for the specified user
$sql = "SELECT * FROM mata_kuliah WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Include header
include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-book"></i> Mata Kuliah</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMataKuliahModal">
                <i class="fas fa-plus"></i> Tambah Mata Kuliah
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Kode MK</th>
                            <th>Nama Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Semester</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['kode_mk']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_mk']); ?></td>
                                <td><?php echo htmlspecialchars($row['sks']); ?></td>
                                <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info edit-btn" 
                                                data-mk-id="<?php echo $row['mk_id']; ?>"
                                                data-kode="<?php echo htmlspecialchars($row['kode_mk']); ?>"
                                                data-nama="<?php echo htmlspecialchars($row['nama_mk']); ?>"
                                                data-sks="<?php echo htmlspecialchars($row['sks']); ?>"
                                                data-semester="<?php echo htmlspecialchars($row['semester']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#editMataKuliahModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                data-mk-id="<?php echo $row['mk_id']; ?>"
                                                data-nama="<?php echo htmlspecialchars($row['nama_mk']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#deleteMataKuliahModal">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada mata kuliah yang ditambahkan. Silakan tambahkan mata kuliah baru.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Mata Kuliah Modal -->
<div class="modal fade" id="addMataKuliahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMataKuliahForm" action="process/proses_mata_kuliah.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_mk" class="form-label">Kode Mata Kuliah</label>
                        <input type="text" class="form-control" id="kode_mk" name="kode_mk" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_mk" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="nama_mk" name="nama_mk" required>
                    </div>
                    <div class="mb-3">
                        <label for="sks" class="form-label">SKS</label>
                        <input type="number" class="form-control" id="sks" name="sks" min="1" max="6" required>
                    </div>
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
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

<!-- Edit Mata Kuliah Modal -->
<div class="modal fade" id="editMataKuliahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMataKuliahForm" action="process/proses_mata_kuliah.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_kode_mk" class="form-label">Kode Mata Kuliah</label>
                        <input type="text" class="form-control" id="edit_kode_mk" name="kode_mk" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_mk" class="form-label">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="edit_nama_mk" name="nama_mk" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sks" class="form-label">SKS</label>
                        <input type="number" class="form-control" id="edit_sks" name="sks" min="1" max="6" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_semester" class="form-label">Semester</label>
                        <select class="form-select" id="edit_semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <input type="hidden" id="edit_mk_id" name="mk_id">
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

<!-- Delete Mata Kuliah Modal -->
<div class="modal fade" id="deleteMataKuliahModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Hapus Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus mata kuliah <strong id="delete_mk_name"></strong>?</p>
                <p class="text-danger">Perhatian: Semua data terkait mata kuliah ini (tugas dan jadwal) juga akan dihapus!</p>
            </div>
            <form id="deleteMataKuliahForm" action="process/proses_mata_kuliah.php" method="post">
                <input type="hidden" id="delete_mk_id" name="mk_id">
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
            document.getElementById('edit_mk_id').value = this.getAttribute('data-mk-id');
            document.getElementById('edit_kode_mk').value = this.getAttribute('data-kode');
            document.getElementById('edit_nama_mk').value = this.getAttribute('data-nama');
            document.getElementById('edit_sks').value = this.getAttribute('data-sks');
            document.getElementById('edit_semester').value = this.getAttribute('data-semester');
        });
    });

    // Set data for delete modal
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete_mk_id').value = this.getAttribute('data-mk-id');
            document.getElementById('delete_mk_name').textContent = this.getAttribute('data-nama');
        });
    });
</script>

<?php
// Close the statement and connection
$stmt->close();
// Include footer
include 'includes/footer.php';
?>