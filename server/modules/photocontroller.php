<?php
class Photo{
    public $id;
    public $owner;
    public $url;
    function __construct($id, $owner, $url){
        $this->id = $id;
        $this->owner = $owner;
        $this->url = $url;
    }
}
class Answer{
   public $status;
   public $url;
   public $width;
   public $height;
   function __construct($status, $url, $width,$height){
       $this->status = $status;
       $this->url = $url;
       $this->width = $width;
       $this->height = $height;
   }
}
   class PhotoCtrl{

      public $mysqli;
      public $user;

      function __construct($USERID,$mysqli){
          $this->user = $USERID;
          $this->mysqli = $mysqli;
      }

      function query($query){
          return $this->mysqli->query($query);
      }
      function load($file){
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
          if (! empty($file['name'])){
            if ($file['error'] == 0){
              if (substr($file['type'],0,5)=='image'){
                $image_info = getimagesize($file['tmp_name']);
                $height = $image_info[1];
                $width = $image_info[0];
                $namefile = 'VH'.md5(time().date('d')).generateHash(10);
                move_uploaded_file($file['tmp_name'],'photos/'.$namefile.'.jpg');
                $query = 'INSERT INTO `photos` (id,owner,filename) VALUES (NULL ,'.$this->user.',"'.'photos/'.$namefile.'.jpg'.'")';
                $this->query($query);
                return new Answer(true,'photos/'.$namefile.'.jpg',$width,$height);
              }
            }
          }
      }
      function get_photo_user($id){
          $query = 'SELECT * FROM `photos` WHERE owner='.$id.' ORDER BY id DESC';
          $rez = $this->query($query);
          $i = 0;
          while ($row = $rez->fetch_assoc()){
            $arr[$i++] = new Photo($row['id'],$row['owner'],$row['filename']);
          }
          return $arr;
      }

      function delete_photo($photoid){

      }

   }
   $photo_ctrl = new PhotoCtrl($USERID,$mysqli);
   if ($_GET['type'] == 'load'){
      print json_encode($photo_ctrl->load($_FILES['photo']));
   } elseif ($_GET['type'] == 'get') {
      print json_encode($photo_ctrl->get_photo_user($_GET['userid']));
   } elseif ($_GET['type'] == 'load') {
      $photo_ctrl->load($_FILES['photo']);
   }
?>
