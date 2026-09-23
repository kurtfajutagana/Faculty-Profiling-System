<?php
session_start();

$session_message = '';
$session_error = '';

if (isset($_SESSION['message'])) {
    $session_message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $session_error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - PLP</title>
  <link rel="stylesheet" href="../css/styleFP.css" />
  <style>
    .message { color: green; margin-bottom: 15px; text-align: center; font-weight: bold; }
    .error { color: red; margin-bottom: 15px; text-align: center; font-weight: bold; }
    .success-message { 
      display: none;
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      margin: 20px 0;
      border-radius: 5px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="../images/log_in_page.png" alt="PLP Building" class="bg-image" />
    </div>
    <div class="right">
        <div class="content-wrapper">
      <div class="logo-header">
        <img src="../images/PLP_logo.png" alt="PLP Logo" class="logo" />
        <h2>PAMANTASAN NG LUNGSOD NG PASIG</h2>
      </div>
      <div class="form-box">
        <h1>Forgot your password</h1>
        <?php if (!empty($session_message)): ?>
            <p class="message"><?= htmlspecialchars($session_message) ?></p>
        <?php endif; ?>
        <?php if (!empty($session_error)): ?>
            <p class="error"><?= htmlspecialchars($session_error) ?></p>
        <?php endif; ?>
        <div id="successMessage" class="success-message"></div>
        <p>Please enter the email address you'd like your password reset information sent to</p>
        <form id="forgotPasswordForm" action="process_forgotpassword.php" method="POST">
          <label for="email">Enter email address</label>
          <input type="email" id="email" name="email" placeholder="faculty@plpasig.edu.ph" required />
          <button type="submit">Request reset link</button>
        </form>
        <a href="../login/index.php" class="back-link">Back To Login</a>
      </div>
    </div>
  </div>
  
  <script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const form = e.target;
      const formData = new FormData(form);
      const successMessage = document.getElementById('successMessage');
      
      fetch(form.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          successMessage.textContent = data.message;
          successMessage.style.display = 'block';
          successMessage.style.backgroundColor = '#d4edda';
          successMessage.style.color = '#155724';
          
          // Hide the form
          form.style.display = 'none';
          
          // Hide after 5 seconds
          setTimeout(() => {
            successMessage.style.display = 'none';
            form.style.display = 'block';
          }, 5000);
        } else {
          // Show error message
          successMessage.textContent = data.error;
          successMessage.style.display = 'block';
          successMessage.style.backgroundColor = '#f8d7da';
          successMessage.style.color = '#721c24';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        successMessage.textContent = 'An error occurred. Please try again.';
        successMessage.style.display = 'block';
        successMessage.style.backgroundColor = '#f8d7da';
        successMessage.style.color = '#721c24';
      });
    });
  </script>
</body>
</html>