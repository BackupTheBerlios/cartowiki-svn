<?php

// TODO : chargement table locations par partie
// Probleme copie si fichier deja present
// Attention au safe mode sur le .. !

define('CARTOWIKI_VERSION','0.1');

if (file_exists(dirname(__FILE__).'/../../wakka.config.php')) include(dirname(__FILE__).'/../../wakka.config.php');

if (file_exists(dirname(__FILE__).'/../../conf/cartowiki.config.php')) include(dirname(__FILE__).'/../../conf/cartowiki.config.php');

if ($CartoConfig["cartowiki_version"] != CARTOWIKI_VERSION) {
	if (!isset($_REQUEST["installAction"]) OR !$installAction = trim($_REQUEST["installAction"])) $installAction = "default";
	include("header.php");
	if (file_exists($installAction.".php")) include($installAction.".php"); else echo "<i>Invalid action</i>";
	include("footer.php");
}
// Deja installé : retour 
else {
	header("Location: ". $wakkaConfig["base_url"]);
	exit;
}
?>