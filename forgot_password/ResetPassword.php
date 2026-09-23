<?php
session_start();
require_once '../db_connection.php';

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    $_SESSION['error'] = "Invalid reset link";
    header("Location: forgotpassword.php");
    exit();
}

$user_id = intval($_GET['user_id']);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "Please fill in all fields";
        header("Location: resetpassword.php?user_id=$user_id");
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: resetpassword.php?user_id=$user_id");
        exit();
    }
    
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long";
        header("Location: resetpassword.php?user_id=$user_id");
        exit();
    }
    
    if ($conn->connect_error) {
        $_SESSION['error'] = "Database connection failed";
        header("Location: resetpassword.php?user_id=$user_id");
        exit();
    }
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Password updated successfully. You can now login with your new password.";
        header("Location: ../login/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update password. Please try again.";
        header("Location: resetpassword.php?user_id=$user_id");
        exit();
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password - PLP</title>
  <link rel="icon" type="image/png" href="../images/logo.png">
  <link rel="stylesheet" href="../css/ResetPassword.css">
  <style>
    .input-group {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      background: none;
      border: none;
      padding: 0;
    }
    .error {
      color: #dc3545;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <img src="../images/log_in_page.png" alt="PLP Building" class="bg-image">
    </div>
    <div class="right-panel">
      <div class="header">
        <img src="../images/PLP_logo.png" alt="PLP Logo" class="logo">
        <h1>PAMANTASAN NG LUNGSOD NG PASIG</h1>
      </div>
      <div class="form-section">
        <h2>Reset your password</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <p class="description">
          Strong passwords include numbers, letters, and punctuation marks.
        </p>
        <form method="POST">
          <label for="new-password">Enter new password</label>
          <div class="input-group">
            <input type="password" id="new-password" name="new_password" placeholder="New Password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('new-password')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>

          <label for="confirm-password">Confirm new password</label>
          <div class="input-group">
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('confirm-password')">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>

          <button type="submit" class="reset-button">RESET PASSWORD</button>
        </form>
      </div>
    </div>
  </div>
  <script>
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const toggleIcon = field.nextElementSibling.querySelector('svg');
      
      if (field.type === "password") {
        field.type = "text";
        toggleIcon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
      } else {
        field.type = "password";
        toggleIcon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
      }
    }
  </script>
</body>
</html>