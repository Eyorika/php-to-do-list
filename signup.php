<?php
session_start();

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    require 'db_conn.php';

    $username = $_POST['username'];
    $password = $_POST['password'];
    //$email = $_POST ['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if($stmt->fetch()) {

        $error_message = "Username already exists. Please choose a different username.";
    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);


        header("Location: login.php");
        echo 'create';
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="signup-section ">
    <form action="signup.php" method="POST" class="signup-form border shadow p-3 rounded">
    <h2>Sign Up</h2>
    <?php if(isset($error_message)) { ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php } ?>
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
    </div>

    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
    </div>
   
    <button type="submit" class="btn btn-primary">Sign Up</button>
    <div class="login-link">
    Already have an account? <a href="login.php">Login</a>
</div>
</form>


    </div>

    
</body>
</html>
