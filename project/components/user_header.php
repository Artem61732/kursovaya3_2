<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">ArtФорум</a>

      <form action="search.php" method="POST" class="search-form">
         <input type="text" name="search_box" class="box" maxlength="100" placeholder="Найти публикации" required>
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <nav class="navbar">
         <a href="home.php"> <i class="fas fa-angle-right"></i> Главная</a>
         <a href="posts.php"> <i class="fas fa-angle-right"></i> Публикации</a>
         <a href="all_category.php"> <i class="fas fa-angle-right"></i> Категория</a>
         <a href="authors.php"> <i class="fas fa-angle-right"></i> Авторы</a>
         <a href="login.php"> <i class="fas fa-angle-right"></i> Вход</a>
         <a href="register.php"> <i class="fas fa-angle-right"></i> Регистрация</a>
      </nav>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p class="name"><?= $fetch_profile['name']; ?></p>
         <a href="update.php" class="btn">Изменить профиль</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Вход</a>
            <a href="register.php" class="option-btn">Регистрация</a>
         </div> 
         <a href="components/user_logout.php" onclick="return confirm('Выйти?');" class="delete-btn">Выйти</a>
         <?php
            }else{
         ?>
            <p class="name">Сначала выполните вход!</p>
            <a href="login.php" class="option-btn">Вход</a>
         <?php
            }
         ?>
      </div>

   </section>

</header>