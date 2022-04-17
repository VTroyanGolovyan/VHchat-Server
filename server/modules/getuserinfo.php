<?php
     class Answer {
       public $status = false;
       public $id = 0;
       public $name = '';
       public $avatar = '';
       public $second_name = '';
       public $contact = false;
       public $access = 0;
       public $userstatus = '';
       public $country = '';
       public $city = '';
     }
     $id = (int)$_GET['id'];
     $query = 'SELECT id,name,second_name,access,avatar,status,country,city FROM `users` WHERE id='.$id;
     $rez = $mysqli->query($query);
     $answer = new Answer();
     if (!$rez->num_rows == 0){
          if ($row = $rez->fetch_assoc()){
              $answer->status = true;
              $answer->id = $row['id'];
              $answer->name = $row['name'];
              $answer->avatar = $row['avatar'];
              $answer->access = $row['access'];
              $answer->second_name = $row['second_name'];
              $answer->userstatus = $row['status'];
              $answer->country = $row['country'];
              $answer->city = $row['city'];
              $query = 'SELECT * FROM `contacts` WHERE contactid='.$row['id'].' AND owner='.$USERID;
              $rez = $mysqli->query($query);
              if (!$rez->num_rows == 0){
                  $answer->contact = true;
              }else{
                  $answer->contact = false;
              }
          }
     }
     print json_encode($answer);
?>
