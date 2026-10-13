
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

$result = $conn->query(
    "SELECT id, name, email, role, created_at
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Manage Users</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Registered At</th>
        <th>Action</th>
    </tr>

    <?php if ($result && $result->num_rows > 0) { ?>

        <?php while ($user = $result->fetch_assoc()) { ?>

            <tr>
                <td><?php echo $user["id"]; ?></td>

                <td>
                    <?php echo htmlspecialchars($user["name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($user["email"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($user["role"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($user["created_at"]); ?>
                </td>
             <td>
    <?php if ($user["role"] !== "admin") { ?>

        <a href="edit_user.php?id=<?php echo $user['id']; ?>">
            Edit
        </a>

        |

        <form
            method="POST"
            action="delete_user.php"
            style="display:inline;"
            onsubmit="return confirm('Are you sure you want to delete this user?');"
        >

            <input
                type="hidden"
                name="user_id"
                value="<?php echo $user['id']; ?>"
            >

            <button type="submit">Delete</button>

        </form>

    <?php } else { ?>

        Protected Admin

    <?php } ?>
</td>
            </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="6">No users found.</td>
        </tr>

    <?php } ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
