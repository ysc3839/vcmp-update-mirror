<?php
define('PASSWORD', '');
define('REMOTE_PASSWORD', '');
define('CURL_PROXYTYPE', null);
define('CURL_PROXY', null);
define('CHECK_TIME', 600);

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
function CheckVCMPUpdate(/* array */$versions)
{
	$json = ['password' => REMOTE_PASSWORD, 'versions' => $versions];
	$postFields = ['json' => json_encode($json)];
	if ($curl = curl_init())
	{
		curl_setopt($curl, CURLOPT_URL, 'http://u04.maxorator.com/check');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
		curl_setopt($curl, CURLOPT_PROXYTYPE, CURL_PROXYTYPE);
		curl_setopt($curl, CURLOPT_PROXY, CURL_PROXY);

		$data = curl_exec($curl);
		curl_close($curl);

		return $data;
	}
}

function DownloadVCMPGame(/* string */$version)
{
	$json = ['password' => REMOTE_PASSWORD, 'version' => $version];
	$postFields = ['json' => json_encode($json)];
	if ($curl = curl_init())
	{
		$filename = './files/tmp' . sprintf("%'04X", rand());
		$file = fopen($filename, 'wb');
		if ($file)
		{
			curl_setopt($curl, CURLOPT_FILE, $file);
			curl_setopt($curl, CURLOPT_URL, 'http://u04.maxorator.com/download');
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
			curl_setopt($curl, CURLOPT_PROXYTYPE, CURL_PROXYTYPE);
			curl_setopt($curl, CURLOPT_PROXY, CURL_PROXY);

			$dlfilename = null;
			curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$dlfilename) {
				if ($dlfilename === null)
				{
					$pattern = 'Content-Disposition:';
					$len = strlen($pattern);
					if (substr($header, 0, $len) == $pattern)
					{
						$pattern = 'filename="';
						$pos = strpos($header, $pattern, $len - 1);
						if ($pos !== false)
						{
							$pos += strlen($pattern);
							$pos2 = strpos($header, '"', $pos);
							if ($pos !== false)
								$dlfilename = substr($header, $pos, $pos2 - $pos);
						}
					}
				}
				return strlen($header);
			});
			$curlRet = curl_exec($curl);
			fclose($file);

			if ($dlfilename === null || $curlRet == false) // Don't know the filename.
				unlink($filename); // Remove the file.
			else
			{
				rename($filename, './files/' . $dlfilename);
				curl_close($curl);
				return $dlfilename;
			}
		}
		curl_close($curl);
	}
}

function GetSetting(/* string */$key)
{
	global $db;
	if ($query = $db->query("SELECT value FROM settings WHERE key='$key'"))
	{
		return $query->fetchArray(SQLITE3_NUM)[0];
	}
	return null;
}

function SetSetting(/* string */$key, /* string */$value = '')
{
	global $db;
	return $db->exec("REPLACE INTO settings VALUES ('$key','$value')");
}
