<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['send'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $number = $_POST['number'];
   $msg = mysqli_real_escape_string($conn, $_POST['message']);

   // File upload
   $file_name = $_FILES['image']['name'];
   $file_tmp = $_FILES['image']['tmp_name'];
   $file_type = $_FILES['image']['type'];

      $file_name_parts = explode('.', $file_name);
      $file_ext = strtolower(end($file_name_parts));

   if (empty($errors) === true) {
      move_uploaded_file($file_tmp, "uploads/" . $file_name);
   }

   $select_message = mysqli_query($conn, "SELECT * FROM `message` WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('query failed');

   if(mysqli_num_rows($select_message) > 0){
      $message[] = 'message sent already!';
   }else{
      mysqli_query($conn, "INSERT INTO `message`(user_id, name, email, number, message, image) VALUES('$user_id', '$name', '$email', '$number', '$msg', '$file_name')") or die('query failed');
      $message[] = 'message sent successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>contact us</h3>
   <p> <a href="home.php">home</a> / contact </p>
</div>

<section class="contact">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>say something!</h3>
      <input type="text" name="name" required placeholder="enter your name" class="box">
      <input type="email" name="email" required placeholder="enter your email" class="box">
      <input type="number" name="number" required placeholder="enter your number" class="box">
      <textarea name="message" class="box" placeholder="enter your message" id="" cols="30" rows="10"></textarea>
      <p>Upload the image here: <input type="file" name="image" class="box"></p>
      <input type="submit" value="send message" name="send" class="btn">
      <p>For Proof of Payment</p>
      <p>+63 9562347729 C***N L****A</p>
   </form>

</section>








<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
