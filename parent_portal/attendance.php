<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'navbar.php';

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
$attendance = [];

if (!empty($enrollment_no)) {
    $conn = new mysqli("localhost", "root", "12345678", "newton_db");
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT fname, lname FROM student WHERE enrollment_no = ?");
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $stmt->bind_result($fname, $lname);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT attendance_date, status FROM student_attendance WHERE enrollment_no = ? ORDER BY attendance_date DESC");
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
        $stmt->close();
        $conn->close();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report - Newton House School</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .main-content {
            margin-left: 220px; /* Adjust for sidebar */
            padding: 30px;
        }

        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }

        .info {
            text-align: center;
            font-size: 1.1em;
            margin-bottom: 25px;
            color: #555;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .attendance-block {
            width: 120px;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            font-size: 0.95rem;
            background-color: #f9f9f9;
        }

        .attendance-block .date {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        .Present {
            background-color: #d4edda;
            color: #155724;
        }

        .Absent {
            background-color: #f8d7da;
            color: #721c24;
        }

        .Holiday {
            background-color: #fff3cd;
            color: #856404;
        }

        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .attendance-block {
                width: 100px;
                padding: 10px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="container">
        <h2>Attendance Report</h2>
       

        <div class="grid">
            <?php if (!empty($attendance)): ?>
                <?php foreach ($attendance as $row): ?>
                    <div class="attendance-block <?= str_replace(' ', '', $row['status']) ?>">
                        <div class="date"><?= date("d-m-Y", strtotime($row['attendance_date'])) ?></div>
                        <div class="status"><?= htmlspecialchars($row['status']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No attendance records found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
