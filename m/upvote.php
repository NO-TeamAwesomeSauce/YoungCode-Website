<?php
error_reporting(E_ALL);
if(!isset($_GET['id'])){
    header('Location: ../?p=codehub');
}

require '../includes/sql.class.php';
$sql = new sql;

if($sql->status()){
    
    $time = time();
    $postid = $_GET['id'];
    if($_GET['type'] == "post"){
        $target = "as_Post";
    }else{
        $target = "as_Comment";
    }
    
    
    $userst = $sql->dbh->query("SELECT `id` FROM `".$sql->tbl['users']."` WHERE username = '".$_COOKIE['username']."'");
    $userid = $userst->fetch()['id'];
    
    $alreadymade = $sql->dbh->query("SELECT id FROM `".$sql->tbl['vote']."` WHERE target = 'as_Post' AND `target_id` = $postid AND `user` = $userid");
    
    if(!($made = $alreadymade->fetch())){
        
        $st = $sql->dbh->prepare("INSERT INTO `".$sql->tbl['vote']."` (`user`, `target`, `target_id`, `timestamp`)
            VALUES (:user, :target, :targetid, :timestamp)");
        $st->bindParam("user", $userid);
        $st->bindParam("target", $target);
        $st->bindParam("targetid", $postid);
        $st->bindParam("timestamp", $time);
        $res = $st->execute();

        if($res){
            header("Location: ../?p=showcode&id=".$postid."&status=success");
        }else{
            header("Location: ../?p=showcode&id=".$postid."&status=failed");
        }
    }else{
        $qry = $sql->dbh->prepare("DELETE FROM `".$sql->tbl['vote']."` WHERE `target_id` = $postid AND `user` = $userid AND `target` = '$target' LIMIT 1");
        $suc = $qry->execute();
        if($suc = 1){
            header("Location: ../?p=showcode&id=".$postid."&status=success");
        }else{
            header("Location: ../?p=showcode&id=".$postid."&status=failed");
        }
    }

}

?>