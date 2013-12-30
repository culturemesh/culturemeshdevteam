<?php
class UserSettings{
    private $UID;
    public function __construct($uid = 0) {
        $this->UID = $uid;
    }
    public function setID($id){
        $this->ID = $id;
    }
    public function setUID($uid){
        $this->UID = $uid;
    }
    public function setEventsUpcoming($eu_notifications){
        $this->EventsUpcoming = intval($eu_notifications);
    }
    public function setEventsInterested($ei_notifications){
        $this->EventsInterested = intval($ei_notifications);
    }
    public function setCompanyNews($cn_notifications){
        $this->CompanyNews = intval($cn_notifications);
    }
    public function setNetworkActivity($na_notifications){
        $this->NetworkActivity = intval($na_notifications);
    }
    public function Save(){
        $d = getRowQuery("SELECT * FROM user_notifications WHERE uid={$this->UID}");
        if($d){
            return actionQuery("UPDATE user_notifications SET "
                    . "events_upcoming={$this->EventsUpcoming},"
                    . "events_interested_in={$this->EventsInterested},"
                    . "company_news={$this->CompanyNews},"
                    . "network_activity={$this->NetworkActivity}"
                    . " WHERE uid={$this->UID}");
        }
        else{
            return actionQuery("INSERT INTO user_notifications (uid,events_upcoming,events_interested_in,company_news,network_activity) values ( "
                    . "{$this->UID},"
                    . "{$this->EventsUpcoming},"
                    . "{$this->EventsInterested},"
                    . "{$this->CompanyNews},"
                    . "{$this->NetworkActivity})");
        }
    }
}
?>