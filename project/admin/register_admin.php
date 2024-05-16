<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, ENT_QUOTES, 'UTF-8');
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, ENT_QUOTES, 'UTF-8');
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, ENT_QUOTES, 'UTF-8');

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
   $select_admin->execute([$name]);
   
   if($select_admin->rowCount() > 0){
      $message[] = 'Имя пользователя уже существует!';
   }else{
      if($pass != $cpass){
         $message[] = 'Пароль не совпадает!';
      }else{
         $insert_admin = $conn->prepare("INSERT INTO `admin`(name, password) VALUES(?,?)");
         $insert_admin->execute([$name, $cpass]);
         $message[] = 'Новый админ зарегестрирован!';
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
   <title>Регистрация</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="form-container">

   <form action="" method="POST">
      <h3>Зарегистрировать</h3>
      <input type="text" name="name" maxlength="20" required placeholder="Введите имя пользователя" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" maxlength="20" required placeholder="Введите пароль" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" maxlength="20" required placeholder="Подтвердите пароль" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Добавить" name="submit" class="btn">
   </form>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>