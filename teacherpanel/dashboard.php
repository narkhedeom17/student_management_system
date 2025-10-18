<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['teacher_loggedin']) || $_SESSION['teacher_loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

// Include the teacher navbar
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Dashboard</title>
  <style>
    .main-content {
      margin-left: 220px;
      padding: 20px;
      background-color: #f9f9f9;
      min-height: calc(100vh - 60px);
    }

    .welcome-box {
      background: white;
      border: 1px solid #ddd;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }

    .welcome-box h2 {
      margin: 0;
      color: #093c56;
    }

    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .card {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
      width: calc(33.333% - 20px);
      min-width: 250px;
      text-align: center;
      border: 1px solid #ccc;
      transition: 0.3s ease;
    }

    .card:hover {
      box-shadow: 0 0 10px rgba(9, 60, 86, 0.3);
    }

    .card h3 {
      margin-bottom: 10px;
      color: #093c56;
    }

    .card a {
      text-decoration: none;
      color: #093c56;
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .cards {
        flex-direction: column;
      }

      .card {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="welcome-box">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['staff_name']) ?>!</h2>
    <p>You are now logged in to the Teacher Dashboard.</p>
  </div>

  <div class="cards">
    <div class="card">
      <h3>Profile</h3>
      <a href="teacher_profile.php">View Profile</a>
    </div>
    <div class="card">
      <h3>Students</h3>
      <a href="manage_students.php">Manage Students</a>
    </div>
   
  </div>
</div>

</body>
</html>
