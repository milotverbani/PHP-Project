<?php
include "db.php"; 


session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$id = $_GET['id']; 


if (isset($_POST['update_user'])) {
    $fullname = $_POST['fullname'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

   
    $stmt = $conn->prepare("UPDATE users SET fullname = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssi", $fullname, $password, $id);
    if ($stmt->execute()) {
        header('Location:Usersuptade.php');
        echo "Të dhënat u përditësuan me sukses!";
    } else {
        echo "Gabim gjatë përditësimit!";
    }
}


$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Edit User</title>
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

<div class="p-4">
    <form method="POST" action="">
        <label for="fullname" class="block mb-2 text-gray-600">Fullname:</label>
        <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" class="border rounded px-2 py-1 w-full mb-4" required />

        <label for="password" class="block mb-2 text-gray-600">Fjalëkalimi i Ri:</label>
        <input type="password" name="password" placeholder="Fjalëkalimi i Ri" class="border rounded px-2 py-1 w-full mb-4" required />

        <button type="submit" name="update_user" class="bg-blue-500 text-white px-4 py-2 rounded">Ruaj Ndryshimet</button>
    </form>
</div>

</body>
</html>
