<?php
/**
 * Zone permettant l'affichage du détail d'une personne
 * à partir de l'id de la personne. On va récupèrer l'objet
 * représentant la personne et on place dans le template la
 * variable personne qui contient cet objet.
 * Cette zone doit être appelée avec comme paramètre idPers qui
 * contient l'id de la personne.
 */
class ZoneDetailPers extends CopixZone {
   function _createContent (&$toReturn) {
      $tpl = & new CopixTpl ();
      $dao = CopixDAOFactory::create ('personne');
      $criteres = CopixDAOFactory::createSearchConditions();
      $idPers = $this->params['idPers'];
      $criteres->addCondition('id', '=', $idPers);
      $listePers = $dao->findBy($criteres);
      $tpl->assign ('personne',  $listePers[0]);
      $toReturn = $tpl->fetch ('detailpers.tpl');
      return true;
   }
}
?>