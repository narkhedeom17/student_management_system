<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "12345678";
$dbname = "newton_db";

// Redirect to dashboard if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $enrollment_no = $_POST['enrollment_no'] ?? '';
    $password_input = $_POST['password'] ?? '';

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT * FROM student WHERE enrollment_no = ?");
    $stmt->bind_param("s", $enrollment_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $dob_clean = str_replace("-", "", $student['dob']); // Convert to YYYYMMDD format

        if ($password_input === $dob_clean) {
            $_SESSION['loggedin'] = true;
            $_SESSION['enrollment_no'] = $student['enrollment_no'];
            $_SESSION['student_name'] = $student['fname'] . ' ' . $student['lname'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid enrollment number or password.";
        }
    } else {
        $error = "Invalid enrollment number or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Login | Newton House School</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .left-panel {
      width: 50%;
      background: linear-gradient(to right, #093c56, #a2c8db);
      color: white;
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .left-panel h1 {
      font-size: 2.4rem;
      margin-bottom: 20px;
      text-align: center;
    }

    .left-panel p {
      font-size: 1.1rem;
      line-height: 1.6;
      text-align: center;
      max-width: 400px;
    }

    .left-panel img.bottom-image {
      max-width: 400px;
      margin-top: 30px;
      border-radius: 14px;
      box-shadow: 0 0 18px rgba(0, 0, 0, 0.3);
    }

    .right-panel {
      width: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #ffffff;
    }

    .login-box {
      width: 90%;
      max-width: 400px;
      padding: 35px;
      background: #ffffff;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    .login-box h2 {
      text-align: center;
      color: #093c56;
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }

    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #093c56;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0f4a6e;
    }

    .error-message {
      color: red;
      text-align: center;
      margin-bottom: 15px;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .left-panel,
      .right-panel {
        width: 100%;
        height: auto;
      }

      .left-panel {
        padding: 30px 20px;
      }

      .left-panel img.bottom-image {
        max-width: 250px;
      }
    }
  </style>
</head>
<body>

<div class="left-panel">
  <h1>Welcome to Student Portal</h1>
  <p>Access your profile, exam results, documents, and fees in one place.<br>Login using your enrollment number and birthdate.</p>
  <img src="images/newtonp.jpg" alt="Newton School Logo" class="bottom-image">
</div>

<div class="right-panel">
  <div class="login-box">
    <h2>Student Login</h2>

    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="enrollment_no">Username</label>
      <input type="text" name="enrollment_no" id="enrollment_no" required placeholder="username">

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required placeholder="password">

      <button type="submit">Login</button>
    </form>
  </div>
</div>

</body>
</html>
