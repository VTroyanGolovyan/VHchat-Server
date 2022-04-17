<?php
    class User {
        public $id;
        public $name;
        public $second_name;
        public $avatar;
        public $access;
        public $online;
        function __construct($id, $name, $second_name, $avatar, $access, $online){
            $this->id = $id;
            $this->name = $name;
            $this->second_name = $second_name;
            $this->avatar = $avatar;
            $this->access = $access;
            $this->online = $online;
        }
    }
    $dialog = $_GET['dialog'];
    $query = 'SELECT * FROM `users_in_dialogs` WHERE userid='.$USERID.' AND dialog_table="'.$dialog.'"';
    $rez = $mysqli->query($query);
    if ( !$rez->num_rows == 0){
          $query = 'SELECT * FROM `users_in_dialogs` WHERE dialog_table="'.$dialog.'"';
          $rez = $mysqli->query($query);
          $query = 'SELECT id,name,second_name,access,avatar,online FROM `users` WHERE ';
          $i = 1;
          $n = $rez->num_rows;
          while ( $row = $rez->fetch_assoc() ){
            $query=$query.'id ='.$row['userid'];
            if ($i < $n){
              $query=$query.' or ';
            }
            $i++;
          }
          $rez = $mysqli->query($query);
          if ( !$rez->num_rows == 0 ){
            $i = 0;
            while ($row = $rez->fetch_assoc()){
              if ((int)date('U') - (int)$row['online']<60){
                  $online = 1;
              }else{
                  $online = 0;
              }
              $answer[$i] = new User($row['id'],$row['name'],$row['second_name'],$row['avatar'],$row['access'],$online);
              $i++;
            }
            print json_encode($answer);
          }
    }
?>
