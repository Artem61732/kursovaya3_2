<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['save'])){

   $post_id = $_GET['id'];
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $content = $_POST['content'];
   $content = filter_var($content, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);

   $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE id = ?");
   $update_post->execute([$title, $content, $category, $status, $post_id]);

   $message[] = 'Публикация обновлена!';
   
   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/'.$image;

   $select_image = $conn->prepare("SELECT * FROM `posts` WHERE image = ? AND admin_id = ?");
   $select_image->execute([$image, $admin_id]);

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'Размер изображения слишком большой!';
      }elseif($select_image->rowCount() > 0 AND $image != ''){
         $message[] = 'Переименуйте изображение!';
      }else{
         $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
         move_uploaded_file($image_tmp_name, $image_folder);
         $update_image->execute([$image, $post_id]);
         if($old_image != $image AND $old_image != ''){
            unlink('../uploaded_img/'.$old_image);
         } 
         $message[] = 'Изображение обновлено!';
      }
   }

   
}

if(isset($_POST['delete_post'])){

   $post_id = $_POST['post_id'];
   $post_id = filter_var($post_id, FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $delete_post = $conn->prepare("DELETE FROM `posts` WHERE id = ?");
   $delete_post->execute([$post_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE post_id = ?");
   $delete_comments->execute([$post_id]);
   $message[] = 'Публикация успешно удалена!';

}

if(isset($_POST['delete_image'])){

   $empty_image = '';
   $post_id = $_POST['post_id'];
   $post_id = filter_var($post_id, FILTER_SANITIZE_STRING);
   $delete_image = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $delete_image->execute([$post_id]);
   $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);
   if($fetch_delete_image['image'] != ''){
      unlink('../uploaded_img/'.$fetch_delete_image['image']);
   }
   $unset_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE id = ?");
   $unset_image->execute([$empty_image, $post_id]);
   $message[] = 'Изображение успешно удалено!';

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Публикации</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="post-editor">

   <h1 class="heading">Изменить</h1>

   <?php
      $post_id = $_GET['id'];
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
      $select_posts->execute([$post_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_posts['image']; ?>">
      <input type="hidden" name="post_id" value="<?= $fetch_posts['id']; ?>">
      <p>Статус публикации <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= $fetch_posts['status']; ?>" selected><?= $fetch_posts['status']; ?></option>
         <option value="active">Активна</option>
         <option value="deactive">Закрыта</option>
      </select>
      <p>Название публикации <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="add post title" class="box" value="<?= $fetch_posts['title']; ?>">
      <p>Текст публикации <span>*</span></p>
      <textarea name="content" class="box" required maxlength="10000" placeholder="write your content..." cols="30" rows="10"><?= $fetch_posts['content']; ?></textarea>
      <p>Категория публикации <span>*</span></p>
      <select name="category" class="box" required>
         <option value="<?= $fetch_posts['category']; ?>" selected><?= $fetch_posts['category']; ?></option>
         <option value="painting">Живопись</option>
         <option value="glass">Стекло</option>
         <option value="sculpture">Скульптура</option>
      </select>
      <p>Изображение публикации</p>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <?php if($fetch_posts['image'] != ''){ ?>
         <img src="../uploaded_img/<?= $fetch_posts['image']; ?>" class="image" alt="">
         <input type="submit" value="delete image" class="inline-delete-btn" name="delete_image">
      <?php } ?>
      <div class="flex-btn">
         <input type="submit" value="Изменить" name="save" class="btn">
         <a href="view_posts.php" class="option-btn">Назад</a>
         <input type="submit" value="Удалить" class="delete-btn" name="delete_post">
      </div>
   </form>

   <?php
         }
      }else{
         echo '<p class="empty">Публикации не найдены!</p>';
   ?>
   <div class="flex-btn">
      <a href="view_posts.php" class="option-btn">Посмотреть публикации</a>
      <a href="add_posts.php" class="option-btn">Добавить публикацию</a>
   </div>
   <?php
      }
   ?>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>