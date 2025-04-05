<?php
include "db.php";
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'klient') {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
$matches_query = "
    SELECT matches.home_team, matches.away_team, COUNT(reservations.id) as tickets_sold
    FROM matches
    LEFT JOIN reservations ON matches.id = reservations.match_id
    GROUP BY matches.id
";
$matches_result = $conn->query($matches_query);
$user_query = "SELECT COUNT(*) as total_users FROM users";
$user_result = $conn->query($user_query);
$user_count = $user_result->fetch_assoc()['total_users'];


$match_query = "SELECT COUNT(*) as total_matches FROM matches";
$match_result = $conn->query($match_query);
$match_count = $match_result->fetch_assoc()['total_matches'];

$total_price_query = "SELECT SUM(price) as total_price FROM reservations";
$total_price_result = $conn->query($total_price_query);
$total_price = $total_price_result->fetch_assoc()['total_price'] ;



$reservations_query = "
    SELECT users.fullname, matches.home_team, matches.away_team, matches.match_date, reservations.reservation_date, 
           reservations.ticket_type, reservations.price 
    FROM reservations
    INNER JOIN users ON reservations.user_id = users.id
    INNER JOIN matches ON reservations.match_id = matches.id
    ORDER BY reservations.reservation_date DESC
";
$reservations_result = $conn->query($reservations_query);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <style>
        #ticketChart {
    width: 400px !important;
    height: 400px !important;
}
    </style>
    <title>Admin Panel</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <div class="container mx-auto  p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 ml-[20px] mt-[100px] gap-4">
            <div class="bg-blue-500 text-white p-4 rounded-lg shadow-md text-center">
                <h2 class="text-2xl font-bold">Total Users</h2>
                <p class="text-3xl"><?php echo $user_count; ?></p>
            </div>

            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md text-center">
                <h2 class="text-2xl font-bold">Total Matches</h2>
                <p class="text-3xl"><?php echo $match_count; ?></p>
            </div>
            <div class="bg-purple-500 text-white p-4 rounded-lg text-center">
                <h2 class="text-2xl font-bold">Total Price</h2>
                <p class="text-3xl">
                    <?php echo number_format($total_price, 2); ?> €
                </p>
            </div>
         
        </div>

        <div class="mt-8 ">
            <h2 class="text-2xl font-bold mb-4">Lista e Rezervimeve</h2>

            <table class="w-full table-auto border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-100">
            <th class="border border-gray-300 p-2 text-left">Full Name</th>
            <th class="border border-gray-300 p-2 text-left">Ndeshja</th>
            <th class="border border-gray-300 p-2 text-left">Data e Ndeshjes</th>
            <th class="border border-gray-300 p-2 text-left">Data e Rezervimit</th>
            <th class="border border-gray-300 p-2 text-left">Tipi i Biletës</th>
            <th class="border border-gray-300 p-2 text-left">Çmimi (€)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($reservations_result->num_rows > 0) {
            while ($row = $reservations_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td class='border border-gray-300 p-2'>{$row['fullname']}</td>";
                echo "<td class='border border-gray-300 p-2'>{$row['home_team']} vs {$row['away_team']}</td>";
                echo "<td class='border border-gray-300 p-2'>" . date('d M Y, H:i', strtotime($row['match_date'])) . "</td>";
                echo "<td class='border border-gray-300 p-2'>" . date('d M Y, H:i', strtotime($row['reservation_date'])) . "</td>";
                echo "<td class='border border-gray-300 p-2'>{$row['ticket_type']}</td>"; 
                echo "<td class='border border-gray-300 p-2'>€" . number_format($row['price'], 2) . "</td>"; 
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='text-center p-4'>Nuk ka rezervime për momentin.</td></tr>";
        }
        ?>
    </tbody>
</table>
        </div>

    </div>
    <h2 class="text-2xl font-bold text-center">Shitja e Bileteve për Ndeshje</h2>
    <div class="container mx-auto p-6 flex justify-center">
    <canvas id="ticketChart" width="200" height="200"></canvas>
</div>

<script>
    
    var ctx = document.getElementById('ticketChart').getContext('2d');

    var labels = [];
    var data = [];

    <?php
    if ($matches_result->num_rows > 0) {
        while ($row = $matches_result->fetch_assoc()) {
            echo "labels.push('{$row['home_team']} vs {$row['away_team']}');";
            echo "data.push({$row['tickets_sold']});";
        }
    }
    ?>

    var ticketChart = new Chart(ctx, {
        type: 'pie', 
        data: {
            labels: labels, 
            datasets: [{
                label: 'Biletat e Shitura',
                data: data,
                backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A8', '#FFBB33'],
                borderColor: ['#fff', '#fff', '#fff', '#fff', '#fff'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' bileta';
                        }
                    }
                }
            }
        }
    });
</script>


</body>
</html>
