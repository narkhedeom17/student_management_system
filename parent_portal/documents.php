<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$enrollment_no = $_SESSION['enrollment_no'] ?? '';
$documents = [];

if (!empty($enrollment_no)) {
    $conn = new mysqli("localhost", "root", "12345678", "newton_db");
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT document_type, file_path FROM student_documents WHERE enrollment_no = ?");
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Documents</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
  }

  .content {
    margin-left: 220px;
    padding: 30px;
  }

  h2 {
    text-align: center;
    color: #093c56;
    margin-bottom: 20px;
    font-size: 22px;
  }

  table {
    width: 90%;
    margin: 0 auto;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    font-size: 14px;
  }

  th, td {
    padding: 8px 10px;
    border: 1px solid #ddd;
    text-align: left;
  }

  th {
    background-color: #093c56;
    color: white;
    font-size: 15px;
  }

  tr:hover {
    background-color: #f1f1f1;
  }

  a.view-link {
    color: #093c56;
    text-decoration: none;
    font-weight: 500;
  }

  a.view-link:hover {
    text-decoration: underline;
  }

  .no-docs {
    text-align: center;
    padding: 15px;
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    width: 90%;
    margin: 0 auto;
    font-size: 14px;
  }
</style>

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
  <h2>Your Uploaded Documents</h2>

  <?php if (count($documents) > 0): ?>
    <table>
      <tr>
        <th>Document Type</th>
        <th>View</th>
      </tr>
      <?php foreach ($documents as $doc): ?>
        <tr>
          <td><?= htmlspecialchars($doc['document_type']) ?></td>
          <td>
            <a class="view-link" href="<?= 'http://localhost/project_backend/' . htmlspecialchars($doc['file_path']) ?>" target="_blank">
              View Document
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <div class="no-docs">No documents found for your profile.</div>
  <?php endif; ?>
</div>

</body>
</html>
