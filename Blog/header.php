<?php
session_start();
//link a passord and username session
$_SESSION['password'];
$_SESSION['userName'];
$fileTypes=array("/.php$/","/.html$/");
$currentPage=preg_replace($fileTypes,'', basename($_SERVER['SCRIPT_NAME']));
 ?>
<!-- 
    Authors: Matthew Rowlandson, Calin Cohan, Kevin Kan
    File Description: This is the header for each page. It will be included in each file
    Date Created: 22/11/2013
-->
<!-- -------------------------------------------------------------------------------------- -->
<!DOCTYPE HTML>
<head>
    <meta charset="utf-8" lang="en" />
    <title>The Blogging Site</title>
    <!-- Stylesheet and Javascript Links-->
    <link href="css/mainStyles.css" rel="stylesheet" type="text/css"/>
    <?php
    //standard jquery script files
    $scriptFiles=array('http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js');
    //add external scripts
    foreach($scriptFiles as $scriptName){
    	echo"<script src='".$scriptName."' type='text/javascript'></script> \n";
    }
    if ($currentPage=="index"){echo"<script src='js/indexJs.js' type='text/javascript'></script>";}// if index base file load indexJs.js?>
    <script src="js/javascript.js"></script>
</head>
<?php require_once('php/dbConnection.php');?> <!-- Open the database connection-->
<a href="index.php"><img src="imgs/logo.png" alt="Logo"/></a> <!-- Change the logo here! -->
<?php 
if ((!empty($_SESSION['password'])) || (!empty($_SESSION['userName']))){
echo'<div id="userLogin">Welcome '.$_SESSION['userName'].'<a href="login.php">Log Out</a></div>';
echo'<nav>
		<ul>
			<li><a href="editBlog.php">Post a Blog</a></li> <!-- Post a blog -->
			<li><a href="manageBlog.php">Manage A Blog</a></li> <!-- Manage a blog -->
			<li><a href="profile.php">My Profile</a></li> <!-- My Profile -->
			<li><a href="contactus.php">Contact Us</a></li> <!-- Contact Us -->
		</ul>
	</nav>';
}
else{
	echo'<div id="userLogin">Welcome Guest <a href="login.php">Login</a></div>';
}
?>
<body>
<!-- -------------------------------------------------------------------------------------- -->