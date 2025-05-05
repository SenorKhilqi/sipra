<?php
// Include database configuration
require_once '../config/db_config.php';

// Check if form is submitted or action through GET (for toggle status)
if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['action'])) {
    // Get action type
    $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
    
    // Process based on action type
    switch ($action) {
        case 'add':
            // Add new tugas
            addTugas($conn);
            break;
            
        case 'edit':
            // Edit existing tugas
            editTugas($conn);
            break;
            
        case 'delete':
            // Delete tugas
            deleteTugas($conn);
            break;
            
        case 'toggle_status':
            // Toggle status (pending/completed)
            toggleStatus($conn);
            break;
            
        default:
            // Invalid action
            echo "Error: Invalid action.";
            exit;
    }
    
    // Redirect back to tugas page
    header("Location: ../tugas.php");
    exit;
}

// Function to add new tugas
function addTugas($conn) {
    // Get form data
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $mk_id = isset($_POST['mk_id']) ? intval($_POST['mk_id']) : 0;
    $judul = isset($_POST['judul']) ? trim($_POST['judul']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    $deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
    $kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
    
    // Validate form data
    if ($user_id <= 0 || $mk_id <= 0 || empty($judul) || empty($deadline) || empty($kategori)) {
        echo "Error: All fields are required (except description).";
        exit;
    }
    
    // Format datetime
    $deadline = date('Y-m-d H:i:s', strtotime($deadline));
    
    // Prepare and execute query
    $sql = "INSERT INTO tugas (user_id, mk_id, judul, deskripsi, deadline, kategori) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $user_id, $mk_id, $judul, $deskripsi, $deadline, $kategori);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to edit existing tugas
function editTugas($conn) {
    // Get form data
    $tugas_id = isset($_POST['tugas_id']) ? intval($_POST['tugas_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $mk_id = isset($_POST['mk_id']) ? intval($_POST['mk_id']) : 0;
    $judul = isset($_POST['judul']) ? trim($_POST['judul']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    $deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : '';
    $kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
    
    // Validate form data
    if ($tugas_id <= 0 || $user_id <= 0 || $mk_id <= 0 || empty($judul) || empty($deadline) || empty($kategori)) {
        echo "Error: All fields are required (except description).";
        exit;
    }
    
    // Format datetime
    $deadline = date('Y-m-d H:i:s', strtotime($deadline));
    
    // Prepare and execute query
    $sql = "UPDATE tugas SET mk_id = ?, judul = ?, deskripsi = ?, deadline = ?, kategori = ? WHERE tugas_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssis", $mk_id, $judul, $deskripsi, $deadline, $kategori, $tugas_id, $user_id);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to delete tugas
function deleteTugas($conn) {
    // Get form data
    $tugas_id = isset($_POST['tugas_id']) ? intval($_POST['tugas_id']) : 0;
    
    // Validate form data
    if ($tugas_id <= 0) {
        echo "Error: Invalid tugas ID.";
        exit;
    }
    
    // Prepare and execute query
    $sql = "DELETE FROM tugas WHERE tugas_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tugas_id);
    
    if ($stmt->execute()) {
        // Success
        $stmt->close();
    } else {
        // Error
        echo "Error: " . $stmt->error;
        exit;
    }
}

// Function to toggle status
function toggleStatus($conn) {
    // Get tugas ID
    $tugas_id = isset($_GET['tugas_id']) ? intval($_GET['tugas_id']) : 0;
    
    // Validate tugas ID
    if ($tugas_id <= 0) {
        echo "Error: Invalid tugas ID.";
        exit;
    }
    
    // Get current status
    $sql_get = "SELECT status FROM tugas WHERE tugas_id = ?";
    $stmt_get = $conn->prepare($sql_get);
    $stmt_get->bind_param("i", $tugas_id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_status = $row['status'];
        $new_status = ($current_status == 'pending') ? 'completed' : 'pending';
        
        // Update status
        $sql_update = "UPDATE tugas SET status = ? WHERE tugas_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $tugas_id);
        
        if ($stmt_update->execute()) {
            // Success
            $stmt_update->close();
        } else {
            // Error
            echo "Error: " . $stmt_update->error;
            exit;
        }
        
        $stmt_get->close();
    } else {
        echo "Error: Tugas not found.";
        exit;
    }
}
?>