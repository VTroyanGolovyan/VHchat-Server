<?php
   //обьект хранящий ответ сервера
   class Answer {
     public $status = false;
     public $hash = '';
     public $id = 0;
     public $name_and_second_name = '';
     public $avatar = '';
     public $text = '';
   }
   //функция для генерации хеша
   function generateHash($length){
        //символы из которых генерируем
        $string='qwertyuiopadfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM';
        //генерация
        $hash='';
        for ($i = 0;$i<$length;$i++){
          $hash.=$string[mt_rand(0,strlen($string)-1)];
        }
        return $hash;
    }

    $answer = new Answer();
    if( isset($_POST['password']) && isset($_POST['gmail']) ){
           $gmail = $_POST['gmail'];
           $password = $_POST['password'];
           $query = 'SELECT * FROM `users` WHERE gmail="'.$gmail.'"';
           $rez = $mysqli->query($query);
           if($rez->num_rows != 0){
                $row = $rez->fetch_assoc();
                if ( md5( md5($password) ) == $row['password']){
                    $newhash = generateHash(mt_rand(35,40)).md5(Date.time());
                    $query = 'INSERT INTO `sessions` (id,user_id,user_hash,user_ip,user_agent,access) VALUES (NULL,"'.$row['id'].'","'.$newhash.'","'.$_SERVER['REMOTE_ADDR'].'","'.$_SERVER['HTTP_USER_AGENT'].'",'.(int)$row['access'].')';
                    $mysqli->query($query);
                    $answer->status = true;
                    $answer->hash = $newhash;
                    $answer->id = (int)$row['id'];
                    $answer->avatar = $row['avatar'];
                    $answer->name_and_second_name = $row['name'].' '.$row['second_name'];
                    $answer->text = 'topchik';
               }else{
                 $answer->status = false;
                 $answer->hash = '';
                 $answer->text = 'incorrect password';
               }
           }else{
             $answer->status = false;
             $answer->hash = '';
             $answer->text = 'Incorrect gmail';
           }
   }
   print json_encode($answer);
?>
