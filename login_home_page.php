<?php
session_start();
if(isset($_POST['logout']))
{
	$_SESSION['logged_in_user']['login'] = NULL;
	header('Location: '.$uri.'/index.php/');
}
$logged_in_user = $_SESSION['logged_in_user']['login'];
echo 'Welcome '.$logged_in_user;
if(isset($_POST['send_email'])) {
	header('Location: '.$uri.'/send_email.php/');
} else if(isset($_POST['view_sent_emails'])) {
	header('Location: '.$uri.'/view_sent_mails.php/');
} else if(isset($_POST['view_inbox'])) {
	header('Location: '.$uri.'/updated_inbox.php/');
}
?>

<!DOCTYPE html>

<form action="" method="post">
<input type="submit" name="send_email" value="Send Email">
<input type="submit" name="view_sent_emails" value="View Sent Mails">
<input type="submit" name="view_inbox" value="View Inbox">

</form>


<form action="login_home_page.php" method="post">
<input type="submit" name="logout" value="Logout">
</form>

</body>
</html> 