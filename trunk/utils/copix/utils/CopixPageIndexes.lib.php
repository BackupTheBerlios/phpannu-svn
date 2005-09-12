<?php
/**
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixPageIndexes.lib.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes G�rald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

   /**
    * calcul diff�rents index de pages (prec, suivant, fenetre suivante etc...)
    * @param integer $pageCourante      numero page courante (de 1 � ...)
    * @param integer $nbLigneParPage   nombre de ligne par page
    * @param integer $totalLigne      nombre total de ligne
    * @param integer $nbLiensMax      nombre de liens maximum � afficher
    * @return array   tableau associatif contenant les differents index. un index � 0= index inexistant
    */
   function getPageIndexes($pageCourante, $nbLigneParPage, $totalLigne, $nbLiensMax ){
        $pages=array();
      $index=array();
      $pageCourante = intval($pageCourante);
      if($pageCourante<1)
         $pageCourante=1;
      // calcul du nombre de page au total
      $nombrePages = intval($totalLigne / $nbLigneParPage);
      if ($totalLigne % $nbLigneParPage) $nombrePages++;

      // calcul du nombre de fenetre (fenetre = un ensemble de page)
      $nombreFenetre = intval($nombrePages / $nbLiensMax);
      if ($nombrePages % $nbLiensMax) $nombreFenetre++;

      // calcul index fenetre courante
      $fenetreCourante = intval($pageCourante / $nbLiensMax);
      if ($pageCourante % $nbLiensMax) $fenetreCourante++;

      // calcul index page precedente
         $pagePrecedente= ($pageCourante > 1)?$pageCourante-1:0;
      // calcul index page suivante
         $pageSuivante= ($pageCourante < $nombrePages && $nombrePages>1)?$pageCourante+1:0;

      // calcul index fenetre precedente
         $fenetrePrecedente=($fenetreCourante>1)?($fenetreCourante - 1) * $nbLiensMax : 0;

      // calcul index fenetre suivante
         $fenetreSuivante=($fenetreCourante<$nombreFenetre)?$fenetreCourante * $nbLiensMax + 1: 0;


      // calcul des index de page de la fenetre courante
      for ($jump_to_page = 1 + (($fenetreCourante - 1) * $nbLiensMax); ($jump_to_page <= ($fenetreCourante * $nbLiensMax)) && ($jump_to_page <= $nombrePages); $jump_to_page++) {
         $pages[]=$jump_to_page;
      }

      // calcul de l'intervalle du nombre de ligne inclus dans la page
      // (pour affichage du genre : produit 5 � 15)
      $lignesMinPage=($nbLigneParPage * ($pageCourante - 1))+1;
      $lignesMaxPage=($nbLigneParPage * $pageCourante);
      if ($lignesMaxPage > $totalLigne) $lignesMaxPage = $totalLigne;

      $index=array();
      $index['currpage']=$pageCourante;
      $index['prevpage']=$pagePrecedente;
      $index['nextpage']=$pageSuivante;
      $index['prevwin']=$fenetrePrecedente;
      $index['nextwin']=$fenetreSuivante;
      $index['pages']=$pages;
      $index['linemin']=$lignesMinPage;
      $index['linemax']=$lignesMaxPage;
      $index['maxlinks']=$nbLiensMax;
      $index['totallines']=$totalLigne;
      $index['totalpages']=$nombrePages;
      return $index;
   }
?>