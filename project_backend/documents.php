<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$enrollment_no = $_GET['enrollment_no'] ?? '';
if (!$enrollment_no) die("Enrollment number not specified in the URL.");

// Fetch student full name
$student_name = '';
$stmt = $conn->prepare("SELECT fname, mname, lname FROM student WHERE enrollment_no = ?");
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $student_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['lname']);
}
$stmt->close();

// Handle upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["upload_doc"])) {
    $doc_type = $_POST["document_type"];
    $upload_date = $_POST["upload_date"];
    $file = $_FILES["doc_file"];

    if ($file["error"] === 0) {
        $folder = "uploads/";
        if (!is_dir($folder)) mkdir($folder);
        $filename = uniqid() . "_" . basename($file["name"]);
        $target_file = $folder . $filename;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $dup_check = $conn->prepare("SELECT COUNT(*) AS cnt FROM student_documents WHERE enrollment_no = ? AND document_type = ?");
            $dup_check->bind_param("ss", $enrollment_no, $doc_type);
            $dup_check->execute();
            $dup_result = $dup_check->get_result();
            $row = $dup_result->fetch_assoc();
            $dup_check->close();

            if ($row['cnt'] == 0) {
                $stmt = $conn->prepare("INSERT INTO student_documents (enrollment_no, document_type, file_path, upload_date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $enrollment_no, $doc_type, $target_file, $upload_date);
                $stmt->execute();
                $stmt->close();
                $message = "Document uploaded successfully!";
            } else {
                $message = "This document type is already uploaded.";
            }
        } else {
            $message = "Failed to upload file.";
        }
    } else {
        $message = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Documents</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 750px;
            margin: 40px auto;
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        input, select {
            padding: 10px;
            margin: 8px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: rgb(0, 96, 155);
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        button:hover {
            background-color: rgb(0, 80, 130);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color:rgb(0, 96, 155);;
            color: white;
        }

        .message {
            padding: 10px;
            margin-top: 15px;
            border-left: 4px solid #198754;
            background: #d1e7dd;
            color: #155724;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Upload Documents for: <span style="color: green"><?= htmlspecialchars($student_name) ?></span></h2>

    <?php if (isset($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="documents.php?enrollment_no=<?= urlencode($enrollment_no) ?>">
        <label>Choose Document Type:</label>
        <select name="document_type" required>
            <option value="">-- Select Document Type --</option>
            <option value="Aadhar Card">Aadhar Card</option>
            <option value="Birth Certificate">Birth Certificate</option>
            <option value="Previous Marksheet">Previous Marksheet</option>
            <option value="Transfer Certificate">Transfer Certificate</option>
            <option value="Passport Photo">Passport Photo</option>
            <option value="Medical Certificate">Medical Certificate</option>
            <option value="Domicile">Domicile</option>
            <option value="Cast Certificate">Cast Certificate</option>
            <option value="Income Certificate">Income Certificate</option>
            <option value="Ration Card">Ration Card</option>
        </select>

        <input type="hidden" name="upload_date" value="<?= date('Y-m-d') ?>">
        <label>Choose File:</label>
        <input type="file" name="doc_file" required>

        <button type="submit" name="upload_doc">Upload Document</button>
    </form>

    <hr>
    <h3>Uploaded Documents:</h3>

    <?php
    $stmt = $conn->prepare("SELECT * FROM student_documents WHERE enrollment_no = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $enrollment_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Document Type</th>
                <th>View</th>
                <th>Uploaded At</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['document_type']) ?></td>
                    <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">View</a></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No documents uploaded yet.</p>
    <?php endif;
    $stmt->close(); ?>
</div>

</body>
</html>
