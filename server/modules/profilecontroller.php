<?php
      class profilecontroller{
           public $userid;
           public $mysqli;
           function query($query){
               return $this->mysqli->query($query);
           }
           function __construct($userid,$mysqli){
                $this->userid = $userid;
                $this->mysqli = $mysqli;
           }
           function change_status($text){
                    $query = 'UPDATE `users` SET status="'.$text.'" WHERE id='.$this->userid;
                    $this->query($query);
           }
      }
      $controller = new profilecontroller($USERID,$mysqli);
      if ($_GET['type'] == 'changestatus'){
          $controller->change_status(trim(strip_tags($_POST['status']),' '));
      }
?>
