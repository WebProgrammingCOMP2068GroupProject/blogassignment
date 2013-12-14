<?php
/**Authors Kevin Kan and Calin Cohan
 * Date Dec 13 2013
 * This is the login page to access regestered members content.
 */
session_set_cookie_params(0);
session_start();
//link a passord and username session
$_SESSION['password'];
$_SESSION['userName'];

require_once('php/dbConnection.php');
$errMsg="";

function login($loginName,$loginPassword){
	$_SESSION['userName']=$loginName;
	$_SESSION['password']=$loginPassword;
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$page = 'index.php';
	header("Location: http://$host$uri/$page");
	exit();
}

if(isset($_POST['Login']))
{
	$loginUser=trim($_POST['username']);
	$crypted = hash('md5',$_POST['password']);
	$crypted = hash('sha256',$crypted);
	$ValidationSql = "Select username from blogAccounts where password =".'"'.$crypted.'"'." AND userName='".$loginUser."' LIMIT 1";
	//echo $ValidationSql;
	$ValidationQuery = $database->prepare($ValidationSql);
	$ValidationQuery->execute();
	if($ValidationQuery->rowCount()==1){
		login($loginUser,$crypted);
	}
	else
	{
		$errMsg="<span class='errorMsg'>Sorry but your username and or password was inccorect. Please try again.</span>";
	}
}
elseif(isset($_POST['Register'])){
	//echo"register one: ".$_POST['newPassword']." two ".$_POST['confirmPassword'];
	if(($_POST['newPassword']==$_POST['confirmPassword'])&&(!empty($_POST['newPassword']))){
		$user=trim($_POST['newUserName']);
		$checkUserSql="SELECT userName FROM blogAccounts WHERE userName ='".$user."' LIMIT 1";
		$checkUserQuery= $database->prepare($checkUserSql);
		$checkUserQuery->execute();
		//echo$checkUserSql." \n";
		//echo$checkUserQuery->rowCount();
		if($checkUserQuery->rowCount()==0){
			$crypted = hash('md5',$_POST['newPassword']);
			$crypted = hash('sha256',$crypted);
			$firstName=trim($_POST['firstName']);
			$lastName=trim($_POST['lastName']);
			$email=trim($_POST['email']);
			$registerNewSql="INSERT INTO blogAccounts (firstName,lastName,email,userName,password) VALUES('".$firstName."','".$lastName."','".$email."','".$user."','".$crypted."')";
			$registerNewQuery= $database->prepare($registerNewSql);
			$registerNewQuery->execute();
			login($user,$crypted);
		}
		else{
			$errMsg="<span class='errorMsg'>Sorry but that user name is already in use. Please try again.</span>";
		}
	}
	else{
		$errMsg="<span class='errorMsg'>Sorry but your password did not match the confirmation password. Please try again.</span>";
	}
}
 ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>The Blogging Site</title>
<?php
if(!empty($_SESSION['userName'])&&!empty($_SESSION['password'])){
//echo "test emptying";
	$loginMessage= "<p>You've just logged out, log back in?</p>";
	unset($_SESSION['userName']);
	unset($_SESSION['password']);
}

?>

<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/animate.css">
<link rel="stylesheet" href="css/styles.css">
<?php  
$scriptFiles=array('http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js','js/loginJs.js');
    //add external scripts
    foreach($scriptFiles as $scriptName){
    	echo"<script src='".$scriptName."' type='text/javascript'></script> \n";
    }?>
</head>

<body>	
	<div id="container">
		<img src="imgs/loginTop.png" class="center" />
		<form action="login.php" enctype="multipart/form-data"  method="post" id="form">
		<?php echo$errMsg;?>
			<label for="name">Username:</label>
			<input type="name" name="username" value="<?php echo $loginUser;?>" required/>
			<label for="password">Password:</label>
			<input type="password" name="password" required/>
			<div id="lower">
			<input type="submit" value="Login" name="Login">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" id='toggleRegister' value="Register" />
			</div>
		</form>
	</div>
	<div id='registerForm'>
	<form action="login.php" enctype="multipart/form-data"  method="post" id="registerForm">
		<label for="firstName">First Name:</label>
		<input type="name" name="firstName" value="<?php echo$firstName;?>" required/>
		<label for="lastName">Last Name:</label>
		<input type="name" name="lastName" value="<?php echo$lastName;?>" required/>
		<label for="newUserName">Username:</label>
		<input type="name" name="newUserName" required/>
		<label for="newPassword">Password:</label>
		<input type="password" name="newPassword"  required/>
		<label for="confirmPassword">Confirm Password:</label>
		<input type="password" name="confirmPassword" required/>
		<label for="email">Email:</label>
		<input type="email" name="email" value="<?php echo$email;?>" required/>
		<div id="registerLower">
			<input type="submit" id="toggleLogin" value="Cancel"/>
			<input type="submit" name="Register" value="Register"/>
		</div>  
	</form>
	</div>

</body>
</html>