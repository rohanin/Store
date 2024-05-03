<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

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
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home">

   <div class="content">
      <h3>We provide various printing services</h3>
   </div>

</section>

<section class="products">

   <h1 class="title">latest products</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 4") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
   <form action="" method="post" class="box" onsubmit="return validateForm()">
      <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
      <div class="name"><?php echo $fetch_products['name']; ?></div>
      <div class="price">₱<?php echo $fetch_products['price']; ?></div>
      <div class="stock">Stocks: <?php echo $fetch_products['stock']; ?></div>
      <div class="options">
         <label>Size:</label>
         <?php
            $sizes = explode(",", $fetch_products['sizes']);
            foreach($sizes as $size){
               echo '<label style="padding:1rem 0; font-size: 2rem; color:var(--black);"><input type="radio" name="sizes[]" value="'.$size.'" style="transform: scale(2); margin: 1rem;">'.$size.'</label>';
            }
         ?>
      </div>
      <div class="options">
         <label>Color:</label>
         <?php
            $colors = explode(",", $fetch_products['colors']);
            foreach($colors as $color){
               echo '<label style="padding:1rem 0; font-size: 2rem; color:var(--black);"><input type="radio" name="colors[]" value="'.$color.'" style="transform: scale(2); margin: 1rem;">'.$color.'</label>';
            }
         ?>
      </div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_stock" value="<?php echo $fetch_products['stock']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
      <input type="hidden" name="size" value="<?php echo $size; ?>">
      <input type="hidden" name="color" value="<?php echo $color; ?>">
      <input type="submit" value="add to cart" name="add_to_cart" class="btn">
   </form>
      <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">load more</a>
   </div>

</section>

<section class="about">

   <div class="flex">

         <style>
         .video {
            width: 50%;
            max-width: 600px;
            margin: 0 auto;
         }

         video {
            width: 100%;
            height: auto;
         }
      </style>

      <div class="video">
         <video controls>
            <source src="images/introlps.mp4" type="video/mp4">
            Your browser does not support the video tag.
         </video>
      </div>

      <div class="content">
         <h3>about us</h3>
         <p>Linson Printing Services was established in May 2018 that caters Boxes/Packaging, Sticker Labels, Flyers/Brochures, Invitations, Souvenir Programs, Calling Cards, Receipts, and many more! As we were chosen as the BEST RELIABLE PRINTING SERVICES PROVIDER, we offer you the Best Quality Printing for the cheapest price!</p>
         <a href="about.php" class="btn">read more</a>
      </div>

   </div>

</section>

<section class="home-contact">

   <div class="content">
      <h3>have any questions?</h3>
      <p>For In-the-phone Inquiries, You can contact us in the site provided</p>
      <a href="contact.php" class="white-btn">contact us</a>
   </div>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
