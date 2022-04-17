<?php
class session{
    public $id;
    public $session_ip;
    public $user_agent;
    function __construct($id,$session_ip,$user_agent){
        $this->id =$id;
        $this->session_ip = $session_ip;
        $this->user_agent = $user_agent;
    }
}
     class session_controller{
          public $sessions;
          public $mysqli;
          public $n;
          public $SESSIONID;
          function __construct($id,$mysqli,$SESSIONID){
              $query = 'SELECT * FROM `sessions` WHERE user_id='.(int)$id.' AND id!='.$SESSIONID;
              $rez = $mysqli->query($query);
              $this->n = $rez->num_rows;
              $i = 0;
              while ($row = $rez->fetch_assoc()){
                 $arr[$i] = $row;
                 $i++;
              }
              $this->sessions = $arr;
              $this->mysqli = $mysqli;
              $this->SESSIONID = $SESSIONID;
          }
          function delete($id){
              for ($i = 0;$i < $this->n;$i++){
                   if ($this->sessions[$i]['id'] == $id){
                       $query = 'DELETE FROM `sessions` WHERE id='.(int)$id;
                       $this->mysqli->query($query);
                   }
              }
          }
          function print_sessions(){
            for ($i = 0;$i < $this->n;$i++){
                  $arr[$i] = new session($this->sessions[$i]['id'],$this->sessions[$i]['user_ip'],$this->sessions[$i]['user_agent']);
            }
            if($this->n > 0){
              print json_encode($arr);
            }
          }
     }
     $sessions = new session_controller($USERID,$mysqli,$SESSIONID);
     if ( $_GET['type'] == 'delete'){
        $sessionid = (int)$_GET['session_id'];
        $sessions->delete($sessionid);
     }elseif ($_GET['type'] == 'print'){
        $sessions->print_sessions();
     }
?>
