<?php
error_reporting(E_ALL);

if(!isset($sql)){
    require 'sql.class.php';
    $sql = new sql;
}

if(!isset($_COOKIE['username'])){
    echo "You have to be logged in to post code.";
}else if(!isset($_POST['title'])){
?>
<form action="?p=createcode" method="POST">
    <table>
        <tr><td>Title:</td><td><input type="text" name="title" /></td></tr>
        <tr><td>Add language:</td><td><select name="tag" id="languages">
            <option value="" selected="selected"></option>
            <?php
            $stt = $sql->dbh->query("SELECT id, name FROM `".$sql->tbl['languages_master']."` ORDER BY name ASC");
            while($langs = $stt->fetch()){
                echo "<option value='".$langs['id']."'>".$langs['name']."</option>
                ";
            }
            ?>
        </select></td></tr>
        <tr id="languagecontainer"><td id="td1" style="vertical-align:top;"></td><td id="td2"></td></tr>
        <tr><td>Public: </td><td><input type="checkbox" name="public" checked="true" /></td></tr>
        <tr><td>Category:</td><td><select name="type">
            <option value="tutorial">Tutorial</option>
            <option value="showcase">Showcase</option>
            <option value="question">Question</option>
        </select></td></tr>
        <tr><td style="vertical-align:top;">Description:</td><td><textarea type="text" name="desc" style="width:600px;height:500px;padding:5px;" placeholder="Paste your code here" ></textarea></td></tr>
        <tr><td colspan=2 style="text-align:center;"><input id="submittt" type="submit" value="Submit" style="padding:10px 40px 10px 40px;cursor:pointer;" /></td></tr>
    </table>
</form>

<script>
var langindex = 0;
$('#languages').change(function(){
    if(this.value != ""){
        var val = this.value;
        var text = $("select[name='tag'").find('option:selected').text();
        
        if($("td").val() == val){
            alert("exists");
        }else{
            var langs = document.getElementById("languagecontainer");
            var td1 = document.getElementById("td1");
            var td2 = document.getElementById("td2");
            
            td1.innerHTML = "Languages: ";
            
            var p = document.createElement("p");
            p.innerHTML = " " + text;
            
            var data = document.createElement("input");
            data.type = "hidden";
            data.name = "data" + langindex;
            data.value = val;
            p.appendChild(data);
            
            var check = document.createElement("input");
            check.type = "checkbox";
            check.checked = "true";
            check.name = "lang" + langindex;
            
            p.insertBefore(check, p.firstChild);
            
            td2.appendChild(p);
            
            langindex++;
        }
    }
});
</script>
<?php
}else{
    error_reporting(E_ALL);
    $title = $_POST['title'];
    $type = $_POST['type'];
    $desc = $_POST['desc'];
    $date = time();
    $userq = $sql->dbh->prepare("SELECT `id` FROM `".$sql->tbl["users"]."` WHERE username = '".$_COOKIE['username']."'");
    $userq->execute();
    $userid = $userq->fetch()['id'];
    $public = $_POST['public'];
    if($public == 'on'){
        $public = 1;
    }
    
    $q = $sql->dbh->prepare("INSERT INTO `".$sql->tbl["code"]."` (`title`, `user_id`, `timestamp`, `public`, `desc`, `type`) 
        VALUES (:title, :userid, :date, :public, :desc, :type)");
    $suc = $q->execute(array('title'=>$title, 'userid'=>$userid, 'date'=>$date, 'public'=>$public, 'desc'=>$desc, 'type'=>$type));
    
    if($suc == 1){
        $projectq = $sql->dbh->prepare("SELECT `id` FROM `".$sql->tbl["code"]."` WHERE `title` = '".$title."' AND `timestamp` = '$date'");
        $projectq->execute();
        $projectid = $projectq->fetch()['id'];
        for($c = 0;isset($_POST['lang'.$c]);$c++){
            if($_POST['lang'.$c] == "on"){
                $langid = $_POST['data'.$c];
                $qr = $sql->dbh->prepare("INSERT INTO `".$sql->tbl["languages"]."` (`languageid`, `userid`, `projectid`) 
                    VALUES (:languageid, :userid, :projectid)");
                $suc = $qr->execute(array('languageid'=>$langid, 'userid'=>0, 'projectid'=>$projectid));
                if($suc == 1){
                    header("Location: ?p=createcode&status=success");
                }else{
                    header("Location: ?p=createcode&status=failed");
                }
            }
        }
    }else{
        
    }
    
}
?>