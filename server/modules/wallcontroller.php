<?php
      class wall_controller{
            public $user;
            public $mysqli;
            public $username;
            public $useravatar;
            function query($query){
                $rez = $this->mysqli->query($query);
                return $rez;
            }
            function __construct($mysqli,$USERID){
                $query = 'SELECT name,second_name,avatar FROM `users` WHERE id='.$USERID;
                $rez = $mysqli->query($query);
                $row = $rez->fetch_assoc();

                $this->username = $row['name'].' '.$row['second_name'];
                $this->useravatar = $row['avatar'];
                $this->user = $USERID;
                $this->mysqli = $mysqli;
            }
            function push($text,$attachments){
                $text = htmlspecialchars($text);
                $text = trim($text,' ');
                $text = str_replace(array("\r\n","\r","\n"),"<br>",$text);
                $text=trim($text,'<br>');
                if ( $text != '' || $attachments != ''){
                    $query = 'INSERT INTO `walls` (id,owner,text,attachments,datetime,avatar,username) VALUES (NULL,'.$this->user.',"'.$text.'","'.$attachments.'","'.date('Y-m-d H:i:s').'","'.$this->useravatar.'","'.$this->username.'")';
                    $this->query($query);
                }
            }
      }
      $controller = new wall_controller($mysqli,$USERID);
      if ($_GET['type'] == 'push'){
           $controller->push($_POST['text'],$mysqli->real_escape_string($_POST['attachments']));
      }
?>
