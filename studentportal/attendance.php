<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'navbar.php';

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
$attendance = [];

if (!empty($enrollment_no)) {
    $conn = new mysqli("localhost", "root", "12345678", "newton_db");
    if (!$conn->connect_error) {
        // Student Name
        $stmt = $conn->prepare("SELECT fname, lname FROM student WHERE enrollment_no = ?");
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $stmt->bind_result($fname, $lname);
        $stmt->fetch();
        $stmt->close();

        // Auto insert 'P' for missing working days
        $today = date("Y-m-d");
        $start = date("Y-m-01");

        $existing = [];
        $stmt = $conn->prepare("SELECT attendance_date FROM student_attendance WHERE enrollment_no = ? AND attendance_date BETWEEN ? AND ?");
        $stmt->bind_param("sss", $enrollment_no, $start, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $existing[$row['attendance_date']] = true;
        }
        $stmt->close();

        $date = $start;
        while ($date <= $today) {
            $dayOfWeek = date("w", strtotime($date)); // 0=Sunday, 6=Saturday
            if ($dayOfWeek != 0 && $dayOfWeek != 6 && !isset($existing[$date])) {
                // Insert default Present
                $insert = $conn->prepare("INSERT INTO student_attendance (enrollment_no, attendance_date, status) VALUES (?, ?, 'P')");
                $insert->bind_param("ss", $enrollment_no, $date);
                $insert->execute();
                $insert->close();
            }
            $date = date("Y-m-d", strtotime($date . " +1 day"));
        }

        // Now fetch attendance
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
            margin-left: 220px;
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
            width: 150px;
            padding: 14px;
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

        .attendance-block .day {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 5px;
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
                width: 120px;
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
        <div class="info">Student: <?= htmlspecialchars($fname . ' ' . $lname) ?> (<?= htmlspecialchars($enrollment_no) ?>)</div>

        <div class="grid">
            <?php if (!empty($attendance)): ?>
                <?php foreach ($attendance as $row): ?>
                    <?php
                        $date = date("d-m-Y", strtotime($row['attendance_date']));
                        $day = date("l", strtotime($row['attendance_date']));
                        $statusClass = match($row['status']) {
                            'P' => 'Present',
                            'A' => 'Absent',
                            'H' => 'Holiday',
                            default => '',
                        };
                    ?>
                    <div class="attendance-block <?= $statusClass ?>">
                        <div class="date"><?= $date ?></div>
                        <div class="day"><?= $day ?></div>
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
