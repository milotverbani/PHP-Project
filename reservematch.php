<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Rezervo Ndeshje</title>
</head>
<body class="bg-gray-100">

<header class="flex justify-evenly items-center text-white bg-blue-500 p-2">
    <div class="flex items-center">
        <h1 class="text-2xl mr-3">Milot</h1>
        <img src="https://static.vecteezy.com/system/resources/thumbnails/009/665/124/small_2x/football-ball-illustration-3d-image-transparent-background-png.png" class="w-[40px]" alt="">
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

<div class="max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-semibold text-center mb-8">Ndeshjet e Disponueshme</h1>

    <div class="mb-8">
        <form method="GET" class="flex justify-center space-x-4">
            <input type="text" name="search" placeholder="Kërko ndeshje..." class="px-4 py-2 border rounded-md w-1/3" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <select name="sort_price" class="px-4 py-2 border rounded-md">
                <option value="">Zgjidh Renditjen</option>
                <option value="asc" <?php echo isset($_GET['sort_price']) && $_GET['sort_price'] === 'asc' ? 'selected' : ''; ?>>Çmimi: Nga më i ulëti</option>
                <option value="desc" <?php echo isset($_GET['sort_price']) && $_GET['sort_price'] === 'desc' ? 'selected' : ''; ?>>Çmimi: Nga më i larti</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md">Kërko</button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">

    <?php
    include "db.php";

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $sort_price = isset($_GET['sort_price']) ? $_GET['sort_price'] : '';
    
  
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $matches_per_page = 4; 
    $offset = ($page - 1) * $matches_per_page; 
    
    
    $sql = "SELECT * FROM matches WHERE home_team LIKE ? OR away_team LIKE ? OR stadium LIKE ?";
    if ($sort_price === 'asc') {
        $sql .= " ORDER BY ticket_price ASC";
    } elseif ($sort_price === 'desc') {
        $sql .= " ORDER BY ticket_price DESC";
    }
    $sql .= " LIMIT $matches_per_page OFFSET $offset"; 
    
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $home_team = $row['home_team'];
            $away_team = $row['away_team'];
            $match_date = $row['match_date'];
            $stadium = $row['stadium'];
            $ticket_price = $row['ticket_price'];
            $available_tickets = $row['available_tickets'];
            $match_image = $row['match_image'];

            $button_class = $available_tickets == 0 ? 'bg-red-600 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-500';
            $button_text = $available_tickets == 0 ? 'Të gjitha biletat janë shitur per këtë ndeshje' : 'Rezervo Tani';
            $button_disabled = $available_tickets == 0 ? 'disabled' : '';

            echo "
            <div class='bg-gray-200 p-4 rounded-lg shadow-md'>
                <img src='$match_image' alt='Match Image' class='w-full h-64 object-cover rounded-md'>
                <h2 class='text-xl font-semibold mt-4'>$home_team vs $away_team</h2>
                <p class='text-gray-600'>Data: " . date('d M Y, H:i', strtotime($match_date)) . "</p>
                <p class='text-gray-600'>Stadium: $stadium</p>
                <p class='text-gray-600'>Çmimi i biletës: €$ticket_price</p>
                <p class='text-gray-600'>Biletat e disponueshme: $available_tickets</p>
                <a href='matchdetails.php?match_id=" . $row['id'] . "' class='mt-4 block text-white text-center py-2 rounded-md $button_class' $button_disabled>$button_text</a>
            </div>
            ";
        }
    } else {
        echo "<p class='text-center text-red-500'>Nuk ka ndeshje të disponueshme për t'u rezervuar për këtë kërkim.</p>";
    }

    
    $count_query = "SELECT COUNT(*) as total FROM matches WHERE home_team LIKE ? OR away_team LIKE ? OR stadium LIKE ?";
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_matches = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_matches / $matches_per_page); 

    $conn->close();
    ?>
    </div>

 
    <div class="flex justify-center mt-8">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?search=<?php echo $search; ?>&sort_price=<?php echo $sort_price; ?>&page=<?php echo $i; ?>"
               class="px-4 py-2 mx-1 border rounded-md <?php echo $i === $page ? 'bg-blue-500 text-white' : 'bg-white'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

</div>

</body>
</html>
            