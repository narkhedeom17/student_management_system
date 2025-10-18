<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === "newtonedu@gmail.com" && $password === "newtonedu") {
        $_SESSION['loggedin'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Super Admin Login | Newton House School</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      display: flex;
      height: 100vh;
      background-color: #f4f6f9;
    }

    .left-panel {
      width: 50%;
      background: linear-gradient(110deg, rgb(9, 60, 86), rgb(162, 200, 219)); 
      color: white;
      padding: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background-size: cover;
      background-position: center;
      animation: backgroundShift 20s infinite alternate ease-in-out;
    }

    @keyframes backgroundShift {
      0% {
        background-position: top left;
      }
      100% {
        background-position: bottom right;
      }
    }

    .left-panel h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    .left-panel p {
      font-size: 1.1rem;
      line-height: 1.6;
      opacity: 0.9;
    }

    .right-panel {
      width: 50%;
      background: white;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      width: 80%;
      max-width: 400px;
      padding: 40px;
      background: #ffffff;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      text-align: center;
    }

    .logo-row {
      display: flex;
      justify-content: center;
      margin-bottom: 15px;
    }

    .logo-row img {
      height: 60px;
    }

    .login-box h2 {
      margin-bottom: 25px;
      color: rgb(9, 60, 86);
    }

    .login-box label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
      text-align: left;
    }

    .login-box input[type="email"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    .login-box button {
      width: 100%;
      padding: 12px;
      background-color: rgb(9, 60, 86);
      color: white;
      font-size: 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .login-box button:hover {
      background-color: rgb(80, 101, 111);
    }

    .error-message {
      color: red;
      margin-bottom: 15px;
      font-size: 0.95rem;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .left-panel,
      .right-panel {
        width: 100%;
        height: 50vh;
      }

      .left-panel {
        padding: 30px;
        text-align: center;
        align-items: center;
      }

      .login-box {
        width: 90%;
      }
    }
  </style>
</head>
<body>

  <div class="left-panel">
    <h1>Welcome Admin Panel</h1>
    <p>
      Access and manage all key school operations like admissions, staff management,
      performance reports, financial records and more – all in one place.
    </p>
  </div>

  <div class="right-panel">
    <div class="login-box">
      <div class="logo-row">
        <img src="images/logo.jpg" alt="School Logo">
      </div>
      <h2>Admin Login</h2>

      <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="admin@newtonschool.edu" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit">Login</button>
      </form>
    </div>
  </div>

</body>
</html>
