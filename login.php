<?php

require_once "pdo.php";

require_once "util.php";

session_start();

if ( isset($_POST['cancel'] ) ) {

	unset($_SESSION['name']);
	
	unset($_SESSION['pass']);
    
    header("Location: index.php");
    
    return;

}

$salt = 'XyZzy12*_';

if ( isset($_POST['email']) && isset($_POST['pass']) ) {

	if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
    
        $_SESSION['error'] = "All fields are required";
        
        header("Location: login.php");
        
        return;
        
    } elseif (strpos($_POST['email'], '@') == false) {
    
		$_SESSION['error'] = "Email address must contain (@)";
		
        header("Location: login.php");
        
        return;
	
	} else {
    
    	$check = hash('md5', $salt.$_POST['pass']);

		$stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');

		$stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if ( $row !== false ) {

			$_SESSION['name'] = $row['name'];

			$_SESSION['user_id'] = $row['user_id'];

			header("Location: index.php");

			return;
			
		} else {
		
			$_SESSION['error'] = "Incorrect password";
		
			error_log("Login fail ".$_POST['email']." $check");
            
    		header("Location: login.php");
        	
    		return;
        
        }
          
    }
    
}

?>

<!DOCTYPE html>

<html>

<head>

<?php require_once "bootstrap.php"; ?>

<title>Fang Xiaoyong - Profile login page</title>

</head>

<body>

<div class="container">

<h1>Please Log In</h1>

<?php

flashMessages();

?>

<script>

function doValidate() {

	console.log('Validating...');

	try {
	
		em = document.getElementById('id_email').value;
		
		console.log("Validation email ="+em);

		pw = document.getElementById('id_pass').value;

		console.log("Validating password ="+pw);
		if ( em == null || em == "" ||pw == null || pw == "" ) {
		
			alert("Both fields must be filled out");
			
			return false;
		
		}

		if ( em.indexOf('@') == -1 ) {

			alert("Email address must contain @");

			return false;

		}

		return true;

	} catch(e) {

	return false;

	}

return false;

}

</script>

<form method="POST">

<strong>Email</strong> <input type="text" name="email" id="id_email"><br>

<strong>Password</strong> <input type="password" name="pass" id="id_pass"><br>

<input type="submit" onclick="return doValidate();" value="Log In">

<input type="submit" name="cancel" value="Cancel">

</form><br>

<p>

For a password hint, view source and find an account and password hint in the HTML comments.

<!-- Hint: The account is umsi@umich.edu. The password is the three character name of the 
programming language used in this class (all lower case) followed by 123. -->

</p>

</div>

</body>

</html>