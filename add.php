<?php

require_once "pdo.php";

require_once "util.php";

session_start();

//Access without name or user_id will be denied
accessDenied();

//Submit cancel will redirect to index.php
if ( isset($_POST['cancel']) ) {

	header('Location: index.php');

    return;
	
}

//Submit form will go to validation process
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
&& isset($_POST['headline']) && isset($_POST['summary']) ) {

	//Validate profile: empty input or wrong email format
	$msg = validateProfile();

    if ( is_string($msg) ) {
    	
    	$_SESSION['error'] = $msg;
    	
    	header("location: add.php");
    	
    	return;
    	
    } 
    
    //Validate position: empty input or non-numeric year
    $msg = validatePos();

    if ( is_string($msg) ) {
    	
    	$_SESSION['error'] = $msg;
    	
    	header("location: add.php");
    	
    	return;
    	
    }
	
    //Validation ok
    //Insert profile data
	$sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
		VALUES ( :uid, :fn, :ln, :em, :he, :su)";
			
	$stmt = $pdo->prepare($sql);

	$stmt->execute(array(

		':uid' => $_SESSION['user_id'],

		':fn' => $_POST['first_name'],

		':ln' => $_POST['last_name'],

		':em' => $_POST['email'],

		':he' => $_POST['headline'],

		':su' => $_POST['summary']));
	
	//Get profile_id of the just inserted profile
	$profile_id = $pdo->lastInsertId();
	
	//Insert position data
	$rank = 1;
		
	for ($i=1; $i<=9; $i++) {
		
		if ( ! isset($_POST['year'.$i]) ) continue;
			
		if ( ! isset($_POST['desc'.$i]) ) continue;
			
		$year = $_POST['year'.$i];
			
		$desc = $_POST['desc'.$i];
			
		$stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) 
			VALUES (:pid, :rank, :year, :desc)');
				
		$stmt->execute(array(
			
			':pid' => $profile_id,
				
			':rank' => $rank,
				
			':year' => $year,
				
			':desc' => $desc));
			
		$rank++;
		
	}

	//Add complete, redirect to index.php and show success message
	$_SESSION['success'] = "Profile added";

	header("Location: index.php");
		
	return;
    
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Fang Xiaoyong - Profile add page</title>

<?php require_once "head.php"; ?>

</head>

<body>

<div class="container">

<?php

echo("<h1>Adding Profile for ".htmlentities($_SESSION['name'])."</h1>\n");

//Show validation messages
flashMessages();

?>

<form method="post">

<p><strong>First Name: </strong><input type="text" name="first_name" size="60"></p>

<p><strong>Last Name: </strong><input type="text" name="last_name" size="60"></p>

<p><strong>Email: </strong><input type="text" name="email" size="30"></p>

<p><strong>Headline: </strong><br><input type="text" name="headline" size="80"></p>

<p><strong>Summary: </strong><br><textarea name="summary" rows="8" cols="80"></textarea></p>

<p><strong>Position: </strong><input type="submit" id="addPos" value="+"><div id="position_fields"></div></p>

<p><input type="submit" value="Add"><input type="submit" name="cancel" value="Cancel"</p>

</form>

<script>

//Each click will add one position form into #position_fields
//Position number count from 1 to 9
countPos = 0;

$(document).ready(function(){

	window.console && console.log('Document ready called');
	
	$('#addPos').click(function(event){
	
		event.preventDefault();
	
		if ( countPos >=9 ) {
	
			alert("Maximum of nine position entries exceeded");
		
			return;

		}
	
		countPos++;
	
		window.console && console.log("Adding position "+countPos);
	
		$('#position_fields').append(
			//Append content must be in one long line otherwise doesn't work. I don't know why. Anyone understands please tell me thanks
			'<div id="position'+countPos+'"><p><strong>Year: </strong><input type="text" name="year'+countPos+'" value="" /><input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p><textarea name="desc'+countPos+'" rows="8" cols="80"></textarea></div>');});

});

</script>

</div>

</body>

</html>