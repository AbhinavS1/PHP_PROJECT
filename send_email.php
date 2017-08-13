<?php 
session_start();
if(isset($_POST['home_page']))
{
	header('Location: '.$uri.'/login_home_page.php/');
}

$from_email = $_SESSION['logged_in_user']['login'];
echo 'You are logged in as '.$from_email;
if(isset($_POST['submit'])){
    $to_email = $_POST['to_email']; // this is your Email address
    $subject = $_POST['subject'];
    $message = $_POST['message'];
	$servername = 'localhost';
	$username = 'root';
	$password = '';
	$dbname = 'mailing_application';
	$conn = new mysqli($servername, $username, $password, $dbname);
	$sql = "INSERT INTO mails (from_email, to_email, subject, body) VALUES ('$from_email', '$to_email', '$subject', '$message')";
    if ($conn->query($sql) === TRUE) {
    echo "Mail was successfully sent";
   } else {
    echo "Error in sending mail: " . $sql . "<br>" . $conn->error;
    }
	$conn->close();
}
?>

<!DOCTYPE html>
<head>
<title>Form submission</title>
</head>
<body>

<form action="" method="post">
To: <input type="text" name="to_email"><br>
Subject: <input type="text" name="subject"><br>
Message:<br><textarea rows="5" name="message" cols="30"></textarea><br>
<input type="submit" name="submit" value="Submit">
</form>


<form action="login_home_page.php" method="post">
<input type="submit" name="home_page" value="Back To Home">
</form>
 

</body>
</html> 