<?php
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $farmer_id_number = mysqli_real_escape_string($conn, $_POST['farmer_id_number']);
    $asc_center = mysqli_real_escape_string($conn, $_POST['asc_center']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $asc_name = mysqli_real_escape_string($conn, $_POST['asc_name']);
    $organization_name = mysqli_real_escape_string($conn, $_POST['organization_name']);
    $full_name_en = mysqli_real_escape_string($conn, $_POST['full_name_en']);
    $full_name_si = mysqli_real_escape_string($conn, $_POST['full_name_si']);
    $initials_name = mysqli_real_escape_string($conn, $_POST['initials_name']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $nic = mysqli_real_escape_string($conn, $_POST['nic']);
    $mobile_phone = mysqli_real_escape_string($conn, $_POST['mobile_phone']);
    $land_phone = mysqli_real_escape_string($conn, $_POST['land_phone'] ?? '');
    $permanent_address_si = mysqli_real_escape_string($conn, $_POST['permanent_address_si']);
    $gn_division = mysqli_real_escape_string($conn, $_POST['gn_division']);
    $arpa_name = mysqli_real_escape_string($conn, $_POST['arpa_name']);
    $ai_range = mysqli_real_escape_string($conn, $_POST['ai_range']);
    $temp_address = mysqli_real_escape_string($conn, $_POST['temp_address'] ?? '');
    $farming_nature = mysqli_real_escape_string($conn, $_POST['farming_nature']);
    $pension_contributor = mysqli_real_escape_string($conn, $_POST['pension_contributor']);
    $land_type_goda = isset($_POST['land_type_goda']) ? 1 : 0;
    $land_type_mada = isset($_POST['land_type_mada']) ? 1 : 0;
    $acres = mysqli_real_escape_string($conn, $_POST['acres'] ?? '0.00');
    $roods = mysqli_real_escape_string($conn, $_POST['roods'] ?? '0.00');
    $perches = mysqli_real_escape_string($conn, $_POST['perches'] ?? '0.00');
    $land_ownership = mysqli_real_escape_string($conn, $_POST['land_ownership']);

    // Handle file uploads
    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = $upload_dir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    $signature = '';
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $signature = $upload_dir . basename($_FILES['signature']['name']);
        move_uploaded_file($_FILES['signature']['tmp_name'], $signature);
    }

    // SQL query
    $sql = "INSERT INTO tbl_farmer_applications (
        farmer_id_number, asc_center, photo, signature, district, asc_name, organization_name,
        full_name_en, full_name_si, initials_name, birthdate, gender, nic, mobile_phone,
        land_phone, permanent_address_si, gn_division, arpa_name, ai_range, temp_address, farming_nature,
        pension_contributor, land_type_goda, land_type_mada, acres, roods, perches, land_ownership
    ) VALUES (
        '$farmer_id_number', '$asc_center', '$photo', '$signature', '$district', '$asc_name',
        '$organization_name', '$full_name_en', '$full_name_si', '$initials_name', '$birthdate',
        '$gender', '$nic', '$mobile_phone', '$land_phone', '$permanent_address_si', '$gn_division', '$arpa_name',
        '$ai_range', '$temp_address', '$farming_nature', '$pension_contributor', '$land_type_goda',
        '$land_type_mada', '$acres', '$roods', '$perches', '$land_ownership'
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Form submitted successfully! Record ID: " . $conn->insert_id;
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
