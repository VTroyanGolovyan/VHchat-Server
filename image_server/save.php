<?php
    header("Access-Control-Allow-Origin: *");
    //подключение базы данных
    include('db/db.php');
    class Answer{
        public $key;
        public $status;
        public $height;
        public $width;
        function __construct($status,$key,$height,$width){
            $this->key = $key;
            $this->status = $status;
            $this->width = $width;
            $this->height = $height;
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

      if (! empty($_FILES['file']['name'])){
        if($file['error'] == 0){
            if(substr($_FILES['file']['type'],0,5)=='image'){
                $image_info = getimagesize($_FILES['file']['tmp_name']);
                $height = $image_info[1];
                $width = $image_info[0];

                $filename = $_FILES['file']['tmp_name'];
                $size = getimagesize($filename);
                if ($size[0] > 600){
                    $mash = $size[0]/600;
                    $h = $size[1]/$mash;
                    $w = 600;
                    $canvas = imagecreatetruecolor($w,$h);
                    switch ($size['mime']){
                        case 'image/png':
                            $image = imagecreatefrompng($filename);
                        break;
                        case 'image/gif':
                            $image = imagecreatefromgif($filename);
                        break;
                        case 'image/jpeg':
                            $image = imagecreatefromjpeg($filename);
                        break;
                    }
                    imagecopyresampled($canvas,$image,0,0,0,0,$w,$h,$size[0],$size[1]);
                    $filenew = 'tmp/img_'.generateHash(50).'_min';
                    switch ($size['mime']){
                        case 'image/png':
                            $image = imagepng($canvas,$filenew);
                        break;
                        case 'image/gif':
                            $image = imagegif($canvas,$filenew);
                        break;
                        case 'image/jpeg':
                            $image = imagejpeg($canvas,$filenew);
                        break;
                    }
                    $image=file_get_contents($filenew);
                    $image=$mysqli->real_escape_string($image);

                    $key = generateHash(mt_rand(35,40)).md5(Date.time());
                    $mysqli->query('INSERT INTO `msg_images` (id,image_key,image) VALUES (NULL,"'.$key.'","'.$image.'")');
                    $answer = new Answer(true,$key,$height,$width);
                    unlink($filenew);
                }else{
                    $image=file_get_contents($_FILES['file']['tmp_name']);
                    $image=$mysqli->real_escape_string($image);
                    $key = generateHash(mt_rand(35,40)).md5(Date.time());
                    $mysqli->query('INSERT INTO `msg_images` (id,image_key,image) VALUES (NULL,"'.$key.'","'.$image.'")');
                    $answer = new Answer(true,$key,$height,$width);
               }
          }else{
            $answer = new Answer(false,'',0,0);
          }
        }else{
          $answer = new Answer(false,'',0,0);
        }
      }else{
        $answer = new Answer(false,'',0,0);
      }


    print json_encode($answer);
?>
