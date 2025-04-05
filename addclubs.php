<?php
include "db.php";

session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $points = $_POST["points"];
    $vote_points = $_POST["vote_points"];

    if (!empty($name) && is_numeric($points) && is_numeric($vote_points)) {
        $query = "INSERT INTO clubs (name, points, vote_points) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $name, $points, $vote_points);
        
        if ($stmt->execute()) {
            echo "<div class='text-green-500 text-center mt-4'>Klubi u shtua me sukses!</div>";
        } else {
            echo "<div class='text-red-500 text-center mt-4'>Gabim gjate shtimit te klubit: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='text-red-500 text-center mt-4'>Ju lutem plotësoni të gjitha fushat me vlera të vlefshme.</div>";
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shto Klub</title>
    <link rel="stylesheet" href="output.css">
</head>
<body class="bg-gray-100">
<header class="flex justify-evenly items-center bg-emerald-400 text-white text-lg p-2">
        <h1>ADMIN PANEL</h1>
        <div>
            <a href="admin.php">Home</a>
            <a href="Usersuptade.php" class="mx-2 text-white">Update Users</a>
            <a href="Addmatches.php" class="mx-2 text-white">Add Matches</a>
            <a href="addusers.php">Add Users</a>
            <a href="uptadematches.php">Uptade matches</a>
            <a href="uptadeclubs.php">Uptade Clubs</a>
            <form action="" method="POST" class="inline-block">
                <button type="submit" class="cursor-pointer" name="logout">Logout</button>
            </form>
        </div>
    </header>


    <div class="container mx-auto p-6">
        <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-center mb-6">Shto Klub të Ri</h2>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Emri i Klubit:</label>
                    <input type="text" name="name" id="name" class="w-full p-3 border border-gray-300 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div class="mb-4">
                    <label for="points" class="block text-sm font-medium text-gray-700">Pikët:</label>
                    <input type="number" name="points" id="points" class="w-full p-3 border border-gray-300 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div class="mb-6">
                    <label for="vote_points" class="block text-sm font-medium text-gray-700">Pikët e Votimit:</label>
                    <input type="number" name="vote_points" id="vote_points" class="w-full p-3 border border-gray-300 rounded-lg mt-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Shto Klub</button>
            </form>
        </div>
    </div>

</body>
</html>
