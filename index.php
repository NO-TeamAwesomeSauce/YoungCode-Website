<?php
error_reporting(E_ALL);

//Include classes
require 'includes/sql.class.php';
$sql = new sql;
require 'includes/notifications.class.php';
$notifications = new notifications;

$pages = array('main'=>'Main', 'codehub'=>'Codehub', 'social'=>'Social', 'messages'=>'Messages','profile'=>'Profile');
$dropdowns = array('profile'=>'You are not logged in.<br><a id="loginbutton">Login</a> or <a id="registerbutton">register</a>');
if(isset($_COOKIE['username'])){
    $dropdowns['codehub'] = '<p><a data-href="codehub" style="width:initial;">View Codes</a></p><p><a data-href="createcode" style="width:initial;">Upload code</a></p>';
}

if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
    $sql->login($_COOKIE['username'], $_COOKIE['password']);
}

if(!isset($_GET['p'])){
    $current = 'Main';
}else{
    if(isset($pages[$_GET['p']])/* || (file_exists('includes/'.$_GET['p'].'.php')*/){
        //if(isset($pages[$_GET['p']]){
            $current = $pages[$_GET['p']];
        /*}else{
            $current = ucfirst($_GET['p']);
        }*/
    }else{
        $current = 'Main';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $current; ?> | Young Code</title>
    <link rel="shortcut icon" type="image/png" href="imgs/favicon.png"/>
    
    <style>
    * {margin:0; padding:0;}
    @font-face {
        font-family: bodoni;
        src: url(fonts/BodoniXT.ttf);
    }
    @font-face {
        font-family: dayrom;
        src: url(fonts/DAYROM__.ttf);
    }
    @font-face {
        font-family: ubuntu;
        src: url(fonts/Ubuntu-L.ttf);
    }
    @font-face {
        font-family: cabin;
        src: url(fonts/Cabin-Regular.otf);
    }
    html, body{
        height:100%;
        background-color: #DDD;
    }
    
    h2{font-family: cabin;}
    
    #DivPage{
        width:700px;
        min-height:200px;
        margin: 50px auto 0 auto;
        display:block;
        position:relative;
        font-family: dayrom;
    }
    #DivPage a{
        display:inline;
    }
    
    #gradient{
        width:100%;
        height:5px;
        background: linear-gradient(to bottom, /*#FFCC99*/  #8985B7  /* #1984CC */ , transparent);
        position:fixed;
        top:40px;
        left:0;
    }
    #DivHeader{
        width:100%;
        height:40px;
        //background:#8985B7;
        background-color: #1984CC;
        display:block;
        position:fixed;
        top:0;
        left:0;
    }
	#DivHeader img{
		position:absolute;
		left:10px;
	}
    #DivHeader #menuitems{
        width:700px;
        display:inline-block;
        position:absolute;
		margin: 0 auto;
		left:0; right:0;
        height:100%;
		float:right;
    }
    #DivHeader #menuitems a{
        height:100%;
        display:inline-block;
        position:relative;
        line-height:40px;
        font-size:17px;
        width:20%;
        text-align:center;
        cursor:pointer;
        background-color: #1984CC;
        transition: color 1s ease;
        color:white;
        text-decoration:none;
        font-family: cabin;
    }
    #DivHeader #menuitems a:hover{
        color:#222;
    }
    
    #DivPage #Posts{
        min-height:700px;
        background:#DDD;
    }
    #leftpane{
        width: 250px;
        height:100%;
        left:0;
        top: 0;
        position:fixed;
        box-shadow: 0 0px 10px #AAF;
        background:#E1E1E1;
        overflow-y:scroll;
        direction:rtl;
    }
    #leftpane div{direction:ltr;}
    #leftpane #Sort{
        min-height:200px;
        background:#EEE;
    }
    #leftpane #Notifications{
        min-height:200px;
        background-color: #E1E1E1;
    }
    
    
    .dropdown{
        width:250px;
        position:absolute;
    }
    .dropdown #arrow{
        display:block;
        width: 0; 
        height: 0; 
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        
        border-bottom: 15px solid #1984CC;
        margin-left:250 - 82.5px;
        margin-left: 152.5px;
        //position:absolute;
    }
    .dropdown #main{
        display:block;
        background-color:#1984CC;
        border-radius:10px;
        min-height:100px;
        padding:10px;
        font-family: ubuntu;
    }
    
    .dropdown #main a{
        display:inline;
        font-size: initial;
        text-decoration: underline;
        line-height:initial;
        background-color: initial;
        transition: color 0.7s ease;
    }
    
    
    .noselect{
        -webkit-user-select: none;
         -moz-user-select: -moz-none;
          -ms-user-select: none;
              user-select: none;
    }
    
    
    
    #login{
        position:fixed;
        margin:0 auto;
        width:400px;
        height:300px;
        left:0; right:0;
        top:70px;
        background:#8985C7;
        padding:10px;
        border-radius: 15px;
        box-shadow: 0 0 15px #000;
        font-size:12pt;
    }
    #login p{
        margin-bottom:10px;
    }
    #login table{
        width:100%;
    }
    #login table tr{
        
    }
    #login table tr td{
        
    }
    #login table tr td input{
        font-size:12pt;
        padding:5px;
        width:96%;
    }
    #login table tr td button{
        width:100%;
        padding:5px;
        font-size:12pt;
        cursor:pointer;
        margin-top:10px;
    }
    #mapparent{
        width:700px;
        height:500px;
    }
    
    #codediv p, #commentdiv, #commentdiv textarea, #commentdiv button{
        font-family: dayrom;
    }
    #commentdiv h2, #codediv h2, #commentdiv h2{
        font-family:cabin;
    }
    </style>
    <!-- Dependencies -->
    <?php $loc = 'http://norbye.com/Addon$/'; ?>
    <link href="<?php echo $loc; ?>jquery-ui-1.10.4/css/ui-lightness/jquery-ui-1.10.4.custom.css" rel="stylesheet">
	<script src="<?php echo $loc; ?>jquery-ui-1.10.4/js/jquery-1.10.2.js"></script>
	<script src="<?php echo $loc; ?>jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.js"></script>
    <script src="<?php echo $loc; ?>Alertify/lib/alertify.min.js"></script>
    <link rel="stylesheet" href="<?php echo $loc; ?>Alertify/themes/alertify.core.css" />
    <link rel="stylesheet" href="<?php echo $loc; ?>Alertify/themes/alertify.default.css" />
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css" />
    <script src="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js"></script>
</head>
<body>
<div id="DivPage">
    <?php
    //echo md5("birdie");
    if(!isset($_GET['p'])){
        require 'includes/main.php';
    }else if(file_exists('includes/'.$_GET['p'].'.php')){
        require 'includes/'.$_GET['p'].'.php';
    }else{
        echo 'Error';
    }
    ?>
</div>
<div id="leftpane">
    <div id="Sort">
        
    </div>
    <div id="Notifications">
        <?php
        if(isset($_COOKIE['username'])){
            displayNotification('Success', 'You are now logged in', 'success');
        }
        
        
        function displayNotification($title, $content, $type){
            if($title == 'Success'){
                $col = 'lime';
            }else{
                $col = 'red';
            }
            echo '<div class="notificationleft" style="float:left;">'.$title.' - '.$content.'</div><div style="width:50px;height:50px;border-radius:25px;background-color:'.$col.';float:right;"></div>';
        }
        ?>
    </div>
</div>
<div id="gradient"></div>
<?php
echo '<div id="DivHeader"><img src="imgs/YC1.png" style="height:40px;" /><div id="menuitems">';

foreach($pages as $page){
    echo '<a data-href="'.array_search($page, $pages).'" class="noselect">'.$page.'</a>';
    if(array_search($page, $pages) == 'profile'){
        if($sql->status()){
            $str = 'Hello '.$_COOKIE['username'].'<br><a href="m/verify.php?type=logout">Logout</a>';
        }else{
            $str = 'You are not logged in.<br><a id="loginbutton">Login</a> or <a id="registerbutton">register</a>';
        }
        echo '<div class="dropdown" style="display:none;"><div id="arrow"></div><div id="main">'.$str.'</div></div>';
    }else if(isset($dropdowns[array_search($page, $pages)])){
        echo '<div class="dropdown" style="display:none;"><div id="arrow"></div><div id="main">'.$dropdowns[array_search($page, $pages)].'</div></div>';
    }
}
echo '</div></div>';
?>

<div id="login" style="display:none;overflow-y:hidden;">
<p></p>
<form action="m/verify.php" method="POST">
<table>

<tr id="user"><td>Username</td><td><input name="user" type="text" maxlength="200" placeholder="" /></td></tr>
<tr id="pass"><td>Password</td><td><input name="pass" type="password" maxlength="200" placeholder="" /></td></tr>

<tr class="register" style="display:none;" id="pass2"><td>Repeat password</td><td><input type="password" name="pass2" maxlength="200" placeholder="" /></td></tr>
<tr class="register" style="display:none;" id="email"><td>Email</td><td><input type="text" name="email" maxlength="200" placeholder="" /></td></tr>
<tr class="register" style="display:none;" id="age"><td>Age</td><td><input type="number" name="age" maxlength="200" placeholder="" /></td></tr>
<tr class="register" style="display:none;" id="city"><td>City</td><td><input type="text" name="city" maxlength="200" placeholder="" /></td></tr>
<tr class="register" style="display:none;" id="postcode"><td>Postcode</td><td><input type="number" name="postcode" maxlength="200" placeholder="" /></td></tr>
<!--<tr class="register" style="display:none;" id="location"><td>Location: </td></-->
<tr class="register" style="display:none;" id="language"><td>Language (English, Norwegian, etc.)</td><td><input name="language" type="text" maxlength="200" placeholder="" /></td></tr>
<tr class="register" style="display:none;" id="programming"><td>Programming languages</td><td><input type="text" name="programming" maxlength="200" placeholder="PHP, Java, Javascript, etc." /></td></tr>
<tr><td colspan=2><button>Login</button></td></tr>

</table>
<input type="hidden" id="lat" name="lat" value="" />
<input type="hidden" id="long" name="long" value="" />
</form>
<b></b>
</div>
<script>
$(document).ready(function(){
  //Set the leftpane outside the page
    $('#leftpane').css('left', '-225px');
    
  //Set the menu to absolute if screen be too small
    if($(window).width() <= 900){
        $('#DivHeader').css('position', 'absolute');
        $('#gradient').css('position', 'absolute');
    }
    
  //Set the width of notification elements
    $('.notificationleft').width(200 - getScrollBarWidth() + 'px');
    
  //jQuery dropdown menus
    $('#DivHeader a, .dropdown').mouseover(function(e){
      //Check if the next sibling of the element is a dropdown menu
        if($(this).next().attr('class') == 'dropdown'){
          //Move the dropdown menu to underneath the hovered item
            $(this).next().stop();
            if($(this).prop("tagName") == "A"){
                var distance = $(this).position().left - $(this).scrollLeft() - $(this).first().width()/2;
            }
            $(this).next().css('marginLeft', distance + 'px');
            $(this).next().slideDown();
      //Keep the dropdown open when it is hovered
        }else if($(this).attr('class') == 'dropdown'){
            $(this).stop();
            $(this).slideDown();
        }
    });
    $('#DivHeader a, .dropdown').mouseout(function(e){
      //Check if the next sibling of the element is a dropdown menu
        if($(this).next().attr('class') == 'dropdown'){
          //Stop current animations, if not it would start to scroll up and down when mouse has already left
            $(this).next().stop();
            $(this).next().slideUp(function(){
              //Ensure that the element is not visible when the slideUp is complete
                $(this).css('display', 'none');
            });
      //Close the dropdown if mouse leaves it
        }else if($(this).attr('class') == 'dropdown'){
            $(this).stop();
            $(this).slideUp(function(){
              //Ensure that the element is not visible when the slideUp is complete
                $(this).css('display', 'none');
            });
        }
    });
    
  //jQuery animate the leftpane slide out
    $('#leftpane').mouseover(function(e){
        $(this).stop();
        $(this).animate({left: '0px'});
    });
    $('#leftpane').mouseout(function(e){
      //Stop current animations, if not it would start to scroll to the sides when mouse has already left
        $(this).stop();
        $(this).animate({left: '-225px'});
    });
    
  //Activate the menu buttons
    $('#DivHeader a').click(function(e){
        if($(this).attr('data-href') != null){
            if($(this).attr('data-href') == 'createcode'){
                window.location.href = "?p=createcode";
            }
            $('#DivPage').html('<b>Loading content... Please be patient</b>');
            
            var url = 'includes/' + $(this).attr('data-href') + '.php';
            if(UrlExists(url)){
                $.ajax({
                  url: url,
                  beforeSend: function( xhr ) {
                    xhr.overrideMimeType( "text/plain; charset=UTF-8" );
                  }
                })
                  .done(function( data ) {
                    if ( console && console.log ) {
                      $('#DivPage').html(data);
                    }
                  });
                window.history.pushState({"html":'?p=' + $(this).attr('data-href'),"pageTitle": $(this).attr('data-href')},"", '?p=' + $(this).attr('data-href'));
                document.title = $(this).html() + ' | Young Code';
                
            }else{
                console.log('The file you\'re trying to reach does not exist');
            }
        }
    });
    
  //Activate the login button
    $('#loginbutton').click(function(e){
        $('.register').hide();
        $('#login').css('overflow-y', 'hidden');
        $('#login button').html('Login');
        $('#login p').html('Login to our pleasant service');
        $('#login').height('initial');
        $('#login').fadeIn(500);
        $('#login form').attr("action", "m/verify.php?type=login");
        if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos){
                    $("#lat").val(pos.coords.latitude);
                    $("#long").val(pos.coords.longitude);
                    $('#city, #postcode').hide();
                });
            } else {
                
            }
    });
  //Activate the register button
    $('#registerbutton').click(function(e){
        $('.register').show();
        $('#login form').attr("action", "m/verify.php?type=register");
        $('#login button').html('Register');
        $('#login p').html('Register in our pleasant service');
        var height = $(window).height() - $('#login').css('top').replace(/\D/g,'') - 100;
        $('#login').css('height', height + 'px');
        //console.log($(window).height() + '-' + $('#login').css('top').replace(/\D/g,'') + '-' + 100);
        if($('#login')[0].scrollHeight > height){
            $('#login').css('overflow-y', 'scroll');
        }else{
            $('#login').css('overflow-y', 'hidden');
            $('#login').css('height', 'initial');
        }
        $('#login').fadeIn(500);
      //Geolocation data
        getLocation();
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos){
                    $("#lat").val(pos.coords.latitude);
                    $("#long").val(pos.coords.longitude);
                    $('#city, #postcode').hide();
                });
            } else {
                $('#city, #postcode').show();
            }
        }
    });
    
    $(document).mouseup(function (e)
{
        var container = $("#login");
        var container2 = $("#loginbutton, #registerbutton");

        if ((!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
              && (!container2.is(e.target)))
        {
            container.fadeOut(500);
        }
    });
    
  //Login system
    $('#login button').click(function(e){login(e);});
    
    $('#login input').on('keyup',function(e){
    if(e.which == 13 || e.type == 'dblclick') login(e);
  });
    
    function login(e){
        if($('#login button').html() == 'Login'){
            //alert('at least you tried to login');
            /*var user = $('#user').children()[1].children[0].value;
            var pass = $('#pass').children()[1].children[0].value;
            console.log(user + '-' + pass)
            if((user != '') && (pass != '') && (user.length > 2) && (pass.length > 5)){
                $('#login b').html("");
                window.location.href = 'm/verify.php?type=login&name=' + user + '&pass=' + pass;
            }else{
                if((user == '') || (pass == '')){
                    $('#login b').html('You have to insert a username and password');
                }else if((user.length < 2) || (pass.length < 5)){
                    $("#login b").html('Usernames have to be 3 characters or longer<br>Passwords have to be 6 characters or longer');
                }else{
                    $('#login b').html('wat');
                }
                $('#login b').css('color', 'red');
            }*/
        }else{
            /*var user = $('#user').children()[1].children[0].value;
            var pass = $('#pass').children()[1].children[0].value;
            var pass2 = $('#pass2').children()[1].children[0].value;
            var email = $('#email').children()[1].children[0].value;
            var age = $('#age').children()[1].children[0].value;
            var city = $('#city').children()[1].children[0].value;
            var postcode = $('#postcode').children()[1].children[0].value;
            var language = $('#language').children()[1].children[0].value;
            var programming = $('#programming').children()[1].children[0].value;
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos){
                    var lat = pos.coords.latitude;
                    var longi = pos.coords.longitude;
                });
            } else {
                var lat = '';
                var longi = '';
            }
            
            var vars = [user, pass, pass2, email, age, city, postcode, language, programming];
            var noinput = false;
            for(var f = 0;f<vars.length;f++){
                if(vars[f] == ''){
                    if((vars[f] != city) && (vars[f] != postcode)){
                        noinput = true;
                    }
                }
            }
            if(!noinput){
                //No errors <3
                window.location.href = 'm/verify.php?type=register&user=' + user + '&pass=' + pass + '&pass2=' + pass2 + '&email=' + email + 
                '&age=' + age + '&city=' + city + '&postcode=' + postcode + '&language=' + language + '&programming=' + programming + 
                '&lat=' + lat + '&long=' + longi;
            }else if(noinput){
                //You need to fill inn all data u fgt
                
            }else{
                //wat
                
            }*/
        }
    }
});

function UrlExists(urlToFile){
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', urlToFile, false);
    xhr.send();
     
    if (xhr.status == "404") {
        return false;
    } else {
        return true;
    }
}

function getScrollBarWidth () {
  var inner = document.createElement('p');
  inner.style.width = "100%";
  inner.style.height = "200px";

  var outer = document.createElement('div');
  outer.style.position = "absolute";
  outer.style.top = "0px";
  outer.style.left = "0px";
  outer.style.visibility = "hidden";
  outer.style.width = "200px";
  outer.style.height = "150px";
  outer.style.overflow = "hidden";
  outer.appendChild (inner);

  document.body.appendChild (outer);
  var w1 = inner.offsetWidth;
  outer.style.overflow = 'scroll';
  var w2 = inner.offsetWidth;
  if (w1 == w2) w2 = outer.clientWidth;

  document.body.removeChild (outer);

  return (w1 - w2);
};
</script>
</body>
</html>