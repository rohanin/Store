<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>your orders</h3>
   <p> <a href="home.php">home</a> / orders </p>
</div>

<section class="placed-orders">

   <h1 class="title">placed orders</h1>

   <h2 class="note">want to cancel your order? <a href="contact.php"> contact us!</a></h2>

   <div class="box-container">

      <?php
         $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('query failed');
         if(mysqli_num_rows($order_query) > 0){
            while($fetch_orders = mysqli_fetch_assoc($order_query)){
      ?>
      <div class="box">
         <p> Order ID: <span><?php echo $fetch_orders['id']; ?></span> </p>
         <p> Full Name : <span><?php echo $fetch_orders['name']; ?></span> </p>
         <p> Contact Number : <span><?php echo $fetch_orders['number']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
         <p> Address : <span><?php echo $fetch_orders['address']; ?></span> </p>
         <p> Payment Method : <span><?php echo $fetch_orders['method']; ?></span> </p>
         <p> Your Orders : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
         <p> Total Price : <span>₱<?php echo $fetch_orders['total_price']; ?></span> </p>
         <p> Payment Status : <span style="color:<?php if($fetch_orders['payment_status'] == 'Pending'){ echo 'red'; } else{ echo 'green'; } ?>;"><?php echo $fetch_orders['payment_status']; ?></span> </p>
         <p> Service Status : <span style="color:<?php if($fetch_orders['service_status'] == 'Pending'){ echo 'red'; } else if($fetch_orders['service_status'] == 'Work in Progress'){ echo 'orange'; } else{ echo 'green'; } ?>;"><?php echo $fetch_orders['service_status']; ?></span> </p>
         <p> Product Deliver Estimation : <span><?php echo $fetch_orders['delivery_estimation']; ?></span> </p>

         <!-- Display uploaded files -->
         <p> Files uploaded:</p>
         <ul>
            <?php
               $uploaded_file_query = mysqli_query($conn, "SELECT * FROM `uploaded_files` WHERE order_id = '{$fetch_orders['id']}'");
               while ($uploaded_file_row = mysqli_fetch_assoc($uploaded_file_query)) {
            ?>
            <li><a href="<?php echo $uploaded_file_row['file_path']; ?>" download><?php echo basename($uploaded_file_row['file_path']); ?></a></li>
            <?php
               }
            ?>
         </ul>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
