<?php 
session_start();
function isUserPresent($conn, $login) {
	$result = mysqli_query($conn, "SELECT * from users WHERE login = '$login'");
	while($row = mysqli_fetch_array($result)) {
		return true;
	}
	return false;
}
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	$username = 'root';
	$password = '';
	$dbname = 'mailing_application';
	$servername = 'localhost';
	$register_email = 'register_email';
		$register_password = 'register_password';
		$register_new_user = "register_new_user";
	if(isset($_POST['login_submit'])) {
		echo 'Logging in user';
		$conn = mysqli_connect($servername,$username,$password,$dbname);
		$login = $_POST['login_email'];
		$password = $_POST['password'];
		echo 'Querying DB with login '.$login.' and password '.$password;
		$result = mysqli_query($conn, "SELECT * from users WHERE login = '$login' AND password = '$password'");
		while($row = mysqli_fetch_array($result)) {
			echo 'Got here';
	        $data['login'] = $_POST['login_email'];
	        $_SESSION['logged_in_user'] = $data;
	        header('Location: '.$uri.'/login_home_page.php/');
		}
		echo 'Login password combination is invalid';
	} else if(isset($_POST['register'])) {
		echo 'Registering new user';
		echo "
		<form action='' method='post'>
        Enter your login email address: <input type='text' name='$register_email'><br>
        Password: <input type='text' name='$register_password'><br>
		<input type='submit' name='$register_new_user' value='Confirm Registration'><br>
		</form>
		<br><br>
		";
		}
		if(isset($_POST['register_new_user'])) {
			echo 'Registering new user with inputs';
		$conn = mysqli_connect($servername,$username,$password,$dbname);
		
		$login = $_POST[$register_email];
		$password = $_POST[$register_password];
		if(isUserPresent($conn, $login)) {
			echo 'User is already registered';
		} else {
		mysqli_query($conn, "INSERT into users (login, password) VALUES('$login', '$password')");
		echo 'Successfully registered';}
	}
?>

<!DOCTYPE html>
<head>
<title>Form submission</title>
</head>
<body>

<form action="" method="post">
Enter your login email address: <input type="text" name="login_email"><br>
Password: <input type="text" name="password"><br>
<input type="submit" name="login_submit" value="Login"><br>
<br><br><br>
</form>

<form action="" method="post">
New User. Please register<br>
<input type="submit" name="register" value="Register">
</form>



</body>
</html> 