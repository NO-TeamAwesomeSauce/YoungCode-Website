<?php
error_reporting(E_ALL);
if(!isset($_GET['type'])){
    header('Location: ../');
}
require '../includes/sql.class.php';
$sql = new sql;

$t = $_GET['type'];
if($t == 'login'){
    $name = $_POST['user'];
    $pass = md5($_POST['pass']);
    
    $status = $sql->login($name, $pass);
    if($status == 1){
        header('Location: ../');
        echo 'success';
    }else{
        print_r($status);
    }
    
}else if($t == 'register'){
    $name = $_POST['user'];
    $pass = md5($_POST['pass']);
    $pass2 = md5($_POST['pass2']);
    $email = $_POST['email'];
    $age = $_POST['age'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $language = $_POST['language'];
    $programming = $_POST['programming'];
    $lat = floatval($_POST['lat']);
    $long = floatval($_POST['long']);
    
    $error = false;
    if($pass != $pass2){
        $error = true;
    }else{
        if($sql->register($name, $pass, $email, $age, $city, $postcode, $language, $programming, $lat, $long)){
            header("Location: ../?success=true");
        }else{
            header("Location: ../?success=false");
        }
    }
    
}else if($t == 'logout'){
    $sql->createCookie(false);
    header('Location: ../');
}

?>