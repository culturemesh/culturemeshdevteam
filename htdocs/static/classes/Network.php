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
    public function getCountry(){
        return $this->Country;
    }
    public function setCountry($country){
        $this->Country = $country;
    }
    public function getLocationName(){
        return $this->City.', '.$this->Region;
    }
    public function Save(){
        $data = getRowQuery("SELECT * FROM networks WHERE city='{$this->City}' AND region='{$this->Region}' AND country='{$this->Country}' OR language='{$this->Language}'");
        if(!$data){
            if(isset($this->Language)){
                return actionQuery("INSERT INTO networks (language,date_added) values('{$this->Language}',NOW())");
            }
            else{
                return actionQuery("INSERT INTO networks (city,region,country,date_added) values('{$this->City}','{$this->Region}','{$this->Country}',NOW())");
            }
        }
        else{ 
            return actionQuery("UPDATE networks SET 
                            city='{$this->City}',
                            state='{$this->State}',
                            country='{$this->Country}',
                            language='{$this->Language}'
                            WHERE id={$data['id']}");
        }
    }
}
?>