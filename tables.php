<?php
session_start();
include "db.php"; 


if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php"); 
    exit();
}


if (isset($_GET['logout'])) {
    session_destroy(); 
    header("Location: index.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_club_id'])) {
    $user_id = $_SESSION['user_id']; 
    $club_id = $_POST['vote_club_id'];

   
    $checkVoteSql = "SELECT * FROM users_club WHERE user_id = ?";
    $stmt = $conn->prepare($checkVoteSql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $voteResult = $stmt->get_result();

    if ($voteResult->num_rows > 0) {
        
        echo "<p class='text-center text-red-600 mt-4'>Ju mund të votoni vetëm një herë dhe vetëm për një klub!</p>";
    } else {
       
        $insertVoteSql = "INSERT INTO users_club (user_id, club_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertVoteSql);
        $stmt->bind_param("ii", $user_id, $club_id);
        $stmt->execute();

        $updatePointsSql = "UPDATE clubs SET vote_points = vote_points + 1 WHERE id = ?";
        $stmt = $conn->prepare($updatePointsSql);
        $stmt->bind_param("i", $club_id);
        $stmt->execute();

        echo "<p class='text-center text-green-600 mt-4'>Faleminderit për votën tuaj!</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = htmlspecialchars($_POST['message']);

    if (!empty($message)) {
        $insertMessageSql = "INSERT INTO messages (user_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($insertMessageSql);
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
    }
}


if (isset($_POST['delete_message_id'])) {
    $message_id = $_POST['delete_message_id'];
    $user_id = $_SESSION['user_id'];

    $deleteMessageSql = "DELETE FROM messages WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($deleteMessageSql);
    $stmt->bind_param("ii", $message_id, $user_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="output.css">
    <title>Tables-Clubs</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<header class="flex justify-evenly items-center text-white bg-blue-500 p-2">
    <div class="flex items-center">
        <h1 class="text-2xl mr-3">Milot</h1>
        <img src="https://static.vecteezy.com/system/resources/thumbnails/009/665/124/small_2x/football-ball-illustration-3d-image-transparent-background-png.png" class="w-[40px]" alt="Football Icon">
    </div>
    <div class="flex gap-3">
        <a href="index.php">Home</a>
        <a href="reservematch.php">Reserve</a>
        <?php 
        if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a> 
            <a href="tables.php">Tables</a> 
            <a href="?logout=true">Logout</a>
        <?php else: ?>
            <a href="Login.php">Login</a>
            <a href="Register.php">Register</a>
        <?php endif; ?>
    </div>
</header>

<h2 class="text-center text-3xl font-bold mt-8 mb-4 text-gray-800">Tabela e Klubeve dhe Pikëve</h2>


<div class="flex justify-between space-x-12">
    <div class="w-1/2">
        <table class="min-w-full bg-white rounded-lg shadow-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-200">
                    <th class="text-left px-6 py-4 text-gray-600 uppercase text-sm tracking-wider">Klubi</th>
                    <th class="text-left px-6 py-4 text-gray-600 uppercase text-sm tracking-wider">Pikët</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300">
                <?php
                
                $sql = "SELECT id, name, points FROM clubs ORDER BY points DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='hover:bg-gray-100'>";
                        echo "<td class='px-6 py-4 text-gray-700'>" . $row['name'] . "</td>";
                        echo "<td class='px-6 py-4 text-gray-700'>" . $row['points'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2' class='px-6 py-4 text-center text-gray-500'>Nuk ka klube të regjistruara</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

  
    <div class="w-1/2 mr-5">
        <h2 class="text-center text-2xl font-bold mt-12 mb-4 text-gray-800 relative top-[150px]">Voto kush mendon se e fiton UCHL
        <i class="fa-solid fa-arrow-up"></i>
        </h2>
        <form method="POST" class="bg-white p-6 rounded-lg shadow-lg relative bottom-[95px]">
            <label for="club" class="block mb-2 text-gray-700">Zgjedh klubin për të votuar:</label>
            <select name="vote_club_id" id="club" class="w-full px-4 py-2 border rounded-lg mb-4">
                <?php
                
                $sql = "SELECT id, name FROM clubs";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                }
                ?>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Voto</button>
        </form>
    </div>
</div>


<h2 class="text-center text-2xl font-bold mt-12 mb-4 text-gray-800">Votat e Fansave (Grafik)</h2>

<div class="w-1/2 mx-auto">
    <canvas id="votesChart"></canvas>
</div>

<script>

<?php

$sql = "SELECT clubs.name, COUNT(users_club.club_id) as total_votes
        FROM clubs 
        LEFT JOIN users_club ON clubs.id = users_club.club_id 
        GROUP BY clubs.id";
$result = $conn->query($sql);

$clubs = [];
$votes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clubs[] = $row['name'];
        $votes[] = $row['total_votes'];
    }
}
?>


const clubNames = <?php echo json_encode($clubs); ?>;
const voteCounts = <?php echo json_encode($votes); ?>;


const ctx = document.getElementById('votesChart').getContext('2d');
const votesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: clubNames, 
        datasets: [{
            label: 'Votat Totale',
            data: voteCounts, 
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<div class="w-1/2 mx-auto mt-8 p-4 bg-white rounded-lg shadow-lg">
    <h2 class="text-center text-2xl font-bold mb-4 text-gray-800">Chat</h2>
    <div class="overflow-y-auto h-64 border p-2">
        <?php
        $chatSql = "SELECT messages.id, messages.message, users.fullname, messages.user_id FROM messages JOIN users ON messages.user_id = users.id ORDER BY messages.id DESC";
        $result = $conn->query($chatSql);
        
        while ($row = $result->fetch_assoc()) {
            echo "<div class='border-b py-2 px-4 flex justify-between items-center'>";
            echo "<span class='text-gray-700'><strong>" . $row['fullname'] . ":</strong> " . $row['message'] . "</span>";
            if ($row['user_id'] == $_SESSION['user_id']) {
                echo "<form method='POST' class='inline' onsubmit='return confirmDelete()'>";
                echo "<input type='hidden' name='delete_message_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' class='text-red-500 text-sm' onclick='return confirm(\"A je i sigurt që dëshironi të fshini këtë mesazh?\")'>Fshije</button>";
                echo "</form>";
            }
            echo "</div>";
        }
        ?>
    </div>
    <form method="POST" class="mt-4">
        <input type="text" name="message" class="w-full px-4 py-2 border rounded-lg" placeholder="Shkruaj një mesazh..." required>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2 w-full">Dërgo</button>
    </form>
</div>

</body>
</html>
