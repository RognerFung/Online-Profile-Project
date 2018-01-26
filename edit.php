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

//Without profile_id will show error and redirect to index.php
if ( ! isset($_GET['profile_id']) ) {

  $_SESSION['error'] = "Missing profile_id";

  header('Location: index.php');

  return;

}

//Load up profile
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :pid AND user_id = :uid");

$stmt->execute(array(':pid' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));

$profile = $stmt->fetch(PDO::FETCH_ASSOC);

//wrong profile_id or user_id will show error and redirect to index.php
if ( $profile === false ) {

    $_SESSION['error'] = 'Could not load profile';
    
    header( 'Location: index.php' );
    
    return;

}

//Submit form will go to validation process
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
	&& isset($_POST['headline']) && isset($_POST['summary']) ) {

	//Validate profile: empty input or wrong email format
	$msg = validateProfile();

    if ( is_string($msg) ) {
    	
    	$_SESSION['error'] = $msg;
    	
    	header('location: edit.php?profile_id='.$_REQUEST['profile_id']);
    	
    	return;
    	
    } 
    
    //Validate position: empty input or non-numeric year
    $msg = validatePos();
    
    if ( is_string($msg) ) {
    	
    	$_SESSION['error'] = $msg;
    	
    	header('location: edit.php?profile_id='.$_REQUEST['profile_id']);
    	
    	return;
    	
    }
    
    //Validation ok
    //Update profile data
	$sql = "UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, 
		headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid";
			
	$stmt = $pdo->prepare($sql);

	$stmt->execute(array(

		':uid' => $_SESSION['user_id'],

		':fn' => $_POST['first_name'],

		':ln' => $_POST['last_name'],

		':em' => $_POST['email'],

		':he' => $_POST['headline'],

		':su' => $_POST['summary'],
			
		':pid' => $_REQUEST['profile_id']));
		
	//Delete old position data
	$sql = "DELETE FROM Position WHERE profile_id = :pid";

    $stmt = $pdo->prepare($sql);
    
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
	
	//Insert new position data
	$rank = 1;
		
	for ($i=1; $i<=9; $i++) {
		
		if ( ! isset($_POST['year'.$i]) ) continue;
			
		if ( ! isset($_POST['desc'.$i]) ) continue;
			
		$year = $_POST['year'.$i];
			
		$desc = $_POST['desc'.$i];
			
		$stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) 
			VALUES (:pid, :rank, :year, :desc)');
				
		$stmt->execute(array(
			
			':pid' => $_REQUEST['profile_id'],
				
			':rank' => $rank,
				
			':year' => $year,
				
			':desc' => $desc));
			
		$rank++;
		
	}
	
	//Update complete, redirect to index.php and show success message
   	$_SESSION['success'] = 'Profile updated';
    	
	header( 'Location: index.php' );
    	
	return;

}

//Load up position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

?>

<!DOCTYPE html>

<html>

<head>

<title>Fang Xiaoyong - Profile edit page</title>

<?php require_once "head.php"; ?>

</head>

<body>

<div class="container">

<?php

echo("<h1>Editing Profile for ".htmlentities($_SESSION['name'])."</h1>\n");

//Show validation messages
flashMessages();

?>

<form method="post">

<p><strong>First Name: </strong><input type="text" name="first_name" size="60" value="<?= htmlentities($profile['first_name']); ?>"></p>

<p><strong>Last Name: </strong><input type="text" name="last_name" size="60" value="<?= htmlentities($profile['last_name']); ?>"></p>

<p><strong>Email: </strong><input type="text" name="email" size="30" value="<?= htmlentities($profile['email']); ?>"></p>

<p><strong>Headline: </strong><br><input type="text" name="headline" size="80" value="<?= htmlentities($profile['headline']); ?>"></p>

<p><strong>Summary: </strong><br><textarea name="summary" rows="8" cols="80"><?= htmlentities($profile['summary']); ?></textarea></p>

<?php

echo('<p><strong>Position: </strong><input type="submit" id="addPos" value="+">'."\n");

echo('<div id="position_fields">'."\n");

//Show every position form
$pos = 0;

foreach( $positions as $position ) {

	$pos++;
	
	echo('<div id="position'.$pos.'">'."\n");
	
	echo('<p><strong>Year: </strong><input type="text" name="year'.$pos.'" value="'.htmlentities($position['year']).'" />'."\n");

	echo('<input type="button" value="-" onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
	
	echo("</p>\n");
	
	echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
	
	echo(htmlentities($position['description'])."\n");
	
	echo("\n</textarea>\n</div>\n");

}

echo('</div></p>');

?>

<p><input type="submit" value="Save"><input type="submit" name="cancel" value="Cancel"</p>

</form>

<script>

//Each click will add one position form into #position_fields
//Position number count from $pos to 9
countPos = <?= $pos ?>;

$(document).ready(function(){

	window.console && console.log('Document ready called');
	
	$('#addPos').click(function(event){
	
		event.preventDefault();
	
		if ( countPos >= 9 ) {
	
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

