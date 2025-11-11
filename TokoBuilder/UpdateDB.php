<!-- NIATNYA JADI CLASS GENERAL KHUSUS UTK HANDLING UPDATE DB -->

<?php
require_once('db.php'); // Pastikan file ini berisi koneksi database Anda

header('Content-Type: application/json');

try {
    // Ambil input dari client (AJAX)
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (!isset($inputData['table']) || !isset($inputData['updates'])) {
        throw new Exception('Invalid request format. "table" and "updates" are required.');
    }

    $table = $inputData['table'];
    $updates = $inputData['updates'];

    foreach ($updates as $update) {
        if (!isset($update['id']) || !isset($update['columns'])) {
            throw new Exception('Each update must contain "id" and "columns".');
        }

        $id = $update['id'];
        $columns = $update['columns'];

        // Bangun query SQL dinamis
        $setParts = [];
        foreach ($columns as $column => $value) {
            $setParts[] = sprintf("%s = '%s'", $column, mysqli_real_escape_string($conn, $value));
        }
        $setClause = implode(", ", $setParts);

        // Jalankan query update
        $query = sprintf("UPDATE %s SET %s WHERE id = '%s'", 
            mysqli_real_escape_string($conn, $table), 
            $setClause, 
            mysqli_real_escape_string($conn, $id)
        );

        if (!mysqli_query($conn, $query)) {
            throw new Exception('Failed to execute query: ' . mysqli_error($conn));
        }
    }

    echo json_encode(['success' => true, 'message' => 'Updates applied successfully.']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
