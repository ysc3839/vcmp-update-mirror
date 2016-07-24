<?php
global $db;

if ($db = new SQLite3(".database.db"))
{
	$db->exec("CREATE TABLE IF NOT EXISTS versions (version TEXT NOT NULL UNIQUE, build TEXT NOT NULL)");
}
else
{
	//header("HTTP/1.0 500 Internal Server Error");
	exit();
}