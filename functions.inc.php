<?php
define('PASSWORD', '');

date_default_timezone_set('UTC');

if (!file_exists('./files/'))
	mkdir('./files/');

global $db;
if ($db = new SQLite3('./files/.database.db'))
{
	$db->exec('CREATE TABLE IF NOT EXISTS versions (version TEXT NOT NULL UNIQUE, build INTEGER NOT NULL)');
	$db->exec('CREATE TABLE IF NOT EXISTS settings (key TEXT NOT NULL UNIQUE, value TEXT NOT NULL)');
}
else
{
	http_response_code(500);
	exit();
}

/*
$versions = ['04rel002' => '00000001', '04rel003' => '00000001'];
*/
function CheckVCMPUpdate(/* Array */$versions)
{
	$json = ['password' => '', 'versions' => $versions];
	$postFields = ['json' => json_encode($json)];
	if ($curl = curl_init())
	{
		curl_setopt($curl, CURLOPT_URL, 'http://u04.maxorator.com/check');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

		$data = curl_exec($curl);
		curl_close($curl);

		return $data;
	}
}

function GetSetting(/* string */$key)
{
	global $db;
	if ($query = $db->query("SELECT value FROM settings WHERE key='" . $key . "'"))
	{
		return $query->fetchArray(SQLITE3_NUM)[0];
	}
	return null;
}

function SetSetting(/* string */$key, /* string */$value = '')
{
	global $db;
	return $db->exec("REPLACE INTO settings VALUES ('" . $key . "','" . $value . "')");
}
