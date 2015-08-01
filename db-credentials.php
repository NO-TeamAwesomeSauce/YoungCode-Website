<?php
$tbl = array();
$tbl['users'] = 'as_User';
$tbl['comments'] = 'as_Comment';
$tbl['locations'] = 'as_Location';
$tbl['code'] = 'as_Post';
$tbl['tags'] = 'as_Tagg';
$tbl['vote'] = 'as_Vote';
$tbl['languages_master'] = 'as_Language_master';
$tbl['languages'] = 'as_Language';
$tbl['ranks'] = 'as_Rank';
/*$tbl['code'] = 'teamawesomesauce-code';
$tbl['friends'] = 'teamawesomesauce-follow';
$tbl['language'] = 'teamawesomesauce-language';
$tbl['languages_master'] = 'teamawesomesauce-language_master';
$tbl['locations'] = 'teamawesomesauce-locations';
$tbl['login'] = 'teamawesomesauce-login';
$tbl['messages'] = 'teamawesomesauce-messages';
$tbl['notifications'] = 'teamawesomesauce-notifications';
$tbl['online'] = 'teamawesomesauce-online';
$tbl['projects'] = 'teamawesomesauce-projects';
$tbl['project_ranks'] = 'teamawesomesauce-project_ranks';
$tbl['strings'] = 'teamawesomesauce-strings';
$tbl['users'] = 'teamawesomesauce-users';*/

$dbhost = '';
$dbname = '';
$dbuser = '';
$dbpass = '';

$dbh = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $dbuser, $dbpass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
?>