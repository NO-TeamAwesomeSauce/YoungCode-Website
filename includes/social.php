<?php
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css" />
<script src="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js"></script>
<style>
#map{
    width: 700px;
    height: 100%;
}
</style>

<div id="mapparent"><div id="map"></div></div>
<i>All locations, except your location (if you have that enabled), are approximate to ensure your privacy if you do not want to share your absolute position.</i>
<script>
//o.LatLng {lat: 52.517539680234776, lng: -1.8980491161346433}
var map = L.map('map').setView([52.517539680234776, -1.8980491161346433], 12);

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

<?php
//$loc = array();
//$loc['jonatan'] = [52.517539680234776, -1.8980491161346433];
//echo '//'.round($loc['jonatan'][0], strlen($loc['jonatan'][0]) - 10).'
//';
//echo 'L.marker(['.round($loc['jonatan'][0], strlen($loc['jonatan'][0]) - 14).', '.round($loc['jonatan'][1], strlen($loc['jonatan'][1]) - 15).']).addTo(map).bindPopup("Jonatan\'s approximate location").openPopup();';
//echo 'L.marker(['.calculatelocation($loc['jonatan'][0],$loc['jonatan'][1]).']).addTo(map).bindPopup("Jonatan_98 &nbsp; - &nbsp; Age 16<br>Programming: PHP, HTML, CSS, JavaScript, Java (Android)<br>Last online: 24/06/1914")
//';


require 'db-credentials.php';
$st = $dbh->query("SELECT * FROM `".$tbl['locations']."`");
while($res = $st->fetch()){
    $ust = $dbh->query("SELECT * FROM `".$tbl['users']."` WHERE id = ".$res['userid']);
    $u = $ust->fetch();
    $lst = $dbh->query("SELECT m.name, m.id, l.projectid FROM `".$tbl['languages_master']."` m INNER JOIN `".$tbl['languages']."` l ON l.languageid=m.id WHERE l.userid = ".$res['userid']." AND l.projectid = 0");
    $langarr = array();
    while($lng = $lst->fetch()){
        array_push($langarr, $lng['name']);
    }
    $languages = implode(", ", $langarr);
    if($languages == ""){
        $languages = "Nothing :-(";
    }
    echo 'L.marker(['.calculatelocation($res['lat'],$res['long']).']).addTo(map).bindPopup("'.$u['username'].', '.$u['age'].' years old<br>Programming: '.$languages.'<br>Registered: '.date("d. \of F Y", $u['timestamp']).'");
';
}


?>

/*L.marker([52.517539680234776, -1.8980491161346433]).addTo(map)
    .bindPopup("Jonatan_98 &nbsp; - &nbsp; Age 16<br>Programming: PHP, HTML, CSS, JavaScript, Java (Android)<br>Last online: 24/06/1914").openPopup();
*/

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos){
        L.marker([pos.coords.latitude, pos.coords.longitude]).addTo(map).bindPopup("Your location").openPopup();
        //$('#city, #postcode').hide();
    });
} else {
    //$('#city, #postcode').show();
    console.log("disabled");
}

</script>


<?php
function calculatelocation($x, $y){
    if((strlen($x) > 13) && (strlen($y) > 13)){
        $roundx = round($x, strlen($x) - 13);
        $roundy = round($y, strlen($y) - 14);
    }else if((strlen($x) >= 3) && (strlen($y) >= 3)){
        $roundx = round($x, strlen($x) - 5);
        $roundy = round($y, strlen($y) - 5);
    }else{
        $roundx = $x;
        $roundy = $y;
    }
    
    if(!strpos($roundx, '.')){
        $roundx .= '.';
    }
    if(!strpos($roundy, '.')){
        $roundy .= '.';
    }
    
    $finalx = $roundx.rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $finaly = $roundy.rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    
    return $finalx.','.$finaly;
}
?>