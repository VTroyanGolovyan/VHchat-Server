<?php
     $query = 'UPDATE `users` SET online="'.date('U').'" WHERE id='.(int)$USERID;
     $mysqli->query($query);
     $query = 'SELECT * FROM `events` WHERE  seen=0 AND id2='.(int)$USERID;
     $rez = $mysqli->query($query);
     if (!$rez->num_rows == 0){
         $i = 0;
         while ($row = $rez->fetch_assoc()){
             $arr[$i] = new event($row['id1'],$row['id2'],$row['seen'],$row['sender_name'],$row['type'],$row['text'],$row['href']);
             $i++;
         }
         print json_encode($arr);
     }
     $query = 'UPDATE `events` SET seen=1 WHERE id2='.(int)$USERID.' AND seen != 1';
     $mysqli->query($query);
     $query = 'DELETE FROM `events`  WHERE id2='.(int)$USERID.' AND seen = 1';
     $mysqli->query($query);
?>
