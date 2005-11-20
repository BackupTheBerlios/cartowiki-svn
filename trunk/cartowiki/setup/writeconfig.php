<?php
/*
writeconfig.php
*/


if (!defined('CARTOWIKI_VERSION')) {
	echo "acces direct interdit";
	exit;
}


$config = $config2 = unserialize(html_entity_decode(stripslashes(($_POST["config"]))));

// Ca vient de spip, merci spip.

function test_ecrire($my_dir) {
        $ok = true;
        $nom_fich = "$my_dir/test.txt";
        $f = @fopen($nom_fich, "w");
        if (!$f) $ok = false;
        else if (!@fclose($f)) $ok = false;
        else if (!@unlink($nom_fich)) $ok = false;
        return $ok;
}


// rajouter celui passer dans l'url ou celui du source (a l'installation)

$test_dirs[]=dirname(__FILE__).'/../conf/';
$test_dirs[]=dirname(__FILE__).'/../CACHE/';

unset($bad_dirs);
unset($absent_dirs);

while (list(, $my_dir) = each($test_dirs)) {
        if (!test_ecrire($my_dir)) {
                @umask(0);
                if (@file_exists($my_dir)) {
                        @chmod($my_dir, 0777);
                        // ???
                        if (!test_ecrire($my_dir))
                                @chmod($my_dir, 0775);
                        if (!test_ecrire($my_dir))
                                @chmod($my_dir, 0755);
                        if (!test_ecrire($my_dir))
                                $bad_dirs[] = "<LI>".$my_dir;
                } else
                        $absent_dirs[] = "<LI>".$my_dir;
        }
}

if ($bad_dirs || $absent_dirs) {
        if ($bad_dirs) {
                echo "<p>";
                echo ("Probleme d'écriture  sur le(s) repertoire(s) suivant(s) :");
                foreach ($bad_dirs as $bad_dir){
					echo $bad_dir;
				}

        }
        if ($absent_dirs) {
        	    echo "<p>";
                echo ("Le ou les repertoires suivants sont absents :");
                foreach ($absent_dirs as $absent_dir){
					echo $absent_dir;
				}
        }

}

echo "<p>";

$config["cartowiki_version"] = CARTOWIKI_VERSION;

$configCode = "<?php\n// cartowiki.config.php cree le ".strftime("%c")."\n// ne changez pas la cartowiki_version manuellement!\n\n\$CartoConfig = array(\n";

foreach ($config as $k => $v)
{
	$entries[] = "\t'".$k."' => '".$v."'";
}

$configCode .= implode(",\n", $entries).");\n?>";

// try to write configuration file
echo "<b>Cr&eacute;ation du fichier de configuration en cours...</b><br>\n";
test("&Eacute;criture du fichier de configuration <tt>cartowiki/conf/cartowiki.config.php</tt>...", $fp = @fopen(dirname(__FILE__).'/../conf/cartowiki.config.php', "w"), "", 0);

if ($fp)
{
	fwrite($fp, $configCode);
	// write
	fclose($fp);

	echo "<p>Voila c'est termin&eacute; ! Vous pouvez <a href=\"",$wakkaConfig["base_url"],"\">retourner sur votre site WikiNi</a>. Il est conseill&eacute; de retirer l'acc&egrave;s en &eacute;criture au fichier <tt>cartowiki/conf/cartowiki.config.php</tt>. Ceci peut &ecirc;tre une faille dans la s&eacute;curit&eacute;.</p>";

}
else
{
	// complain
	echo"<p><span class=\"failed\">AVERTISSEMENT:</span> Le
fichier de configuration <tt>cartowiki/conf/cartowiki.config.php</tt> n'a pu &ecirc;tre
cr&eacute;&eacute;. Veuillez vous assurez que votre serveur a les droits d'acc&egrave;s en &eacute;criture pour ce fichier. Si pour une raison quelconque vous ne pouvez pas faire &ccedil;a vous devez copier les informations suivantes dans un fichier et les transf&eacute;rer au moyen d'un logiciel de transfert de fichier (ftp) sur le serveur dans un fichier <tt>wakka.config.php</tt> directement dans le r&eacute;pertoire  cartowiki/conf . Une fois que vous aurez fait cela, votre Carto devrait fonctionner correctement.</p>\n";
	?>
	<form action="<?php echo  myLocation() ?>?installAction=writeconfig" method="POST">
	<input type="hidden" name="config" value="<?php echo addslashes(htmlentities(serialize($config2))) ?>">
	<input type="submit" value="Essayer &agrave; nouveau">
	</form>
	<?php
	echo"<div style=\"background-color: #EEEEEE; padding: 10px 10px;\">\n<xmp>",$configCode,"</xmp>\n</div>\n";
}

// try to copy  file

test("Copie action mapview.php ...",@copy(dirname(__FILE__).'/../actions/mapview.php',dirname(__FILE__).'/../../actions/mapview.php'),"",0);
test("Copie formatter wakka.php ...",@copy(dirname(__FILE__).'/../formatters/wakka.php',dirname(__FILE__).'/../../formatter/wakka.php'),"",0);


?>
