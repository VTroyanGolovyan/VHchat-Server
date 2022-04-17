<?php
//структура ответа сервера
   class Answer {
      public $status = false;
      public $text = '';
   }
   function get_city($city_id,$mysqli){
      $query = 'SELECT * FROM city WHERE city_id = '.(int)$city_id;
      $rez = $mysqli->query($query);
      $row = $rez->fetch_assoc();
      return $row['name'];
   }
   function get_country($country_id,$mysqli){
      $query = 'SELECT * FROM country WHERE country_id = '.(int)$country_id;
      $rez = $mysqli->query($query);
      $row = $rez->fetch_assoc();
      return $row['name'];
   }
   $answer = new Answer();
   //получаем входные данные
   $name = trim(strip_tags($_POST['name']),' ');
   $second_name = trim(strip_tags($_POST['second_name']),' ');
   $gmail = trim(strip_tags($_POST['gmail']),' ');
   $password = htmlspecialchars($_POST['password']);
   $chekpassword = htmlspecialchars($_POST['chek']);
   //проверяем данные на корректность и в случае их корректности регистрируем пользователя
   if (!empty($name)&&!empty($second_name)&&!empty($gmail)&&
       !empty($gmail)&&!empty($password)&&!empty($chekpassword)
       ){
          if ($password == $chekpassword){
            if(strlen($password )>=8){
              if(preg_match("/[0-9a-z_\.\-]+@[0-9a-z_\.\-]+\.[a-z]{2,4}/i", $gmail)){
                        $city = get_city($_POST['city'],$mysqli);
                        $country = get_country($_POST['country'],$mysqli);
                        $query = 'INSERT INTO `users` (id,name,second_name,gmail,password,access,user_ip,country,city) VALUES (NULL,"'.$name.'","'.$second_name.'","'.$gmail.'","'.md5(md5($password)).'",10,"'.$_SERVER['REMOTE_ADDR'].'","'.$country.'","'.$city.'")';
                        $mysqli->query($query);
                        $answer->status = true;
                        $answer->text = 'You have successfully registered';
                }else{
                  $answer->status = false;
                  $answer->text = 'Incorrect gmail';
                }
            }else{
              $answer->status = false;
              $answer->text = 'Short password(min. 8 letters)';
            }
          }else{
            $answer->status = false;
            $answer->text = 'Passwords do not match';
          }
        }else{
          $answer->status = false;
          $answer->text = 'Fill in all';
        }
   print json_encode($answer);
?>
