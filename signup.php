<?php
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: message.php?msg=NO to that weenis");
    exit();
}
?><?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("php_includes/db_con.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM users WHERE username='$username' LIMIT 1";
    $query = mysqli_query($db_con, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("php_includes/db_con.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST["u"]);
	$e = mysqli_real_escape_string($db_con, $_POST["e"]);
	$p = $_POST["p"];
	$g = preg_replace('#[^a-z]#', '', $_POST["g"]);
	$c = preg_replace('#[^a-z ]#i', '', $_POST["c"]);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM users WHERE username='$u' LIMIT 1";
    $query = mysqli_query($db_con, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_con, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($u == "" || $e == "" || $p == "" || $g == "" || $c == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		$cryptpass = crypt($p);
		include_once ("php_includes/randStrGen.php");
		$p_hash = randStrGen(20)."$cryptpass".randStrGen(20);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO users (username, email, password, gender, country, ip, signup, lastlogin, notescheck)       
		        VALUES('$u','$e','$p_hash','$g','$c','$ip',now(),now(),now())";
		$query = mysqli_query($db_con, $sql); 
		$uid = mysqli_insert_id($db_con);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_con, $sql);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
		}
		// Email the user their activation link
		$to = "$e";							 
		$from = "auto_responder@lattu.com";
		$subject = 'Lattu Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Lattu</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://localhost:28342/root/index.php"><img src="http://localhost:28342/root/images/logo70.png" width="36" height="30" alt="Lattu" style="border:none; float:left;"></a>Lattu Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$u.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://localhost:28342/root/activation.php?id='.$uid.'&u='.$u.'&e='.$e.'&p='.$p_hash.'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		$headers = "From: $from\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
		if(mail($to, $subject, $message, $headers)){
		    echo "signup success and please check your email for an activation link.";
		    exit();
		}else{    
		echo "signup success and you will get an activation link within one hour to your email Id";
		exit();
        }
	}
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>SignUP</title>
<link rel="stylesheet" href="style/style.css">
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
    function restrict(elem) {
        var tf = _(elem);
        var rx = new RegExp; ;
        if (elemm == "email") {
            rx = /[' "]/gi;
        } else if (elem == "username") {
            rx = /[^a-z0-9]/gi;
        }
        tf.value = tf.value.replace(rx, "");
    }

    function emptyElement(x) {
        _(x).innerHTML = "";
    }

    function checkusername() {
        var u = _("username").value;
        if (u != "") {
            _("unamestatus").innerHTML = 'checking ...';
            var ajax = ajaxObj("POST", "signup.php");
            ajax.send("usernamecheck=" + u);
            ajax.onreadystatechange = function () {
                if (ajaxReturn(ajax) == true) {
                    _("unamestatus").innerHTML = ajax.responseText;
                }
            }

        }
    }

    function openTerms() {
        _("terms").style.display = "block";
        emptyElement("status");
    }

    function signup() {
        var u = _("username").value;
        var e = _("email").value;
        var p1 = _("pass1").value;
        var p2 = _("pass2").value;
        var c = _("country").value;
        var g = _("gender").value;
        var status = _("status");
        if (u == "" || e == "" || p1 == "" || p2 == "" || c == "" || g == "") {
            status.innerHTML = "Fill out all of the form data";
        } else if (p1 != p2) {
            status.innerHTML = "Your password fields don't match";
        } else if (_("terms").style.display.none == "none") {
            status.innerHTML = "Please view the terms of use";
        } else {
            _("signupbtn").style.display = "none";
            status.innerHTML = 'please wait ...';
            var ajax = ajaxObj("POST", "signup.php");
            ajax.send("u=" + u + "&e=" + e + "&p=" + p1 + "&c=" + c + "&g=" + g);
            ajax.onreadystatechange = function () {
                if (ajaxReturn(ajax) == true) {
                    if (ajax.responseText != "signup_success") {
                        status.innerHTML = ajax.responseText;
                        _("signupbtn").style.display = "block";
                    } else {
                        window.scrollTo(0, 0);
                        _("signupform").innerHTML = "OK" + u + ", check your email."
                    }
                }
            }
        }
    }

    /*    function addEvents() {
    _("elemID").addEventListener("click", func, false);
    }

    window.onload = addEvents;
    */
</script>
<style type="text/css">
#signupform{
    margin-top: 24px;
}
#signupform >div{
    margin-top: 12px;
}
#signupform>input,select{
        width: 200px;
        padding: 3px;
        background: #F3F9DD;
}
#signupbtn{
   font-size: 18px;
   padding: 12px;     
}
#terms{
        border: #CCC 1px solid;
        background: #F5F5F5;
        padding: 12px;
    }
</style>
</head>

<body>
<?php
    include_once("template_PageTop.php");     
?>
<div id="pageMiddle">
    <h3>Sign Up Here</h3>
    <form name="signupform" id="signupform" onsubmit="return false">
        <div>Username: </div>
        <input id="username" class="text" onblur="checkusername()" onkeyup="restrict('username')" maxlength=16>
        <span id="unamestatus"></span>
        <div>Email Address: </div>
        <input id="email" class="text" type="text" onblur="emptyElement('status')" onkeyup="restrict('email')" maxlength=88>
        <div>Create Password: </div>
        <input id="pass1" type="password" onblur="emptyElement('status')"  maxlength=16>
        <div>Create Password: </div>
        <input id="pass2" type="password" onblur="emptyElement('status')"  maxlength=16>
        <div>Gender:</div>
        <select id="gender" onfocus="emptyElement('status')">
            <option value=""></option>
            <option value="m">Male</option>
            <option value="f">Female</option>
        </select>
        <div>Country:</div>
        <select id="country" onfocus="emptyElement('status')">
            <option value=""></option>
            <option value="india">India</option>
            <option value="usa">USA</option>
            <option value="england">England</option>
            <option value="australia">Australia</option>
            <option value="pakistan">Pakistan</option>
        </select>
        <div>
            <a href="#" onclick="return false" onmousedown="openTerms()">
                View the Terms of Use
            </a>
        </div>
        <div id="terms" style="display: none">
            <h3>Lattu Terms Of Use</h3>
            <p>1. play nice here.</p>
            <p>2. Take a bath before you visit.</p>
            <p>3. Brush your teeth before bed.</p>
        </div>
        <br/><br/>
        <button id="signupbtn" class="btn btn-primary" onclick="signup()">Create Account</button>
        <span id="status"></span>
    </form>
</div>   
<?php
    include_once("template_PageBottom.php");     
?>  
</body>

</html>
