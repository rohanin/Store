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

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if($product_quantity <= $product_stock) {

      $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

      if(mysqli_num_rows($check_cart_numbers) > 0){
         $message[] = 'already added to cart!';
      }else{
         mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
         $message[] = 'product added to cart!';

         // Update the product stock in the database
         $new_stock = $product_stock - $product_quantity;
         mysqli_query($conn, "UPDATE `products` SET stock = '$new_stock' WHERE name = '$product_name'") or die('query failed');
      }
   } else {
      $message[] = 'Not enough stock available!';
   }

}

if(isset($_GET['id'])){
    $product_id = $_GET['id'];
    $select_product_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$product_id'") or die('query failed');
    if(mysqli_num_rows($select_product_query) > 0){
        $product_details = mysqli_fetch_assoc($select_product_query);
        $product_name = $product_details['name'];
        $product_price = $product_details['price'];
        $product_stock = $product_details['stock'];
        $product_image = $product_details['image'];

        // Retrieve and display product options (sizes and colors)
        $select_options_query = mysqli_query($conn, "SELECT * FROM `product_options` WHERE product_id = '$product_id'") or die('query failed');
        $options = array();
        while($option = mysqli_fetch_assoc($select_options_query)){
            $options[] = $option;
        }
    }else{
        echo 'Product not found!';
    }
}else{
    header('location:admin_products.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo $product_name; ?></title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
   <h3>product details</h3>
   <p> <a href="home.php">home</a> / product details </p>
</div>

<section class="product-details">
   <div class="product-image">
      <img src="uploaded_img/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>">
   </div>
   <div class="product-info">
      <h2><?php echo $product_name; ?></h2>
      <p>Price: ₱<?php echo $product_price; ?></p>
      <p>Stock: <?php echo $product_stock; ?></p>
      <form action="" method="post">
         <label for="quantity">Quantity:</label>
         <input type="number" id="quantity" name="product_quantity" value="1" min="1" max="<?php echo $product_stock; ?>">
         <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">
         <input type="hidden" name="product_price" value="<?php echo $product_price; ?>">
         <input type="hidden" name="product_stock" value="<?php echo $product_stock; ?>">
         <input type="hidden" name="product_image" value="<?php echo $product_image; ?>">
         <input type="submit" name="add_to_cart" value="Add to Cart" class="btn">
      </form>
   </div>
</section>

<section class="product-options">
   <h3>Options:</h3>
   <?php if(count($options) > 0): ?>
      <ul>
         <?php foreach($options as $option): ?>
            <li><?php echo $option['option_name']; ?></li>
         <?php endforeach; ?>
      </ul>
   <?php else: ?>
      <p>No options available for this product.</p>
   <?php endif; ?>
</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
