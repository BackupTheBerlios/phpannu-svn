<?php
/**
 * Zone permettant l'affichage du formulaire de saisie des donn�es
 * d'une personne
 */
class ZonePersSaisie extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();
      $dao = CopixDAOFactory::create ('emailcat');
      $tpl->assign('listeEmailCat', $dao->findAll());
      // retour de la fonction :
      $toReturn = $tpl->fetch ('saisiePers.tpl');
      return true;
   }
}
?>