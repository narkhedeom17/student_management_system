<?php
include 'navbar.php';

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle search input
$search = $_GET['search'] ?? '';

// Build query to show only Active Technical staff
$sql = "SELECT * FROM staff WHERE staff_type = 'Technical' AND status = 'Active'";
if (!empty($search)) {
  $search_escaped = $conn->real_escape_string($search);
  $sql .= " AND (staff_no LIKE '%$search_escaped%' OR name LIKE '%$search_escaped%')";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teaching Staff</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f2f4f8; margin: 0; padding: 0; }
    .container { width: 100%; padding: 20px; }

    .header-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    h2 {
      color: #003366;
      margin: 0;
    }

    .search-box {
      text-align: right;
    }

    input[type="text"] {
      padding: 8px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      padding: 8px 14px;
      background-color: #004080;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 6px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border: 1px solid #ccc;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 12px;
      text-align: center;
    }

    th {
      background-color: #004080;
      color: white;
    }

    .btn {
      padding: 6px 10px;
      margin: 2px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
      font-size: 13px;
      color: #fff;
    }

    .edit-btn { background-color: #007bff; }
    .delete-btn { background-color: #dc3545; }
    .view-btn { background-color: #28a745; }
    .btn:hover { opacity: 0.9; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-row">
      <h2>Teaching Staff</h2>
      <div class="search-box">
        <form method="GET">
          <input type="text" name="search" placeholder="Search by Staff No or Name..." value="<?php echo htmlspecialchars($search); ?>">
          <button type="submit">Search</button>
        </form>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Sr No.</th>
          <th>Staff No</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sr = 1;
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
              <td>{$sr}</td>
              <td>" . htmlspecialchars($row['staff_no']) . "</td>
              <td>" . htmlspecialchars($row['name']) . "</td>
              <td>" . htmlspecialchars($row['contact']) . "</td>
              <td>
                <a href='edit-staff.php?staff_no=" . urlencode($row['staff_no']) . "' class='btn edit-btn'>Edit</a>
                <a href='delete-staff.php?staff_no=" . urlencode($row['staff_no']) . "' class='btn delete-btn' onclick=\"return confirm('Are you sure?');\">Delete</a>
                <a href='viewdetail-staff.php?staff_no=" . urlencode($row['staff_no']) . "' class='btn view-btn'>Full View</a>
              </td>
            </tr>";
            $sr++;
          }
        } else {
          echo "<tr><td colspan='5'>No records found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
