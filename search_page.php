<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_stock = $_POST['product_stock'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   if(isset($_POST['sizes']) && isset($_POST['colors'])) {
       $size = $_POST['sizes'][0]; // Only one size can be chosen
       $color = $_POST['colors'][0]; // Only one color can be chosen

       $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

       if($product_quantity <= $product_stock) {

          $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

          if(mysqli_num_rows($check_cart_numbers) > 0){
             $message[] = 'already added to cart!';
          }else{
             mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image, sizes, colors) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image', '$size', '$color')") or die('query failed');
             $message[] = 'product added to cart!';

             // Update the product stock in the database
             $new_stock = $product_stock - $product_quantity;
             mysqli_query($conn, "UPDATE `products` SET stock = '$new_stock' WHERE name = '$product_name'") or die('query failed');
          }
       } else {
          $message[] = 'Not enough stock available!';
       }
   } else {
       $message[] = 'Please select size and color!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>search page</h3>
   <p> <a href="home.php">home</a> / search </p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="search products..." class="box">
      <input type="submit" name="submit" value="search" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
   <?php
      if(isset($_POST['submit'])){
         $search_item = $_POST['search'];
         $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
         while($fetch_product = mysqli_fetch_assoc($select_products)){
   ?>
<form action="" method="post" class="box">
    <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
    <div class="name"><?php echo $fetch_product['name']; ?></div>
    <div class="price">₱<?php echo $fetch_product['price']; ?></div>
    <div class="stock">Stocks: <?php echo $fetch_product['stock']; ?></div>
    <div class="options">
         <label>Size:</label>
         <?php
            $sizes = explode(",", $fetch_product['sizes']);
            foreach($sizes as $size){
               echo '<label style="padding:1rem 0; font-size: 2rem; color:var(--black);"><input type="radio" name="sizes[]" value="'.$size.'" style="transform: scale(2); margin: 1rem;">'.$size.'</label>';
            }
         ?>
      </div>
      <div class="options">
         <label>Color:</label>
         <?php
            $colors = explode(",", $fetch_product['colors']);
            foreach($colors as $color){
               echo '<label style="padding:1rem 0; font-size: 2rem; color:var(--black);"><input type="radio" name="colors[]" value="'.$color.'" style="transform: scale(2); margin: 1rem;">'.$color.'</label>';
            }
         ?>
    <input type="number" class="qty" name="product_quantity" min="1" value="1">
    <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
    <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
    <input type="hidden" name="product_stock" value="<?php echo $fetch_product['stock']; ?>">
    <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
    <input type="hidden" name="product_size" value="<?php echo $fetch_product['sizes']; ?>">
    <input type="hidden" name="product_color" value="<?php echo $fetch_product['colors']; ?>">
    <input type="submit" class="btn" value="add to cart" name="add_to_cart">
</form>

   <?php
            }
         }else{
            echo '<p class="empty">no result found!</p>';
         }
      }else{
         echo '<p class="empty">search something!</p>';
      }
   ?>
   </div>
  

</section>









<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>