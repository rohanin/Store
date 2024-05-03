<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['order_btn'])){

   // Process the order details
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $method = mysqli_real_escape_string($conn, $_POST['method']);
   $address = mysqli_real_escape_string($conn, $_POST['street'].', '. $_POST['city'].', '. $_POST['country'].' - '. $_POST['pin_code']);
   $note = mysqli_real_escape_string($conn, $_POST['customer_note']);
   $delivery = mysqli_real_escape_string($conn, $_POST['delivery_method']);

   
   $selected_size = isset($_POST['selected_size']) ? mysqli_real_escape_string($conn, $_POST['selected_size']) : '';
   $selected_color = isset($_POST['selected_color']) ? mysqli_real_escape_string($conn, $_POST['selected_color']) : '';
   
   $upload_dir = 'uploaded_file/';
   
   // Process each product in the cart
   $cart_total = 0;
   $cart_products[] = '';
   
   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
       while($cart_item = mysqli_fetch_assoc($cart_query)){
           // Process the uploaded file for each product
            $uploaded_file_key = 'upload_file_' . $cart_item['id'];
            if(isset($_FILES[$uploaded_file_key]) && $_FILES[$uploaded_file_key]['error'] === UPLOAD_ERR_OK){
               $uploaded_file = $upload_dir . basename($_FILES[$uploaded_file_key]['name']);
               $file_type = strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION));

               if($file_type == "docx" || $file_type == "pdf"){
                  if(move_uploaded_file($_FILES[$uploaded_file_key]['tmp_name'], $uploaded_file)){
                        $file_paths[$cart_item['id']] = $uploaded_file;
                        // Insert the file path into the database
                        mysqli_query($conn, "INSERT INTO `uploaded_files`(user_id, order_id, product_id, file_path) VALUES ('$user_id', LAST_INSERT_ID(), '{$cart_item['id']}', '$uploaded_file')") or die('query failed');
                  }else{
                        $message[] = 'Failed to upload file.';
                  }
               }else{
                  $message[] = 'Invalid file type. Please upload only .docx or .pdf files.';
               }
            }
   
           // Calculate the total price for each product
           $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
           $sub_total = ($cart_item['price'] * $cart_item['quantity']);
           $cart_total += $sub_total;

           // Reduce the stock of the product in the shop
           $product_id = $cart_item['product_id'];
           mysqli_query($conn, "UPDATE `products` SET stock = stock - {$cart_item['quantity']} WHERE id = $product_id") or die('query failed');
       }
   }
   
   $total_products = implode(', ',$cart_products);
   
   // Insert the order into the database
   $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND delivery_method = '$delivery' AND customer_note = '$note' AND address = '$address'  AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');
   
   if($cart_total == 0){
       $message[] = 'your cart is empty';
   }else{
       if(mysqli_num_rows($order_query) > 0){
           $message[] = 'order already placed!'; 
       }else{
           mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, delivery_method, customer_note, address, total_products, total_price) VALUES('$user_id', '$name', '$number', '$email', '$method', '$delivery', '$note', '$address' ,'$total_products', '$cart_total')") or die('query failed');
           $message[] = 'order placed successfully!';
           mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   
           // Insert the file paths into the database
           foreach ($file_paths as $product_id => $file_path) {
               mysqli_query($conn, "INSERT INTO `uploaded_files`(user_id, order_id, file_path) VALUES ('$user_id', LAST_INSERT_ID(), '$file_path')") or die('query failed');
           }
       }
   }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>checkout</h3>
   <p> <a href="home.php">home</a> / checkout </p>
</div>

<section class="display-order">

   <?php  
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_cart) > 0){
         while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
   ?>
   <p> <?php echo $fetch_cart['name']; ?> <span>(<?php echo '₱'.$fetch_cart['price'].''.' x '. $fetch_cart['quantity']; ?>)</span> </p>
   <?php
      }
   }else{
      echo '<p class="empty">your cart is empty</p>';
   }
   ?>
   <div class="grand-total"> Total : <span>₱<?php echo $grand_total; ?></span> </div>

</section>

<section class="checkout">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Place your order</h3>
      <h2>Please rename your file into your requested service. <span>[e.g Mats(Size;Color).pdf, Print(Size;Color).docx]</span></h2>
      <div class="flex">
         <div class="inputBox">
            <span>Full Name :</span>
            <input type="text" name="name" required placeholder="enter your name">
         </div>
         <div class="inputBox">
            <span>Contact Number :</span>
            <input type="number" name="number" required placeholder="enter your number">
         </div>
         <div class="inputBox">
            <span>Email :</span>
            <input type="email" name="email" required placeholder="enter your email">
         </div>
         <div class="inputBox">
         <span>Mode of Payment :</span>
         <select name="method" id="payment_method" onchange="toggleProofOfPayment()">
            <option value="Cash on Delivery">Cash On Delivery</option>
            <option value="Gcash">Gcash</option>
            <option value="Person-to-person">Person-to-person</option>
         </select>
         </div>
         <div id="proof_of_payment_section" class="inputBox" style="display: none;">
            <span>Upload Proof of Payment After Ordering:</span>
            <span style="margin-top: 0;">On the <a href="contact.php">Contact Page</a> with the Order ID</span>
         </div>
         <script>
         function toggleProofOfPayment() {
            var paymentMethod = document.getElementById("payment_method").value;
            var proofOfPaymentSection = document.getElementById("proof_of_payment_section");

            if (paymentMethod === "Gcash") {
               proofOfPaymentSection.style.display = "grid";
            } else {
               proofOfPaymentSection.style.display = "none";
            }
         }
         </script>
         <div class="inputBox">
            <span>Delivery Method :</span>
            <select name="delivery_method" id="delivery_method">
               <option value="Pick-up">Pick-up</option>
               <option value="Delivery">Delivery</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Street Name :</span>
            <input type="text" name="street" required placeholder="e.g. Malaya st.">
         </div>
         <div class="inputBox">
            <span>City :</span>
            <input type="text" name="city" required placeholder="e.g. Quezon City">
         </div>
         <div class="inputBox">
            <span>Province :</span>
            <input type="text" name="state" required placeholder="e.g. Metro Manila">
         </div>
         <div class="inputBox">
            <span>Country :</span>
            <input type="text" name="country" required placeholder="e.g. Philippines">
         </div>
         <div class="inputBox">
            <span>Pin Code :</span>
            <input type="number" min="0" name="pin_code" required placeholder="e.g. 123456">
         </div>
         <?php
            $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){
         ?>
            <div class="inputBox">
               <span>Upload Files for <?php echo $fetch_cart['name'] ?>; Size: <?php echo $fetch_cart['sizes']; ?>, Color: <?php echo $fetch_cart['colors']; ?></span>
               <input type="file" name="upload_file_<?php echo $fetch_cart['id']; ?>" accept=".docx, .pdf">
            </div>
         <?php
         }
         ?>
         <div class="inputBox">
            <span>Customer Note:</span>
            <input type="text" name="customer_note" placeholder="Customize Options and Concerns, kindly put the Details here.">
         </div>

      </div>
      
      <input type="submit" value="order now" class="btn" name="order_btn">
   </form>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
