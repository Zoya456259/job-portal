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

$result = $conn->query("SELECT * FROM job_categories ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>

<head>
    <title>Job Categories</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<h1>Job Categories</h1>

<p>
    Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
</p>

<a href="create.php">Add New Category</a>

<br><br>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>

    <?php while ($category = $result->fetch_assoc()) { ?>

        <tr>

            <td>
                <?php echo $category["id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($category["name"]); ?>
            </td>

            <td>
                <?php echo $category["created_at"]; ?>
            </td>

            <td>

                <a href="edit.php?id=<?php echo $category["id"]; ?>">
                    Edit
                </a>

                |

                <a href="delete.php?id=<?php echo $category["id"]; ?>"
                   onclick="return confirm('Are you sure you want to delete this category?');">
                    Delete
                </a>

            </td>

        </tr>

    <?php } ?>

</table>

<br>

<a href="../dashboard.php">Back to Dashboard</a>

</body>

</html>