<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Log the POST data
    error_log("Form submitted with POST data: " . print_r($_POST, true));
    // Function to sanitize input
    function sanitize($data) {
        return htmlspecialchars(trim($data));
    }

    // Initialize variables and sanitize inputs
    $farmer_id_number = sanitize($_POST['farmer_id'] ?? '');
    $asc_center = sanitize($_POST['asc_center'] ?? '');
    
    // If asc_center is empty, try to extract from farmer_id
    if (empty($asc_center) && !empty($farmer_id_number)) {
        // Farmer ID format: 01/1/0001, extract ASC by looking up the code
        $farmer_parts = explode('/', $farmer_id_number);
        if (count($farmer_parts) >= 2) {
            $dis_code = $farmer_parts[0];
            $asc_num = str_pad($farmer_parts[1], 3, '0', STR_PAD_LEFT);
            $asc_code = $dis_code . '/' . $asc_num;
            
            // Look up ASC ID from code
            $asc_lookup = "SELECT asc_id FROM tbl_asc WHERE asc_code = '$asc_code'";
            $asc_lookup_result = $conn->query($asc_lookup);
            if ($asc_lookup_result && $asc_lookup_result->num_rows > 0) {
                $asc_row = $asc_lookup_result->fetch_assoc();
                $asc_center = $asc_row['asc_id'];
                error_log("Extracted asc_center from farmer_id: $asc_center");
            }
        }
    }
    
    $district = sanitize($_POST['district'] ?? '');
    $asc_name = sanitize($_POST['asc_name'] ?? '');
    $organization_name = sanitize($_POST['registered-org'] ?? '');
    $full_name_en = strtoupper(sanitize($_POST['full-name-english'] ?? ''));
    $full_name_si = sanitize($_POST['full-name-sinhala'] ?? '');
    $initials_name = sanitize($_POST['name-with-initials'] ?? '');
    $birthdate = sanitize($_POST['birth-date'] ?? '');
    // Map form values to ENUM values (keep as lowercase per database)
    $gender = ($_POST['gender'] ?? '') === 'male' ? 'male' : (($_POST['gender'] ?? '') === 'female' ? 'female' : '');
    $nic = sanitize($_POST['nic-number'] ?? '');
    $mobile_phone = sanitize($_POST['mobile-number'] ?? '');
    $land_phone = sanitize($_POST['land-phone'] ?? '');
    $permanent_address_si = sanitize($_POST['permanent-address'] ?? '');
    $gn_division = sanitize($_POST['gn_devision'] ?? '');
    $arpa_name = sanitize($_POST['agrarian-service'] ?? '');
    $temp_address = sanitize($_POST['temporary-address'] ?? '');
    // Map form values to ENUM values (keep as lowercase per database)
    $farming_nature_map = [
        'full-time' => 'full_time',
        'part-time' => 'part_time',
        'home-garden' => 'home_garden',
        'rental' => 'contract'
    ];
    $farming_nature = $farming_nature_map[$_POST['farming-nature'] ?? ''] ?? '';
    $pension_contributor = ($_POST['pension-contributor'] ?? '') === 'yes' ? 'yes' : (($_POST['pension-contributor'] ?? '') === 'no' ? 'no' : '');
    
    // Handle land type checkboxes
    $land_type_goda = isset($_POST['land-type']) && $_POST['land-type'] === 'dry' ? 1 : 0;
    $land_type_mada = isset($_POST['land-type']) && $_POST['land-type'] === 'wet' ? 1 : 0;
    
    $acres = !empty($_POST['acres']) ? floatval($_POST['acres']) : null;
    $roods = !empty($_POST['roods']) ? floatval($_POST['roods']) : null;
    $perches = !empty($_POST['perches']) ? floatval($_POST['perches']) : null;
    
    // Map form values for land ownership
    $land_ownership = sanitize($_POST['ownership-of-the-land'] ?? '');
    // Initialize file paths
    $photo = null;
    $signature = null;

    // Handle file uploads
    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle id_photo upload
    if (isset($_FILES['id-photo']) && $_FILES['id-photo']['error'] == UPLOAD_ERR_OK) {
        $id_photo_tmp = $_FILES['id-photo']['tmp_name'];
        $id_photo_name = 'photo_' . time() . '_' . basename($_FILES['id-photo']['name']);
        $id_photo_path = $upload_dir . $id_photo_name;
        if (move_uploaded_file($id_photo_tmp, $id_photo_path)) {
            $photo = $id_photo_path;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload ID photo']);
            exit;
        }
    }

    // Handle signature upload
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == UPLOAD_ERR_OK) {
        $signature_tmp = $_FILES['signature']['tmp_name'];
        $signature_name = 'signature_' . time() . '_' . basename($_FILES['signature']['name']);
        $signature_path = $upload_dir . $signature_name;
        if (move_uploaded_file($signature_tmp, $signature_path)) {
            $signature = $signature_path;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload signature']);
            exit;
        }
    }

    // Validate required fields
    $required_fields = [
        'farmer_id_number' => $farmer_id_number,
        'asc_center' => $asc_center,
        'district' => $district,
        'asc_name' => $asc_name,
        'full_name_en' => $full_name_en,
        'full_name_si' => $full_name_si,
        'birthdate' => $birthdate,
        'gender' => $gender,
        'nic' => $nic,
        'mobile_phone' => $mobile_phone,
        'permanent_address_si' => $permanent_address_si,
        'farming_nature' => $farming_nature,
        'pension_contributor' => $pension_contributor
    ];

    foreach ($required_fields as $field_name => $value) {
        if (empty($value)) {
            echo json_encode(['status' => 'error', 'message' => "Missing or invalid required field: $field_name"]);
            $conn->close();
            exit;
        }
    }

    // Validate ENUM values
    if (!in_array($gender, ['male', 'female'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid gender value']);
        $conn->close();
        exit;
    }
    if (!in_array($farming_nature, ['full_time', 'part_time', 'home_garden', 'contract'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid farming nature value']);
        $conn->close();
        exit;
    }
    if (!in_array($pension_contributor, ['yes', 'no'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid pension contributor value']);
        $conn->close();
        exit;
    }

    // Prepare SQL statement
    $sql = "INSERT INTO tbl_farmer_applications (
        farmer_id_number, asc_center, photo, signature, district, asc_name, organization_name,
        full_name_en, full_name_si, initials_name, birthdate, gender, nic,
        mobile_phone, land_phone, permanent_address_si, gn_division, arpa_name, temp_address,
        farming_nature, pension_contributor, land_type_goda, land_type_mada, acres, roods, perches, land_ownership
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param(
        'sssssssssssssssssssssiiddds',
        $farmer_id_number, $asc_center, $photo, $signature, $district, $asc_name, $organization_name,
        $full_name_en, $full_name_si, $initials_name, $birthdate, $gender, $nic,
        $mobile_phone, $land_phone, $permanent_address_si, $gn_division, $arpa_name, $temp_address,
        $farming_nature, $pension_contributor, $land_type_goda, $land_type_mada, $acres, $roods, $perches, $land_ownership
    );

    // Execute the statement
    if ($stmt->execute()) {
        $insert_id = $stmt->insert_id;
        $stmt->close();
        $conn->close();
        
        // Redirect to success page or show success message
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>සාර්ථකයි</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .success { color: green; font-size: 24px; margin: 20px; }
        .info { color: #333; font-size: 18px; }
        .button { background: #4CAF50; color: white; padding: 15px 32px; text-decoration: none; display: inline-block; font-size: 16px; margin: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='success'>✓ ගොවි ලියාපදිංචිය සාර්ථකයි!</div>
    <div class='info'>ගොවි හැඳුනුම්පත් අංකය: <strong>$farmer_id_number</strong></div>
    <div class='info'>දත්ත ගබඩා ID: <strong>$insert_id</strong></div>
    <a href='../Farmer_id_form.php' class='button'>නව ආකෘතියක් පිරවන්න</a>
</body>
</html>";
    } else {
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>දෝෂයකි</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: red; font-size: 24px; margin: 20px; }
        .details { color: #666; font-size: 14px; background: #f5f5f5; padding: 20px; margin: 20px auto; max-width: 600px; text-align: left; }
        .button { background: #f44336; color: white; padding: 15px 32px; text-decoration: none; display: inline-block; font-size: 16px; margin: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='error'>✗ දත්ත ගබඩා කිරීමේ දෝෂයකි</div>
    <div class='details'>
        <strong>දෝෂය:</strong><br>" . htmlspecialchars($stmt->error) . "<br><br>
        <strong>SQL දෝෂය:</strong><br>" . htmlspecialchars($conn->error) . "
    </div>
    <a href='../Farmer_id_form.php' class='button'>ආපසු යන්න</a>
</body>
</html>";
        $stmt->close();
        $conn->close();
    }
} else {
    echo "<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>දෝෂයකි</title></head>
<body style='font-family: Arial; text-align: center; padding: 50px;'>
    <div style='color: red; font-size: 24px;'>✗ වලංගු නොවන ඉල්ලීම් ක්‍රමයකි</div>
    <a href='../Farmer_id_form.php' style='background: #f44336; color: white; padding: 15px 32px; text-decoration: none; display: inline-block; margin: 20px;'>ආපසු යන්න</a>
</body>
</html>";
}
?>


