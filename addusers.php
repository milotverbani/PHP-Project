<?php
include "db.php";
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($email) || empty($fullname) || empty($password) || empty($role)) {
        $error_message = "Të gjitha fushat janë të detyrueshme!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      
        $stmt = $conn->prepare("INSERT INTO users (email, fullname, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $fullname, $hashed_password, $role);
        if ($stmt->execute()) {
            $success_message = "Përdoruesi u shtua me sukses!";
        } else {
            $error_message = "Ka ndodhur një gabim gjatë shtimit të përdoruesit.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Shto Përdorues</title>
</head>
<body>
<header class="flex justify-evenly items-center bg-emerald-400 text-white text-lg p-2">
        <h1>ADMIN PANEL</h1>
        <div>
             <a href="admin.php">Home</a>
            <a href="Usersuptade.php" class="mx-2 text-white">Update Users</a>
            <a href="Addmatches.php" class="mx-2 text-white">Add Matches</a>
            <a href="addusers.php">Add Users</a>
            <a href="uptadematches.php">Uptade matches</a>
            <a href="uptadeclubs.php">Uptade Clubs</a>
            <a href="addclubs.php">Add Clubs</a>
            <form action="" method="POST" class="inline-block">
                <button type="submit" class="cursor-pointer" name="logout">Logout</button>
            </form>
        </div>
    </header>

<div class="container mx-auto p-6">
    <?php if (isset($success_message)): ?>
        <div class="bg-green-500 text-white p-4 rounded-md mb-4"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="bg-red-500 text-white p-4 rounded-md mb-4"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="addusers.php" method="POST" class="space-y-4">
        <div>
            <label for="email" class="block">Email</label>
            <input type="email" name="email" id="email" class="border p-2 w-full" required>
        </div>

        <div>
            <label for="fullname" class="block">Fullname</label>
            <input type="text" name="fullname" id="fullname" class="border p-2 w-full" required>
        </div>

        <div>
            <label for="password" class="block">Fjalëkalimi</label>
            <input type="password" name="password" id="password" class="border p-2 w-full" required>
        </div>

        <div>
            <label for="role" class="block">Roli</label>
            <select name="role" id="role" class="border p-2 w-full" required>
                <option value="klient">Klient</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white p-2 w-full rounded-md">Shto Përdorues</button>
    </form>
</div>

</body>
</html>
