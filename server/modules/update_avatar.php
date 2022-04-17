<?php
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
  if (! empty($_FILES['avatar']['name'])){
    if($_FILES['avatar']['error'] == 0){
      if(substr($_FILES['avatar']['type'],0,5)=='image'){
        $image_info = getimagesize($_FILES['avatar']['tmp_name']);
        $height = $image_info[1];
        $width = $image_info[0];
        $namefile = 'VH'.md5(time().date('d')).generateHash(10);
        move_uploaded_file($_FILES['avatar']['tmp_name'],'avatars/'.$namefile.'.jpg');
        $query = 'SELECT id,avatar FROM `users` WHERE id = '.$USERID;
        $rez = $mysqli->query($query);
        if ($rez->num_rows != 0){
            $row = $rez->fetch_assoc();
            unlink($row['avatar']);
        }
        $query = 'UPDATE `users` SET `avatar`="'.'avatars/'.$namefile.'.jpg'.'" WHERE `id` = '.$USERID;
        $mysqli->query($query);
        $answer = new Answer(true,$height,$width,'avatars/'.$namefile.'.jpg');
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
?>
