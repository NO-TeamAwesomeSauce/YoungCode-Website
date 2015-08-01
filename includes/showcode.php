<?php
error_reporting(E_ALL);
?>
<style>
.input{
    width:100%;
    margin-bottom:30px;
}
#h2{
    display:inline-block;
    text-align:left;
    width:50%;
    font-size:17pt;
    text-decoration: none;
    margin-bottom:5px;
    font-family: cabin;
}
#data1{
    display:inline-block;
    text-align:right;
    width:50%;
}
#data2{
    margin-bottom:15px;
    display:block;
    width:100%;
    text-decoration:none;
}
p{
    display:block;
    line-height:22px;
}

#note{
    color:lime;
}

#commentdiv{
    width:100%;
    min-height:20px;
    background-color:#FFF;
}
#commentdiv h2{
    padding:5px;
}
#commentdiv #comment{
    width:100%;
    display:block;
    margin-bottom:20px;
}
#commentdiv #comment #text{
    display:inline-block;
    width:80%;
    vertical-align:top;
}
#commentdiv #comment #text #content{
    padding:5px;
}
#commentdiv #comment #user{
    display:inline-block;
    width:20%;
    vertical-align:top;
}
#commentdiv #comment #user #content{
    padding:5px;
}
#newcomment{
    padding-bottom:20px;
}
#commentdiv #newcomment textarea{
    width:100%;
    min-height:150px;
}
#newcomment button{
    width:100%;
    padding: 10px
}
#newcomment p{
    margin:3px;
}
</style>
<?php
require 'db-credentials.php';
$st = $dbh->prepare("SELECT * FROM `".$tbl['code']."` WHERE public = 1 AND id = ".$_GET['id']." LIMIT 1");
$st->execute();

if($row = $st->fetch()){
    $upvotes = $dbh->query("SELECT COUNT(*) FROM `as_Vote` WHERE target = 'as_Post' AND `target_id` = '".$row['id']."'")->fetchAll()[0][0];
    $comments = $dbh->query("SELECT COUNT(*) FROM `as_Comment` WHERE `Project_id` = '".$row['id']."'")->fetchAll()[0][0];
    $creator = $dbh->query("SELECT `username` FROM `".$tbl['users']."` WHERE id = ".$row['user_id'])->fetch()['username'];
    
    //Grab language
    $lst = $sql->dbh->query("SELECT m.name,m.id,l.projectid FROM `".$sql->tbl['languages_master']."` m INNER JOIN `".$sql->tbl['languages']."` l ON l.languageid=m.id WHERE l.projectid = ".$row['id']);
    $langarr = array();
    while($lng = $lst->fetch()){
        array_push($langarr, $lng['name']);
    }
    $languages = implode(", ", $langarr);
    
    if($languages == ""){
        $languages = "unknown";
    }
    
    $descc = htmlentities($row['desc']);
    $descc = preg_replace('/([\t])/',"&nbsp;&nbsp;&nbsp;&nbsp;", $descc);
    //$descc = str_replace(' ', '&nbsp;', $descc);
    $desc = "";
    foreach(explode(PHP_EOL, $descc) as $line){
        if(strstr($line, '//')){
            /*$line = str_replace('//', '<span id="note">//', $line);
            $line .= '</span>';*/
        }
        $desc .= $line."<br>";
    } 
    $desc = str_replace(PHP_EOL, '<br>', $desc);
    //$desc = preg_replace('/([\r\n])/',"<br>", $desc);
    
    if(isset($_COOKIE['language'])){
        $userst = $sql->dbh->query("SELECT `id` FROM `".$sql->tbl['users']."` WHERE username = '".$_COOKIE['username']."'");
        $userid = $userst->fetch()['id'];
    
    
        $imgfilest = $sql->dbh->query("SELECT `id` FROM `".$sql->tbl['vote']."` WHERE `user` = ".$userid." AND `target_id` = ".$row['id']." AND `target` = 'as_Post'");
        if($fdj = $imgfilest->fetch()){
            $imgfile = 'UpVoteGreen';
        }else{
            $imgfile = 'UpVote';
        }
        $img = 'src=imgs/'.$imgfile.'.png';
        $imgg = "<img style='height:20px;margin-right:10px;cursor:pointer;' $img onclick='upvote(".$row['id'].", 1)'>";
    }else{
        $img = '';
        $imgg = '';
    }
    
    
    echo "<div id='codediv' class='input'><h2 id='h2'>$imgg".escape($row['title'])."</h2><i id='data1'>by ".$creator.", ".date("d. \of F Y - H:i:s", $row['timestamp'])."</i><i id='data2'>Programmed in: ".$languages." - $upvotes upvotes</i><p>".escape($desc)."</p></div>";
    
    echo "<div id='commentdiv'><h2>Comments</h2>";
    
    $comsql = $dbh->query("SELECT * FROM `".$tbl['comments']."` WHERE `Project_id` = ".$row['id']);
    while($com = $comsql->fetch()){
        $userstt = $dbh->query("SELECT * FROM `".$tbl['users']."` WHERE id = ".$com['user_id']);
        if($user = $userstt->fetch()){
            $points = $user['points'];
            $rankst = $dbh->query("SELECT * FROM `".$tbl['ranks']."` WHERE requirement <= ".$points." ORDER BY requirement DESC LIMIT 1");
            $rank = $rankst->fetch()['name'];
            echo "<div id='comment'><div id='user'><div id='content'><b>".$user['username']."</b><br>Points: ".$points."<br>Rank: $rank</div></div><div id='text'><div id='content'><i>".date("d-m-Y - H:i:s", $com['timestamp'])."</i><br>".$com['text']."</div></div></div>";
        }
    }
    if(isset($_COOKIE['username'])){
        echo "<div id='newcomment'><form action='m/newcomment.php' method='POST'>
        <input type='hidden' name='articleid' value='".$row['id']."' />
        <textarea placeholder='Comment' name='comment'></textarea><button style='font-size:16px;cursor:pointer;'>Send</button></form></div>";
    }else{
        echo "<div id='newcomment'><p>You have to be logged in to post a comment</p></div>";
    }
    echo '</div>';
}

function escape($str){
    
    /*$str = str_replace('æ', '&aelig;', $str);
    $str = str_replace('Æ', '&AElig;', $str);
    $str = str_replace('ø', '&oslash;', $str);
    $str = str_replace('Ø', '&Oslash;', $str);
    $str = str_replace('å', '&aring;', $str);
    $str = str_replace('Å', '&Aring;', $str);*/
    
    
    
    return $str;
}
?>
<script>
$('#newcomment').children(0).css("width", "100% - 10px");
$('#newcomment').children(0).css("padding", "5px");

function upvote(idd, type){
    if(type == 1){
        type = "post";
    }else{
        type = "comment";
    }
    window.location.href = 'm/upvote.php?id=' + idd + '&type=' + type;
}
</script>