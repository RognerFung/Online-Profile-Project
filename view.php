<?php

require_once "pdo.php";

require_once "util.php";

session_start();

//Without profile_id will show error and redirect to index.php
if ( ! isset($_GET['profile_id']) ) {

  $_SESSION['error'] = "Missing profile_id";

  header('Location: index.php');

  return;

}

//Load up profile
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :pid");

$stmt->execute(array(":pid" => $_REQUEST['profile_id']));

$profile = $stmt->fetch(PDO::FETCH_ASSOC);

//wrong profile_id will show error and redirect to index.php
if ( $profile === false ) {

    $_SESSION['error'] = 'Bad value for profile_id';
    
    header( 'Location: index.php' );
    
    return;

}

//Load up position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

?>

<!DOCTYPE html>

<html>

<head>

<title>Fang Xiaoyong - Profile view page</title>

<?php require_once "head.php"; ?>

</head>

<body>

<div class="container">

<?php

echo("<h1>Profile information</h1>");

echo("<p><strong>First Name: </strong>".htmlentities($profile['first_name'])."</p>");

echo("<p><strong>Last Name: </strong>".htmlentities($profile['last_name'])."</p>");

echo("<p><strong>Email: </strong>".htmlentities($profile['email'])."</p>");

echo("<p><strong>Headline: </strong><br/>".htmlentities($profile['headline'])."</p>");

echo("<p><strong>Summary: </strong><br/>".htmlentities($profile['summary'])."</p>");

//If no position data don't show position at all
if ( !empty($positions) ) {

	echo("<p><strong>Position: </strong><br/>");

	echo("<ul>");

	foreach( $positions as $position ) {

		echo("<li>".htmlentities($position['year']).":".htmlentities($position['description'])."</li>");
	
	}

	echo("</ul>");

}

echo("<a href='index.php'>Done</a>");

?>

</div>

</body>

</html>