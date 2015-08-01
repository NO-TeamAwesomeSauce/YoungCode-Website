<?php
$filename = 'locationdata.txt';
if(!isset($_GET['locationdata'])){
    die('you need to insert locationdata to be saved');
}

$content = $_GET['locationdata'].PHP_EOL;
$content .= file_get_contents($filename);

file_put_contents($filename, $content);



?>