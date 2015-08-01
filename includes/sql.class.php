<?php
class sql{
    
    function __construct(){
        require 'db-credentials.php';
        //Implement the tablenames
        $this->tbl = $tbl;
        //EStablishing connection to database
        $this->dbh = $dbh;
    }
    
    public function dbh(){
        return $this->dbh;
    }
    
    function login($username, $pass){
      //Upload geodata
        /*$stloc = $this->dbh->prepare("INSERT INTO `".$this->tbl['locations']."` (`userid`, `lat`, `long`, `city`, `postnumber`) VALUES (:userid, :lat, :long, :city, :postnumber)");
        $stloc->bindParam("userid", $userid);
        $stloc->bindParam("lat", $lat);
        $stloc->bindParam("long", $long);
        $stloc->bindParam("city", $city);
        $stloc->bindParam("postnumber", $postcode);
        $resloc = $stloc->execute();*/
        
      //Push mysql query to check if credentials are correct
        $st = $this->dbh->prepare('SELECT * FROM `'.$this->tbl['users'].'` WHERE username=:username AND password=:password');
        $st->bindParam(':username', $username);
        $st->bindParam(':password', $pass);
        $st->execute();
        
        
        if($res = $st->fetch()){
          //Create cookie to ensure that the user stays logged in
            $this->createCookie(true, $res['username'], $res['password']);
            return true;
        }else{
            $this->createCookie(false);
            return $this->dbh->errorInfo();
        }
        
      //Upload the try
        //Wat ???
        
    }
    
    function register($name, $pass, $email, $age, $city, $postcode, $language, $programming, $lat, $long){
        $timestamp = time();
        $st = $this->dbh->prepare("INSERT INTO `".$this->tbl['users']."` (`first_name`, `last_name`, `email`, `age`, `username`, `password`, `timestamp`, `language`)
            VALUES ('','',:email, :age, :username, :password, :timestamp, :language)");
        $st->bindParam("email", $email);
        $st->bindParam("age", $age);
        $st->bindParam("username", $name);
        $st->bindParam("password", $pass);
        $st->bindParam("timestamp", $timestamp);
        $st->bindParam("language", $language);
        $res = $st->execute();
        
        $userst = $this->dbh->query("SELECT `id` FROM `".$this->tbl['users']."` WHERE `username` = '".$name."' AND `timestamp` = ".$timestamp);
        $userid = $userst->fetch()['id'];
        
        $stloc = $this->dbh->prepare("INSERT INTO `".$this->tbl['locations']."` (`userid`, `lat`, `long`, `city`, `postnumber`) VALUES (:userid, :lat, :long, :city, :postnumber)");
        $stloc->bindParam("userid", $userid);
        $stloc->bindParam("lat", $lat);
        $stloc->bindParam("long", $long);
        $stloc->bindParam("city", $city);
        $stloc->bindParam("postnumber", $postcode);
        $resloc = $stloc->execute();
        
        $programmering = explode(",", $programming);
        for($y = 0; $y < count(explode(",", $programming)); $y++){
            $langst = $this->dbh->query("SELECT `id` FROM `".$this->tbl['languages_master']."` WHERE `name` = '".trim($programmering[$y])."'");
            $langid = $langst->fetch()['id'];
            
            $stlang = $this->dbh->prepare("INSERT INTO `".$this->tbl['languages']."` (`languageid`, `userid`) VALUES (:langid, :userid)");
            $stlang->bindParam("langid", $langid);
            $stlang->bindParam("userid", $userid); 
            $reslang = $stlang->execute();
        }
        if($resloc && $res && $reslang){
            $this->createCookie(true, $name, $pass);
            return true;
        }else{
            return false;
        }
    }
    
    function status(){
      //Check that the cookies are set
        if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
          //Push mysql query to check if the stored credentials are correct
            $st = $this->dbh->prepare('SELECT * FROM `'.$this->tbl['users'].'` WHERE username=:username AND password=:password');
            $st->bindParam(':username', $_COOKIE['username']);
            $st->bindParam(':password', $_COOKIE['password']);
            $st->execute();
            
            
            if($res = $st->fetch()){
                return true;
            }else{
                return false;
            }
        }
    }
    
    function logout(){
        //Destroy the loggedin cookie
        $this->createCookie(false);
    }
    
    function createCookie($bol, $name='', $pass=''){
        if($bol){
            //Recreating the original cookie
            setcookie('username', $name, time()+3600*24*7, '/');
            setcookie('password', $pass, time()+3600*24*7, '/');
        }else{
            //Logging out
            setcookie('username', '', 1, '/');
            setcookie('password', '', 1, '/');
        }
    }
}