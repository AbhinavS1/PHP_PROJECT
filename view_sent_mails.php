<?php
require_once "message_track_displayer.php";
session_start();
if(isset($_POST['home_page']))
{
	header('Location: '.$uri.'/login_home_page.php/');
}
$logged_in_user = $_SESSION['logged_in_user']['login'];
echo 'Welcome '.$logged_in_user;

    $username = 'root';
	$password = '';
	$dbname = 'mailing_application';
	$servername = 'localhost';
	$con=mysqli_connect($servername,$username,$password,$dbname);
    display_outbox_message_history($con, $logged_in_user);
    mysqli_close($con);
?>


<!DOCTYPE html>

<form action="login_home_page.php" method="post">
<input type="submit" name="home_page" value="Back To Home">
</form>

</body>
</html> 