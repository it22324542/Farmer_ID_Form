<?php
include '../includes/db_connect.php';

header('Content-Type: application/json');

if (isset($_POST['asc_id'])) {
    $asc_id = mysqli_real_escape_string($conn, $_POST['asc_id']);
    
    // Step 1: Get asc_code from tbl_asc
    $asc_query = "SELECT asc_code FROM tbl_asc WHERE asc_id = '$asc_id'";
    $asc_result = $conn->query($asc_query);
    
    if ($asc_result && $asc_result->num_rows > 0) {
        $asc_row = $asc_result->fetch_assoc();
        $asc_code = $asc_row['asc_code']; // e.g., "01/010"
        
        // Step 2: Format asc_code (01/010 -> 01/10)
        $parts = explode('/', $asc_code);
        $first_part = $parts[0]; // "01"
        $second_part = ltrim($parts[1], '0'); // "010" -> "10"
        if ($second_part == '') $second_part = '0'; // Handle case like "000"
        
        $formatted_code = $first_part . '/' . $second_part; // "01/10"
        
        // Step 3: Get the last sequence number for this ASC
        $sequence_query = "SELECT farmer_id_number FROM tbl_farmer_applications 
                          WHERE farmer_id_number LIKE '$formatted_code/%' 
                          ORDER BY farmer_id_number DESC 
                          LIMIT 1";
        $sequence_result = $conn->query($sequence_query);
        
        $next_sequence = 1; // Default starting sequence
        
        if ($sequence_result && $sequence_result->num_rows > 0) {
            $sequence_row = $sequence_result->fetch_assoc();
            $last_farmer_id = $sequence_row['farmer_id_number']; // e.g., "01/10/0005"
            
            // Extract the last sequence number
            $id_parts = explode('/', $last_farmer_id);
            if (count($id_parts) == 3) {
                $last_sequence = intval($id_parts[2]); // "0005" -> 5
                $next_sequence = $last_sequence + 1;
            }
        }
        
        // Step 4: Format sequence number with leading zeros (4 digits)
        $sequence_number = str_pad($next_sequence, 4, '0', STR_PAD_LEFT); // "0001"
        
        // Step 5: Generate final farmer ID
        $farmer_id = $formatted_code . '/' . $sequence_number; // "01/10/0001"
        
        echo json_encode([
            'success' => true,
            'farmer_id' => $farmer_id,
            'asc_code' => $asc_code,
            'formatted_code' => $formatted_code,
            'sequence' => $sequence_number
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Service center not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No ASC ID provided'
    ]);
}

$conn->close();
?>
