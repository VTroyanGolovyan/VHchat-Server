<?php
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
if (isset($_GET['max_id'])){
    $max_id = (int)$_GET['max_id'];
}else{
    $max_id = 0;
}
if (isset($_GET['min_id'])){
    $min_id = (int)$_GET['min_id'];
}else{
    $min_id = 0;
}
$dialog_table = $_GET['dialog'];
$query = 'SELECT * FROM `users_in_dialogs` WHERE userid='.$USERID.' AND dialog_table="'.$dialog_table.'"';
$rez = $mysqli->query($query);
if (!$rez->num_rows == 0){
  $query = 'UPDATE `users_in_dialogs` SET `unread` = 0 WHERE dialog_table="'.$dialog_table.'" AND userid='.$USERID;
  $mysqli->query($query);

  //получаем содержимое диалога и формируем массив сообщений

        $query = 'SELECT * FROM `'.$dialog_table.'` WHERE id>'.$min_id;
        if ($max_id > 0){
            $query = $query.' AND id<='.$max_id;
        }
        $query = $query.' LIMIT 50';
        $rez = $mysqli->query($query);
        if (!$rez->num_rows == 0){
            $i = 0;
            while ($row = $rez->fetch_assoc()){
                $messages[$i] = $row;
                $users_id[$i] = (int)$row['id1'];
                $i++;
            }
        } else {
            $messages = '';
        }
        $n = $i;
        $avatars = get_avatars($users_id,$mysqli);

        $i = 0;
        while ($i < $n){
            $msgarr[$i] = new message($messages[$i]['id'] ,$messages[$i]['id1']
                                      ,$messages[$i]['sender'] ,$messages[$i]['message']
                                      , $messages[$i]['attachments'],$messages[$i]['send_time']
                                      ,$mysqli->real_escape_string($avatars[(int)$messages[$i]['id1']]));
            $i++;
        }


 //формируем и отправляем ответ

        print json_encode($msgarr);
   }

?>
