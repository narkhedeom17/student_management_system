<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $enrollment_no = $_POST['enrollment_no'];
    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["profile"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file)) {
        $conn = new mysqli("localhost", "root", "12345678", "newton_db");
        if (!$conn->connect_error) {
            $stmt = $conn->prepare("UPDATE student SET profile = ? WHERE enrollment_no = ?");
            $stmt->bind_param("ss", $target_file, $enrollment_no);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            echo "Profile uploaded successfully.";
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
