<?php
// Include database configuration
require_once '../config/db_config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action type
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Get user ID
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    
    // Validate user_id is not 0
    if ($user_id == 0) {
        echo "Error: Invalid user ID.";
        exit;
    }
    
    // Process based on action type
    switch ($action) {
        case 'add':
            // Add new mata kuliah
            addMataKuliah($conn, $user_id);
            break;
            
        case 'edit':
            // Edit existing mata kuliah
            editMataKuliah($conn);
            break;
            
        case 'delete':
            // Delete mata kuliah
            deleteMataKuliah($conn);
            break;
            
        default:
            // Invalid action
            echo "Error: Invalid action.";
            exit;
    }
    
    // Redirect back to mata kuliah page
    header("Location: ../mata_kuliah.php");
    exit;
}

// Function to add new mata kuliah
function addMataKuliah($conn, $user_id) {
    // Get form data
    $kode_mk = isset($_POST['kode_mk']) ? trim($_POST['kode_mk']) : '';
    $nama_mk = isset($_POST['nama_mk']) ? trim($_POST['nama_mk']) : '';
    $sks = isset($_POST['sks']) ? intval($_POST['sks']) : 0;
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
    
    // Validate form data
    if (empty($kode_mk) || empty($nama_mk) || $sks <= 0 || empty($semester)) {
        echo "Error: All fields are required and valid.";
        exit;
    }
    
    // Prepare and execute query
    $sql = "INSERT INTO mata_kuliah (user_id, kode_mk, nama_mk, sks, semester) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issis", $user_id, $kode_mk, $nama_mk, $sks, $semester);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to edit existing mata kuliah
function editMataKuliah($conn) {
    // Get form data
    $mk_id = isset($_POST['mk_id']) ? intval($_POST['mk_id']) : 0;
    $kode_mk = isset($_POST['kode_mk']) ? trim($_POST['kode_mk']) : '';
    $nama_mk = isset($_POST['nama_mk']) ? trim($_POST['nama_mk']) : '';
    $sks = isset($_POST['sks']) ? intval($_POST['sks']) : 0;
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    
    // Validate form data
    if ($mk_id <= 0 || empty($kode_mk) || empty($nama_mk) || $sks <= 0 || empty($semester) || $user_id <= 0) {
        echo "Error: All fields are required and valid.";
        exit;
    }
    
    // Prepare and execute query
    $sql = "UPDATE mata_kuliah SET kode_mk = ?, nama_mk = ?, sks = ?, semester = ? WHERE mk_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $kode_mk, $nama_mk, $sks, $semester, $mk_id, $user_id);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to delete mata kuliah
function deleteMataKuliah($conn) {
    // Get form data
    $mk_id = isset($_POST['mk_id']) ? intval($_POST['mk_id']) : 0;
    
    // Validate form data
    if ($mk_id <= 0) {
        echo "Error: Invalid mata kuliah ID.";
        exit;
    }
    
    // Prepare and execute query
    $sql = "DELETE FROM mata_kuliah WHERE mk_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $mk_id);
    
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