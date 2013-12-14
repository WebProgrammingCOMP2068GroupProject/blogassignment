<?php
/**Author Kevin Kan, Matt R. Calin C.
 * Date Dec 12 2013
 * Tis file edits and creates new blogs depending on the getter vars. 
 */
//include header
require_once 'header.php';
$userLoggedIn=(isset($_SESSION['password'])&&isset($_SESSION['userName']))?true:false;
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$page;

$blogTitle;
$blogDataId;
$blogContent;
$userAccount;
$newBlog=true;
$errMsg="";
$showForm=true;
$createBlogMsg="";
$error="";
$test="";

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
		$contentArray=explode("\n",trim($_POST['blogContent']));
		$blogContent="";
		$replaceTags=array("<p>","</p>","'","\"");
		$replaceWith=array("","","&prime;","&quot;");
		foreach($contentArray as $paragraph){
			$blogContent.="<p>".str_replace($replaceTags,$replaceWith,$paragraph)."</p>";
		}
		$errMsg.=(empty($userAccount)||$userAccount<=0)?"User Account,":"";
		$errMsg.=(empty($blogTitle))?"Blog Title is empty,":"";
		$errMsg.=(strlen($blogTitle)>100)?"Blog Title is too long. Title can only have 100 characters,":"";
		$errMsg.=(empty($blogContent))?"Blog Contents is empty":"";
		$errMsg.=(strlen($blogContent)>3000)?"Blog Contents too long. The Blog can only have 3000 characters":"";
		if($errMsg==""){
			$createBlogSql="INSERT INTO blogTable (ownerID,blogTitle,blogContent,blogLock) VALUES(".$userAccount.",'".$blogTitle."','".$blogContent."',0)";
			$createBlogQuery= $database->prepare($createBlogSql);
			$createBlogQuery->execute();
			$addLinkSql="SELECT blogID FROM blogTable WHERE blogLock!=2 ORDER BY dateCreated DESC LIMIT 1";
			$addLinkQuery=$database->prepare($addLinkSql);
			$addLinkQuery->execute();
			//echo"after".$createBlogSql;
			while ($addLinkRow = $addLinkQuery->fetch(PDO::FETCH_ASSOC))
			{
				$page = 'editBlog.php?editBlog='.$addLinkRow['blogID'].'&newBlog=true';
				header("Location: http://$host$uri/$page");
				exit();
			}
		}
		else{
			$error="<h3>An error involving ".$errMsg."has occured that is preventing the creation of a new blog. Please try again or contact the administrator </h3>";
		}
	}
	elseif($_POST['submit']=="Update Blog"){ 
		$blogTitle=trim($_POST['blogTitle']);
		$contentArray=explode("\n",trim($_POST['blogContent']));
		$blogContent="";
		$replaceTags=array("<p>","</p>");
		foreach($contentArray as $paragraph){
			$blogContent.="<p>".str_replace($replaceTags,"",$paragraph)."</p>";
		}
		$blogDataId=trim($_POST['editBlogID']);
	
		$errMsg.=(empty($userAccount)||$userAccount<=0)?"User Account,":"";
		$errMsg.=(empty($blogDataId))?" Blog Id,":"";
		$errMsg.=(empty($blogTitle))?" Blog Title,":"";
		$errMsg.=(empty($blogContent))?" Blog Contents":"";
		if($errMsg==""){
			$createBlogSql="UPDATE blogTable SET blogTitle='".$blogTitle."',blogContent='".$blogContent."'WHERE blogID=".$blogDataId." AND ownerID=".$userAccount;
			$createBlogQuery= $database->prepare($createBlogSql);
			$createBlogQuery->execute();
		}
		else{
			$error="<h3>An error involving ".$errMsg."has occured that prevents the update of the blog. Please try again or contact the administrator </h3>";
		}
	}
	
	if(isset($_GET['editBlog'])){
		if($_GET['newBlog']==true){
			$createBlogMsg="<h3 id='blogChangeMessage'>New Blog Created! Check it out <a href='index.php?blog=".$_GET['editBlog']."'>here</a></h3>";
		}
		elseif($_GET['newBlog']=="updated"){
			$createBlogMsg="<h3 id='blogChangeMessage'>Blog Updated</h3>";
		}
		$newBlog=false;
		$getEditBlogSql="SELECT * FROM blogTable AS BT INNER JOIN blogAccounts AS BA ON BA.accountID = BT.ownerID WHERE blogLock!=2 AND blogID=".$_GET['editBlog']." AND userName='".$_SESSION['userName']."' AND password='".$_SESSION['password']."'";
		//echo$getEditBlogSql;
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
		echo $test;
?>

<div id='pageContent'>
<h2><?php echo($newBlog)?"Create A Blog":"Update A Blog"; ?></h2>
<?php echo$createBlogMsg; echo$error; ?>
<form id="alterBlogForm" action='editBlog.php<?php echo (isset($_GET['editBlog']))?"?editBlog=".$_GET['editBlog']:""; echo(isset($_GET['newBlog']))?"&newBlog=".$_GET['newBlog']:""; ?>' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='editBlogID' value="<?php echo$blogDataId ?>"/>
<label for='blogTitle'>Blog Title</label>
<input type='text' name='blogTitle' value='<?php echo$blogTitle;?>' maxlength='100'/>
<label for='blogContent'>Blog Content</label>
<textarea name='blogContent' maxlength='3000'><?php echo$blogContent;?></textarea>
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
