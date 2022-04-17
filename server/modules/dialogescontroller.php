<?php
   //класс для управления диалогом
    Class dialog_controller{
        public $mysqli;//соединение
        public $user; //пользователь
        public $table_name;//имя таблицы диалога
        function __construct($db,$userid){
           $this->mysqli = $db;
           $this->user = $userid;
        }
        //выполняет запрос к бд(для упрощения кода)
        function query($query){
          $rez = $this->mysqli->query($query);
          return $rez;
        }
        //метод, подключающий диалог
        function conect($table){
            $this->table_name = $table;
        }
        //метод, создающий диалог
        function create($users){
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
            //генерируем уникальное имя таблицы
            $newdialog = 'VH'.md5(time().date('d')).generateHash(8);
            //cоздаем таблицу для диалога
            $query =
              'CREATE TABLE IF NOT EXISTS `'.$newdialog.'` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `id1` int(11) NOT NULL,
              `sender` text COLLATE utf8_bin NOT NULL,
              `message` text COLLATE utf8_bin NOT NULL,
              `attachments` text COLLATE utf8_bin NOT NULL,
              `send_time` text COLLATE utf8_bin NOT NULL,
              PRIMARY KEY (`id`)
              ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
            $this->query($query);
            $this->table_name = $newdialog;
            //вытаскиваем имена пользователей для формирования имени диалога
            $query = 'SELECT id,name,second_name FROM `users` WHERE id='.$this->user.' or id='.$users[0];
            $rez = $this->query($query);
            //формируем имя диалога
            $dialog_name = '';
            $i = 0;
            while ($row = $rez->fetch_assoc()){
                if ($i == 0){
                  $dialog_name = $dialog_name.' '.$row['name'];
                }else{
                  $dialog_name = $dialog_name.','.$row['name'];
                }
                $i++;
            }
            //cохраняем информацию о диалоге
            $dialog_img = 'dialogs_imgs/defolt.jpg';  //ссылка на дефолтный аватар
            $dialog_table = $newdialog;
            $query = 'INSERT INTO `dialogs_info` (id,dialog_name,dialog_img,dialog_table) VALUES (NULL,"'.$dialog_name.'","'.$dialog_img.'","'.$dialog_table.'")';
            $this->query($query);
            //добавляем пользователя ,создавшего диалог с правами администратора
            $query = 'INSERT INTO `users_in_dialogs` (id,userid,user_access_in_dialog,dialog_table) VALUES (NULL,'.$this->user.',255,"'.$dialog_table.'")';
            $this->query($query);
            //добавляем остальных пользователей
            $this->add_users($users);
        }
        //функция проверяет наличие в диалоге пользователя
        function in_dialog($id){
            $query = 'SELECT * FROM `users_in_dialogs` WHERE userid='.$id.' AND dialog_table="'.$this->table_name.'"';
            $rez = $this->query($query);
            if ($rez->num_rows == 0){
                return false;
            }else{
                return true;
            }
        }
        //смена названия диалога
        function change_name($name){
             //Получаем новое имя диалога,вырезаем теги
             $name = trim(strip_tags($name),' ');
             //проверяем наличие пользователя в диалоге и корректность введенного имени
             if ( $this->in_dialog($this->user) && $name!='' && strlen($name) <=50){
                  $query = 'UPDATE `dialogs_info` SET `dialog_name` = "'.$name.'" WHERE `dialog_table` = "'.$this->table_name.'"';
                  $this->query($query);
             }
        }
        //метод, добавляющий пользователей в диалог из массива, записанного в json формате
        function add_users($users){
            //проверка доступа к диалогу
            if ($this->in_dialog($this->user)){
                foreach ($users as $value){
                    //проверяем, нет ли пользователя уже в диалоге
                    if (!$this->in_dialog($value)){
                        //добавляем
                        $dialog_table = $this->table_name;
                        $query = 'INSERT INTO `users_in_dialogs` (id,userid,user_access_in_dialog,dialog_table) VALUES (NULL,'.$value.',10,"'.$dialog_table.'")';
                        $this->query($query);
                    }
                }
            }
        }
        //метод, удаляющий пользователя из диалога
        function delete_user($id){
            //проверяем наличие пользователя в диалоге
            if ($this->in_dialog($this->user)){
                  $query = 'SELECT * FROM `users_in_dialogs` WHERE userid='.$this->user.' AND dialog_table="'.$this->table_name.'"';
                  $rez = $this->query($query);
                  $row = $rez->fetch_assoc();
                  //проверяем права в диалоге на удаление,также можно удалить себя
                  if ( $row['user_access_in_dialog'] >= 255 || $id == $this->user ){
                    //удаление
                    $query = 'DELETE FROM `users_in_dialogs` WHERE userid ='.$id.' and dialog_table="'.$this->table_name.'"';
                    $this->query($query);
                  }
            }
        }
        //метод, отправляющий сообщение
        function send_message($message, $attachments){
            //проверка полученых данных и доступа к диалогу
            if ( $this->in_dialog($this->user) && ($message != '' || $attachments!='')){
                //обновляем счетчик непрочитаных сообщений
                $query = 'UPDATE `users_in_dialogs` SET `unread` = `unread` + 1 WHERE dialog_table="'.$this->table_name.'" AND userid!='.$this->user;
                $this->query($query);
                //обновляем информацию о последнем сообщении и его время
                if ($message == ''){
                   $last_message = 'photo';
                }else{
                   $last_message = $message;
                }
                $last_message = str_replace(array("<br>")," ", $last_message);
                $query = 'UPDATE `dialogs_info` SET last_msg="'.$last_message.'" , last_msg_date="'.date('Y-m-d H:i:s').'" WHERE dialog_table="'.$this->table_name.'"';
                $this->query($query);
                //извлекаем информацию отправителя
                $query = 'SELECT name,second_name,avatar FROM `users` WHERE id='.$this->user;
                $rez = $this->query($query);
                $row = $rez->fetch_assoc();
                //наконец-то отправляем сообщение
                $query = 'INSERT INTO `'.$this->table_name.'` (id,id1,sender,message,attachments,send_time) VALUES (NULL,'.$this->user.',"'.$row['name'].' '.$row['second_name'].'","'.$message.'","'.$attachments.'","'.date('H-i').'")';
                $this->query($query);
                return true;
            }else{
                return false;
            }
        }
    }
    //создаем контроллер
    $dialog = new dialog_controller($mysqli,$USERID);
    //получаем тип действия
    if ( $_GET['type'] == 'create' ){
        //создание
        $users = json_decode($_POST['users']);
        $dialog->create($users);
    }elseif ( $_GET['type'] == 'send' ){
        //отправка сообщения
        $msg = htmlspecialchars($_POST['message']);
        $msg = trim($msg,' ');
        $msg = str_replace(array("\r\n","\r","\n"),"<br>",$msg);
        $msg=trim($msg,'<br>');
        $msg = str_replace(array("<br><br>"),"<br>",$msg);
        if ( $msg != '' || (isset($_POST['attachments']) && $_POST['attachments']!='') ){
          $dialog->conect($_GET['dialog']);
          if (isset($_POST['attachments'])){
              $attachments =$mysqli->real_escape_string($_POST['attachments']);
          }else{
              $attachments = '';
          }
          $f = $dialog->send_message($msg,$attachments);
          if($f){
            $query = 'SELECT * FROM `users_in_dialogs` WHERE dialog_table="'.$dialog->table_name.'"';
            $rez = $mysqli->query($query);
            if (!$rez->num_rows == 0){
               $i = 0;
               while ($row = $rez->fetch_assoc()){
                   if ($row['userid']!=$USERID){
                        $push[$i] = new event($USERID,$row['userid'],0,'vh','msg',$msg,$dialog->table_name);
                   }
                   $i++;
               }
               push($push,$mysqli);
            }
          }
       }
    }elseif ($_GET['type'] == 'delete_user'){
        //удаление
        $dialog->conect($_GET['dialog']);
        $dialog->delete_user((int)$_GET['userid']);
    }elseif ($_GET['type'] == 'add_users'){
        //добавление пользователя
        $users = json_decode($_GET['users']);
        $dialog->conect($_GET['dialog']);
        $dialog->add_users($users);
    }elseif ($_GET['type'] == 'change_name'){
        //изменение имени диалога
        $dialog->conect($_GET['dialog']);
        $dialog->change_name($_POST['name']);
    }elseif ($_GET['type'] == 'typing') {
        $dialog->conect($_GET['dialog']);
        if ($dialog->in_dialog($dialog->user)){
          $query = 'SELECT * FROM `users_in_dialogs` WHERE dialog_table="'.$dialog->table_name.'"';
          $rez = $mysqli->query($query);
          if (!$rez->num_rows == 0){
             $i = 0;
             while ($row = $rez->fetch_assoc()){
                 if ($row['userid']!=$USERID){
                      $push[$i] = new event($USERID,$row['userid'],0,'vh','typing',strip_tags($_POST['name']),$dialog->table_name);
                 }
                 $i++;
             }
             push($push,$mysqli);
          }
        }
    } elseif ($_GET['type'] == 'update_dialog_image'){
      //изменение картинки диалога
      $dialog = $_GET['dialog'];
      class Answer{
          public $status;
          public $height;
          public $width;
          public $src;
          function __construct($status,$height,$width,$src){
              $this->status = $status;
              $this->width = $width;
              $this->height = $height;
              $this->src = $src;
          }
      }
      $query = 'SELECT * FROM `users_in_dialogs` WHERE userid='.$USERID.' AND dialog_table="'.$dialog.'"';
      $rez = $mysqli->query($query);
      if (!$rez->num_rows == 0){
        if (! empty($_FILES['image']['name'])){
          if($_FILES['image']['error'] == 0){
            if(substr($_FILES['image']['type'],0,5)=='image'){
              $image_info = getimagesize($_FILES['image']['tmp_name']);
              $height = $image_info[1];
              $width = $image_info[0];
              $query = 'SELECT * FROM `dialogs_info` WHERE dialog_table="'.$dialog.'"';
              $rez = $mysqli->query($query);
              $row = $rez->fetch_assoc();
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
              $filename = md5(time()).$dialog.generateHash(10);
              move_uploaded_file($_FILES['image']['tmp_name'],'dialogs_imgs/'.$filename.'.jpg');
              if ($row['dialog_img'] != 'dialogs_imgs/defolt.jpg')
                 unlink($row['dialog_img']);
              $query = 'UPDATE `dialogs_info` SET `dialog_img`="'.'dialogs_imgs/'.$filename.'.jpg'.'" WHERE `dialog_table` = "'.$dialog.'"';
              $mysqli->query($query);
              $answer = new Answer(true,$height,$width,'dialogs_imgs/'.$filename.'.jpg');
            }else{
              $answer = new Answer(false,0,0,'');
            }
          }else{
            $answer = new Answer(false,0,0,'');
          }
        }else{
          $answer = new Answer(false,0,0,'');
        }
      print json_encode($answer);
    }
   }
?>
