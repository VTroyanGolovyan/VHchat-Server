<?php
      class User {
        public $id;
        public $name;
        public $second_name;
        public $avatar;
        public $access;
        public $online;
        public $status;
        function __construct($id, $name, $second_name, $avatar, $access, $online, $status){
           $this->id = $id;
           $this->name = $name;
           $this->second_name = $second_name;
           $this->avatar = $avatar;
           $this->access = $access;
           $this->online = $online;
           $this->status = $status;
        }
      }
      //получаем контакты пользователя
      $owner = (int)$USERID;
      $query = 'SELECT * FROM `contacts` WHERE owner='.$owner;
      $rez = $mysqli->query($query);
      if (! $rez->num_rows == 0){
        //генерируем запрос и получаем  информацию о контактах
          $query = 'SELECT id,name,second_name,access,avatar,online,status FROM `users` WHERE ';
          $i = 1;
          $n = $rez->num_rows;
          while ( $row = $rez->fetch_assoc() ){
            $query=$query.'id ='.$row['contactid'];
            if ($i < $n){
              $query=$query.' or ';
            }
            $i++;
          }
          $contactsrezult = $mysqli->query($query);
          //формируем и отправляем ответ
          if ( !$contactsrezult->num_rows == 0 ){
            $i = 0;
            while ($row = $contactsrezult->fetch_assoc()){
              if ((int)date('U') - (int)$row['online']<60){
                  $online = 1;
              }else{
                  $online = 0;
              }
              $answer[$i] = new User($row['id'],$row['name'],$row['second_name'],$row['avatar'],$row['access'],$online,$row['status']);
              $i++;
            }
            print json_encode($answer);
          }
      }

?>
