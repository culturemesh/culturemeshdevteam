<?php
class SuggestedNetwork extends Network{
    public function Save(){
        //if language has been set
        if(isset($this->Language)){
            //check if it exists in db first
            if(!getRowQuery("SELECT * FROM suggested_networks WHERE language='{$this->Language}'")){
                actionQuery("INSERT INTO suggested_networks (language, date_suggested) values('{$this->Language}', NOW())");
            }
        }
        //if city&region has been set
        if(isset($this->City) && isset($this->Region)){
            if(!getRowQuery("SELECT * FROM suggested_networks WHERE city='{$this->City}' AND region ='{$this->Region}'")){
                actionQuery("INSERT INTO suggested_networks (city,region,date_suggested) values('{$this->City}', '{$this->Region}', NOW())");
            }
        }
    }
    private function __destruct(){
    }
}
?>