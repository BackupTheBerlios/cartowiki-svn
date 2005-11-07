<?php
/*
mapview.php

Copyright 2005 David Delon, some piece of code are borrowed from "carto" by
Yann le Guennec 2005, a project under "Licence Art Libre".

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// ~~ Commune (departement) [commentaire] ~~

/*

TODO : parametre a externaliser (et à enfouir dans les commentaires de la photo ...)
TODO : revoir la notation des parametres
TODO : Documenter l'inclusion des parametres dans l'image , et proposer les deux options
TODO : chantier optimisation :
	cherchez toutes les villes dans une meme requete ...
TODO : zoom sur département ...
TODO : test centrage
TODO : parametre de desactivation du cache
TODO : revoir la notation des commentaires
TODO : centrage point sur la maille optionnel
TODO : retraiter le nom de l'image generer pour syntaxe correcte
TODO : Gerer un peu mieux la suppression des anciennes version : risque de collision si nom de page
debutant de la meme facon
*/


// Gestion du cache :
// Cache de la dernière page uniquement, verrou de mise à jour pris en charge par Wikini
// Forcage rafraichissement par adjonction de &refresh=1 à la requête.
//

include('cartowiki.config.php');

if ($this->page['latest']=='Y') {
	if (!isset($_REQUEST['refresh']) || ($_REQUEST['refresh']!=1)) {
			$cachefile = $CartoWikiConfig['cache_path'].'/'.$this->getPageTag().$this->page['time'].'.cache.txt';
			if (file_exists($cachefile) ) {
		    	include($cachefile);
	    		echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
	    		return;
			}
		ob_start(); //  Gestion du cache
	}
}

// Les parametres sont dans le commentaire Jpeg de l'image, utiliser le programme jhead pour les initialiser
// Libraires de lecture des informations associées à l'image

include_once($CartoWikiConfig['cartowiki_path'].'/bib/metadata/'.'JPEG.php');

// Lecture Parametres de l'action :

// nom de la carte

$src_map = $this->GetParameter('srcmap');
if (!$src_map) {
	echo $this->Format('//Parametre srcmap absent//');
	exit;
}

// Couleur par defaut : vert

// Test valeurs de parametres historique pour compatibilité ascendante
$couleur = $this->GetParameter('color');
if (!$couleur) {
	$couleur = $this->GetParameter('pointcolor');
}

// Taille point par défaut : 10

$point_size=$this->GetParameter('pointsize');
if (!$point_size) {
	$point_size=10;
}

// Fin lecture Parametre de l'action


// Lecture commentaires embarqués dans la page

$comment=get_jpeg_Comment(get_jpeg_header_data($CartoWikiConfig['cartowiki_path'].'/images/'.$src_map));

// Solution facile de lecture, mais difficile à maintenir : notamment la notation
parse_str($comment);



// Fuseau 31T :
// Rappel : Pixel : O,0 en haut gauche

// X Coin inferieur gauche en Pixel
//$Px_echelle_X1['31T']=0;
$Px_echelle_X1['31T'] = $X131T;
// Y Coin inferieur gauche en Pixel
//$Px_echelle_Y1['31T']=539;
$Px_echelle_Y1['31T']=$Y131T;


// Pour calcul Resolution
// X Coin inferieur droit en Pixel
//$Px_echelle_X2['31T']=805;
$Px_echelle_X2['31T']=$X231T;
// Y Coin inferieur droit en Pixel
//$Px_echelle_Y2['31T']=539;
$Px_echelle_Y2['31T']=$Y231T;

// X Coin inferieur gauche en UTM
//$M_UTM_X1['31T']=300092;
$M_UTM_X1['31T']=$X131TUTM;
// Y Coin inferieur gauche en UTM
//$M_UTM_Y1['31T']=4536867;
$M_UTM_Y1['31T']=$Y131TUTM;

// Pour calcul "resolution"

// X Coin inferieur droit en UTM
//$M_UTM_X2['31T']=105371; //
$M_UTM_X2['31T']=$X231TUTM;
// Y Coin inferieur droit en UTM
//$M_UTM_Y2['31T']=5042332;
$M_UTM_Y2['31T']=$Y231TUTM;


// "Resolution"
$p['31T']=($Px_echelle_X2['31T'] - $Px_echelle_X1['31T']) / ($M_UTM_X2['31T'] - $M_UTM_X1['31T']);

// Fuseau 32T :

// Pixel : O,0 en haut gauche

// X Coin inferieur gauche en Pixel
//$Px_echelle_X1['32T']=483;
$Px_echelle_X1['32T']=$X132T;

// Y Coin inferieur gauche en Pixel
//$Px_echelle_Y1['32T']=536;
$Px_echelle_Y1['32T']=$Y132T;

// X Coin inferieur droit en Pixel
//$Px_echelle_X2['32T']=805;
$Px_echelle_X2['32T']=$X232T;
// Y Coin inferieur droit en Pixel
//$Px_echelle_Y2['32T']=536;
$Px_echelle_Y2['32T']=$Y232T;


// X Coin inferieur gauche en UTM
//$M_UTM_X1['32T']=247615;
$M_UTM_X1['32T']=$X132TUTM;
// Y Coin inferieur gauche en UTM
//$M_UTM_Y1['32T']=4540000;
$M_UTM_Y1['32T']=$Y132TUTM;

//$angle3132;

// "Resolution"
$p['32T']=($Px_echelle_X2['31T'] - $Px_echelle_X1['31T'] ) / ($M_UTM_X2['31T'] - $M_UTM_X1['31T']);
//

// Fuseau 30T :

// X Coin inferieur gauche en Pixel
//$Px_echelle_X1['30T']=483;
$Px_echelle_X1['30T']=$X130T;

// Y Coin inferieur gauche en Pixel
//$Px_echelle_Y1['30T']=536;
$Px_echelle_Y1['30T']=$Y130T;

// X Coin inferieur droit en Pixel
//$Px_echelle_X2['30T']=805;
$Px_echelle_X2['30T']=$X230T;
// Y Coin inferieur droit en Pixel
//$Px_echelle_Y2['30T']=536;
$Px_echelle_Y2['30T']=$Y230T;

// X Coin inferieur gauche en UTM
//$M_UTM_X1['30T']=247615;
$M_UTM_X1['30T']=$X130TUTM;
// Y Coin inferieur gauche en UTM
//$M_UTM_Y1['30T']=4540000;
$M_UTM_Y1['30T']=$Y130TUTM;

// angle
//$a=356.0; // (-4 degre)
//$angle3031;

// "Resolution"
$p['30T']=($Px_echelle_X2['31T'] - $Px_echelle_X1['31T'] ) / ($M_UTM_X2['31T'] - $M_UTM_X1['31T']);


// Initialisation

$usemap='';
$ellipse='';

unset($_SESSION['location']);

$dest_map = $this->getPageTag().$this->page['time'].'.jpg';

$img = imagecreatefromjpeg($CartoWikiConfig['cartowiki_path'].'/images/'.$src_map);

switch ($couleur) {
		case 'green':
		   $fill = imagecolorallocate($img, 0, 255, 0);
		   break;
		case 'red':
		   $fill = imagecolorallocate($img, 255, 0, 0);
		   break;
		case 'blue':
		   $fill = imagecolorallocate($img, 0, 0, 255);
		   break;
		case 'black':
		   $fill = imagecolorallocate($img, 0, 0, 0);
		   break;
		default:
		   $fill = imagecolorallocate($img, 0, 255, 0);
}


echo "<a name=\"topmap\"></a>";

// Lecture des localités :
// Ordre de recherche
// 1 : Correspondance exacte : localite + departement
// 2 : Correspondance exacte : localite
// 3 : Correspondance approchée : localite sans le departement si présent
// 4 : Correspondance approchée : soundex sur la ville
// 5 : (On pourrait faire un super soundex  ici ...)


if (preg_match_all('/~~(.*)~~/',$this->page['body'],$locations)){
	$i=0;
	foreach ($locations[1] as $location){
		// extraction commentaire, si present
		preg_match('/\[(.*)\]/',$location,$comments);
		$comment=$comments[1];
		if ($comment) {
			// On enleve le commentaire, c'est plus simple pour la suite (c'est bof hein)
			$location=preg_replace('/\[(.*)\]/', '', $location);
			$comment=' : '.$comment;
		}
		// La ville et le departement ont ete passe en parametre
		preg_match('/(.*)\((.*)\)/',$location,$elements);
		if ($elements[1]) {
			$name=$elements[1];
			$code=$elements[2];
			$utm=$this->LoadSingle("select * from locations where name = '".mysql_escape_string($name)."' and code = '".mysql_escape_string($code)."' limit 1");
		}
		else {
			// Seule la ville a ete passe en parametre
			preg_match('/(.*)/',$location,$elements);
			$name=$elements[1];
			$utm=$this->LoadSingle("select * from locations where name = '".mysql_escape_string($name)."' limit 1");
		}
		if (!$utm) {
			// On a rien trouvé : nouvelles tentatives
			// Ville seule
			$utm=$this->LoadSingle("select * from locations where name = '".mysql_escape_string($name)."' limit 1");

			// Toujours rien ?
			// Ville soundex
			if (!$utm) {
				$utm=$this->LoadSingle("select * from locations where soundex(name) = soundex('".mysql_escape_string($name)."') limit 1");
				 //on stocke ce qu'on a trouver avec le  soundex    pour l'afficher
				if ($utm) {
					$_SESSION['location'] [$i]='AF';
					$_SESSION['location_message'] [$i]=$utm['name'].' '.$utm['code'];
				}
			}
			else {
				// on stocke ce qu'on a trouver sans le departement pour l'afficher
				$_SESSION['location'] [$i]='AF';
				$_SESSION['location_message'] [$i]=$utm['name'].' '.$utm['code'];
			}
		}

		// C'est trouvé !

		if ($utm) {

			// On centre le point au milieu de la maille 10x10 par defaut ...

			$pad = str_repeat ('0' ,(7 - strlen( $utm['x_utm'])));
			$utm['x_utm'] = $pad.$utm['x_utm'];

			$pad = str_repeat ('0' ,(7 - strlen( $utm['y_utm'])));
			$utm['y_utm'] = $pad.$utm['y_utm'];

			$utm['x_utm']=substr($utm['x_utm'] ,0,3);
			$utm['x_utm'] =$utm['x_utm'].'5000';

			$utm['y_utm']=substr($utm['y_utm'] ,0,3);
			$utm['y_utm'] =$utm['y_utm'].'5000';


			// Fuseau 31 T
			if ($utm['sector']=='31T') {
				$x=(($utm['x_utm'] - $M_UTM_X1['31T']) * $p['31T'] ) + $Px_echelle_X1['31T'];
				$y=$Px_echelle_Y2['31T']-(($utm['y_utm'] - $M_UTM_Y1['31T']) * $p['31T'] );
			}
			else {

				// Fuseau 32 T : une rotation + translation est appliquée
				if ($utm['sector']=='32T') {
					$cosa = cos(deg2rad($angle3132));
					$sina = sin(deg2rad($angle3132));

					$xp = (($utm['x_utm'] - $M_UTM_X1['32T']) * $cosa) + (($utm['y_utm']- $M_UTM_Y1['32T']) * $sina);
					$yp = (-($utm['x_utm'] - $M_UTM_X1['32T'])* $sina) + (($utm['y_utm'] - $M_UTM_Y1['32T'])* $cosa);
					$x=($xp * $p['32T'] ) + $Px_echelle_X1['32T'];
					$y=$Px_echelle_Y2['32T']-($yp * $p['32T'] );

				}

				else {
					// Fuseau 30 T : une rotation + translation est appliquée
					if ($utm['sector']=='30T') {

						$cosa = cos(deg2rad($angle3031));
						$sina = sin(deg2rad($angle3031));

						$xp = (($utm['x_utm'] - $M_UTM_X1['30T']) * $cosa) + (($utm['y_utm']- $M_UTM_Y1['30T']) * $sina);
						$yp = (-($utm['x_utm'] - $M_UTM_X1['30T'])* $sina) + (($utm['y_utm'] - $M_UTM_Y1['30T'])* $cosa);
						$x=($xp * $p['30T'] ) + $Px_echelle_X1['30T'];
						$y=$Px_echelle_Y2['30T']-($yp * $p['30T'] );

					}
				}
			}



			$x=round($x);
			$y=round($y);

			$name=$utm['name'];

			// On stocke les commentaires pour affichage dans les tooltips

			$link="<a href=\"#MAP_".$i."\">".$name.$comment."</a>";

			// Commentaire deja présent ? : on ajoute à la suite
			if ($text[$x.'|'.$y]) {
				$link=
				$text[$x.'|'.$y]=$text[$x.'|'.$y].'<br>'.$link;
			}
			// Nouveau commentaire
			else {
				$text[$x.'|'.$y]=$link;
			}

		}
		// Pas trouvé : on stocke l'occurence pour transmission au formateur qui affichera le message d'erreur.

		else {
			$_SESSION['location'] [$i]='NF';
		}
		$i++;
	}

	// Generation maparea + tooltips

	foreach ($text as $coord => $maptext ) {
		list($x,$y)=explode('|',$coord);
		//imagearc($img, $x, $y, 10, 10, 0, 360, $green);
		// Gd2, idealement il faudrait tester la disponibilite de la fonction et se rabbatre sur imagearc sinon
		imagefilledellipse($img, $x, $y, $point_size, $point_size, $fill);
		// pas de double quote dans le texte
		$maptext=preg_replace("/'/", "\'", $maptext);
		$maptext=preg_replace("/\"/", "\\'", $maptext);

		$usemap=$usemap."<area shape=\"circle\" alt=\"\" coords=\"".$x.",".$y.",5\" onmouseover=\"this.T_BGCOLOR='#E6FFFB';this.T_OFFSETX=2;this.T_OFFSETY=2;this.T_STICKY=1;return escape('".$maptext."')\" href=\"#\"/>";

	}

	// Generation direct sur ancienne version de la page ... sinon generation en cache

	if (($this->page['latest']=='N') || (isset($_REQUEST['refresh']) && ($_REQUEST['refresh']==1))) {
		imageinterlace($img,1);
		imagejpeg($img, $CartoWikiConfig['cache_path'].'/'.$dest_map,95);
		imagedestroy($img);
	}


	echo "<img src=\"".($CartoWikiConfig['cache_path'].'/'.$dest_map)."\" style=\"border:none; cursor:crosshair\" alt=\"\" usemap=\"#themap\"></img><br />\n";
	echo "<map name=\"themap\" id=\"themap\">";
	echo $usemap;
	echo "</map>";



	echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"".$CartoWikiConfig['cartowiki_path'].'/bib/tooltip/'."wz_tooltip.js\"></script>";

}

// Affichage image origine
else {
	echo "<img src=\"".$CartoWikiConfig['cartowiki_path'].'/images/'.$src_map."\" style=\"border:none; cursor:crosshair\" alt=\"\"</img><br />\n";
	echo "</map>";
}


// Fin gestion du cache

if ($this->page['latest']=='Y') {
	if (!isset($_REQUEST['refresh']) || ($_REQUEST['refresh']!=1)) {
		// Suppresion texte en cache
		foreach(glob($CartoWikiConfig['cache_path'].'/'.$this->getPageTag().'*'.'.cache.txt') as $fn) {
	    	   unlink($fn);
		}
		// Suppresion image en cache
		foreach(glob($CartoWikiConfig['cache_path'].'/'.$this->getPageTag().'*'.'.jpg') as $fn) {
	    	   unlink($fn);
		}

		// Generation image cache

		imageinterlace($img,1);
		imagejpeg($img, $CartoWikiConfig['cache_path'].'/'.$dest_map,95);
		imagedestroy($img);

		// Generation texte cache

		$fp = fopen($cachefile, 'w');
		$mapview_output = ob_get_contents();
		fwrite($fp, $mapview_output);
		fclose($fp);
		ob_end_clean();
		echo $mapview_output;
	}
}

?>
