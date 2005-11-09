<?php
/*
install.php

*/


if (!defined('CARTOWIKI_VERSION')) {
	echo "acces direct interdit";
	exit;
}


/*
    $CartoWikiConfig = array(
	'cartowiki_version' => '0.1'
	)
*/

if (!$version = trim($CartoWikiConfig["cartowiki_version"])) $version = "0";
switch ($version)
{
case "0":
	echo "<b>Installation</b><br>\n";
		break;
}

?>

<p>
A l'&eacute;tape suivante, le programme d'installation va essayer
d'&eacute;crire le fichier de configuration <tt><?php echo "cartowiki/conf/cartowiki.config.php" ; ?></tt>.
Assurez vous que le serveur web a bien le droit d'&eacute;crire dans ce fichier, sinon vous devrez le modifier manuellement.  </p>

<form action="<?php echo  myLocation(); ?>?installAction=writeconfig" method="POST">
<input type="hidden" name="config" value="<?php echo addslashes(htmlentities(serialize($config))) ?>">
<input type="submit" value="Continuer">
</form>