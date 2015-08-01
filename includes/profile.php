<style>
#Profileinf{
    text-align:center;
}
#Profileinf img{
    display:block;
    margin:0 auto;
}
#Profileinf #infcontainer{
    width:500px;
    margin:0 auto;
    line-height: 30px;
}
#Profileinf #infcontainer #left{
    width:250px;
    float:left;
    text-align:center;
}
#Profileinf #infcontainer #right{
    width: 250px;
    float:right;
    text-align:center;
}
</style>
<?php
error_reporting(E_ALL);
if(!isset($sql)){
    require 'sql.class.php';
    $sql = new sql;
    $imgs = '../imgs/';
}else{
    $imgs = '/imgs/';
}
if(isset($_COOKIE['username']) && isset($_COOKIE['password']) && ($sql->status() == 1)){
    //echo "Account details:<br><br>";
    $sth = $sql->dbh->prepare("SELECT * FROM `".$sql->tbl['users']."` WHERE username = :username");
    $sth->bindParam('username',$_COOKIE['username']);
    $sth->execute();
    if($row = $sth->fetch()){
        echo "<div id='Profileinf'>";
        echo "<img src='".$imgs."Def_ProfilePic.png' style='width:250px;border-radius:125px;' />";
        /*echo "First name: ".$row['first_name']."<br>";
        echo "Last name: ".$row['last_name']."<br>";*/
        echo "<div id='infcontainer'><div id='top'>";
        echo "".$_COOKIE['username']."<br>";
        
        echo "</div><div id='left'>";
        echo "Email: ".$row['email']."<br>";
        echo "Registered: ".date("d. \of F Y", $row['timestamp'])."<br>";
        $comsent = $sql->dbh->query("SELECT COUNT(*) FROM `".$sql->tbl['comments']."` WHERE `user_id` = '".$row['id']."'")->fetchAll()[0][0];
        echo "Comments sent: $comsent<br>";
        /*$lst = $sql->dbh->query("SELECT c.id,u.id FROM `".$sql->tbl['comments']."` c INNER JOIN `".$sql->tbl['users']."` u ON c.`user_id`=u.id WHERE u.id = ".$row['id']);
        $comreq = 0;
        while($arr = $lst->fetch()['id']){
            $comreq++;
        }*/
        /*require 'points.class.php';
        $points = new points;
        $points->get($row['id']);*/
        
        echo "Comments recieved: x<br>";
        $upsent = $sql->dbh->query("SELECT COUNT(*) FROM `".$sql->tbl['vote']."` WHERE target = 'as_Post' AND `user` = '".$row['id']."'")->fetchAll()[0][0];
        echo "Upvotes sent: $upsent<br>";
        echo "Upvotes recieved: x<br>";
        echo "</div><div id='right'>";
        echo "Points: ".$row['points']."<br>";
        $rankst = $sql->dbh->query("SELECT * FROM `".$sql->tbl['ranks']."` WHERE requirement <= ".$row['points']." ORDER BY requirement DESC LIMIT 1");
        $rank = $rankst->fetch()['name'];
        echo "Rank: ".ucfirst(explode(" ", $rank)[0]).' '.ucfirst(explode(" ", $rank)[1]);
        echo "<img src='".$imgs.ucfirst(explode(" ", $rank)[0]).".png' style='width:150px;float:center;' />";
        echo "</div><div id='bottom'>";
        
        echo "</div></div>";
    }
}else{
    echo 'You are not logged in :(';
}

?>