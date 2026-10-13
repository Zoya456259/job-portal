<?php

session_start();

require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Email and password are required.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, password, role FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_role"] = $user["role"];

                if ($user["role"] == "admin") {
                    header("Location: ../admin/dashboard.php");
                    exit;
                }

                if ($user["role"] == "employer") {
                    header("Location: ../employer/dashboard.php");
                    exit;
                }

                if ($user["role"] == "job_seeker") {
                    header("Location: ../job_seeker/dashboard.php");
                    exit;
                }

            } else {

                $message = "Invalid email or password.";
            }

        } else {

            $message = "Invalid email or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h2>Login</h2>

<p><?php echo $message; ?></p>

<form method="POST">

    <label>Email</label><br>
    <input type="email" name="email" required>

    <br><br>

    <label>Password</label><br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit">Login</button>

</form>

<br>

<a href="register.php">Create an account</a>

</body>
</html>