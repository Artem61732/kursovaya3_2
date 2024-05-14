<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}
if (isset($_POST['delete_user'])) {
   $user_id = $_POST['user_id'];
   $user_id = filter_var($user_id, FILTER_SANITIZE_STRING);
   $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_user->execute([$user_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE user_id = ?");
   $delete_comments->execute([$user_id]);
   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ?");
   $delete_likes->execute([$user_id]);
   
   $message[] = 'Пользователь удален успешно!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Аккаунты пользователей</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="accounts">

   <h1 class="heading">Аккаунты пользователей</h1>

   <div class="box-container">

   <?php
      $select_account = $conn->prepare("SELECT * FROM `users`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){ 
            $user_id = $fetch_accounts['id']; 
            $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
            $count_user_comments->execute([$user_id]);
            $total_user_comments = $count_user_comments->rowCount();
            $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
            $count_user_likes->execute([$user_id]);
            $total_user_likes = $count_user_likes->rowCount();
   ?>
   <div class="box">
      <p> ID пользователя: <span><?= $user_id; ?></span> </p>
      <p> Имя пользователя: <span><?= $fetch_accounts['name']; ?></span> </p>
      <p> Эл. адрес: <span><?= $fetch_accounts['email']; ?></span> </p>
      <p> Всего комментариев: <span><?= $total_user_comments; ?></span> </p>
      <p> Всего лайков: <span><?= $total_user_likes; ?></span> </p>
      <div class="flex-btn">
         <?php
            if($fetch_accounts['id'] == $user_id){
         ?>
            <form method="post" class="delete-form">
                  <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                  <button type="submit" name="delete_user" class="delete-btn" onclick="return confirm('Удалить этого пользователя?');">Удалить</button>
            </form>
         <?php
            }
         ?>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Аккаунтов ещё нет</p>';
      }
   ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>