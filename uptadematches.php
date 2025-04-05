<?php
include "db.php"; 

session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}


if (isset($_POST['update'])) {
    $match_id = $_POST['match_id'];
    $ticket_price = $_POST['ticket_price'];
    $available_tickets = $_POST['available_tickets'];

   
    if (empty($ticket_price) || empty($available_tickets)) {
        $error_message = "Çmimi i biletës dhe numri i biletave duhet të jenë të plota!";
    } else {
       
        $stmt = $conn->prepare("UPDATE matches SET ticket_price = ?, available_tickets = ? WHERE id = ?");
        $stmt->bind_param("dii", $ticket_price, $available_tickets, $match_id);

        if ($stmt->execute()) {
            $success_message = "Ndeshja u përditësua me sukses!";
        } else {
            $error_message = "Ka ndodhur një gabim gjatë përditësimit!";
        }
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "Ndeshja u fshi me sukses!";
    } else {
        $error_message = "Ka ndodhur një gabim gjatë fshirjes!";
    }
}


$result = $conn->query("SELECT * FROM matches");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Update Matches</title>
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

    <table class="w-full border-collapse border border-gray-400">
        <thead class="bg-gray-300">
            <tr>
                <th class="border border-gray-400 p-2">ID</th>
                <th class="border border-gray-400 p-2">Ekipi Dhome</th>
                <th class="border border-gray-400 p-2">Ekipi Jashtë</th>
                <th class="border border-gray-400 p-2">Data</th>
                <th class="border border-gray-400 p-2">Çmimi Biletës</th>
                <th class="border border-gray-400 p-2">Biletat e Disponueshme</th>
                <th class="border border-gray-400 p-2">Update</th>
                <th class="border border-gray-400 p-2">Fshi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($match = $result->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-400 p-2"><?php echo $match['id']; ?></td>
                    <td class="border border-gray-400 p-2"><?php echo $match['home_team']; ?></td>
                    <td class="border border-gray-400 p-2"><?php echo $match['away_team']; ?></td>
                    <td class="border border-gray-400 p-2"><?php echo date('d M Y, H:i', strtotime($match['match_date'])); ?></td>
                    <td class="border border-gray-400 p-2"><?php echo $match['ticket_price']; ?> €</td>
                    <td class="border border-gray-400 p-2"><?php echo $match['available_tickets']; ?></td>
                    <td class="border border-gray-400 p-2">
                        <a href="uptadematches.php?edit_id=<?php echo $match['id']; ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edito</a>
                    </td>
                    <td class="border border-gray-400 p-2">
                        <a href="uptadematches.php?delete_id=<?php echo $match['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('A jeni të sigurt që dëshironi ta fshini këtë ndeshje?')">Fshi</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM matches WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $match = $result->fetch_assoc();
?>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Përditëso Ndeshjen</h2>

        <form action="uptadematches.php" method="POST" class="space-y-4">
            <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">

            <div>
                <label for="ticket_price" class="block">Çmimi i Biletës (€)</label>
                <input type="number" name="ticket_price" id="ticket_price" class="border p-2 w-full" value="<?php echo $match['ticket_price']; ?>" required>
            </div>

            <div>
                <label for="available_tickets" class="block">Numri i Biletave të Disponueshme</label>
                <input type="number" name="available_tickets" id="available_tickets" class="border p-2 w-full" value="<?php echo $match['available_tickets']; ?>" required>
            </div>

            <button type="submit" name="update" class="bg-blue-500 text-white p-2 w-full rounded-md">Përditëso Ndeshjen</button>
        </form>
    </div>
<?php } ?>

</body>
</html>
