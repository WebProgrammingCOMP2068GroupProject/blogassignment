<?php 
session_start();
$_SESSION['password'];
$_SESSION['userName'];

require_once('dbConnection.php');

$userAccount;
$getUserID="SELECT accountID FROM blogAccounts WHERE userName = '".$_SESSION['userName']."' AND  password ='".$_SESSION['password']."' LIMIT 1";
$getUserQuery= $database->prepare($getUserID);
$getUserQuery->execute();
while ($accountIDRow = $getUserQuery->fetch(PDO::FETCH_ASSOC))
{
	$userAccount=$accountIDRow['accountID'];
}
$postTitle=trim($_POST['postTitle']);
$postContent=trim($_POST['postContent']);
$blogId=$_POST['blogId'];
if((!empty($postTitle))&&(!empty($postContent))){
	$submitPostSql="INSERT INTO blogPost(blogId,accountId,postTitle,postContent)VALUES (".$blogId.",".$userAccount.",'".$postTitle."','".$postContent."')";
	$submilPostQuery= $database->prepare($submitPostSql);
	$submilPostQuery->execute();
}
//test run generic posts
/*
$submitPostSql="INSERT INTO blogPost(blogId,accountId,postTitle,postContent)VALUES (1,1,'Testing PHP2','The php script writing works again! YAY!')";
$submilPostQuery= $database->prepare($submitPostSql);
$submilPostQuery->execute();*/
?>	