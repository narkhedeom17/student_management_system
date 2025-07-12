<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Registration</title>
  <style>


    * {
      box-sizing: border-box;
    }


    

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }


    .form-container {
      background: #fff;
       padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 1400px;
      height: 95vh;
      overflow-y: auto;
    }


    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    form {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px 20px;
      font-size: 14px;
    }
    input, select, textarea {
      padding: 6px 8px;
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    textarea {
      resize: none;
    }
    .full {
      grid-column: span 2;
    }
    .buttons {
      display: flex;
      justify-content: space-between;
      grid-column: span 2;
      margin-top: 10px;
    }
    button {
      padding: 8px 16px;
      background: #2c3e50;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    #enrollMsg {
      text-align: center;
      font-weight: bold;
      color: green;
      grid-column: span 2;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Student Registration Form</h2>
    <form id="studentForm">
      <input type="text" placeholder="First Name" required />
      <input type="text" placeholder="Middle Name" />
      <input type="text" placeholder="Last Name" required />
      <input type="number" placeholder="Age" required />
      <input type="date" required />
      <select required>
        <option disabled selected>Blood Group</option>
        <option>A+</option><option>B+</option><option>O+</option><option>AB+</option>
        <option>A-</option><option>B-</option><option>O-</option><option>AB-</option>
      </select>
      <input type="text" placeholder="Father's Full Name" required />
      <select required>
        <option disabled selected>Gender</option>
        <option>Male</option><option>Female</option><option>Other</option>
      </select>
      <input type="tel" placeholder="Phone Number" required />
      <input type="tel" placeholder="Alternate Phone Number" />
      <input type="email" placeholder="Email Address" />
      <input type="number" placeholder="Aadhar Card Number" />
      <select>
        <option disabled selected>Religion</option>
        <option>Hinduism</option><option>Islam</option><option>Christianity</option>
        <option>Sikhism</option><option>Buddhism</option><option>Jainism</option>
      </select>
      <input type="text" placeholder="Caste" />
      <select>
        <option disabled selected>Disability</option>
        <option>Yes</option><option>No</option>
      </select>
      <input type="text" placeholder="Qualification" />
      <input type="number" placeholder="Annual Income" />
      <textarea class="full" rows="2" placeholder="Address Line 1"></textarea>
      <textarea class="full" rows="2" placeholder="Address Line 2"></textarea>
      <select>
        <option disabled selected>State</option>
        <option>Andhra Pradesh</option><option>Arunachal Pradesh</option><option>Assam</option>
        <option>Bihar</option><option>Chhattisgarh</option><option>Goa</option>
        <option>Gujarat</option><option>Haryana</option><option>Himachal Pradesh</option>
        <option>Jharkhand</option><option>Karnataka</option><option>Kerala</option>
        <option>Madhya Pradesh</option><option>Maharashtra</option><option>Manipur</option>
        <option>Meghalaya</option><option>Mizoram</option><option>Odisha</option>
        <option>Punjab</option><option>Rajasthan</option>
      </select>
      <input type="text" placeholder="City" />
      <input type="number" placeholder="Pincode" />

      <div class="buttons">
        <button type="submit">Submit</button>
        <button type="reset">Reset</button>
      </div>
      <div id="enrollMsg"></div>
    </form>
  </div>

  <script>
    document.getElementById("studentForm").addEventListener("submit", function (e) {
      e.preventDefault();
      const enrollmentNo = "ENR" + Date.now().toString().slice(-6);
      document.getElementById("enrollMsg").textContent = "Enrollment No.: " + enrollmentNo;
    });
  </script>
</body>
</html>
