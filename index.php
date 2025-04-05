<?php
include "Support.php";

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
    <title>Milot Fotball Ticket</title>
</head>
<body>
<header class="flex justify-evenly items-center text-white bg-blue-500 p-2">
    <div class="flex items-center">
        <h1 class="text-2xl mr-3">Milot</h1>
        <img src="https://static.vecteezy.com/system/resources/thumbnails/009/665/124/small_2x/football-ball-illustration-3d-image-transparent-background-png.png" class="w-[40px]" alt="">
    </div>
    <div class=" flex gap-3">
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


<div class="w-full h-[600px] overflow-hidden rounded-lg shadow-lg">
    <div id="slides" class="flex transition-transform duration-500 w-full ease-in-out">
        <div class="min-w-full">
            <img src="https://www.arsenal.com/sites/default/files/styles/large_16x9/public/images/real-madrid_r4kfeyt4.png?h=6dff888f&auto=webp&itok=fz1lVcaJ" alt="Slide 1" class="w-full h-[700px]">
        </div>
        <div class="min-w-full">
            <img src="https://gazettengr.com/wp-content/uploads/Bayern-VS-Inter.png" alt="Slide 2" class="w-full h-[700px]">
        </div>
        <div class="min-w-full">
            <img src="https://image.discovery.indazn.com/ca/v2/ca/image?id=bp1kmzypix6ewedsir2hn34nf_image-header_pCa_1742465932000&quality=70" alt="Slide 3" class="w-full h-[700px]">
        </div>
    </div>
    <button id="prev" class="absolute top-[350px] left-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition">
        &#10094;
    </button>
    <button id="next" class="absolute top-[350px] right-4 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition">
        &#10095;
    </button>
</div>
<div class="max-w-7xl mx-auto p-8">
    <h2 class="text-3xl font-bold text-center mb-12 text-gray-800"> 3 Ndeshjet më çmimë më të lartë</h2>
    <div class="bg-red-500 h-2 w-full mb-[30px]"></div>
    <a class="relative left-[1050px] bottom-[70px] text-blue-400 underline" href="reservematch.php">Shiko te gjita ndeshjet</a>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php
        include "db.php";

        $sql = "SELECT * FROM matches ORDER BY ticket_price DESC LIMIT 3";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $home_team = $row['home_team'];
                $away_team = $row['away_team'];
                $match_date = $row['match_date'];
                $stadium = $row['stadium'];
                $ticket_price = $row['ticket_price'];
                $available_tickets = $row['available_tickets'];
                $match_image = $row['match_image'];

                echo "
                <div class='bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300'>
                    <img src='$match_image' alt='$home_team vs $away_team' class='w-full h-56 object-cover'>
                    <div class='p-6'>
                        <h3 class='text-2xl font-bold text-gray-800 mb-2'>$home_team vs $away_team</h3>
                        <p class='text-gray-600 text-sm mb-4'>Data: " . date('d M Y, H:i', strtotime($match_date)) . "</p>
                        <p class='text-gray-500 text-sm mb-2'>Stadium: <span class='text-gray-800 font-medium'>$stadium</span></p>
                        <p class='text-gray-500 text-sm mb-2'>Çmimi i biletës: <span class='text-green-600 font-bold'>€$ticket_price</span></p>
                        <p class='text-gray-500 text-sm mb-4'>Biletat e mbetura: <span class='text-red-600 font-bold'>$available_tickets</span></p>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<p class='text-center text-red-500'>Nuk ka ndeshje të disponueshme për momentin.</p>";
        }

        $conn->close();
        ?>
    </div>
</div>
<div class="bg-white p-6 mx-auto mt-[70px] mb-10 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold text-center mb-4">SUPPORT</h2>

        <?php if (!empty($success_message)) : ?>
            <p class="text-green-600 text-center mb-4"><?= $success_message ?></p>
        <?php elseif (!empty($error_message)) : ?>
            <p class="text-red-600 text-center mb-4"><?= $error_message ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block font-medium">fullname</label>
                <input type="text" id="name" name="name" required class="w-full border p-2 rounded-tl-2xl">
            </div>
            <div>
                <label for="Phone" class="block font-medium">Phone</label>
                <input type="text" id="Phone" name="Phone" required class="w-full border p-2 rounded-tl-2xl">
            </div>
            <div>
                <label for="email" class="block font-medium">Email</label>
                <input type="email" id="email" name="email" required class="w-full border p-2 rounded-tl-2xl">
            </div>

            <div>
                <label for="Message" class="block font-medium">Message</label>
                <textarea id="Message" name="Message" rows="4" required class="w-full border p-2 rounded-tl-2xl"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Dërgo
            </button>
        </form>
    </div>

    <footer class="w-full text-center h-[40px] bg-gray-800 text-cyan-100 p-2">
        <p>&copy; Krijoi Milot Verbani 2025</p>
</footer>

<script>
    let currentSlide = 0;
    const slides = document.getElementById('slides');
    const totalSlides = document.querySelectorAll('#slides > div').length;

    function showSlide(index) {
        if (index >= totalSlides) {
            currentSlide = 0;
        } else if (index < 0) {
            currentSlide = totalSlides - 1;
        } else {
            currentSlide = index;
        }
        const offset = -currentSlide * 100;
        slides.style.transform = `translateX(${offset}%)`;
    }

    document.getElementById('next').addEventListener('click', () => {
        showSlide(currentSlide + 1);
    });

    document.getElementById('prev').addEventListener('click', () => {
        showSlide(currentSlide - 1);
    });

    setInterval(() => {
        showSlide(currentSlide + 1);
    }, 5000);
</script>

</body>
</html>
