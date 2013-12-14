<?php
//Authors: Matthew Rowlandson, Calin Cohan , Kevin Kan
//Date: Dec 7 2013
//This is the index page for the blogging site. This page displays first ten blogs from most recent to oldest.

require_once 'header.php';//required to start all commmon header information
//initially set where claus to empty string so that defaults to not logged in
$whereClause="WHERE blogLock !=2 ";
$limitUser="LIMIT 10"; //default not signed in user then show limit of 10 entries
$listOfBlogs=array();
$userLoggedIn=(isset($_SESSION['password'])&&isset($_SESSION['userName']))?true:false;
$numPages="";
$isNextPage=true;
$isPrevPage=false;
$userAccount="";
$blogId="";
$indexUrl="http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\')."/index.php";
date_default_timezone_set('Canada/Eastern');

//check number of pages possible
$countPageSql="SELECT CEIL(COUNT(blogId)/10) FROM blogTable";
$countPageQuery= $database->prepare($countPageSql);
$countPageQuery->execute();
while ($countPageRow = $countPageQuery->fetch(PDO::FETCH_ASSOC))
{
	$numPages=$countPageRow['CEIL(COUNT(blogId)/10)'];
}
//if user is logged in get account number
if($userLoggedIn){
	$getUserID="SELECT accountID FROM blogAccounts WHERE userName = '".$_SESSION['userName']."' AND  password ='".$_SESSION['password']."' LIMIT 1";
	$getUserQuery= $database->prepare($getUserID);
	$getUserQuery->execute();
	while ($accountIDRow = $getUserQuery->fetch(PDO::FETCH_ASSOC))
	{
		$userAccount=$accountIDRow['accountID'];
	}
}
//add if statement to determine if the URL has the ?blog= "blogId"
if(isset($_GET['blog'])){
	$limitUser="LIMIT 1";
	$whereClause.="AND blogId =".$_GET['blog'];
}//see if user has selected a page for list of all blogs
elseif((isset($_GET['page']))&&($_GET['page']>=0)&&($_GET['page']<=$numPages)){
	$limitUser="LIMIT ".($_GET['page']*10).",10";
	$isNextPage=($_GET['page']<$numPages)?true:false;
	$isPrevPage=($_GET['page']>0)?true:false;
}
elseif($userLoggedIn){
	$limitUser="LIMIT 1";
	$whereClause.="AND accountID =".$userAccount;
}
if($_GET['view']=="allUsers"){
	$whereClause="WHERE blogLock !=2 ";
	$limitUser="LIMIT 10";
}
 ?>
<div id='pageContent'>
<?php
$isOpen=true;
$initialSql="SELECT * FROM blogTable AS BT INNER JOIN blogAccounts AS BA ON BA.accountID = BT.ownerID ".$whereClause." ORDER BY dateCreated DESC ".$limitUser;
//echo $initialSql;
$initialQuery=$database->prepare($initialSql);
$initialQuery->execute();
$initialQueryCount=$initialQuery->rowCount();
if($initialQueryCount>=1){
	while ($rowInitial = $initialQuery->fetch(PDO::FETCH_ASSOC))
	{	
		$blogId=$rowInitial['blogID'];
		$numChar=600;
		if($initialQueryCount!=1){
			$blogContent=(strlen($rowInitial['blogContent'])>$numChar)?substr($rowInitial['blogContent'],0,$numChar).'... <a href="'.$indexUrl.'/?blog='.$blogId.'">more &rsaquo;&rsaquo;</a>':$rowInitial['blogContent'];
		}
		else{
			$blogContent=$rowInitial['blogContent'];
		}
		echo"<div class='recentBlogs'>";
			echo"<h2 class='blogTitle'><a href='".$indexUrl."?blog=".$blogId."'>".$rowInitial['blogTitle']."</a></h2>";
			echo'<h3 class="blogAuthor"> By: '.$rowInitial['firstName'].' '.$rowInitial['lastName'].'</h3>';
			echo'<h3 class="blogDate"> Created: '.date('M j Y g:i A', strtotime($rowInitial['dateCreated'])).'</h3>';
			echo"<p>".$blogContent."</p>";
			echo($initialQueryCount==1)?"<button name='showPost' id='showHidePostButton'>View Comments</button>":"";
		echo"</div>";
		$isOpen=($rowInitial['blogLock']==0)?true:false;
	}//end of while fetch row
	if($initialQueryCount==1){
		$getAcountForBlogSql="SELECT ownerID FROM blogTable WHERE  blogLock !=2 AND blogID=".$blogId;
		//echo$getAcountForBlogSql;
		$getAcountForBlogQuery=$database->prepare($getAcountForBlogSql);
		$getAcountForBlogQuery->execute();
		$accountId;
		while($rowAccount = $getAcountForBlogQuery->fetch(PDO::FETCH_ASSOC)){
			$accountId=$rowAccount['ownerID'];
		}
		$blogListSql="SELECT blogTitle,blogID FROM blogTable WHERE  blogLock !=2 AND ownerID =".$accountId;
		//echo$blogListSql;
		$blogListQuery = $database->prepare($blogListSql);
		$blogListQuery->execute();
		$listOfBlogs;
		while ($rowBlogList = $blogListQuery->fetch(PDO::FETCH_ASSOC))
		{
			$listOfBlogs[$rowBlogList['blogID']]= $rowBlogList['blogTitle'];
		}
		echo"<div id='blogTitleList'>
			<h3>Blog Archive</h3>
				<ul>";
		foreach ($listOfBlogs as $blogIdKey => $blogListTile){
			echo"<li><a href='".$indexUrl."?blog=".$blogIdKey."'>".$blogListTile."</a></li>";
		}
		echo"</ul></div>";
	}//end if rowCount==1
}
else{
	echo"<div class='recentBlog'>No Blog created for this account.</div>";
}
//if only one blog entry
//echo"blog counter = ".$initialQueryCount;
if($initialQueryCount==1){
$isNextPage=false;
echo"<div id='allPostsContainer'>";
	if(($isOpen)&&($userLoggedIn)){//if the user is logged in and blog is not locked

		echo"<div id='postCommentForm'>
			<form name='postCommentOnBlog' >
				<input type='hidden' name='blogId'id='postBlogId' value='".$blogId."'/>
				<label for='postCommentTitle'>Post Title</label>
				<input type='text' name = 'postCommentTitle' required/>
				<label for='postComment'>Post Comment</label>
				<textarea name='postComment' maxlength='2500' required></textarea>
				<label for='securityText'>Prove Your not a bot, copy the text in the box</label>
				<canvas id='securityCanvas' width=\"200\" height=\"50\" style=\"border:1px solid #d3d3d3;\">
				Your browser does not support the HTML5 canvas tag.</canvas>
				<input type='text' name='securityText' />
				<button type='button' id='refreshSecurity' name='refreshSecurity'>Refresh Image</button>
				<button type='button' id='submitPostButton'>Post Comment</button>
			</form></div>";
	}
	//selects all old posts
	$postSql="SELECT postTitle,postContent FROM blogPost WHERE blogId=".$blogId." ORDER BY postTimeStamp DESC";
	$postQuery=$database->prepare($postSql);
	$postQuery->execute();
	echo"<div id='oldPosts'><h3>Comments</h3>";
	if($postQuery->rowCount()>=1){
		while ($rowPosts = $postQuery->fetch(PDO::FETCH_ASSOC))
		{
			echo"<div class='posts'><h4>".$rowPosts['postTitle']."</h4>
					<p>".$rowPosts['postContent']."</p></div>";
		}
	}
	else{echo"<h4>No comments exist for this blog.</h4>";}
	echo"</div>";
echo"</div>";
}
?>
<div id='pageButtons'>
<?php 
echo($isNextPage)?"<a href='".$indexUrl."?page=".($_GET['page']+1)."' class='prevNextButtons' id='nextButton'>Next</a>":"";
echo($isPrevPage)?"<a href='".$indexUrl."?page=".($_GET['page']-1)."'class='prevNextButtons' id='prevButton'>Prev</a>":"";
?>
</div>
</div>
<?php require_once 'footer.php';//required to close html?>