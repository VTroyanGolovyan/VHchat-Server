<?php
    class User {
        public $id;
        public $name;
        public $second_name;
        public $avatar;
        public $access;
        public $status;
        function __construct($id, $name, $second_name, $avatar, $access,$online,$status){
          $this->id = $id;
          $this->name = $name;
          $this->second_name = $second_name;
          $this->avatar = $avatar;
          $this->access = $access;
          $this->online = $online;
          $this->status = $status;
        }
    }
    //получаем текст по которому будем искать и формируем запрос
    if ($_POST['text'] != ''){
        $text = $_POST['text'];
        $textarr = explode(' ',$text);
        $query = 'SELECT id,name,second_name,access,avatar,online,status FROM `users` WHERE name LIKE "%HHHHHHHfffsgsgrgHHHHHHLLLLLLLLL%" ';
        foreach ($textarr as $value){
            $query = $query.' or name LIKE "%'.$value.'%" ';
            $query = $query.' or second_name LIKE "%'.$value.'%" ';
        }
        $query = $query.' or id ='.(int)$text;
        $query = $query.' LIMIT 50';
        $rez = $mysqli->query($query);
        //в случае , если что-то нашли отправляем ответ
        if (! $rez->num_rows == 0){
             $i = 0;
             while ( $row = $rez->fetch_assoc() ){
                 if ((int)date('U') - (int)$row['online']<60){
                     $online = 1;
                 }else{
                     $online = 0;
                 }
                 $answer[$i] = new User($row['id'], $row['name'], $row['second_name'],
                                        $row['avatar'], $row['access'],$online,$row['status']);
                 $i++;
             }
             print json_encode($answer);
        }
    }
?>
