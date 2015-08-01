<?php
	
$servername = "norbye.com";
$user = "craftfwd_ads4mc";
$password = "BeastMad3[]";
$dbname = "craftfwd_db";

$conn = new mysqli($servername, $user, $password, $dbname);

if($conn->connect_error){
	die("Connection failed: " . $conn->connect_error);
}

//Help functions

function encode($arr){
	if(isset($arr['text'])){
		$arr['text'] = utf8_encode($arr['text']);
	}
	if(isset($arr['title'])){
		$arr['title'] = utf8_encode($arr['title']);
	}
	if(isset($arr['username'])){
		$arr['username'] = utf8_encode($arr['username']);
	}
	if(isset($arr['desc'])){
		$arr['desc'] = utf8_encode($arr['desc']);
	}
	if(isset($arr['timestamp'])){
		$arr['timestamp'] = date("d/M/Y", $arr['timestamp']);
	}
	if(isset($arr['date'])){
		$arr['date'] = date("d/M/Y", $arr['date']);
	}
	return $arr;
}

function oposite($a){
	if($a == "true"){return "false";}else{return 'true';}
}

function sqlquery($conn, $sql){
//	echo $sql;
	return $conn->query($sql);
}

function select($conn, $select, $from, $where){
	if($where == ""){$where = "1=1";}
	return sqlquery($conn, "SELECT " . $select . " FROM " . $from . " WHERE " . $where);
}
function selectorder($conn, $select, $from, $where, $order){
	if($where == ""){$where = "1=1";}
	if($where == ""){$where = "1=1";}
	return sqlquery($conn, "SELECT " . $select . " FROM " . $from . " WHERE " . $where . " ORDER BY " . $order);
}
function printjs($array){
	$arr[] = encode($array);
	echo json_encode($arr);
}
function printjs2($array){
	$arr = Array();
	foreach($array as $a){
		$arr[] = encode($a);
	}
	echo json_encode($arr);
}

function istrue($conn, $select, $from, $where, $column, $test){
	
	$arr = select($conn, $select,$from, $where)->fetch_assoc();
	if(isset($arr[$column])){
		if($arr[$column] == $test){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

/*
Commands:
profile=user_id - user, rank, city, posts, comments, votes
ispostliked=post_id, user=user_id - true/false
ispostliked=post_id, user=user_id - true/false
username=user_name, password=user_password - id/false
comments=post_id - list of comments(comment_id, comment_text, user_username)
id=post_id, user=user_id - post, votes, comments, hasvoted
id=post_id - post, votes, comments
user=user_id - posts(post, votes, comments, hasvoted)
 - posts(post, votes, comments)
*/


if(isset($_GET["profile"])){//-------------------------------------------------------Get Profile
	$row = select($conn, "first_name, last_name, email, age, username, `timestamp`, points", "as_User", "id = " . $_GET["profile"])->fetch_assoc();
	$row["votes"] = (string)((int)select($conn, "COUNT(*) as votes", "as_Vote", "user = " . $_GET["profile"])->fetch_assoc()["votes"] + (int)select($conn, "COUNT(*) as votes", "as_Vote, as_Post", "target_id= as_Post.id AND as_Post.user_id=" . $_GET["profile"])->fetch_assoc()["votes"]);
	$row["posts"] = select($conn, "COUNT(*) as posts", "as_Post", "user_id = " . $_GET["profile"])->fetch_assoc()["posts"];
	$row["points"] = (int)$row["votes"] + (int)$row["posts"];
	$row["rank"] = selectorder($conn,"name","as_Rank","requirement <= " . $row["points"], "requirement DESC")->fetch_assoc()["name"];
	$row["city"] = $conn->query("SELECT city FROM as_Location WHERE userid = " . $_GET["profile"])->fetch_assoc()["city"];
	$row["comments"] = select($conn, "COUNT(*) as comments", "as_Comment", "user_id = " . $_GET["profile"])->fetch_assoc()["comments"];
	printjs($row);
}else if(isset($_GET["ispostliked"]) && isset($_GET["user"])){//-------------------is post liked
	if(istrue($conn, "*", "as_Vote", "user = " . $_GET["user"] . " AND target_id = " . $_GET["ispostliked"] . " AND target = 'as_Post'", "target_id", $_GET["ispostliked"])){
		echo "true";
	}else{
		echo "false";
	}
}else if(isset($_GET["iscommentliked"]) && isset($_GET["user"])){//-------------------is comment liked
	if(istrue($conn, "*", "as_Vote", "user = " . $_GET["user"] . " AND target_id = " . $_GET["iscommentliked"] . " AND target = 'as_Comment'", "target_id", $_GET["iscommentliked"])){
		echo "true";
	}else{
		echo "false";
	}
}else if(isset($_GET["username"]) && isset($_GET["password"])){
	if(istrue($conn,"password", "as_User","username = '" . $_GET['username'] . "'", "password", $_GET["password"])){
		echo select($conn, "id", "as_User", "username = '" . $_GET['username'] . "'")->fetch_assoc()['id'];
	}else{
		echo "false";
	}
}else if(isset($_GET['comments'])){
	$result = select($conn,"as_Comment.id, as_Comment.`text`, as_User.`username`","as_Comment, as_User","as_User.`id` = as_Comment.`user_id` AND as_Comment.`Project_id` = " . $_GET['comments']);
	$arrContainer = Array();
	while($row = $result->fetch_assoc()){
		$row['votes'] = $conn->query("SELECT COUNT(*) AS 'votes' FROM as_Vote WHERE target = 'as_Comment' AND target_id = " . $row['id'])->fetch_assoc()['votes'];
		$arrContainer[] = $row;
	}
	echo json_encode($arrContainer);
}else if(isset($_GET["votepost"]) && isset($_GET["user"])){
	$result = select($conn,"*","as_Vote","target = 'as_Post' AND `user` = " . $_GET["user"] . " AND target_id = " . $_GET["votepost"])->fetch_assoc()["id"];
	if(!isset($result)){
		$conn->query("INSERT INTO as_Vote (`user`, `target`, `target_id`, `timestamp`) VALUES (" . $_GET["user"] . ", 'as_Post', " . $_GET["votepost"] . ", " . TIME() . ")");
		echo "Added one vote to post";
	}else{
		$conn->query("DELETE FROM as_Vote WHERE id = " . $result);
		echo "Removed one vote from post";
	}
}else if(isset($_GET["votecom"]) && isset($_GET["user"])){
	$result = select($conn,"*","as_Vote","target = 'as_Comment' AND `user` = " . $_GET["user"] . " AND target_id = " . $_GET["votecom"])->fetch_assoc()["id"];
	if(!isset($result)){
		$conn->query("INSERT INTO as_Vote (`user`, `target`, `target_id`, `timestamp`) VALUES (" . $_GET["user"] . ", 'as_Comment', " . $_GET["votecom"] . ", " . TIME() . ")");
		echo "Added one vote to comment";
	}else{
		$conn->query("DELETE FROM as_Vote WHERE id = " . $result);
		echo "Removed one vote to comment";
	}
}else if(isset($_GET['id'])){
	
	$row = select($conn,"*","as_Post","public = 1 AND id = " . $_GET['id'])->fetch_assoc();
	
	if(!isset($row["id"])){
		return;
	}
	
	if(isset($_GET["type"])){
		if($_GET["type"] == "kake"){
			echo "KAKE!!!";
		}else if($_GET["type"] == "question" || $_GET["type"] == "tutorial"){
			$row = select($conn,"*","as_Post","public = 1 AND id = " . $_GET['id'] . " AND type = '" . $_GET["type"] . "'")->fetch_assoc();	
		}
	}
	
	$row['user'] = select($conn,"username","`as_User`","id = " . $row["user_id"])->fetch_assoc()["username"];
	$row['votes'] = select($conn,"COUNT(*) AS 'votes'","as_Vote","target = 'as_Post' AND target_id = " . $row['id'])->fetch_assoc()['votes'];
	$row['antcom'] = select($conn,"COUNT(*) AS 'antcom'","as_Comment","Project_id = " . $row["id"])->fetch_assoc()["antcom"];
	$row['type'] = select($conn,"type","as_Post","id = " . $row['id'])->fetch_assoc()['type'];
	if(isset($_GET['user'])){
		if((select($conn,"*","as_Vote","user = " . $_GET["user"] . " AND target_id = " . $row["id"] . " AND target = 'as_Post'")->fetch_assoc()['id']) != null){
			$row['hasvoted'] = "true";
		}else{
			$row['hasvoted'] = "false";
		}
	}
	$taggarr = array();
	$tag = $conn->query("SELECT languageid FROM as_Language WHERE projectid = " . $_GET["id"]);
	while($tageg = $tag->fetch_assoc()){
		array_push($taggarr, $conn->query("SELECT `name` FROM as_Language_master WHERE id = " . $tageg["languageid"])->fetch_assoc()["name"]);
	}
	$row['taggs'] = implode(", ", $taggarr);
	printjs($row);
}else{
	
	$result = selectorder($conn,"*","as_Post","public = 1","id DESC");
	
	if(isset($_GET["type"])){
		if($_GET["type"] == "kake"){
			echo "[{\"id\":\"1\",\"title\":\"KAKE\",\"user_id\":\"1\",\"date\":\"1000000000\",\"public\":\"1\",\"desc\":\"KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!KAKE!\",\"type\":\"KAKE\",\"antcom\":\"10000000\",\"votes\":\"1000000\",\"taggs\":\"KAKE\"}]";
			return;
		}else if($_GET["type"] == "question" || $_GET["type"] == "tutorial" || $_GET["type"] == "showcase"){
			$result = selectorder($conn,"*","as_Post","public = 1 AND type = '" . $_GET["type"] . "'", "id DESC");	
		}
	}
	$arr = Array();
	while($row = $result->fetch_assoc()){
		if(isset($_GET['user'])){
			if(select($conn,"*","as_Vote","user = " . $_GET["user"] . " AND target_id = " . $row["id"] . " AND target = 'as_Post'")->fetch_assoc()['id'] != null){
				$row['hasvoted'] = "true";
			}else{
				$row['hasvoted'] = "false";
			}
		}
		$row['antcom'] = select($conn,"COUNT(*) AS 'antcom'","as_Comment","Project_id = " . $row["id"])->fetch_assoc()["antcom"];
		$row['votes'] = select($conn,"COUNT(*) AS 'votes'","as_Vote","target = 'as_Post' AND target_id = " . $row['id'])->fetch_assoc()['votes'];
		$row['type'] = select($conn,"type","as_Post","id = " . $row['id'])->fetch_assoc()['type'];
		
		$taggarr = array();
		$tag = $conn->query("SELECT languageid FROM as_Language WHERE projectid = " . $row["id"]);
		while($tageg = $tag->fetch_assoc()){
			array_push($taggarr, $conn->query("SELECT `name` FROM as_Language_master WHERE id = " . $tageg["languageid"])->fetch_assoc()["name"]);
		}
		$row['taggs'] = implode(", ", $taggarr);
		$arr[] = $row;
	}
	printjs2($arr);
}

$conn->close();
?>
