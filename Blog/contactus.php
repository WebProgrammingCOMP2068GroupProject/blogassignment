<?php
SESSION_START();
//Matthew Rowlandson is doing this file!
require_once('header.php');
?>
<h2>Contact Us!</h2>
<div id="contactForm">
    <form method="post" id="contact" action="contact_action.php">
        Your Name:<br>
        <input type="text" name="name"><br>
        Your Email:<br>
        <input type="text" name="email"><br>
        Message:<br>
        <textarea name="message"></textarea><br>
        <input type="submit" value="send">
        <input type="reset" value="clear">
    </form>
</div>
<?php
require_once('footer.php');
?>
