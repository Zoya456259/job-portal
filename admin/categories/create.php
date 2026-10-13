<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../../authentication/login.php");
    exit;
}

require_once "../../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);

    if (empty($name)) {

        $message = "Category name is required.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO job_categories (name) VALUES (?)"
        );

        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {

            header("Location: index.php");
            exit;

        } else {

            $message = "Failed to create category.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<h1>Add Job Category</h1>

<p><?php echo $message; ?></p>

<form method="POST">

    <label>Category Name</label><br>

    <input
        type="text"
        name="name"
        placeholder="Enter category name"
        required
    >

    <br><br>

    <button type="submit">
        Add Category
    </button>

</form>

<br>

<a href="index.php">Back to Categories</a>

</body>

</html>