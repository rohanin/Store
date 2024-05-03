<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Forgot Password</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<div class="form-containerfor">

   <form action="send-password-reset.php" method="post">
      <h3>Forgot password</h3>
      <input type="email" name="email" placeholder="enter your email" required class="box">
      <input type="submit" name="submit" value="Send" class="btn">
   </form>

</div>

</body>
</html>