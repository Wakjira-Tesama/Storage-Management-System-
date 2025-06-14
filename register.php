<?php
session_start();
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username  = $_POST['username'];
    $password  = $_POST['password'];
    $email     = $_POST['email'];
    $gender    = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $phone     = $_POST['phone'];

    // Basic validation (for demo - use better security in production)
    if (!empty($username) && !empty($password) && !empty($email) && !empty($gender) && !empty($birthdate) && !empty($phone)) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, gender, birthdate, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'Buyer')");
        $stmt->bind_param("ssssss", $username, $password, $email, $gender, $birthdate, $phone);

        if ($stmt->execute()) {
            $success = "Registration successful. <a href='login.php'>Click here to login</a>.";
        } else {
            $error = "Registration failed. Username or email may already exist.";
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Page</title>
    
    <link rel="stylesheet" href="styleregister.css">
</head>
<body>
    <div class="register_container">
        <h2 class="register_title">Register New Buyer</h2>
        <form method="post" class="register_page">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select><br>
            <input type="date" name="birthdate" required><br>
            <input type="tel" name="phone" placeholder="Phone Number" required><br>
            <button type="submit">Register</button>
            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </form>
        <?php
            if (isset($error)) echo "<p style='color:red;'>$error</p>";
            if (isset($success)) echo "<p style='color:green;'>$success</p>";
        ?>
    </div>
</body>
</html>
