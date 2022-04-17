<?php
    $user = (int)$_GET['id'];
    //удаляем контакт
    $query = 'DELETE FROM `contacts` WHERE contactid='.$user.' AND owner='.$USERID;
    $mysqli->query($query);
?>
