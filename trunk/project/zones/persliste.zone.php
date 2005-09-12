<?php
/**
 * Zone permettant l'affichage d'une liste de personnes
 */
class ZonePersListe extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();

      $dao = CopixDAOFactory::create ('personne');

      //Préparation de la liste des personnes
      $tpl->assign ('listePers', $dao->findAll());

      // retour de la fonction :
      $toReturn = $tpl->fetch ('pers.tpl');
      return true;
   }
}
?>