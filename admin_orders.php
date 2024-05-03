<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

if(isset($_POST['update_order'])) {
    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];
    $delivery_estimation = $_POST['delivery_estimation'];
    $update_service = $_POST['update_service'];

    mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment', service_status = '$update_service', delivery_estimation = '$delivery_estimation' WHERE id = '$order_update_id'") or die('query failed');
    $message[] = 'Payment and Service status and Delivery Estimation have been Updated!';
}


if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Delete associated uploaded files
    $uploaded_file_query = mysqli_query($conn, "SELECT * FROM `uploaded_files` WHERE order_id = '$delete_id'");
    while ($uploaded_file_row = mysqli_fetch_assoc($uploaded_file_query)) {
        unlink($uploaded_file_row['file_path']);
    }

    // Delete order from orders table
    mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_orders.php');
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

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

    <?php include 'admin_header.php'; ?>

    <section class="orders">

        <h1 class="title">placed orders</h1>

        <?php
            $select_orders = mysqli_query($conn, "SELECT *, o.customer_note FROM `orders` o") or die('query failed');
            if (mysqli_num_rows($select_orders) > 0) {
                $prev_user_id = null;
                while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
                    if ($prev_user_id != $fetch_orders['user_id']) {
                        if ($prev_user_id !== null) {
                            echo '</div>';
            }
            echo '<div class="box-container">';
        }
        ?>

            <div class="box" style="background-color:<?php if($fetch_orders['service_status'] == 'Pending'){ echo '#ffd6d6'; } else if($fetch_orders['service_status'] == 'Work in Progress'){ echo '#ffe7bb'; } else if($fetch_orders['service_status'] == 'Completed'){ echo '#b5ffc8'; } else{ echo '#ddd'; } ?>;"><?php echo $fetch_orders['service_status']; ?>
            <p> Order ID : <span><?php echo $fetch_orders['id']; ?></span> </p>
            <p> User ID : <span><?php echo $fetch_orders['user_id']; ?></span> </p>
            <p> Name : <span><?php echo $fetch_orders['name']; ?></span> </p>
            <p> Contact Number : <span><?php echo $fetch_orders['number']; ?></span> </p>
            <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
            <p> Address : <span><?php echo $fetch_orders['address']; ?></span> </p>
            <p> Total Products : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
            <p> Total Price : <span>₱<?php echo $fetch_orders['total_price']; ?></span> </p>
            <p> Payment Method : <span><?php echo $fetch_orders['method']; ?></span> </p>
            <p> Delivery Method : <span><?php echo $fetch_orders['delivery_method']; ?></span> </p>
            <p> Customer Note: <span><?php echo $fetch_orders['customer_note']; ?></span> </p>
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
            <p>Products:</p>
 
            <form action="" method="post">

            <p> Payment Status </p>
            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
            <select name="update_payment">
                <option value=""><?php echo $fetch_orders['payment_status']; ?></option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
            </select>

            <p> Service Status </p>
            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
            <select name="update_service">
                <option value=""><?php echo $fetch_orders['service_status']; ?></option>
                <option value="Pending">Pending</option>
                <option value="Work in Progress">Work in Progress</option>
                <option value="Completed">Completed</option>
            </select>

            <p> Deliver Estimation </p>
            <input type="date" name="delivery_estimation" placeholder="Delivery Estimation" value="<?php echo $fetch_orders['delivery_estimation']; ?>">

            <input type="submit" value="update" name="update_order" class="option-btn">
            <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" onclick="return confirm('delete this order?');" class="delete-btn">delete</a>

        </form>

</div>
        
        <?php
        $prev_user_id = $fetch_orders['user_id'];
    }
    echo '</div>';
} else {
    echo '<p class="empty">no orders placed yet!</p>';
}
?>

    </section>

    <!-- custom admin js file link  -->
    <script src="js/admin_script.js"></script>

</body>

</html>
