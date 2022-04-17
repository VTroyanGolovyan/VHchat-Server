<?php
     //удаляем данные о сессии 
     $hash = $_GET['hash'];
     $query = 'DELETE FROM `sessions` WHERE user_hash="'.$hash.'"';
     $mysqli->query($query);
?>
