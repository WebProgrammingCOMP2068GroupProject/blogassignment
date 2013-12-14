<?php
/**Author Kevin Kan
 * Date Dec 13 2013
 * This file handles the management of blogs
 */
//include header
require_once 'header.php';

    //open, close, hide from public, update blog
$userLoggedIn=(isset($_SESSION['password'])&&isset($_SESSION['userName']))?true:false;
$userAccount;
$numberOfBlogs=0;
if($userLoggedIn){
	//if logged in get user info
	$getUserID="SELECT accountID FROM blogAccounts WHERE userName = '".$_SESSION['userName']."' AND  password ='".$_SESSION['password']."' LIMIT 1";
	$getUserQuery= $database->prepare($getUserID);
	$getUserQuery->execute();
	while ($accountIDRow = $getUserQuery->fetch(PDO::FETCH_ASSOC))
	{
		$userAccount=$accountIDRow['accountID'];
	}
	//if form is submitted
	if(isset($_POST['submit'])){
		for ($i = 0; $i<$_POST['numberOfBlogs']; $i++){
			$blogStatusSql="UPDATE blogTable SET blogLock =".$_POST['blogStatus'.$i]." WHERE blogID=".$_POST['blogStatusId'.$i]." AND ownerID =".$userAccount;
			echo"Query: ".$blogStatusSql." <br/>";
			$blogStatusQuery= $database->prepare($blogStatusSql);
			$blogStatusQuery->execute();
		}
	}
	
	$whereClause="WHERE blogLock !=2 AND accountID =".$userAccount;
	$getBlogSql="SELECT * FROM blogTable AS BT INNER JOIN blogAccounts AS BA ON BA.accountID = BT.ownerID ".$whereClause." ORDER BY dateCreated DESC ";
	?>
	
<div id='pageContent'>
<h2>Manage Your Blogs</h2>
	<?php
	$getBlogQuery=$database->prepare($getBlogSql);
	$getBlogQuery->execute();
	if($getBlogQuery->rowCount()>0){?>
	<p>Below is a list of all your blogs. Click the title of your blog to see the blog post. Under "Status" section select if you want to
open, close or delete your blog. Closed blogs can no longer be posted to unless they are turn back onto open. The final option for Blog Status is the delete checkbox.
If the delete status is selected and form submitted, the blog is deleted. The "Edit?" area alows you to edit the contents of your blog by clicking
on the link.</p>
	<form id='mangeBlogForm' action='manageBlog.php' method='POST' enctype='multipart/form-data'>
		<table>
		<thead>
			<tr>
				<th>Blog Title</th>
				<th>Blog Status</th>
				<th>Edit Blog</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			while ($blogDataRow = $getBlogQuery->fetch(PDO::FETCH_ASSOC))
			{
				$isOpen=($blogDataRow['blogLock']==0)?true:false;
				?>
				<tr>
	    				<td><a href='index.php?blog=<?php echo$blogDataRow['blogID'];?>'><?php echo$blogDataRow['blogTitle']?></a></td>
	    				<td>
	    				<input type="hidden" name='blogStatusId<?php echo$numberOfBlogs;?>' value="<?php echo$blogDataRow['blogID'];?>" />
	    					<select name='blogStatus<?php echo$numberOfBlogs;?>'>
	    						<option value='0' <?php echo($isOpen)?selected:null;?>>Open</option>
	    						<option value='1' <?php echo(!$isOpen)?selected:null;?>>Closed</option>
	    						<option value='2'>Delete</option>
	    					</select>
	    					
	    				</td>
	    				<td><a href="editBlog.php?editBlog=<?php echo$blogDataRow['blogID']; ?>">Edit</a></td>
	    		</tr><?php 
	    		$numberOfBlogs++;
			}
		echo"</tbody></table>
	    	<div><input type='hidden' name='numberOfBlogs' value='".$numberOfBlogs."'/>
 			<input type='submit' name='submit' value='Submit'/></div>
 		</form>";
	}
	else{
		echo"<h2>You have no blogs in this account. Click <a href='editBlog.php'>here</a> to create one </h2>";
	}
	 ?>
</div>
	<?php 
}
else{
	echo"<h2>You need to <a href='login.php'>login</a> first</h2>";
}
//include footer
require_once 'footer.php';
?>
