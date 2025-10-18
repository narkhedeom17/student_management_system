<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Newton School - Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    html, body {
      height: 100%;
      width: 100%;
      overflow: hidden; /* Disable both scrollbars */
      background-color: #f0f2f5;
    }

    main {
      height: calc(100% - 60px); /* Adjust if your navbar is taller */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
      text-align: center;
    }

    main h1 {
      font-size: 36px;
      color: #2c2f36;
      margin-bottom: 8px;
    }

    main p {
      font-size: 18px;
      color: #555;
      margin-bottom: 32px;
    }

    .features-title {
      font-size: 24px;
      color: #333;
      font-weight: bold;
      margin-bottom: 24px;
      border-bottom: 3px solid #2c2f36;
      padding-bottom: 6px;
    }

    .dashboard-cards {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
      justify-content: center;
      width: 100%;
      max-width: 1100px;
    }

    .card {
      background-color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
      padding: 18px;
      width: 220px;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 10px 22px rgba(0, 0, 0, 0.15);
    }

    .card h3 {
      color: #2c3e50;
      font-size: 20px;
      margin-bottom: 10px;
    }

    .card p {
      font-size: 14px;
      color: #666;
      line-height: 1.4;
    }

    @media (max-width: 600px) {
      main h1 { font-size: 28px; }
      .features-title { font-size: 20px; }
      .card { width: 100%; max-width: 280px; }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<main>
  <h1>Welcome to Newton School Dashboard</h1>
  <p>Select a section from the Navbar below to get started.</p>

  <div class="features-title">Features</div>

  <div class="dashboard-cards">
    <div class="card">
      <h3>Admissions</h3>
      <p>View and manage student admissions.</p>
    </div>
    <div class="card">
      <h3>Fees</h3>
      <p>Check student fee structure and payment status.</p>
    </div>
    <div class="card">
      <h3>Staff</h3>
      <p>Manage teaching and non-teaching staff data.</p>
    </div>
    <div class="card">
      <h3>Student Details</h3>
      <p>See all registered student details in one place.</p>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
