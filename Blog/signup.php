<!DOCTYPE html>
<!-- Done by Calin Cohan.-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sign Up Form</title>
<style type="text/css">
	#newaccountform {
		display: block;
		margin-left:auto;
		margin-right:auto;
	}
	body { 
	background-color:#000;
	}
	label {
		color:#FFF;
	}
</style>
<?php $dbc =  mysql_connect("webdesign4.georgianc.on.ca","db200240236","68069");
mysql_select_db('db200240236', $dbc) or die('Could not select database.');
/*
Testing locally with wamp (no username and password on root user)

$con = mysql_connect("localhost","root" ,"");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

 Testing with Calin's webserver
 
 mysqli_connect("webdesign4.georgianc.on.ca","db200240236","68069","db200240236");
*/
 ?>
</head>
<body>
<div id = "container">
			<div id="newaccountform">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="menu">
			<label for="NewName"> Enter your name</label>
            <br/>
			<input type="text" name="NewName" id="NewName" />
            <br/>
			<label for="LastName"> Enter your last name</label>
            <br/>
			<input type="text" name="LastName" id="LastName" />
            <br/>
			<label for="NewUsername"> Enter your new username</label>
            <br/>
			<input type="text" name="NewUsername" id="NewUsername" />
            <br/>
			<label for="NewPassword"> Enter your password </label>
            <br/>
			<input type="text" name="NewPassword" />
            <br/>
			<label for="Email"> Enter your email</label>
            <br/>
			<input type="text" name="Email" id="Email" />
            <br/>
            <input type="submit" name="createAccount" />
			</form>
            </div>
		</div>
			<?php
            if(isset($_POST['createAccount']))
			{
				$crypted = hash('md5',$_POST['NewPassword']);
				$crypted = hash('sha256',$crypted);
				$createAccount = "Insert into blogAccounts (firstName, lastName, email, userName, password) values (".'"'.$_POST['NewName'].'"'.',"'.$_POST['LastName'].''.',"'.$_POST['Email'].'"'.',"'.$_POST['NewUsername'].'"'.',"'.$crypted.'")'; 
				//   '.',"'.$crypted.'"
				echo $createAccount;
				mysql_query($createAccount);
				echo "<script type='application/javascript'> alert('Your Account Has Been Created') </script>";
				echo "<script type='text/javascript'> window.opener = self; window.close(); </script>";
			}
            ?>
</body>
</html>