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

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = $_GET["id"];

$stmt = $conn->prepare("SELECT * FROM job_categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: index.php");
    exit;
}

$category = $result->fetch_assoc();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);

    if (empty($name)) {

        $message = "Category name is required.";

    } else {

        $update = $conn->prepare(
            "UPDATE job_categories SET name = ? WHERE id = ?"
        );

        $update->bind_param("si", $name, $id);

        if ($update->execute()) {

            header("Location: index.php");
            exit;

        } else {

            $message = "Failed to update category.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<h1>Edit Job Category</h1>

<p><?php echo $message; ?></p>

<form method="POST">

    <label>Category Name</label><br>

    <input
        type="text"
        name="name"
        value="<?php echo htmlspecialchars($category["name"]); ?>"
        required
    >

    <br><br>

    <button type="submit">
        Update Category
    </button>

</form>

<br>

<a href="index.php">Back to Categories</a>

</body>

</html>