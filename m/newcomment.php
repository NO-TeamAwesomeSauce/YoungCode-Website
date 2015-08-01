<?php
error_reporting(E_ALL);
if(!isset($_POST['articleid'])){
    header('Location: ../');
}

require '../includes/sql.class.php';
$sql = new sql;

if($sql->status()){
    
    $time = time();
    $txt = $_POST['comment'];
    
    $userst = $sql->dbh->query("SELECT `id` FROM `".$sql->tbl['users']."` WHERE username = '".$_COOKIE['username']."'");
    $userid = $userst->fetch()['id'];
    
    $st = $sql->dbh->prepare("INSERT INTO `".$sql->tbl['comments']."` (`text`, `user_id`, `Project_id`, `timestamp`)
        VALUES (:text, :userid, :projectid, :timestamp)");
    $st->bindParam("text", $txt);
    $st->bindParam("userid", $userid);
    $st->bindParam("projectid", $_POST['articleid']);
    $st->bindParam("timestamp", $time);
    $res = $st->execute();

    if($res){
        header("Location: ../?p=showcode&id=".$_POST['articleid']."&status=success");
    }else{
        header("Location: ../?p=showcode&id=".$_POST['articleid']."&status=failed");
    }

}

?>