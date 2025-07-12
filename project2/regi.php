<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Registration | Newton House School</title>
  <style>
   body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f4f6f8;
}

.form-container {
  background: #fff;
  padding: 30px;
  max-width: 1000px;
  margin: 30px auto;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

h1, h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 10px;
}

form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 6px;
  color: #333;
}

input, select, textarea {
  width: 100%;
  padding: 8px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
}

textarea {
  resize: vertical;
}

.full-width {
  grid-column: span 2;
}

.buttons {
  display: flex;
  justify-content: center;
  gap: 20px;
  grid-column: span 2;
}

button {
  background-color: #2c3e50;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
}

button:hover {
  background-color: #1a2533;
}

#enrollMsg {
  grid-column: span 2;
  color: green;
  text-align: center;
  font-weight: 600;
  margin-top: 10px;
}

@media (max-width: 768px) {
  form {
    grid-template-columns: 1fr;
  }
}
</style></head>
<body>
  <div class="form-container">
    
    <h2>Student Registration Form</h2><br><br>
    <form id="studentForm">
  <div>
    <label>First Name</label>
    <input type="text" placeholder="Enter first name" required />
  </div>
  <div>
    <label>Middle Name</label>
    <input type="text" placeholder="Enter middle name" />
  </div>
  <div>
    <label>Last Name</label>
    <input type="text" placeholder="Enter last name" required />
  </div>
  <div>
    <label>Age</label>
    <input type="number" placeholder="Enter age" required />
  </div>
  <div>
    <label>Date of Birth</label>
    <input type="date" required />
  </div>
  <div>
    <label>Blood Group</label>
    <select required>
      <option disabled selected>Choose blood group</option>
      <option>A+</option><option>A-</option><option>AB+</option><option>AB-</option>
      <option>B+</option><option>B-</option><option>O+</option><option>O-</option>
    </select>
  </div>
  <div>
    <label>Father's Name</label>
    <input type="text" placeholder="Enter father's full name" required />
  </div>
  <div>
    <label>Gender</label>
    <select required>
      <option disabled selected>Select gender</option>
      <option>Male</option>
      <option>Female</option>
      <option>Other</option>
    </select>
  </div>
  <div>
    <label>Contact Number</label>
    <input type="tel" placeholder="Enter mobile number" required />
  </div>
  <div>
    <label>Alternate Contact</label>
    <input type="tel" placeholder="Enter alternate number" />
  </div>
  <div>
    <label>Email</label>
    <input type="email" placeholder="Enter email address" />
  </div>
  <div>
    <label>Aadhar Card No</label>
    <input type="number" placeholder="Enter 12-digit Aadhar number" />
  </div>
  <div>
    <label>Religion</label>
    <select>
      <option disabled selected>Select religion</option>
      <option>Hindu</option><option>Islam</option><option>Christian</option>
      <option>Sikh</option><option>Buddhist</option><option>Jain</option>
    </select>
  </div>
  <div>
    <label>Caste</label>
    <input type="text" placeholder="Enter your caste" />
  </div>
  <div>
    <label>Disability</label>
    <select>
      <option disabled selected>Select option</option>
      <option>Yes</option><option>No</option>
    </select>
  </div>
  <div>
    <label>Qualification</label>
    <input type="text" placeholder="Enter highest qualification" />
  </div>
  <div>
    <label>Annual Income</label>
    <input type="number" placeholder="Enter annual family income" />
  </div>
  <div class="full-width">
    <label>Address 1</label>
    <textarea rows="2" placeholder="Flat no., Street, Area, City"></textarea>
  </div>
   <div class="full-width">
    <label>Address 2</label>
    <textarea rows="2" placeholder="Flat no., Street, Area, City"></textarea>
  </div>
  
  <div>
    <label>State</label>
    <select>
      <option disabled selected>Select state</option>
      <option>Gujarat</option><option>Maharashtra</option><option>Rajasthan</option>
      <option>Punjab</option><option>Kerala</option><option>Others</option>
    </select>
  </div>
  <div>
    <label>City</label>
    <input type="text" placeholder="Enter city name" />
  </div>
  <div>
    <label>Pincode</label>
    <input type="number" placeholder="Enter area pincode" />
  </div>

  <div class="buttons">
    <button type="submit">Submit</button>
    <button type="reset">Reset</button>
  </div>
  <div id="enrollMsg"></div>
</form>

     
  </div>

  <script>
    const form = document.getElementById("studentForm");
    const enrollMsg = document.getElementById("enrollMsg");

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const date = new Date();
      const yyyy = date.getFullYear();
      const mm = String(date.getMonth() + 1).padStart(2, '0');
      const dd = String(date.getDate()).padStart(2, '0');
      const rand = Math.floor(1000 + Math.random() * 9000);
      const enrollNo = `NHS${yyyy}${mm}${dd}${rand}`;

      enrollMsg.innerHTML = `✅ Enrollment Successful! Your Enrollment No: <strong>${enrollNo}</strong>`;

      form.reset();
    });
  </script>
</body>
</html>
