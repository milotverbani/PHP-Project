<?php
include "db.php";
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php"); 
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT r.ticket_type, r.price, m.home_team, m.away_team, m.match_date, m.stadium
                        FROM reservations r
                        JOIN matches m ON r.match_id = m.id
                        WHERE r.user_id = ? 
                        ORDER BY m.match_date ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reservations_result = $stmt->get_result();

$total_price = 0; 

?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Dashboard - Rezervimet e Biletave</title>
</head>
<body class="bg-gray-100">
    <header class="flex justify-evenly items-center text-white bg-blue-500 p-2">
        <div class="flex items-center">
            <h1 class="text-2xl mr-3">Milot</h1>
            <img src="https://static.vecteezy.com/system/resources/thumbnails/009/665/124/small_2x/football-ball-illustration-3d-image-transparent-background-png.png" 
                 class="w-[40px]" alt="Football Icon">
        </div>
        <div class="flex gap-3">
            <a href="index.php">Home</a>
            <a href="reservematch.php">Reserve</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="tables.php">Tables</a> 
                <a href="?logout=true">Logout</a>
            <?php else: ?>
                <a href="Login.php">Login</a>
                <a href="Register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container mx-auto my-8 p-4 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center mb-6">Rezervimet Tuaja</h2>

        <?php if ($reservations_result->num_rows > 0): ?>
            <div class="space-y-6">
                <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                    <?php $total_price += $reservation['price'];  ?>
                    <div class="bg-gray-50 p-4 rounded-lg shadow">
                        <h3 class="text-xl font-semibold text-blue-500">
                            <?php echo $reservation['home_team'] . " vs " . $reservation['away_team']; ?>
                        </h3>
                        <div class="flex justify-between mt-2 text-lg">
                            <p><span class="font-semibold">Stadiumi:</span> <?php echo $reservation['stadium']; ?></p>
                            <p><span class="font-semibold">Data:</span> <?php echo date('d M Y, H:i', strtotime($reservation['match_date'])); ?></p>
                        </div>
                        <div class="flex justify-between mt-2 text-lg">
                            <p><span class="font-semibold">Lloji i biletës:</span> <?php echo $reservation['ticket_type']; ?></p>
                            <p><span class="font-semibold">Çmimi:</span> €<?php echo number_format($reservation['price'], 2); ?></p>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">Rezervuar me sukses!</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="mt-6 text-center text-xl font-bold text-blue-600">
                Totali i të gjitha biletave: €<?php echo number_format($total_price, 2); ?>
            </div>

        <?php else: ?>
            <p class="text-center text-lg text-gray-500">Nuk keni asnjë rezervim ende.</p>
        <?php endif; ?>
    </div>

</body>
</html>
