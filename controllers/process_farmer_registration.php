<? php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Function to sanitize input
    function sanitize($data) {
        return htmlspecialchars(trim($data));
    }

    // Initialize variables and sanitize inputs
    $farmer_id = sanitize($_POST['farmer_id'] ?? '');
    $asc_center = sanitize($_POST['asc_center'] ?? '');
    $district = sanitize($_POST['district'] ?? '');
    $asc_name = sanitize($_POST['asc_name'] ?? '');
    $registered_org = sanitize($_POST['registered-org'] ?? '');
    $full_name_english = strtoupper(sanitize($_POST['full-name-english'] ?? ''));
    $full_name_sinhala = sanitize($_POST['full-name-sinhala'] ?? '');
    $name_with_initials = sanitize($_POST['name-with-initials'] ?? '');
    $birth_date = sanitize($_POST['birth-date'] ?? '');
    // Map form values to ENUM values
    $gender = ($_POST['gender'] ?? '') === 'male' ? 'Male' : (($_POST['gender'] ?? '') === 'female' ? 'Female' : '');
    $nic_number = sanitize($_POST['nic-number'] ?? '');
    $mobile_number = sanitize($_POST['mobile-number'] ?? '');
    $land_phone = sanitize($_POST['land-phone'] ?? '');
    $permanent_address = sanitize($_POST['permanent-address'] ?? '');
    $gn_devision = sanitize($_POST['gn_devision'] ?? '');
    $arpa_name = sanitize($_POST['agrarian-service'] ?? ''); // Map agrarian-service to arpa_name
    $temporary_address = sanitize($_POST['temporary-address'] ?? '');
    // Map form values to ENUM values
    $farming_nature_map = [
        'full-time' => 'FullTime',
        'part-time' => 'PartTime',
        'home-garden' => 'HomeGarden',
        'rental' => 'Contract'
    ];
    $farming_nature = $farming_nature_map[$_POST['farming-nature'] ?? ''] ?? '';
    $pension_contributor = ($_POST['pension-contributor'] ?? '') === 'yes' ? 'Yes' : (($_POST['pension-contributor'] ?? '') === 'no' ? 'No' : '');
    $land_type = ($_POST['land-type'] ?? '') === 'dry' ? 'Upland' : (($_POST['land-type'] ?? '') === 'wet' ? 'Paddy' : '');
    $acres = !empty($_POST['acres']) ? floatval($_POST['acres']) : null;
    $roods = !empty($_POST['roods']) ? intval($_POST['roods']) : null;
    $perches = !empty($_POST['perches']) ? intval($_POST['perches']) : null;
    // Map form values to ENUM values
    $ownership_map = [
        'land-owner' => 'Owner',
        'tenant' => 'Sharecropper',
        'landowner-farmer' => 'FarmerOwner'
    ];
    $ownership_of_the_land = $ownership_map[$_POST['ownership-of-the-land'] ?? ''] ?? '';

    // Initialize file paths
    $id_photo = null;
    $signature = null;

    // Handle file uploads
    $upload_dir = '../Uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle id_photo upload
    if (isset($_FILES['id-photo']) && $_FILES['id-photo']['error'] == UPLOAD_ERR_OK) {
        $id_photo_tmp = $_FILES['id-photo']['tmp_name'];
        $id_photo_name = 'id_photo_' . time() . '_' . basename($_FILES['id-photo']['name']);
        $id_photo_path = $upload_dir . $id_photo_name;
        if (move_uploaded_file($id_photo_tmp, $id_photo_path)) {
            $id_photo = $id_photo_path;
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
        'farmer_id' => $farmer_id,
        'asc_center' => $asc_center,
        'district' => $district,
        'asc_name' => $asc_name,
        'full_name_english' => $full_name_english,
        'full_name_sinhala' => $full_name_sinhala,
        'birth_date' => $birth_date,
        'gender' => $gender,
        'nic_number' => $nic_number,
        'permanent_address' => $permanent_address,
        'farming_nature' => $farming_nature,
        'pension_contributor' => $pension_contributor,
        'land_type' => $land_type,
        'ownership_of_the_land' => $ownership_of_the_land
    ];

    foreach ($required_fields as $field_name => $value) {
        if (empty($value)) {
            echo json_encode(['status' => 'error', 'message' => "Missing or invalid required field: $field_name"]);
            $conn->close();
            exit;
        }
    }

    // Validate ENUM values
    if (!in_array($gender, ['Male', 'Female'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid gender value']);
        $conn->close();
        exit;
    }
    if (!in_array($farming_nature, ['FullTime', 'PartTime', 'HomeGarden', 'Contract'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid farming nature value']);
        $conn->close();
        exit;
    }
    if (!in_array($pension_contributor, ['Yes', 'No'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid pension contributor value']);
        $conn->close();
        exit;
    }
    if (!in_array($land_type, ['Upland', 'Paddy'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid land type value']);
        $conn->close();
        exit;
    }
    if (!in_array($ownership_of_the_land, ['Owner', 'Sharecropper', 'FarmerOwner'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ownership value']);
        $conn->close();
        exit;
    }

    // Prepare SQL statement
    $sql = "INSERT INTO farmer_registration (
        farmer_id, asc_center, id_photo, signature, district, asc_name, registered_org,
        full_name_english, full_name_sinhala, name_with_initials, birth_date, gender, nic_number,
        mobile_number, land_phone, permanent_address, gn_devision, arpa_name, temporary_address,
        farming_nature, pension_contributor, land_type, acres, roods, perches, ownership_of_the_land
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param(
        'sssssssssssissssssssssdiis',
        $farmer_id, $asc_center, $id_photo, $signature, $district, $asc_name, $registered_org,
        $full_name_english, $full_name_sinhala, $name_with_initials, $birth_date, $gender, $nic_number,
        $mobile_number, $land_phone, $permanent_address, $gn_devision, $arpa_name, $temporary_address,
        $farming_nature, $pension_contributor, $land_type, $acres, $roods, $perches, $ownership_of_the_land
    );

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Farmer registered successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database insertion failed: ' . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>


