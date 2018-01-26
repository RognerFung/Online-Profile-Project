<?php
require_once "pdo.php";

require_once "util.php";

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demo</title>
    <style>
a.test {
    font-weight: bold;
}
</style>
</head>
<body>
    <a href="http://jquery.com/">jQuery</a><br/>
    <script src="jquery.min.js"></script>
<?php
//Load up position rows
$profile_id = 9;
$positions = loadPos($pdo, $profile_id);
//print_r($positions);
foreach ($positions as $position) {
print_r($position);
echo"<br/>";
}
?>
</body>
</html>