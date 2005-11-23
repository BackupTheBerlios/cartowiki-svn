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
		$sortie_verif .= '<h2>Insertion des données du fichier sql version '.$version.'</h2>';
		$sql_contenu = PMA_readFile(INSTAL_CHEMIN_SQL.'papyrus_v'.$version.'.sql');
		$tab_requete_sql = array();
		PMA_splitSqlFile($tab_requete_sql, $sql_contenu, '');
		foreach ($tab_requete_sql as $value) {
		    $table_nom = '';
		    if (!empty($value['table_nom'])) {
			$table_nom = $value['table_nom'];
		    }
		    $requete_type = '';
		    if (!empty($value['type'])) {
			$requete_type = $value['type'];
		    }
		    if ($requete_type == 'create') {
			$erreur = testerConfig( $sortie_verif, 'Création table '.$table_nom.'...', @mysql_query($value['query'], $dblink),
						'Déjà créée ?', 0, $erreur);
		    } else if ($requete_type == 'alter') {
			$erreur = testerConfig( $sortie_verif, 'Modification structure table '.$table_nom.'...', @mysql_query($value['query'], $dblink),
						'Déjà modifiée ?', 0, $erreur);
		    } else if ($requete_type == 'insert') {
			$erreur = testerConfig( $sortie_verif, 'Insertion table '.$table_nom.'...', @mysql_query($value['query'], $dblink),
						'Données déjà présente ?', 0, $erreur);
		    }
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