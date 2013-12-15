<?php
/*
 * Author: Matthew Rowlandson
 * Description: This is for the user to see their profile and to edit their password and email.
 */
//Matthew Rowlandson is doing this file!
require_once('header.php');
$userName = $_SESSION["userName"];
if($_SESSION["userName"] != "")
{
    //Grab the user's Information from database
        $emailQuery = "SELECT email FROM blogAccounts WHERE userName = '$userName'";
        $nameQuery = "SELECT firstName, lastName FROM blogAccounts WHERE userName = '$userName'";
            try
            {
                //queries
                $userEmail = $database->query($emailQuery);
                $usersName = $database->query($nameQuery);
                
                //Grab the value from the query for email
                foreach($userEmail as $row) {
                    $userEmail = $row["email"];
                }
                //grab the value from the query for name...
                foreach($usersName as $row)
                {
                    $usersName = $row["firstName"]." ".$row["lastName"];
                }
            }
            catch(Exception $ex)
            {
                echo "There was an error when grabbing your information from the database".$ex;
             }
    //Change Password Form
    ?>
    <div id='pageContent'>
        <h1>Profile Info:</h1>
        <?php 
            echo "User: ".$_SESSION["userName"]."<br>";
            echo "Email: ".$userEmail."<br>";
            echo "Name: ".$usersName."<br>";
            
        
        ?>
        <h2 id="passwordHeader">Change Your Password!</h2>
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
        if($currentPass != $_SESSION["password"] )
        {
            echo "Your current password you entered is incorrect! <br>";
        }//end of if(currentPassword does not equal session password)
    }//end of if(currentPassword is set)  
     //if correct (check if new passwords entered match. If so, update in db.
    if($currentPass == $_SESSION["password"])
    {
        if($_POST["newpass"] == $_POST["renewpass"])
        {
            $newpass = hash('sha256',hash('md5',$_POST["newpass"]));
            //This is the query to change the password
            $query = "UPDATE blogAccounts SET password = ('$newpass') WHERE userName = '$userName'";
            //prepare the query by inputting the right values in
            $stmt = $database->prepare($query);
            try
            {
                //execute query
                $stmt->execute(); //should there be an error it will jump to the catch
                echo "Your Password Has Been Updated"; //if all is well. Password has changed
                $_SESSION["password"] = $newpass;
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
        <h2 id="emailheader"> Change Your Email</h2>
        <form id="emailForm" action="<?php echo $PHP_SELF; ?>" method="post">
            New Email: <br>
            <input type="text" name="newemail"><br>
            <input type="submit" value="Change Email">
        </form>
    </div>
    <?php
    if(isset($_POST["newemail"]))
    {
        $newEmail = $_POST["newemail"];
        
        //This is the query to change the email
        $queryEmail = "UPDATE blogAccounts SET email = ('$newEmail') WHERE userName = '$userName'";
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
if(empty($_SESSION["userName"]))
{
    echo "<br> You Shouldnt Be Here <br>";
    echo "Please login";?> <a href='login.php'>here</a> <?php
}
require_once('footer.php');
?>
