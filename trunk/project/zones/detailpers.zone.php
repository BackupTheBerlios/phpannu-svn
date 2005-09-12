<?php
/**
 * Zone permettant l'affichage du d�tail d'une personne
 * � partir de l'id de la personne. On va r�cup�rer l'objet
 * repr�sentant la personne et on place dans le template la
 * variable personne qui contient cet objet.
 * Cette zone doit �tre appel�e avec comme param�tre idPers qui
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