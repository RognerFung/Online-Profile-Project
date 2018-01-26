<?php

require_once "pdo.php";

require_once "util.php";

session_start();

accessDenied();

if ( isset($_POST['cancel']) ) {

    header('Location: index.php');

	return;
	
}
	
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {

	$sql = "DELETE FROM Profile WHERE profile_id = :zip";

    $stmt = $pdo->prepare($sql);
    
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    
    $_SESSION['success'] = 'Profile deleted';
    
    header( 'Location: index.php' );
    
    return;
    
}

if ( ! isset($_GET['profile_id']) ) {

  $_SESSION['error'] = "Missing profile_id";

  header('Location: index.php');

  return;

}

$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM Profile where profile_id = :xyz");

$stmt->execute(array(":xyz" => $_GET['profile_id']));

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false ) {

    $_SESSION['error'] = 'Bad value for profile_id';
    
    header( 'Location: index.php' );
    
    return;

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Fang Xiaoyong - Profile delete page</title>

<?php require_once "bootstrap.php"; ?>

</head>

<body>

<div class="container">

<h1>Deleting Profile</h1>

<p><strong>First Name:</strong><?= htmlentities($row['first_name']) ?></p>

<p><strong>Last Name:</strong><?= htmlentities($row['last_name']) ?></p>

<form method="post">

<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">

<input type="submit" value="Delete" name="delete">

<input type="submit" value="Cancel" name="cancel">

</form>

</div>

</body>

</html>




