<?php
// Include database configuration
require_once '../config/db_config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action type
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Process based on action type
    switch ($action) {
        case 'add':
            // Add new jadwal kuliah
            addJadwalKuliah($conn);
            break;
            
        case 'edit':
            // Edit existing jadwal kuliah
            editJadwalKuliah($conn);
            break;
            
        case 'delete':
            // Delete jadwal kuliah
            deleteJadwalKuliah($conn);
            break;
            
        default:
            // Invalid action
            echo "Error: Invalid action.";
            exit;
    }
    
    // Redirect back to jadwal kuliah page
    header("Location: ../jadwal_kuliah.php");
    exit;
}

// Function to add new jadwal kuliah
function addJadwalKuliah($conn) {
    // Get form data
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $mk_id = isset($_POST['mk_id']) ? intval($_POST['mk_id']) : 0;
    $hari = isset($_POST['hari']) ? trim($_POST['hari']) : '';
    $jam_mulai = isset($_POST['jam_mulai']) ? trim($_POST['jam_mulai']) : '';
    $jam_selesai = isset($_POST['jam_selesai']) ? trim($_POST['jam_selesai']) : '';
    $lokasi = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
    
    // Validate required form data
    if ($user_id <= 0 || $mk_id <= 0 || empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        echo "Error: Required fields are missing.";
        exit;
    }
    
    // Convert to time format
    $jam_mulai = date('H:i:s', strtotime($jam_mulai));
    $jam_selesai = date('H:i:s', strtotime($jam_selesai));
    
    // Check if end time is after start time
    if (strtotime($jam_selesai) <= strtotime($jam_mulai)) {
        echo "Error: Jam selesai harus setelah jam mulai.";
        exit;
    }
    
    // Check for scheduling conflicts
    $sql_check = "SELECT * FROM jadwal_kuliah 
                  WHERE user_id = ? AND hari = ? 
                  AND ((jam_mulai <= ? AND jam_selesai > ?) OR 
                       (jam_mulai < ? AND jam_selesai >= ?) OR 
                       (jam_mulai >= ? AND jam_selesai <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("isssssss", $user_id, $hari, $jam_selesai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_mulai, $jam_selesai);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        echo "Error: Ada jadwal lain yang konflik dengan waktu yang dipilih pada hari " . $hari . ".";
        exit;
    }
    $stmt_check->close();
    
    // Prepare and execute query
    $sql = "INSERT INTO jadwal_kuliah (user_id, mk_id, hari, jam_mulai, jam_selesai, lokasi) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $user_id, $mk_id, $hari, $jam_mulai, $jam_selesai, $lokasi);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to edit existing jadwal kuliah
function editJadwalKuliah($conn) {
    // Get form data
    $jadwal_id = isset($_POST['jadwal_id']) ? intval($_POST['jadwal_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $mk_id = isset($_POST['mk_id']) ? intval($_POST['mk_id']) : 0;
    $hari = isset($_POST['hari']) ? trim($_POST['hari']) : '';
    $jam_mulai = isset($_POST['jam_mulai']) ? trim($_POST['jam_mulai']) : '';
    $jam_selesai = isset($_POST['jam_selesai']) ? trim($_POST['jam_selesai']) : '';
    $lokasi = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
    
    // Validate required form data
    if ($jadwal_id <= 0 || $user_id <= 0 || $mk_id <= 0 || empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        echo "Error: Required fields are missing.";
        exit;
    }
    
    // Convert to time format
    $jam_mulai = date('H:i:s', strtotime($jam_mulai));
    $jam_selesai = date('H:i:s', strtotime($jam_selesai));
    
    // Check if end time is after start time
    if (strtotime($jam_selesai) <= strtotime($jam_mulai)) {
        echo "Error: Jam selesai harus setelah jam mulai.";
        exit;
    }
    
    // Check for scheduling conflicts (excluding the current jadwal)
    $sql_check = "SELECT * FROM jadwal_kuliah 
                  WHERE user_id = ? AND hari = ? AND jadwal_id != ?
                  AND ((jam_mulai <= ? AND jam_selesai > ?) OR 
                       (jam_mulai < ? AND jam_selesai >= ?) OR 
                       (jam_mulai >= ? AND jam_selesai <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("isisssss", $user_id, $hari, $jadwal_id, $jam_selesai, $jam_mulai, $jam_selesai, $jam_mulai, $jam_mulai, $jam_selesai);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        echo "Error: Ada jadwal lain yang konflik dengan waktu yang dipilih pada hari " . $hari . ".";
        exit;
    }
    $stmt_check->close();
    
    // Prepare and execute query
    $sql = "UPDATE jadwal_kuliah SET mk_id = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, lokasi = ? WHERE jadwal_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssii", $mk_id, $hari, $jam_mulai, $jam_selesai, $lokasi, $jadwal_id, $user_id);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to delete jadwal kuliah
function deleteJadwalKuliah($conn) {
    // Get form data
    $jadwal_id = isset($_POST['jadwal_id']) ? intval($_POST['jadwal_id']) : 0;
    
    // Validate form data
    if ($jadwal_id <= 0) {
        echo "Error: Invalid jadwal ID.";
        exit;
    }
    
    // Prepare and execute query
    $sql = "DELETE FROM jadwal_kuliah WHERE jadwal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $jadwal_id);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}
?>