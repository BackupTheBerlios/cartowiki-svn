<?php
/*
header.php
*/
// stuff
function test($text, $condition, $errorText = "", $stopOnError = 1) {
	echo "$text " ;
	if ($condition)
	{
		echo "<span class=\"ok\">OK</span><br>\n" ;
	}
	else
	{
		echo "<span class=\"failed\">ECHEC</span>" ;
		if ($errorText) echo ": ",$errorText ;
		echo "<br>\n" ;
		if ($stopOnError) exit;
	}
}

function myLocation()
{
	list($url, ) = explode("?", $_SERVER["REQUEST_URI"]);
	return $url;
}

?>
<html>
<head>
  <title>Installation de CartoWiki</title>
  <style>
    P, BODY, TD, LI, INPUT, SELECT, TEXTAREA { font-family: Verdana; font-size: 13px; }
    INPUT { color: #880000; }
    .ok { color: #008800; font-weight: bold; }
    .failed { color: #880000; font-weight: bold; }
    A { color: #0000FF; }
  </style>
</head>

<body>
