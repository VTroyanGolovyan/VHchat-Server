<?php
    //структура обьекта сообщения
     Class message{
         public $id;
         public $senderid;
         public $sender;
         public $message;
         public $attachments;
         public $send_time;
         public $avatar;
         function __construct($id,$senderid,$sender,$message,$attachments,$send_time,$avatar){
             $this->id = $id;
             $this->senderid = $senderid;
             $this->sender = $sender;
             $this->message = $message;
             $this->attachments = $attachments;
             $this->send_time = $send_time;
             $this->avatar = $avatar;
         }
     }
     //структура обьекта диалога
     Class dialog{
         public $messages;
         public $table_name;
         public $dialog_name;
         public $dialog_img;
         public $myaccess;
         function __construct($messages,$table_name,$dialog_name,$dialog_img,$myaccess){
            $this->messages = $messages;
            $this->table_name = $table_name;
            $this->dialog_name = $dialog_name;
            $this->dialog_img = $dialog_img;
            $this->myaccess = $myaccess;
         }
     }
     //получаем какой диалог мы хотим видеть и проверяем есть ли у пользователя к нему доступ
     $dialog_table = $_GET['dialog'];
     $query = 'SELECT * FROM `users_in_dialogs` WHERE userid='.$USERID.' AND dialog_table="'.$dialog_table.'"';
     $rez = $mysqli->query($query);
     if (!$rez->num_rows == 0){
       $row = $rez->fetch_assoc();
       $myaccess = $row['user_access_in_dialog'];
       $query = 'UPDATE `users_in_dialogs` SET `unread` = 0 WHERE dialog_table="'.$dialog_table.'" AND userid='.(int)$USERID;
       $mysqli->query($query);
        //получаем информацию о диалоге
        $query = 'SELECT * FROM `dialogs_info` WHERE dialog_table="'.$dialog_table.'"';
        $rez = $mysqli->query($query);
        if (!$rez->num_rows == 0){

             $row = $rez->fetch_assoc();
             $table_name = $row['dialog_table'];
             $dialog_name = $row['dialog_name'];
             $dialog_img = $row['dialog_img'];

       //получаем содержимое диалога и формируем массив сообщений
             $query = 'SELECT * FROM `'.$dialog_table.'` ORDER BY `id` DESC LIMIT 60';
             $rez = $mysqli->query($query);
             $n = $rez->num_rows;
             if (!$rez->num_rows == 0){
                 $n = 0;
                 while ($row = $rez->fetch_assoc()){
                     $rows[$n] = $row;
                     $users_id[$n] = (int)$row['id1'];
                     $n++;
                 }
                 $i = 0;
                 $n--;
                 $avatars = get_avatars($users_id,$mysqli);
                 while ($n >= 0){
                     $messages[$i] = new message($rows[$n]['id'] ,$rows[$n]['id1'] ,$rows[$n]['sender'] ,$rows[$n]['message'] ,
                                                 $rows[$n]['attachments'],$rows[$n]['send_time'],
                                                 $mysqli->real_escape_string($avatars[(int)$rows[$n]['id1']]));
                     $i++;
                     $n--;
                 }
             } else {
                 $messages = '';
             }
      //формируем и отправляем ответ
             $answer = new dialog($messages,$table_name,$dialog_name,$dialog_img,$myaccess);
             print json_encode($answer);
        }
     }
?>
