<?php require_once 'header.php';//required to start all commmon header information
//Authors: Matthew Rowlandson, Calin Cohan , Kevin Kan
//Date: Dec 7 2013
//This is the index page for the blogging site. This page displays first ten blogs from most recent to oldest.

//TODO write query to get ten most recent blogs ?>
<div id='pageContent'>
<?php
//initially set where claus to empty string so that defaults to not logged in
$whereClause="";
$limitUser="LIMIT 10"; //default not signed in user then show limit of 10 entries
$listOfBlogs=array();
$userLoggedIn=(isset($_SESSION['password'])&&isset($_SESSION['userName']))?true:false;;
$numPages="";
$isNextPage=false;
$isPrevPage=false;
$userAccount="";
$blogId="";

//check number of pages possible
$countPageSql="SELECT CEIL(COUNT(blogId)/10) FROM blogTable";
$countPageQuery= $database->prepare($countPageSql);
$countPageQuery->execute();
while ($countPageRow = $countPageQuery->fetch(PDO::FETCH_ASSOC))
{
	$numPages=$countPageRow['CEIL(COUNT(blogId)/10)'];
}
if(($_GET['page']>=0)&&($_GET['page']<=$numPages)){
	$limitUser="LIMIT ".($_GET['page']*10).",10";
	if(($_GET['page']>0)&&($_GET['page']<$numPages)){$isNextPage=true;}
	if(($_GET['page']>1)&&($_GET['page']<=$numPages)){$isPrevPage=true;}
}
//add if statement to determine if the URL has both the ?account= "accountID" 
elseif(isset($_GET['account'])){
	$limitUser="LIMIT 1";
	$whereClause="WHERE accountID =".$_GET['account'];
}
//elseif user is logged in show only their blogs as default 
elseif($userLoggedIn){
	$limitUser="LIMIT 1";
	$getUserID="SELECT accountID FROM blogAccounts WHERE userName = '".$_SESSION['userName']."' AND  password ='".$_SESSION['password']."' LIMIT 1";
	$getUserQuery= $database->prepare($getUserID);
	$getUserQuery->execute();
	while ($accountIDRow = $getUserQuery->fetch(PDO::FETCH_ASSOC))
	{
		$whereClause="WHERE accountID =".$accountIDRow['accountID'];
		$userAccount=$accountIDRow['accountID'];	
	}
}
$initialSql="SELECT * FROM blogTable AS BT INNER JOIN blogAccounts AS BA ON BA.accountID = BT.ownerID ".$whereClause." ORDER BY dateCreated DESC ".$limitUser;
$initialQuery=$database->prepare($initialSql);
$initialQuery->execute();
if($initialQuery->rowCount()>=1){
	while ($rowInitial = $initialQuery->fetch(PDO::FETCH_ASSOC))
	{	
		$blogId=$rowInitial['blogID'];
		echo"<div class='recentBlogs'>";
			echo"<h3 class='blogTitle'>".$rowInitial['blogTitle']."</h3>";
			echo'<h4 class="blogAuthor"> by: '.$rowInitial['firstName'].' '.$rowInitial['lastName'].'</h4>';
			echo"<p>".$rowInitial['blogContent']."</p>";
		echo"</div>";
	}//end of while fetch row
	if($initialQuery->rowCount==1){
		$blogListSql="SELECT title,blogID FROM blogTable ".$whereClause;
		$blogListQuery = $database->prepare($blogListSql);
		$blogListQuery->execute();
		while ($rowBlogList = $blogListQuery->fetch(PDO::FETCH_ASSOC))
		{
			$listOfBlogs[$row['blogID']]= $row['title'];
		}
		echo"<div id='blogTitleList'>
				<ul>";
		foreach ($listOfBlogs as $blogIdKey => $blogListTile){
			echo"<li value='".$blogIdKey."'>".$blogListTile."</li>";
		}
		echo"</ul></div>";
	}//end if rowCount==1
}
else{
	echo"<div class='recentBlog'>No Blog created for this account.</div>";
}
//if user is logged in show post form 
if($userLoggedIn){
	//was a post submitted
	if(isset($_POST['submitPost'])){
		$blogTitle=trim($_POST['blogTitle']);
		$blogComment=trim($_POST['blogContent']);
		if((!empty($blogTitle))&&(!empty($blogComment))){	
			$submitPostSql="INSERT INTO blogPost(blogId,accountId,postTitle,postContent)VALUES (".$blogId.",".$userAccount.",".$blogTitle.",".$blogComment.")";
			$submilPostQuery= $database->prepare($submitPostSql);
			$submilPostQuery->execute();
		}
	}
	if($initialQuery->rowCount()==1){
		if($rowInitial['blogLock']==0){
			echo"<button name='showPost' id='showHidePostButton'>Post a Comment</button>";
			echo"<form name='postCommentOnBlog' action ='index.php' autocomplte='on' enctype='multipart/form-data'>
					<label for='postCommentTitle'>Post Title</label>
					<input type='text' name = 'postCommentTitle' required/>
					<label for='postComment'>Post Comment</label>
					<textarea name='postComment' maxlength='2500' required></textarea>
					<input type='submit' value='Post' name='submitPost'/>
				</form>";
		}
		
	}
}
if($initialQuery->rowCount()==1){
	//selects all old posts
	$postSql="SELECT postTitle,postContent FROM blogPost WHERE blogId=".$blogId." ORDER BY postTimeStamp DESC";
	$postQuery=$database->prepare($postSql);
	$postQuery->execute();
	echo"<div id='oldPosts'>";
	if($postQuery->rowCount()>=1){
		while ($rowPosts = $postQuery->fetch(PDO::FETCH_ASSOC))
		{
			echo"<div class='posts'><h4>".$rowPosts['postTitle']."</h4>
					<p>".$rowPosts['postContent']."</p></div>";
		}
	}
	else{}
echo"</div>";
}
echo($isNextPage)?"<button class='prevNextButtons' id='nextButton'>Next</button>":"";
echo($isPrevPage)?"<button class='prevNextButtons' id='PrevButton'>Prev</button>":"";
?>
</div>
<?php require_once 'footer.php';//required to close html?>