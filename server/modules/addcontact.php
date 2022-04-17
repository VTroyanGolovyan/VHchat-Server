<?php
   $user = (int)$_GET['id'];
   //проверяем добавлен ли в контакты пользователь
   $query = 'SELECT * FROM `contacts` WHERE contactid='.$user.' AND owner='.$USERID;
   $rez = $mysqli->query($query);
   if ($rez->num_rows == 0){
       //добавляем
       $query = 'INSERT INTO `contacts` (id,owner,contactid) VALUES (NULL,'.$USERID.','.$user.')';
       $mysqli->query($query);
   }
?>
