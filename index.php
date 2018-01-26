<?php

require_once "pdo.php";

require_once "util.php";

session_start();

?>

<!DOCTYPE html>

<html>

<head>

<title>Fang Xiaoyong - Profile index page</title>

<?php require_once "head.php"; ?>

</head>

<body>

<div class="container">

<h1>Fang Xiaoyong's Resume Registry</h1>

<?php

flashMessages();

if ( isset($_SESSION['name']) && isset($_SESSION['user_id']) ) {

	echo("<p><a href='logout.php'>Logout</a></p>");
	
	$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
	
	if ( $stmt->rowCount() > 0 ) {
	
		echo("<table border='1'>"."\n");
	
		echo("<tr><th>Name</th>");
	
		echo("<th>Headline</th>");
	
		echo("<th>Action</th></tr>");
		
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		
			echo("<tr><td><a href='view.php?profile_id=".$row['profile_id']."'>");
			
			echo($row['first_name']." ".$row['last_name']."</a></td>");
    	
			echo("<td>".htmlentities($row['headline'])."</td>");
    	
			echo("<td><a href='edit.php?profile_id=".$row['profile_id']."'>Edit</a>"." ");
    	
			echo("<a href='delete.php?profile_id=".$row['profile_id']."'>Delete</a>");
    	
			echo("</td></tr>\n");
    
    	}
    	
    	echo("</table>\n");
		
	}
	
	echo("<br/><p><a href='add.php'>Add New Entry</a></p>");

	
} else {

	echo("<p><a href='login.php'>Please log in</a></p>");
	
	$stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id FROM Profile");
	
	if ( $stmt->rowCount() > 0 ) {
	
		echo("<table border='1'>"."\n");
	
		echo("<tr><th>Name</th>");
	
		echo("<th>Headline</th>");
	
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		
			echo("<tr><td><a href='view.php?profile_id=".$row['profile_id']."'>");
			
			echo($row['first_name']." ".$row['last_name']."</a></td>");
    	
			echo("<td>".htmlentities($row['headline'])."</td></tr>\n");
    	
    	}
    	
    	echo("</table>\n");
	
	}

}

?>

</div>

</body>

</html>