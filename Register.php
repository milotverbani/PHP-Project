<?php
include "db.php";

$error_message = ''; 

if (isset($_POST['Registersubmit'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];

   
    if ($password !== $confirm_password) {
        $error_message = "Fjalëkalimi dhe konfirmimi i fjalëkalimit nuk përputhen!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error_message = "emaili nuk është në rregull";
    }
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
      $error_message = "Ju lutem mbushni të gjitha fushat!";
  }
   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   
    if (empty($error_message)) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $error_message = "Ky email është regjistruar tashmë. Ju lutem përdorni një email tjetër.";
        }
    }


    if (empty($error_message)) {
        $sql = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            header('Location:Login.php');
            echo "<div class='bg-green-100 text-green-800 p-4 rounded-lg mb-4'>Regjistrimi u krye me sukses!</div>";
        } else {
            $error_message = "Gabim gjatë regjistrimit: " . $conn->error;
        }
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="output.css">
    <title>Document</title>
</head>
<body>
<div class="h-screen bg-gradient-to-br from-blue-600 to-indigo-600 flex justify-center items-center w-full">
  <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <div class="bg-white px-10 py-8 rounded-xl w-screen shadow-md max-w-sm">
      <div class="space-y-4">
        <h1 class="text-center text-2xl font-semibold text-gray-600">Register</h1>
        
        <?php if ($error_message): ?>
          <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <strong>Gabim:</strong> <?php echo $error_message; ?>
          </div>
        <?php endif; ?>

        <div>
          <label for="fullname" class="block mb-1 text-gray-600 font-semibold">Fullname</label>
          <input type="text" name="fullname" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" value="<?php echo isset($_POST['fullname']) ? $_POST['fullname'] : ''; ?>" />
        </div>
        <div>
          <label for="email" class="block mb-1 text-gray-600 font-semibold">Email</label>
          <input type="text" name="email" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
        </div>
        <div>
          <label for="password" class="block mb-1 text-gray-600 font-semibold">Password</label>
          <input type="password" name="password" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" />
        </div>
        <div>
          <label for="confirmpassword" class="block mb-1 text-gray-600 font-semibold">Confirm Password</label>
          <input type="password" name="confirmpassword" class="bg-indigo-50 px-4 py-2 outline-none rounded-md w-full" />
        </div>
      </div>
      <button name="Registersubmit" class="mt-4 w-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-indigo-100 py-2 rounded-md text-lg tracking-wide">Register</button>
    </div>
  </form>
</div>
</body>
</html>
