<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body, html {
    overflow-x: hidden;
    font-family: 'Segoe UI', sans-serif;
  }

  nav {
    position: sticky;
    top: 0;
    z-index: 999;
    width: 100%;
    background-color: rgb(0, 96, 155);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    padding: 10px 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  }

  .navbar-left {
    display: flex;
    align-items: center;
    gap: 15px;
    color: #fff;
    font-size: 18px;
    font-weight: bold;
  }

  .navbar-left img {
    height: 32px;
  }

  .back-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .back-arrow:hover {
    background-color: rgba(255, 255, 255, 0.25);
  }

  .navbar-right {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: flex-end;
  }

  .navbar-right a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    padding: 6px 12px;
    border-radius: 4px;
    transition: background 0.2s ease;
    white-space: nowrap;
  }

  .navbar-right a:hover {
    background-color: #3a3f47;
  }

  @media (max-width: 600px) {
    nav {
      flex-direction: column;
      align-items: flex-start;
    }

    .navbar-right {
      justify-content: flex-start;
      width: 100%;
      margin-top: 10px;
    }

    .navbar-left {
      width: 100%;
      justify-content: space-between;
    }
  }
</style>

<nav>
  <div class="navbar-left">
    <div class="back-arrow" onclick="history.back()" title="Go Back">&#8592;</div>
    <img src="images/nw2.png" alt="Logo" />
  </div>
  <div class="navbar-right">
    <a href="dashboard.php">Home</a>
    <a href="admission.php">Admission</a>
    <a href="set_total_fee.php">Fees</a>
    <a href="staff.php">Staff</a>
    <a href="logout.php">Logout</a>
  </div>
</nav>
