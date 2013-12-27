<?php
class Network{
    public function getID(){
        return $this->ID;
    }
    public function setID($id){
        $this->ID = $id;
    }
    public function getLanguage(){
        return $this->Language;
    }
    public function setLanguage($language){
        $this->Language = $language;
    }
    public function getCity(){
        return $this->City;
    }
    public function setCity($city){
        $this->City = $city;
    }
    public function getRegion(){
        return $this->Region;
    }
    public function setRegion($region){
        $this->Region = $region;
    }
    public function getLocationName(){
        return $this->City.', '.$this->Region;
    }
}
?>