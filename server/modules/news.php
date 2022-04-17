<?php
   class wall{
     public $id;
     public $owner;
     public $text;
     public $attachments;
     public $datetime;
     public $avatar;
     public $username;
     function __construct($id, $owner, $text, $attachments, $datetime, $avatar, $username){
         $this->id = $id;
         $this->owner = $owner;
         $this->text = $text;
         $this->attachments= $attachments;
         $this->datetime = $datetime;
         $this->avatar = $avatar;
         $this->username = $username;
     }
   }
   $owner = (int)$USERID;
   $query = 'SELECT * FROM `contacts` WHERE owner='.$owner;
   $rez = $mysqli->query($query);
   if (! $rez->num_rows == 0){
     //генерируем запрос и получаем  информацию о контактах
       $query = 'SELECT * FROM `walls` WHERE (';
       $i = 1;
       $n = $rez->num_rows;
       while ( $row = $rez->fetch_assoc() ){
         $query=$query.'owner ='.$row['contactid'];
         if ($i < $n){
           $query=$query.' or ';
         }
         $i++;
       }
       $query = $query.' or owner='.(int)$USERID.' )';
       if (isset($_GET['max_id'])){
            $query = $query.' and id < '.$_GET['max_id'];
       }
       $query = $query.' ORDER BY `id` DESC LIMIT 15';
   }
   $rez = $mysqli->query($query);
   if (!$rez->num_rows == 0){
       $i = 0;
       while ($row = $rez->fetch_assoc()){
          $rows[$i] = $row;
          $users_id[$i] = (int)$row['owner'];
          $i++;
       }
       $n = $i;
       $i = 0;
       $avatars = get_avatars($users_id,$mysqli);
       while ($i < $n){
         $wall[$i] = new wall($rows[$i]['id'],$rows[$i]['owner'],$rows[$i]['text'],
                              $rows[$i]['attachments'],$rows[$i]['datetime'],
                              $mysqli->real_escape_string($avatars[(int)$rows[$i]['owner']])
                              ,$rows[$i]['username']);
         $i++;
       }
       print json_encode($wall);
   }
?>
