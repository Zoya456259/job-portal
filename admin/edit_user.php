
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../authentication/login.php");
    exit;
}

require_once "../config/database.php";

$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

if ($id <= 0) {
    header("Location: users.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, email, role
     FROM users
     WHERE id = ? AND role != 'admin'"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: users.php");
    exit;
}

$user = $result->fetch_assoc();

$name = $user["name"];
$email = $user["email"];
$role = $user["role"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = $_POST["role"] ?? "";

    if (empty($name) || empty($email) || empty($role)) {

        $message = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif (!in_array($role, ["employer", "job_seeker"], true)) {

        $message = "Invalid role selected.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );

        $check->bind_param("si", $email, $id);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $message = "This email is already in use.";

        } else {

            $update = $conn->prepare(
                "UPDATE users
                 SET name = ?, email = ?, role = ?
                 WHERE id = ? AND role != 'admin'"
            );

            $update->bind_param(
                "sssi",
                $name,
                $email,
                $role,
                $id
            );

            if ($update->execute()) {

                header("Location: users.php");
                exit;

            } else {

                $message = "Failed to update user.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Edit User</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    <input
        type="hidden"
        name="id"
        value="<?php echo $id; ?>"
    >

    <label>Name</label><br>

    <input
        type="text"
        name="name"
        value="<?php echo htmlspecialchars($name); ?>"
        required
    >

    <br><br>

    <label>Email</label><br>

    <input
        type="email"
        name="email"
        value="<?php echo htmlspecialchars($email); ?>"
        required
    >

    <br><br>

    <label>Role</label><br>

    <select name="role" required>

        <option
            value="employer"
            <?php echo $role === "employer" ? "selected" : ""; ?>
        >
            Employer
        </option>

        <option
            value="job_seeker"
            <?php echo $role === "job_seeker" ? "selected" : ""; ?>
        >
            Job Seeker
        </option>

    </select>

    <br><br>

    <button type="submit">Update User</button>

</form>

<br>

<a href="users.php">Back to Users</a>

</body>
</html>
