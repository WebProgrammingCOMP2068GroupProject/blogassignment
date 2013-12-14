<?php
SESSION_START();
//Matthew Rowlandson is doing this file!
require_once('header.php');
//DELETE after login page works...
//_______________________________________________________________________________________
$database; //this is the pdo btw
$SESSION["username"] = "matt_94101"; //testing purposes
$SESSION["password"] = "derp"; //more test purposes
$SESSION["password"] = hash('sha256',hash('md5',$SESSION["password"])); //MORE TESTS
//________________________________________________________________________________________
//displays the users information from blogAccounts. EDIT functions
$username = $SESSION["username"];
if($SESSION["username"] == "")
{
    echo "<br> You Shouldnt Be Here <br>";
    echo "Please login";?> <a href='login.php'>here</a> <?php
}
if($SESSION["username"] != "")
{
    echo '<p>Current user is: '.$username.'</p>';
    //Change Password Form
    ?>
    <h2>Change Your Password!</h2>
    <form id="passwordForm" action="<?php echo $PHP_SELF; ?>" method="post">
         Your Current Password:<br>
        <input type="password" name="current"><br>
        Your New Password:<br>
        <input type="password" name="newpass"><br>
        Retype New Password<br>
        <input type="password" name="renewpass"><br>
        <input type="submit" value="Change Password">
    </form>
    <?php
    //check if entered password is equal to the password in the current session
    if(isset($_POST["current"]))
    {
        $currentPass = hash('sha256',hash('md5',$_POST["current"]));
            //if incorrect...
        if($currentPass != $SESSION["password"] )
        {
            echo "Your current password you entered is incorrect! <br>";
        }//end of if(currentPassword does not equal session password)
    }//end of if(currentPassword is set)  
     //if correct (check if new passwords entered match. If so, update in db.
    if($currentPass == $SESSION["password"])
    {
        if($_POST["newpass"] == $_POST["renewpass"])
        {
            $newpass = hash('sha256',hash('md5',$_POST["newpass"]));
            //This is the query to change the password
            $query = "UPDATE blogAccounts SET password = ('$newpass') WHERE userName = '$username'";
            //prepare the query by inputting the right values in
            $stmt = $database->prepare($query);
            try
            {
                //execute query
                $stmt->execute(); //should there be an error it will jump to the catch
                echo "Your Password Has Been Updated"; //if all is well. Password has changed
                $SESSION["password"] = $newpass;
            }
            catch(Exception $ex)
            {
                echo "There was an error updating your password".$ex;
             }
        }//end of if (new pass = re new pass)
        else
        {
            echo "New password does not match with retyped new password <br>";
            echo "Please Try Again <br>";
        }// end of else
    }//end of if(current password == session password)
    ?>
    <h2> Change Your Email</h2>
    <form id="emailForm" action="<?php echo $PHP_SELF; ?>" method="post">
        New Email: <br>
        <input type="text" name="newemail"><br>
        <input type="submit" value="Change Email">
    </form>
    <?php
    if(isset($_POST["newemail"]))
    {
        $newEmail = $_POST["newemail"];
        
        //This is the query to change the email
        $queryEmail = "UPDATE blogAccounts SET email = ('$newEmail') WHERE userName = '$username'";
        //prepare the query by inputting the right values in
        $stmt2 = $database->prepare($queryEmail);
        try
        {
            //execute query
            $stmt2->execute(); //should there be an error it will jump to the catch
            echo "Your Email Has Been Updated To: ".$newEmail; 
         }
         catch(Exception $ex)
         {
         echo "There was an error updating your email".$ex;
         }
    }//end of if(isset newemail..)
}//end of Main If (if someone is logged in...)
require_once('footer.php');
?>
