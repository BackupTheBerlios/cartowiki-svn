<?php
/*
install.php

*/


if (!defined('CARTOWIKI_VERSION')) {
	echo "acces direct interdit";
	exit;
}


/**Fonction retournerInfoRequete() - Retourne le type de requête sql et le nom de la table touchée.
*
* Cette fonction retourne un tableau associatif contenant en clé 'table_nom' le nom de la table touchée
* et en clé 'type' le type de requête (create, alter, insert, update...).
* Licence : la même que celle figurant dans l'entête de ce fichier
* Auteurs : Jean-Pascal MILCENT
*
* @author Jean-Pascal MILCENT <jpm@tela-botanica.org>
* @return string l'url courante.
*/
function retournerInfoRequete($sql)
{
    $requete = array();
    if (preg_match('/(?i:CREATE TABLE) +(.+) +\(/', $sql, $resultat)) {
        if (isset($resultat[1])) {
            $requete['table_nom'] = $resultat[1];
        }
        $requete['type'] = 'create';
    } else if (preg_match('/(?i:ALTER TABLE) +(.+) +/', $sql, $resultat)) {
        if (isset($resultat[1])) {
            $requete['table_nom'] = $resultat[1];
        }
        $requete['type'] = 'alter';
    } else if (preg_match('/(?i:INSERT INTO) +(.+) +(?i:\(|VALUES +\()/', $sql, $resultat)) {
        if (isset($resultat[1])) {
            $requete['table_nom'] = $resultat[1];
        }
        $requete['type'] = 'insert';
    } else if (preg_match('/(?i:UPDATE) +(.+) +(?i:SET)/', $sql, $resultat)) {
        if (isset($resultat[1])) {
            $requete['table_nom'] = $resultat[1];
        }
        $requete['type'] = 'update';
    }
    return $requete;
}


/**
 * Removes comment lines and splits up large sql files into individual queries
 *
 * Last revision: September 23, 2001 - gandon
 * Origine : fonction provenant de PhpMyAdmin version 2.6.0-pl1
 * Licence : GNU
 * Auteurs : voir le fichier Documentation.txt ou Documentation.html de PhpMyAdmin.
 *
 * @param   array    the splitted sql commands
 * @param   string   the sql commands
 * @param   integer  the MySQL release number (because certains php3 versions
 *                   can't get the value of a constant from within a function)
 *
 * @return  boolean  always true
 *
 * @access  public
 */
function PMA_splitSqlFile(&$ret, $sql, $release)
{
    // do not trim, see bug #1030644
    //$sql          = trim($sql);
    $sql          = rtrim($sql, "\n\r");
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = FALSE;
    $nothing      = TRUE;
    $time0        = time();

    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for
        // backquotes that can't be escaped
        if ($in_string) {
            for (;;) {
                $i         = strpos($sql, $string_start, $i);
                // No end of string found -> add the current substring to the
                // returned array
                if (!$i) {
                    $tab_info = retournerInfoRequete($sql);
                    $ret[] = array('query' => $sql, 'table_nom' => $tab_info['table_nom'], 'type' => $tab_info['type']);
                    return TRUE;
                }
                // Backquotes or no backslashes before quotes: it's indeed the
                // end of the string -> exit the loop
                else if ($string_start == '`' || $sql[$i-1] != '\\') {
                    $string_start      = '';
                    $in_string         = FALSE;
                    break;
                }
                // one or more Backslashes before the presumed end of string...
                else {
                    // ... first checks for escaped backslashes
                    $j                     = 2;
                    $escaped_backslash     = FALSE;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }
                    // ... if escaped backslashes: it's really the end of the
                    // string -> exit the loop
                    if ($escaped_backslash) {
                        $string_start  = '';
                        $in_string     = FALSE;
                        break;
                    }
                    // ... else loop
                    else {
                        $i++;
                    }
                } // end if...elseif...else
            } // end for
        } // end if (in string)
       
        // lets skip comments (/*, -- and #)
        else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*')) {
            $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
            // didn't we hit end of string?
            if ($i === FALSE) {
                break;
            }
            if ($char == '/') $i++;
        }

        // We are not in a string, first check for delimiter...
        else if ($char == ';') {
            // if delimiter found, add the parsed part to the returned array
            $retour_sql = substr($sql, 0, $i);
            $tab_info = retournerInfoRequete($retour_sql);
            $ret[]      = array('query' => $retour_sql, 'empty' => $nothing, 'table_nom' => $tab_info['table_nom'], 'type' => $tab_info['type']);
            $nothing    = TRUE;
            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len    = strlen($sql);
            if ($sql_len) {
                $i      = -1;
            } else {
                // The submited statement(s) end(s) here
                return TRUE;
            }
        } // end else if (is delimiter)

        // ... then check for start of a string,...
        else if (($char == '"') || ($char == '\'') || ($char == '`')) {
            $in_string    = TRUE;
            $nothing      = FALSE;
            $string_start = $char;
        } // end else if (is start of string)

        elseif ($nothing) {
            $nothing = FALSE;
        }

        // loic1: send a fake header each 30 sec. to bypass browser timeout
        $time1     = time();
        if ($time1 >= $time0 + 30) {
            $time0 = $time1;
            header('X-pmaPing: Pong');
        } // end if
    } // end for

    // add any rest to the returned array
    if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
        $tab_info = retournerInfoRequete($sql);
        $ret[] = array('query' => $sql, 'empty' => $nothing, 'table_nom' => $tab_info['table_nom'], 'type' => $tab_info['type']);
    }

    return TRUE;
}



/**
 * Reads (and decompresses) a (compressed) file into a string
 *
 * Origine : fonction provenant de PhpMyAdmin version 2.6.0-pl1
 * Licence : GNU
 * Auteurs : voir le fichier Documentation.txt ou Documentation.html de PhpMyAdmin.
 *
 * @param   string   the path to the file
 * @param   string   the MIME type of the file, if empty MIME type is autodetected
 *
 * @global  array    the phpMyAdmin configuration
 *
 * @return  string   the content of the file or
 *          boolean  FALSE in case of an error.
 */
 
function PMA_readFile($path, $mime = '')
{
    global $cfg;

    if (!file_exists($path)) {
        return FALSE;
    }
    switch ($mime) {
        case '':
            $file = @fopen($path, 'rb');
            if (!$file) {
                return FALSE;
            }
            $test = fread($file, 3);
            fclose($file);
            if ($test[0] == chr(31) && $test[1] == chr(139)) return PMA_readFile($path, 'application/x-gzip');
            if ($test == 'BZh') return PMA_readFile($path, 'application/x-bzip');
            return PMA_readFile($path, 'text/plain');
        case 'text/plain':
            $file = @fopen($path, 'rb');
            if (!$file) {
                return FALSE;
            }
            $content = fread($file, filesize($path));
            fclose($file);
            break;
        case 'application/x-gzip':
            if ($cfg['GZipDump'] && @function_exists('gzopen')) {
                $file = @gzopen($path, 'rb');
                if (!$file) {
                    return FALSE;
                }
                $content = '';
                while (!gzeof($file)) {
                    $content .= gzgetc($file);
                }
                gzclose($file);
            } else {
                return FALSE;
            }
           break;
        case 'application/x-bzip':
            if ($cfg['BZipDump'] && @function_exists('bzdecompress')) {
                $file = @fopen($path, 'rb');
                if (!$file) {
                    return FALSE;
                }
                $content = fread($file, filesize($path));
                fclose($file);
                $content = bzdecompress($content);
            } else {
                return FALSE;
            }
           break;
        default:
           return FALSE;
    }
    return $content;
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
		$sortie_verif .= '<h2>Insertion des données du fichier sql </h2>';
		$sql_contenu = PMA_readFile(dirname(__FILE__).'/sql/locations.000001.sql.gz');
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