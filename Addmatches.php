<?php
session_start();
include "db.php";

$error_message = ''; 

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $home_team = $_POST['home_team'];
    $away_team = $_POST['away_team'];
    $match_date = $_POST['match_date'];
    $stadium = $_POST['stadium'];
    $ticket_price = $_POST['ticket_price'];
    $available_tickets = $_POST['available_tickets'];
    
    $target_file = 'images/noimg.jpeg'; 

    
    if ($_FILES['match_image']['name']) {
        $target_dir = "images/";  
        $target_file = $target_dir . basename($_FILES["match_image"]["name"]);
        
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            move_uploaded_file($_FILES["match_image"]["tmp_name"], $target_file); 
        } else {
           
            $error_message = "Vetëm formate të lejuara (JPG, JPEG, PNG, GIF) janë të pranuara. Ndeshja nuk u shtua.";
        }
    }

    if (empty($error_message)) {
        $sql = "INSERT INTO matches (home_team, away_team, match_date, stadium, ticket_price, available_tickets, match_image) 
                VALUES ('$home_team', '$away_team', '$match_date', '$stadium', '$ticket_price', '$available_tickets', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            echo "Ndeshja u shtua me sukses!";
        } else {
            echo "Ndodhi një gabim: " . $conn->error;
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Shto Ndeshje</title>
</head>
<body class="bg-gray-100 ">

<header class="flex justify-evenly items-center bg-emerald-400 text-white text-lg p-2 mb-4">
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

<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center">Shto Ndeshje Futbolli</h1>

    <?php if ($error_message): ?>
        <div class="text-red-500 text-center mb-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form action="addmatches.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label for="home_team" class="block text-sm font-medium text-gray-700">Emri i ekipit të shtëpisë:</label>
            <input type="text" name="home_team" id="home_team" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="away_team" class="block text-sm font-medium text-gray-700">Emri i ekipit të jashtëm:</label>
            <input type="text" name="away_team" id="away_team" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="match_date" class="block text-sm font-medium text-gray-700">Data dhe ora e ndeshjes:</label>
            <input type="datetime-local" name="match_date" id="match_date" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="stadium" class="block text-sm font-medium text-gray-700">Stadiumi:</label>
            <input type="text" name="stadium" id="stadium" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="ticket_price" class="block text-sm font-medium text-gray-700">Çmimi i biletës:</label>
            <input type="number" name="ticket_price" id="ticket_price" step="0.01" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="available_tickets" class="block text-sm font-medium text-gray-700">Numri i biletave të disponueshme:</label>
            <input type="number" name="available_tickets" id="available_tickets" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="match_image" class="block text-sm font-medium text-gray-700">Ngarko foto të ndeshjes:</label>
            <input type="file" name="match_image" id="match_image" accept="image/*" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <input type="submit" name="submit" value="Shto Ndeshje" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        </div>
    </form>
</div>

</body>
</html>
