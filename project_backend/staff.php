<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Portal - Newton School</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #003366;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
    }

    .container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 40px 20px;
      gap: 30px;
    }

    .card {
      background: white;
      width: 300px;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }

    .card img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 10px;
    }

    .card h2 {
      margin-top: 10px;
      color: #003366;
    }

    .card p {
      color: #666;
      margin: 10px 0 20px;
      font-size: 15px;
    }

    .card a {
      display: inline-block;
      text-decoration: none;
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      transition: background-color 0.3s ease;
    }

    .card a:hover {
      background-color: #0056b3;
    }

    .add-section {
      text-align: center;
      margin: 40px 0;
    }

    .add-section a {
      text-decoration: none;
      background-color: #28a745;
      color: white;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 6px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      transition: background-color 0.3s ease;
    }

    .add-section a:hover {
      background-color: #218838;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>


  <div class="container">
    <div class="card">
      <img src="images/teaching_staff.png" alt="Teaching Staff Icon">
      <h2>Teaching Staff</h2>
      <p>View records of all teaching staff including subjects, experience, and more.</p>
      <a href="view_teaching_staff.php">View Teaching Staff</a>
    </div>

    <div class="card">
      <img src="images/non_teaching_staff.png" alt="Non-Teaching Staff Icon">
      <h2>Non-Teaching Staff</h2>
      <p>Manage staff like office admins, lab assistants, clerks, and many more staffs.</p>
      <a href="view_nonteaching_staff.php">View Non-Teaching Staff</a>
    </div>
  </div>

  <div class="add-section">
    <a href="add-staff.php">➕ Add New Staff</a>
  </div>

</body>
</html>
