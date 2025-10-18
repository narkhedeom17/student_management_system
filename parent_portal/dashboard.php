<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
    }

    .dashboard-container {
      margin-left: 220px;
      padding: 30px;
    }

    h2 {
      text-align: center;
      color: #093c56;
      margin-bottom: 40px;
      font-size: 26px;
    }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 25px;
    }

    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px;
      text-align: center;
      transition: transform 0.2s ease;
      border: 1px solid #ddd;
      text-decoration: none;
      color: inherit;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .card img {
      width: 60px;
      height: 60px;
      object-fit: contain;
      margin-bottom: 15px;
    }

    .card-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #093c56;
    }

    .card-desc {
      font-size: 14px;
      color: #555;
    }
  </style>
</head>
<body>

<div class="dashboard-container">
  <h2>Welcome to Your Dashboard</h2>

  <div class="card-grid">
    <a href="student_profile.php" class="card">
      <img src="assets/icons/profile.png" alt="Profile">
      <div class="card-title">Your Profile</div>
      <div class="card-desc">View and verify your student information.</div>
    </a>

    <a href="fees1.php" class="card">
      <img src="assets/icons/fees.png" alt="Fees">
      <div class="card-title">Fees</div>
      <div class="card-desc">Check fee status, total paid, and pending dues.</div>
    </a>

    <a href="exam.php" class="card">
      <img src="assets/icons/exam.png" alt="Exams">
      <div class="card-title">Exams</div>
      <div class="card-desc">View your exam marks and performance.</div>
    </a>

    <a href="documents.php" class="card">
      <img src="assets/icons/documents.png" alt="Documents">
      <div class="card-title">Documents</div>
      <div class="card-desc">Access your submitted documents.</div>
    </a>

    <a href="leave.php" class="card">
      <img src="assets/icons/leave.png" alt="Leave">
      <div class="card-title">Leave Requests</div>
      <div class="card-desc">Track or request leave from classes.</div>
    </a>
  </div>
</div>

</body>
</html>
