<?php

class DAORecordadresse {
  var $lignes = null;
  var $id = null;
  
  function getLignes() {
    $dao = CopixDAOFactory::create ('adresseligne');
    $criteres = CopixDAOFactory::createSearchConditions();
    $criteres->addCondition('id_adresse', '=', $this->id);
    return $dao->findBy($criteres);
  }

  
}

?>