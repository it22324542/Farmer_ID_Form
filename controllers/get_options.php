<?php
include 'includes/db_connect.php';

if (isset($_GET['type'])) {
    if ($_GET['type'] == 'districts') {
        $sql = "SELECT dis_id, dis_sname FROM tbl_district";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['dis_id'] . "'>" . $row['dis_sname'] . "</option>";
        }
    } elseif ($_GET['type'] == 'asc_centers') {
        $district = $_GET['district'] ?? '';
        $sql = "SELECT asc_id, asc_sname FROM tbl_asc WHERE dis_id = '$district'";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['asc_id'] . "'>" . $row['asc_name'] . "</option>";
        }
    } elseif ($_GET['type'] == 'gn_divisions') {
        $sql = "SELECT gnd_id, gnd_sname FROM tbl_gnd";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['gnd_id'] . "'>" . $row['gnd_sname'] . "</option>";
        }
    }
}
$conn->close();
?>
