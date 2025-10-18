<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logout - Newton House School</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: rgb(247, 247, 247);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    @keyframes fallIn {
      0% {
        opacity: 0;
        transform: translateY(-40px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fall-in {
      animation: fallIn 0.7s ease-out forwards;
    }

    .logout-container {
      background-color: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 400px;
      width: 90%;
    }

    .logout-container h1 {
      color: #333;
      font-size: 24px;
      margin-bottom: 10px;
    }

    .logout-container p {
      color: #666;
      margin-bottom: 25px;
    }

    .logout-button {
      text-decoration: none;
      background-color: rgb(7, 81, 119);
      color: white;
      padding: 12px 24px;
      border-radius: 6px;
      transition: background-color 0.3s;
    }

    .logout-button:hover {
      background-color: rgb(55, 64, 73);
    }

    .icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px auto;
    }

    .icon img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      display: block;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <div class="logout-container fall-in">
    <div class="icon"><img src="images/lock.gif" alt="Unlock Logo"></div>
    <h1>You’ve been logged out</h1>
    <p>Thank you for using Newton House School Portal.</p>
    <a href="index.php" class="logout-button">Return to Login</a>
  </div>
</body>
</html>
