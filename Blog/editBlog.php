<?php
/**Author Kevin Kan
 * Date Dec 12 2013
 * Tis file edits and creates new blogs depending on the getter vars. 
 */
//include header
require_once 'header.php';
$blogTitle;
$blogDataId;
$blogContent;
$userAccount;
$userLoggedIn=(isset($_SESSION['password'])&&isset($_SESSION['userName']))?true:false;
$newBlog=true;
$errMsg="";
$showForm=true;

if($userLoggedIn){
	$getUserID="SELECT accountID FROM blogAccounts WHERE userName = '".$_SESSION['userName']."' AND  password ='".$_SESSION['password']."' LIMIT 1";
	$getUserQuery= $database->prepare($getUserID);
	$getUserQuery->execute();
	while ($accountIDRow = $getUserQuery->fetch(PDO::FETCH_ASSOC))
	{
		$userAccount=$accountIDRow['accountID'];
	}
	if($_POST['submit']=="Create Blog"){
		$blogTitle=trim($_POST['blogTitle']);
		$blogContent=trim($_POST['blogContent']);
		$errMsg.=(empty($userAccount)||$userAccount<=0)?"User Account,":"";
		$errMsg.=(empty($blogTitle))?"Blog Title,":"";
		$errMsg.=(empty($blogContent))?"Blog Contents":"";
		if($errMsg==""){
			$createBlogSql="INSERT INTO blogTable (ownerID,blogTitle,blogContent,blogLock) VALUES(".$userAccount.",'".$blogTitle."','".$blogContent."',0)";
			$createBlogQuery= $database->prepare($createBlogSql);
			$createBlogQuery->execute();
			echo"<h3>New Blog Created</h3>";
		}
		else{
			echo"<h3>An error involving ".$errMsg."has occured that is preventing the creation of a new blog. Please try again or contact the administrator </h3>";
		}
	}
	elseif($_POST['Update Blog']){
		$blogTitle=trim($_POST['blogTitle']);
		$blogContent=trim($_POST['blogContent']);
		$blogDataId=trim($_POST['editBlogID']);
		
		$errMsg.=(empty($userAccount)||$userAccount<=0)?"User Account,":"";
		$errMsg.=(empty($blogDataId))?" Blog Id,":"";
		$errMsg.=(empty($blogTitle))?" Blog Title,":"";
		$errMsg.=(empty($blogContent))?" Blog Contents":"";
		
		if($errMsg==""){
			$createBlogSql="UPDATE blogTable SET blogTitle='".$blogTitle."',blogContent='".$blogContent."'WHERE blogID=".$blogDataId." AND ownerID=".$userAccount;
			$createBlogQuery= $database->prepare($createBlogSql);
			$createBlogQuery->execute();
			echo"<h3>Blog Updated</h3>";
		}
		else{
			echo"<h3>An error involving ".$errMsg."has occured that prevents the update of the blog. Please try again or contact the administrator </h3>";
		}
	}
	if(isset($_GET['editBlog'])){
		$newBlog=false;
		$getEditBlogSql="SELECT * FROM blogTable AS BT INNER JOIN blogAccounts AS BA ON BA.accountID = BT.ownerID WHERE blogLock!=2 AND blogID=".$_GET['editBlog']."AND userName='".$_SESSION['userName']."' AND password='".$_SESSION['password']."'";
		$getEditBlogQuery = $database->prepare($getEditBlogSql);
		$getEditBlogQuery->execute();
		while ($blogData = $getEditBlogQuery->fetch(PDO::FETCH_ASSOC))
		{
			$blogTitle=$blogData['blogTitle'];
			$blogContent=$blogData['blogContent'];
			$blogDataId=$blogData['blogID'];
		}
		if($getEditBlogQuery->rowCount()<=0){
			$showForm=false;
		}
	}
	if($showForm){
?>
<div id='pageContent'>
<form action='editBlog.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='editBlogID' value="<?php echo$blogDataId ?>"/>
<label></label>
<input type='text' name='blogTitle' value='<?php echo$blogTitle;?>' maxlength='100'/>
<label></label>
<textarea name='blogContent' value='<?php echo$blogContent;?>' maxlength='3000'></textarea>
<input type="submit" name='submit' value='<?php echo($newBlog)?"Create Blog":"Update Blog"; ?>'/>
</form>
</div>
<?php 
	}
	else{
		echo"<h3>Sorry you don't have the rights to edit this blog</h3>";
	}
}
else{
	echo"<h2>You need to <a href='login.php'>login</a> first</h2>";
}
//include footer
require_once 'footer.php';
?>
