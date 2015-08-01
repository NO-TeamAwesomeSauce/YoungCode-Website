
<?php

class points{
    function __construct(){
        require 'db-credentials.php';
        $this->tbl = $tbl;
        $this->dbh = $dbh;
    }
    
    function get($userid){
        $votesst = $this->dbh->query("SELECT * FROM `".$this->tbl['code']."` WHERE `user_id` = $userid");
        $votes = 0;
        while($votesres = $votesst->fetch()){
            $votesx = $this->dbh->query("SELECT COUNT(*) FROM `".$this->tbl['vote']."` WHERE `target_id` = ".$votesres['id']." AND `target` = 'as_Post'");
            $votes = $votes + (int) $votesx->fetch();
        }
        
        return $votes;
    }
}




?>