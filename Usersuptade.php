<?php
include "db.php";
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$role_filter = '';
if (isset($_GET['role']) && in_array($_GET['role'], ['admin', 'klient'])) {
    $role_filter = $_GET['role'];
}


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    echo "Përdoruesi u fshi me sukses!";
}


if ($role_filter) {
    $result = $conn->query("SELECT * FROM users WHERE role = '$role_filter'");
} else {
    $result = $conn->query("SELECT * FROM users");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Admin Panel</title>
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
    <h2 class="text-xl font-bold mb-4">Lista e Përdoruesve</h2>

   
    <form action="" method="GET" class="mb-4">
        <label for="role" class="mr-2">Filtroni sipas rolit:</label>
        <select name="role" id="role" class="p-2 border border-gray-400">
            <option value="">Të gjitha</option>
            <option value="klient" <?php echo $role_filter == 'klient' ? 'selected' : ''; ?>>Klient</option>
            <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Filtro</button>
    </form>

    <table class="w-full border-collapse border border-gray-400">
        <thead class="bg-gray-300">
            <tr>
                <th class="border border-gray-400 p-2">ID</th>
                <th class="border border-gray-400 p-2">Email</th>
                <th class="border border-gray-400 p-2">Fullname</th>
                <th class="border border-gray-400 p-2">Roli</th>
                <th class="border border-gray-400 p-2">Edito</th>
                <th class="border border-gray-400 p-2">Fshi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-400 p-2"><?php echo $user['id']; ?></td>
                    <td class="border border-gray-400 p-2"><?php echo $user['email']; ?></td>
                    <td class="border border-gray-400 p-2"><?php echo $user['fullname']; ?></td>
                    <td class="border border-gray-400 p-2"><?php echo $user['role']; ?></td>
                    <td class="border border-gray-400 p-2">
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</a>
                    </td>
                    <td class="border border-gray-400 p-2">
                        <a href="?delete_id=<?php echo $user['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('A jeni të sigurt që dëshironi ta fshini këtë përdorues?');">Fshi</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
