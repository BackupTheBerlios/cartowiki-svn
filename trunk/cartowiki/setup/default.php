<form action="<?php echo  myLocation() ?>?installAction=install" method="POST">
<table>
<tr><td><b>Installation de CartoWiki</b></td></tr>

<?php
/*
default.php
*/
if (!defined('CARTOWIKI_VERSION')) {
	echo "acces direct interdit";
	exit;
}


echo "<tr><td><b>Test de la configuration : </b><br>\n";
echo "</td></tr>";
test("<tr><td>Test connexion MySQL ...", $dblink = @mysql_connect($wakkaConfig["mysql_host"], $wakkaConfig["mysql_user"], $wakkaConfig["mysql_password"]));
echo "</td></tr>";
test("<tr><td>Recherche base de donn&eacute;es ...", @mysql_select_db($wakkaConfig["mysql_database"], $dblink), "La base de donn&eacute;es que vous avez choisie n'existe pas !");
echo "</td></tr>";
echo "<tr><td>";
echo "</td></tr>";

?>
<tr><td><input type="submit" value="Continuer"></td></tr>
</table>
</form>