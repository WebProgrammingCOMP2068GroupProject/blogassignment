<?php
    SESSION_START();
    //for the contact form (data will be sent here)
    //put the data from the form into variables
    $formName = $_POST['name'];
    $formEmail = $_POST['email'];
    $formMessage = $_POST['message'];
    
    $mail_to =  "200219431@student.georgianc.on.ca";
    
    //manipulate the data from the form so it can be emailed...
    if(isset($SESSION["user"]))
    {
        $subject = 'Message from User'.$SESSION["user"];
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
    if($mail_status) { ?>
        <script language="javascript" type="text/javascript">
                // Print a message
                alert('Thank you for the message. We will contact you shortly.');
                // Redirect to some main page
                window.location = 'index.php';
        </script>
        <?php
    }
    else { ?>
    <script language="javascript" type="text/javascript">
        // Print a message
        alert('Message failed. Please, send an email to blogwebsitetester@outlook.com');
        // Redirect to some main page
        window.location = 'contactus.php';
    </script>
<?php
}?>
?>
