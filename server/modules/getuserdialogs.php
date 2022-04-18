<?php
    class Dialog{
        public $dialog_id;
        public $dialog_table;
        public $dialog_name;
        public $dialog_img;
        public $last_msg;
        public $unread;
        function __construct($dialogid, $dialog_table, $dialog_name, $dialog_img ,$last_msg ,$unread){
            $this->dialog_id = $dialogid;
            $this->dialog_table = $dialog_table;
            $this->dialog_name = $dialog_name;
            $this->dialog_img = $dialog_img;
            if (is_null($last_msg)) {
               $this->last_msg = '';
            } else {
               $this->last_msg = $last_msg;
            }
            $this->unread = $unread;
        }
    }
    //извлекаем диалоги , в которых присутствует пользователь
    $query = 'SELECT * FROM users_in_dialogs WHERE userid='.(int)$USERID;
    $rez = $mysqli->query($query);
    if(!$rez->num_rows == 0){
        //получаем информацию о диалогах
        $query = 'SELECT *, id as dialogid FROM `dialogs_info` WHERE ';
        $i = 1;
        $n = $rez->num_rows;
        while( $row = $rez->fetch_assoc() ){
            $unread[$row['dialog_table']] = $row['unread'];
            $query=$query.'dialog_table ="'.$row['dialog_table'].'"';
            if ( $i < $n ){
                $query=$query.' or ';
            }
            $i++;
        }
        $query = $query.' ORDER BY `last_msg_date` DESC';
        $rez = $mysqli->query($query);
        //отправляем информацию о диалогах
        if ( !$rez->num_rows == 0 ){
            $i = 0;
            while ( $row = $rez->fetch_assoc() ){
                $answer[$i] = new Dialog($row['dialogid'], $row['dialog_table'], $row['dialog_name'],
                                         $row['dialog_img'], $row['last_msg'],(int)$unread[$row['dialog_table']]);
                $i++;
            }
            print json_encode($answer);
        }
    }
?>
