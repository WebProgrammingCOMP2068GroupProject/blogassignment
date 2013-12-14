<?php
SESSION_START();
//Matthew Rowlandson is doing this file!
//NOTE: Still needs some more validation...
require_once('header.php');
if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['message']))
 {
    ?>
    <h2>Contact Us!</h2>
    <div id="contactForm">
        <form method="post" id="contactForm" action="<?php echo $PHP_SELF; ?>">
            Your Name:<br>
            <input type="text" name="name"><br>
            Your Email:<br>
            <input type="text" name="email"><br>
            Message:<br>
            <textarea name="message"></textarea><br>
            <input type="submit" value="send">
            <input type="reset" value="reset">
        </form>
    </div>
    <?php
 }
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']))
{
 
    $formName = $_POST['name'];
    $formEmail = $_POST['email'];
    $formMessage = $_POST['message'];
    
    $mail_to =  "200219431@student.georgianc.on.ca";
    
    //manipulate the data from the form so it can be emailed...
    if(isset($SESSION["username"]))
    {
        $subject = 'Message from User'.$SESSION["username"];
    }
    else{
        $subject = 'Message from Site Guest/'.$formName;
    }
    //message's body
    $message_body = 'From: '.$formName."\n";
    $message_body .= 'Email: '.$formEmail."\n";
    $message_body .= 'Message: '.$formMessage;
    
    //headers
    $headers = "From: $formEmail\r\n";
    $headers .= "Reply-To: $formEmail\r\n";
    
    //mail function...
    $mail_status = mail($mail_to, $subject, $message_body, $headers);
    
    //check if the email was delivered or not...
    if($mail_status) { 
        echo "<h2>Email Sent!</h2>";
        ?>
        <script language="javascript" type="text/javascript">
                // Print a message
                alert('Thank you for the message. We will contact you shortly.');
                // Redirect to some main page
                window.location = 'index.php';
        </script>
        <?php
    }
    else { 
        echo "<h2>Email Failed!</h2>";
        ?>
    <script language="javascript" type="text/javascript">
        // Print a message
        alert('Message failed. Please, send an email to blogwebsitetester@outlook.com');
        // Redirect to some main page
        window.location = 'contactus.php';
    </script>
    <?php
    }
}
require_once('footer.php');
?>
