<?php
  class Country{
    public $country_id;
    public $country_name;
    public function __construct($country_id,$country_name){
      $this->country_id = $country_id;
      $this->country_name = $country_name;
    }

  }
  class City{
    public $city_id;
    public $city_name;
    public function __construct($city_id,$city_name){
      $this->city_id = $city_id;
      $this->city_name = $city_name;
    }

  }
  if ($_GET['type'] == 'cities'){
    $query = 'SELECT * FROM `city` WHERE country_id='.(int)$_GET['country_id'].' ORDER BY name';
    $rez = $mysqli->query($query);
    if (!$rez->num_rows == 0){
      $i = 0;
      while ($row = $rez->fetch_assoc()){
        $answer[$i] = new City($row['city_id'],$row['name']);
        $i++;
      }
    }
    print json_encode($answer);

  }elseif ($_GET['type'] == 'countries'){
    $query = 'SELECT * FROM `country`';
    $rez = $mysqli->query($query);
    if (!$rez->num_rows == 0){
      $i = 0;
      while ($row = $rez->fetch_assoc()){
        $answer[$i] = new Country($row['country_id'],$row['name']);
        $i++;
      }
    }
    print json_encode($answer);
  }
?>
