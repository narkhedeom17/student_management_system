<?php
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
$city_id = $_POST['city_id'] ?? '';
if ($city_id) {
    $areas = $conn->query("SELECT area_id, area_name FROM tbl_area WHERE city_id = $city_id");
    echo "<option value=''>Select Area</option>";
    while ($row = $areas->fetch_assoc()) {
        echo "<option value='{$row['area_id']}'>{$row['area_name']}</option>";
    }
}
?>
