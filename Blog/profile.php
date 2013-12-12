<?php
SESSION_START();
//Matthew Rowlandson is doing this file!
require_once('header.php');
$database; //this is the pdo btw
$SESSION["username"] = "matt_94101";
$SESSION["password"] = "tester123";
$SESSION["password"] = hash('sha256',(hash('md5',$_SESSION["password"])));
//displays the users information from blogAccounts. EDIT functions
//Username: _________
$username = $SESSION["username"];
if($SESSION["username"] == "")
{
    echo "<br> You Shouldnt Be Here <br>";
    echo "Please login";?> <a href='login.php'>here</a> <?php
}
if($SESSION["username"] != "")
{
    echo '<p>Current user is: '.$username.'</p>';
    //This is a form
    ?>
    <h2>Change Your Password!</h2>
    <form action="<?php echo $PHP_SELF; ?>" method="post">
         Your Current Password:<br>
        <input type="text" name="current"><br>
        Your New Password:<br>
        <input type="text" name="newpass"><br>
        Retype New Password<br>
        <input type="text" name="renewpass"><br>
        <input type="submit" value="Change Password">
    </form>
    <?php
    //check if entered password is equal to the password in the current session
    if(isset($_POST["current"]))
    {
        $currentPass = hash('sha256',(hash('md5',$_POST["current"])));
    }
    //if incorrect...
    if($currentPass != $SESSION["password"] )
    {
        echo "Your current password you entered is incorrect!";
    }
    //if correct (check if new passwords entered match. If so, update in db.
    if($currentPass == $SESSION["password"])
    {
        if($_POST["newpass"] == $_POST["renewpass"])
        {
            $newpass = hash('sha256',(hash('md5',$_POST["newpass"])));
            //This is the query to change the password
            $query = "UPDATE blog_accounts SET password = :newpass WHERE username = :username";
            //prepare the query but inputting the right values in (as stated in the bindParam)
            $stmt = $database->prepare($query);
            $stmt->bindParam(':newpass', $newpass);
            $stmt->bindParam(':username', $_SESSION["username"]);
            try
            {
                $stmt->execute;
            }
            catch(Exception $ex)
            {
                echo"There was an error updating your password".$ex;
             }
        }
        else
        {
            echo"New password does not match with retyped new password";
        }
    }
//    
//      -Current Password: ________
//      -New Password: _________
//      -Re-enter New Password: ________
//      //Change Password Button
//    Email: __________
//    FirstName: _______
//    LastName: ________
}
require_once('footer.php');
?>
