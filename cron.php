<?php
set_time_limit(0);
require_once('functions.inc.php');

$currentTime = time();
$lastCheck = (int)GetSetting('lastCheck');
if ($currentTime - $lastCheck > CHECK_TIME)
{
	SetSetting('lastCheck', $currentTime);
	echo 'Current time: ' . date('Y-m-d H:i:s', $currentTime) . PHP_EOL;
	echo 'Last check: ' . date('Y-m-d H:i:s', $lastCheck) . PHP_EOL;

	$versionsToCheck = [];

	$verstotest = GetSetting('verstotest');
	if ($verstotest != null && $verstotest != '')
	{
		SetSetting('verstotest', '');
		$verstotest = explode(',', $verstotest);
		foreach ($verstotest as $ver)
			$versionsToCheck[$ver] = '00000000';
	}

	$query = $db->query('SELECT * FROM versions');
	if ($query)
	{
		while ($version = $query->fetchArray(SQLITE3_ASSOC))
			$versionsToCheck[$version['version']] = sprintf("%'08X", $version['build']);
	}

	if (!$versionsToCheck)
	{
		echo 'No version to check, exit.' . PHP_EOL;
		exit();
	}

	echo 'Version to check: ' . print_r($versionsToCheck, true) . PHP_EOL;

	$updates = CheckVCMPUpdate($versionsToCheck);
	if ($updates)
	{
		$updates = explode('|', $updates);

		echo 'Version to update: ' . print_r($updates, true) . PHP_EOL;
		foreach ($updates as $version)
		{
			echo "Start downloading '$version'." . PHP_EOL;
			$filename = DownloadVCMPGame($version);
			if ($filename !== null)
			{
				$build = substr($filename, 5, 8);
				echo "Version '$version' download successfully, build '$build'." . PHP_EOL;
				$build = hexdec($build);
				global $db;
				$db->exec("REPLACE INTO versions VALUES ('$version','$build')");
			}
			else
			{
				$failed[] = $version;
				echo "Version '$version' failed to download!" . PHP_EOL;
			}
		}
		SetSetting('verstotest', implode(',', $failed));
	}
	else
		echo 'No update, exit.' . PHP_EOL;
}
else
{
	//echo 'Last update no longer than 10 minutes, exit.' . PHP_EOL;
}
