
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../authentication/login.php");
    exit;
}

if ($_SESSION["user_role"] !== "job_seeker") {
    header("Location: ../authentication/login.php");
    exit;
}

require_once "../config/database.php";

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT * FROM job_seeker_profiles WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$formData = $profile ?: [
    "phone" => "",
    "education" => "",
    "skills" => "",
    "experience" => ""
];

$message = isset($_GET["saved"])
    ? "Profile saved successfully!"
    : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $phone = trim($_POST["phone"]);
    $education = trim($_POST["education"]);
    $skills = trim($_POST["skills"]);
    $experience = trim($_POST["experience"]);

    $formData = [
        "phone" => $phone,
        "education" => $education,
        "skills" => $skills,
        "experience" => $experience
    ];

    if (empty($phone) || empty($education) || empty($skills)) {

        $message = "Phone, education and skills are required.";

    } else {

        if ($profile) {

            $save = $conn->prepare(
                "UPDATE job_seeker_profiles
                 SET phone = ?, education = ?, skills = ?, experience = ?
                 WHERE user_id = ?"
            );

            $save->bind_param(
                "ssssi",
                $phone,
                $education,
                $skills,
                $experience,
                $user_id
            );

        } else {

            $save = $conn->prepare(
                "INSERT INTO job_seeker_profiles
                 (user_id, phone, education, skills, experience)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $save->bind_param(
                "issss",
                $user_id,
                $phone,
                $education,
                $skills,
                $experience
            );
        }

        if ($save->execute()) {
            header("Location: profile.php?saved=1");
            exit;
        } else {
            $message = "Failed to save profile.";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>My Job Seeker Profile</h1>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    <label>Phone Number *</label><br>
    <input
        type="text"
        name="phone"
        value="<?php echo htmlspecialchars($formData["phone"] ?? ""); ?>"
        required
    >

    <br><br>

    <label>Education *</label><br>
    <input
        type="text"
        name="education"
        placeholder="e.g. BS Computer Science"
        value="<?php echo htmlspecialchars($formData["education"] ?? ""); ?>"
        required
    >

    <br><br>

    <label>Skills *</label><br>
    <textarea
        name="skills"
        placeholder="e.g. PHP, HTML, CSS, Python"
        required
    ><?php echo htmlspecialchars($formData["skills"] ?? ""); ?></textarea>

    <br><br>

    <label>Experience</label><br>
    <textarea
        name="experience"
        placeholder="Describe your experience or projects"
    ><?php echo htmlspecialchars($formData["experience"] ?? ""); ?></textarea>

    <br><br>

    <button type="submit">Save Profile</button>

</form>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
