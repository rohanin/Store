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

          if(mysqli_num_rows($check_cart_numbers) > 100){
             $message[] = 'already added to cart!';
          }else{
             mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image, sizes, colors) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image', '$size', '$color')") or die('query failed');
             $message[] = 'product added to cart!';
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
   <title>shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>our shop</h3>
   <p> <a href="home.php">home</a> / shop </p>
</div>

<section class="products">

   <h1 class="title">latest products</h1>

<div class="box-container">
   <?php  
      $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
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
      <input type="submit" value="add to cart" name="add_to_cart" class="btn">
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>
</div>


</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
function validateForm() {
    var sizes = document.getElementsByName('sizes[]');
    var colors = document.getElementsByName('colors[]');
    var sizeChecked = false;
    var colorChecked = false;

    for(var i = 0; i < sizes.length; i++) {
        if(sizes[i].checked) {
            sizeChecked = true;
            break;
        }
    }

    for(var j = 0; j < colors.length; j++) {
        if(colors[j].checked) {
            colorChecked = true;
            break;
        }
    }

    if(!sizeChecked || !colorChecked) {
        alert("Please select size and color!");
        return false;
    }

    return true;
}

function updateCartLabel(productName, size, color) {
    var label = document.querySelector('input[name="add_to_cart"]');
    label.value = productName + ', size: ' + size + ', color: ' + color;
}
</script>

</body>
</html>
