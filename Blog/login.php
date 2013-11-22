<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>Blog Website</title>
<?php
	$dbc = mysqli_connect("webdesign4.georgianc.on.ca","db200240236","68069","db200240236");
	$expire= time()+60*60*24*1;
?>
<script type="text/javascript">
function registationMode(){
	signupWindow = window.open("signup.php",'Sign Up Form','height=350,width=350');
}
function signOut()
{
	
}
</script>
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/animate.css">
<link rel="stylesheet" href="css/styles.css">
</head>

<body>	
	<div id="container">
		<img src="imgs/loginTop.png" class="center" />
		<form action="" method="post" id="form">
		
		<label for="name">Username:</label>
		
		<input type="name" name="username">
		
		<label for="password">Password:</label>
		
		<input type="password" name="password">
		
		<div id="lower">
		<input type="submit" value="Login" name="Login">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="Register" onclick='registationMode()'  />
		</div>
		
		</form>
	</div>
<?php
if(isset($_POST['Login']))
{
    $crypted = hash('md5',$_POST['password']);
	$crypted = hash('sha256',$crypted);
	$ValidationQuery = "Select username from Accounts where password =".'"'.$crypted.'"';
	$Validation = mysqli_query($dbc,$ValidationQuery);
	if(mysqli_num_rows($Validation) >= 1)
	{
		setcookie("RSscriptUser",$_POST['username'],$expire);
		echo '<script type="text/javascript"> alert("login Successful") </script>';
		header("location: MainPage.php");
	}
	else
	{
		echo '<script type="text/javascript"> alert("Invalid Credentials") </script>';
	}
}
?>
</body>
</html>