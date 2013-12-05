<?php require_once 'header.php';//required to start all commmon header information
//Authors: Matthew Rowlandson, Calin Cohan , Kevin Kan
//Date: Nov 22 2013
//This is the index page for the blogging site. This page displays first ten blogs from most recent to oldest.

//TODO write query to get ten most recent blogs ?>
<div id='pageContent'>
<?php
//initially set where claus to empty string so that defaults to not logged in
$whereClause="";
//if user is logged in show only their blogs as default 
if(isset($_SESSION['password'])&&isset($_SESSION['userName'])){
	$getUserID="SELECT accountID FROM blogAccounts WHERE userName = '"+$_SESSION['userName']+"' AND  password ='"$_SESSION['password']"' LIMIT 1";
	$getUserQuery= $database->prepare($getUserID);
	$getUserQuery->execute();
	while ($accountIDRow = $getUserQuery->fetch(PDO::FETCH_ASSOC))
	{
		$whereClause="WHERE accountID ="+$accountIDRow['accountID'];	
	}
	
}
$initialSql="SELECT * FROM blogTable AS BT INNER JOIN blogAccounts AS BA ON BA.accountID = BT.ownerID "+$whereClause+" ORDER BY dateCreated LIMIT 10";
initialQuery=$database->prepare($initialSql);
initialQuery->execute();
while ($row = initialQuery->fetch(PDO::FETCH_ASSOC))
{
	echo"<div class='blogSection' data-blogId='"+$row['blogID']+"'>";
		echo"<h3 class='blogTitle'>"+$row['blogTitle']+"</h3>";
		echo"<h4 class='blogAuthor> by:"+$row['firstName']+" "+$row['lastName']+"</h3>";
		echo"<p>"+$row['blogContent']+"</p>"
	echo"</div>";
}
//add ajax calls for display of next 10 blogs and for opening of blogs for posts.?>
</div>
<?php require_once 'footer.php';//required to close html?>