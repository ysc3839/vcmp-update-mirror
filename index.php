<?php include 'db.inc.php';
echo $_SERVER['REQUEST_URI'];
if ($_SERVER['REQUEST_URI'] == "/")
	include 'ui.php';