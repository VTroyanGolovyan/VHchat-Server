<?php
    /* VHchat
       http://vh.biz.ua     */
       //ini_set('error_reporting', E_ALL);
       //ini_set('display_errors', 1);
       //ini_set('display_startup_errors', 1);

    header("Access-Control-Allow-Origin: *");
    header('Content-type: text/html; charset=UTF-8');
      //подключение базы данных
    include('db/db.php');
    $LIST['login'] = 'login.php';
    $LIST['reg'] = 'reg.php';
    $LIST['location'] = 'location.php';
      //получаем хеш если он существует и проверяем на подлинность
    if (isset($_GET['hash'])){
        $hash = $_GET['hash'];
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $query = 'SELECT * FROM `sessions` WHERE user_hash="'.$hash.'" LIMIT 1';
        $rez = $mysqli->query($query);
        if ($rez->num_rows!=0){
            $row = $rez->fetch_assoc();
            if ($row['user_hash'] == $hash){
                //формирование списка допустимых модулей для авторизированого пользователя
                $SESSIONID = $row['id'];
                $USERID = $row['user_id'];
                $LIST['logout'] = 'logout.php';
                $LIST['find'] = 'find.php';
                $LIST['contacts'] = 'contacts.php';
                $LIST['getuserinfo'] = 'getuserinfo.php';
                $LIST['addcontact'] = 'addcontact.php';
                $LIST['deletecontact'] = 'deletecontact.php';
                $LIST['dialogescontroller'] = 'dialogescontroller.php';
                $LIST['getuserdialogs'] = 'getuserdialogs.php';
                $LIST['getdialog'] = 'getdialog.php';
                $LIST['update_avatar'] = 'update_avatar.php';
                $LIST['messages'] = 'messages.php';
                $LIST['getusersfromdialog'] = 'getusersfromdialog.php';
                $LIST['sessionscontroller'] = 'sessionscontroller.php';
                $LIST['wall'] = 'wall.php';
                $LIST['wallcontroller'] = 'wallcontroller.php';
                $LIST['news'] = 'news.php';
                $LIST['chek'] = 'chek.php';
                $LIST['events'] = 'events.php';
                $LIST['profilecontroller'] = 'profilecontroller.php';
                $LIST['photocontroller'] = 'photocontroller.php';
            }
        }
    }
    class event{
        public $id1;
        public $id2;
        public $seen;
        public $sender_name;
        public $type;
        public $text;
        public $href;
        function __construct($id1,$id2,$seen,$sender_name,$type,$text,$href){
             $this->id1 = $id1;
             $this->id2 = $id2;
             $this->seen = $seen;
             $this->sender_name = $sender_name;
             $this->type = $type;
             $this->text = $text;
             $this->href = $href;
        }
    }
    function push($arr,$mysqli){
        $query = 'INSERT INTO `events` (id,id1,id2,seen,sender_name,type,text,href) VALUES ';
        foreach ($arr as $value){
             $query = $query.'(NULL,'.$value->id1.','.$value->id2.',0,"'.$value->sender_name.'","'.$value->type.'","'.$value->text.'","'.$value->href.'"),';
        }
        $query = substr($query,0,-1);
        $mysqli->query($query);
    }
    function get_avatars($array,$mysqli){
        $query = 'SELECT id,avatar FROM `users` WHERE id = 0 ';
        foreach ($array as $value){
          $query = $query.' or id = '.$value;
        }
        $rez = $mysqli->query($query);
        if (!$rez->num_rows == 0){
          while ($row = $rez->fetch_assoc()){
            $arr[(int)$row['id']] = $row['avatar'];
          }
        }
        return $arr;
    }
     //подсоединяем нужный модуль если у пользователя есть к нему доступ
    if (isset($_GET['module'])){
        if (isset($LIST[$_GET['module']])){
            include('modules/'.$LIST[$_GET['module']]);
        }
    }
?>
