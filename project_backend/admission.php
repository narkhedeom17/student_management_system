<?php
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Records - Newton School</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f0f2f5;
    }

    .container {
      max-width: 96%;
      margin: 20px auto;
      background: white;
      padding: 20px 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 16px;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .search-box {
      display: flex;
      gap: 8px;
    }

    .search-box input[type="text"] {
      padding: 7px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .search-box button {
      padding: 7px 12px;
      border-radius: 6px;
      background-color: #3498db;
      color: white;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .search-box button:hover {
      background-color: #2980b9;
    }

    .btn-group {
      display: flex;
      gap: 10px;
    }

    .btn {
      padding: 10px 16px;
      font-size: 14px;
      font-weight: 600;
      text-transform: capitalize;
      border: none;
      border-radius: 8px;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      color: white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-add {
      background: linear-gradient(135deg, #2ecc71, #27ae60);
    }

    .btn-add:hover {
      background: linear-gradient(135deg, #27ae60, #1e8449);
      transform: scale(1.05);
    }

    .btn-prev {
      background: linear-gradient(135deg, #3498db, #2980b9);
    }

    .btn-prev:hover {
      background: linear-gradient(135deg, #2980b9, #2471a3);
      transform: scale(1.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      border-radius: 6px;
      overflow: hidden;
    }

    th, td {
      padding: 8px 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
      font-size: 14px;
    }

    th {
      background-color: rgb(0, 96, 155);
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #eef1f5;
    }

    .edit-btn, .delete-btn, .view-btn {
      padding: 6px 12px;
      font-size: 13px;
      font-weight: 500;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      margin: 0 2px;
      transition: all 0.2s ease;
    }

    .edit-btn {
      background: linear-gradient(135deg, #3498db, #2980b9);
      color: white;
    }

    .edit-btn:hover {
      background: linear-gradient(135deg, #2980b9, #2471a3);
      transform: scale(1.03);
    }

    .delete-btn {
      background: linear-gradient(135deg, #e74c3c, #c0392b);
      color: white;
    }

    .delete-btn:hover {
      background: linear-gradient(135deg, #c0392b, #a93226);
      transform: scale(1.03);
    }

    .view-btn {
      background: linear-gradient(135deg, #27ae60, #1e8449);
      color: white;
    }

    .view-btn:hover {
      background: linear-gradient(135deg, #1e8449, #196f3d);
      transform: scale(1.03);
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
  <h2>Student Admission Records</h2>

  <div class="top-bar">
    <form method="GET" class="search-box">
      <input type="text" name="search" placeholder="Search name, enrollment, or city" value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Search</button>
    </form>

    <div class="btn-group">
      <a href="form.php" class="btn btn-add">+ Add Admission</a>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Sr. No.</th>
        <th>Enrollment No.</th>
        <th>Student Name</th>
        <th>Gender</th>
        <th>DOB</th>
        <th>City</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "
        SELECT s.*, c.city_name 
        FROM student s
        LEFT JOIN tbl_city c ON s.city_id = c.city_id
        WHERE s.status = 'Active'
      ";

      if ($search !== '') {
        $searchEscaped = $conn->real_escape_string($search);
        $sql .= "
          AND (
            s.enrollment_no LIKE '%$searchEscaped%' 
            OR s.fname LIKE '%$searchEscaped%' 
            OR s.lname LIKE '%$searchEscaped%' 
            OR c.city_name LIKE '%$searchEscaped%'
          )
        ";
      }

      $sql .= " ORDER BY s.enrollment_no ASC";

      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $sr = 1;
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
            <td>" . $sr++ . "</td>
            <td>" . htmlspecialchars($row["enrollment_no"]) . "</td>
            <td>" . htmlspecialchars($row["fname"] . ' ' . $row["lname"]) . "</td>
            <td>" . htmlspecialchars($row["gender"]) . "</td>
            <td>" . htmlspecialchars($row["dob"]) . "</td>
            <td>" . htmlspecialchars($row["city_name"] ?? '-') . "</td>
            <td>
              <a href='edit.php?enrollment_no=" . urlencode($row["enrollment_no"]) . "' class='edit-btn'>Edit</a>
              <a href='delete.php?enrollment_no=" . urlencode($row["enrollment_no"]) . "' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
              <a href='viewdetail_student.php?enrollment_no=" . urlencode($row["enrollment_no"]) . "' class='view-btn'>Full View</a>
            </td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='7'>No student records found.</td></tr>";
      }

      $conn->close();
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
