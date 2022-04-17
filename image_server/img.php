<?php
     header("Access-Control-Allow-Origin: *");
     header("content-type: image/* charset=utf-8");
    header("Cache-control:  max-age=3600, private");
     include ('db/db.php');
     if( isset($_GET['image_key']) ){
        $rez = $mysqli->query('SELECT * FROM `msg_images` WHERE image_key="'.$_GET['image_key'].'"');
        if ( $rez->num_rows === 0 ){
            $image = file_get_contents('error.jpg');
        }else{
          if (!$rez->num_rows == 0){
           if ( $row=$rez->fetch_assoc() ){
                  if ( !empty( $row['image'] ) ){
                        $image = $row['image'];
                  }else{
                        $image = file_get_contents('error.jpg');
                  }
           }else{
                 $image = file_get_contents('error.jpg');
           }
         }
        }
        print $image;
     }
?>
