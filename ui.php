<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="/mdl/material.min.css">
<script src="/mdl/material.min.js"></script>
<style>
main {
	margin: 24px;
}
.progress {
	margin: auto;
	width: 50%;
	margin-bottom: 20px;
}
.inline-block {
	display: inline-block;
}
.icon-download {
	background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAQAAAD9CzEMAAAAV0lEQVR4Ae3QMQ7EQAzDQD2dP3dKF8E1BpjioHEvYjf1kZof10ADDTRgBsgcDjeB+wrcjyJHWPMLa35hzS+s+YU1v3Dn3wkiwJ3fBBER8hdqbncMNFBVDxodwlpdA7aUAAAAAElFTkSuQmCC") center / cover;
	height: 24px;
}
.footer {
	padding-top: 5px;
}
.maintainer {
	color: #9e9e9e;
}
</style>
<title>VCMP Update Mirror</title>
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-typography--text-center">
	<header class="mdl-layout__header">
		<div class="mdl-layout__header-row">
			<!-- Title -->
			<span class="mdl-layout-title">VCMP Update Mirror</span>
			<!-- Add spacer, to align navigation to the right -->
			<div class="mdl-layout-spacer"></div>
			<!-- Navigation -->
			<!--nav class="mdl-navigation">
				<a class="mdl-navigation__link" href="">Link</a>
				<a class="mdl-navigation__link" href="">Link</a>
				<a class="mdl-navigation__link" href="">Link</a>
				<a class="mdl-navigation__link" href="">Link</a>
			</nav-->
		</div>
	</header>
<main class="mdl-layout__content">
<h3 class="mdl-color-text--primary">In progress...</h3>
<div class="mdl-progress mdl-js-progress mdl-progress__indeterminate mdl-shadow--4dp progress"></div>
<div class="mdl-color-text--grey-600 inline-block"><table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
	<thead>
	<tr>
		<th class="mdl-data-table__cell--non-numeric">Version</th>
		<th class="mdl-data-table__cell--non-numeric">Build</th>
		<th class="mdl-data-table__cell--non-numeric">Date</th>
		<th>Download</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$query = $db->query('SELECT * FROM versions');
	//var_dump($query);
	if ($query)
	{
		for ($i = 0; $item = $query->fetchArray(SQLITE3_ASSOC); $i++)
		{
			$buildhex = strtoupper(dechex($item['build']));
			echo '<tr>';
			echo '<td class="mdl-data-table__cell--non-numeric">' . $item['version'] . '</td>';
			echo '<td class="mdl-data-table__cell--non-numeric">' . $buildhex . '</td>';
			echo '<td class="mdl-data-table__cell--non-numeric">' . date('Y-m-d H:i:s', $item['build']) . '</td>';
			echo '<td><a href="/files/build' . $buildhex . '.7z" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" download=""><i class="material-icons icon-download"></i></a></td>';
			echo '</tr>';
		}
	}
	?>
	</tbody>
</table></div>
</main>
<footer class="mdl-mini-footer">
  <div class="mdl-mini-footer__left-section">
    <div class="mdl-logo">Maintained by <span class="maintainer">ysc3839</span></div>
    <ul class="mdl-mini-footer__link-list">
      <li><a target="_blank" href="https://github.com/ysc3839/vcmp-update-mirror">View on GitHub</a></li>
    </ul>
  </div>
</footer>
</div>
</body>
</html>
