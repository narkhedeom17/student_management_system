<?php
session_start();

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_no = $_POST['enrollment_no'] ?? '';
    $dob_input = $_POST['dob'] ?? '';

    // Convert yyyymmdd to yyyy-mm-dd
    if (preg_match('/^\d{8}$/', $dob_input)) {
        $dob = substr($dob_input, 0, 4) . '-' . substr($dob_input, 4, 2) . '-' . substr($dob_input, 6, 2);
    } else {
        $dob = '';
    }

    if (!empty($enrollment_no) && !empty($dob)) {
        $conn = new mysqli("localhost", "root", "12345678", "newton_db");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        $sql = "SELECT * FROM student WHERE enrollment_no = ? AND dob = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $enrollment_no, $dob);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $_SESSION['enrollment_no'] = $enrollment_no;
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Invalid enrollment number or date of birth.";
        }
    } else {
        $login_error = "Please enter DOB in YYYYMMDD format.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Parent Login - Newton House School</title>
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

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .password-toggle {
      margin-top: -15px;
      margin-bottom: 15px;
      text-align: right;
      font-size: 13px;
      color: #093c56;
      cursor: pointer;
      user-select: none;
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
    <h1>Welcome to<br>Newton House School</h1>

    <img src="images/teacher.png" class="bottom-image" alt="Newton School">
  </div>

  <div class="right-panel">
    <div class="login-box">
      <h2>Parent Login</h2>

      <?php if (!empty($login_error)): ?>
        <p class="error-message"><?= htmlspecialchars($login_error) ?></p>
      <?php endif; ?>

      <form method="POST">
        <label for="enrollment_no">username</label>
        <input type="text" name="enrollment_no" id="enrollment_no" required>

        <label for="dob">password</label>
        <input type="password" name="dob" id="dob" placeholder="" required>
        <div class="password-toggle" onclick="togglePassword()"></div>

        <button type="submit">Login</button>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById("dob");
      const toggle = document.querySelector(".password-toggle");
      if (input.type === "password") {
        input.type = "text";
        toggle.textContent = "Hide DOB";
      } else {
        input.type = "password";
        toggle.textContent = "Show DOB";
      }
    }
  </script>
</body>
</html>
