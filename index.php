<?php
include 'functions.inc.php';

if ($_SERVER['REQUEST_URI'] == '/check')
{
	if ($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		http_response_code(405);
		exit();
	}
	do {
		if (!isset($_POST['json']))
			break;

		$json = json_decode($_POST['json'], true);
		if (!isset($json['password']) || !isset($json['versions']))
			break;
		
		if ($json['password'] != PASSWORD)
		{
			http_response_code(401);
			exit();
		}

		$versions = [];
		foreach ($json['versions'] as $version => $_)
			array_push($versions, $db->escapeString($version));

		$query = $db->query("SELECT * FROM versions WHERE version='" . implode("' OR version='", $versions) . "'");
		if ($query)
		{
			$updateList = [];
			$knownVersions = [];
			while ($result = $query->fetchArray(SQLITE3_ASSOC))
			{
				array_push($knownVersions, $result['version']);
				if (hexdec($json['versions'][$result['version']]) < $result['build'])
					array_push($updateList, $result['version']);
			}

			if (($verstotest = GetSetting('verstotest')) != null)
			{
				$verstotest = explode(',', $verstotest);
				$unknownVers = array_diff($versions, $knownVersions);
				$verstotest = array_unique(array_merge($verstotest, $unknownVers));
				SetSetting('verstotest', implode(',', $verstotest));
			}
			echo implode('|', $updateList);
			exit();
		}
	} while (0);

	http_response_code(400);
	exit();
}
else if ($_SERVER['REQUEST_URI'] == '/download')
{
	if ($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		http_response_code(405);
		exit();
	}

	do {
		if (!isset($_POST['json']))
			break;

		$json = json_decode($_POST['json'], true);
		if (!isset($json['password']) || !isset($json['version']))
			break;

		if ($json['password'] != PASSWORD)
		{
			http_response_code(401);
			exit();
		}

		$version = $db->escapeString($json['version']);

		$query = $db->query("SELECT build FROM versions WHERE version='" . $version . "'");
		if ($query && ($result = $query->fetchArray(SQLITE3_NUM)) != null)
		{
			$fileName = 'build' . dechex($result[0]) . '.7z';
			if (file_exists('./files/' . $fileName))
			{
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . $fileName .'"');
				readfile('./files/' . $fileName);
				exit();
			}
		}
	} while (0);

	http_response_code(404);
	exit();
}
else
	include 'ui.php';
