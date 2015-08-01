<?php
error_reporting(E_ALL);
?>
<style>
.input{
    width:100%;
    margin-bottom:30px;
    cursor:pointer;
}
.input:hover h2{
    text-decoration: underline;
}
h2{
    display:inline-block;
    text-align:left;
    width:50%;
    font-size:17pt;
    text-decoration: none;
}
i{
    display:inline-block;
    text-align:right;
    width:50%;
}
p{
    display:block;
}
</style>
<?php
require 'db-credentials.php';
$st = $dbh->prepare("SELECT * FROM `".$tbl['code']."` WHERE public = 1 ORDER BY id DESC");
$st->execute();

while($row = $st->fetch()){
    $upvotes = $dbh->query("SELECT COUNT(*) FROM `as_Vote` WHERE target = 'as_Post' AND `target_id` = '".$row['id']."'")->fetchAll()[0][0];
    $comments = $dbh->query("SELECT COUNT(*) FROM `as_Comment` WHERE `Project_id` = '".$row['id']."'")->fetchAll()[0][0];
    
    //Grab language
    $lst = $dbh->query("SELECT m.name,m.id,l.projectid FROM `".$tbl['languages_master']."` m INNER JOIN `".$tbl['languages']."` l ON l.languageid=m.id WHERE l.projectid = ".$row['id']);
    $langarr = array();
    while($lng = $lst->fetch()){
        array_push($langarr, $lng['name']);
    }
    $languages = implode(", ", $langarr);
    
    if($languages == ""){
        $languages = "No languages";
    }
    
    $desc = htmlentities($row['desc']);
    if(strlen($desc) > 300){
        $desc = substr($desc, 0, 300).'...';
    }
    
    echo "<div id='codediv' data-id='".$row['id']."' class='input'><h2>".escape($row['title'])."</h2><i>".$languages." - ".$comments." comments - ".$upvotes." upvotes</i><p>".escape($desc)."</p></div>";
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
$('.input').click(function(e){
    window.location.href = '?p=showcode&id=' + $(this).attr('data-id');
});
</script>