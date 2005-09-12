<?php

class DAORecordpersonne {
  var $emails = null;
  var $adresses = null;
  var $id = null;
  
  /**
   * Cette méthode permet de récupèrer la liste des emails
   * de la personne
   * @return Une liste d'objet email
   */
  function getEmails() {
  	debug($this->id,'DAORecordpersonne->getEmails id');
    $dao = CopixDAOFactory::create ('email');
    $criteres = CopixDAOFactory::createSearchConditions();
    $criteres->addCondition('id_pers', '=', $this->id);
    debug($dao->findBy($criteres),'DAORecordpersonne->getEmails result');
    return $dao->findBy($criteres);
  }
  
  /**
   * Permet de récupèrer la liste des adresses
   * de la personne
   * @return Une liste d'objets adresse
   */
  function getAdresses() {
    $dao = CopixDAOFactory::create ('adresse');
    $criteres = CopixDAOFactory::createSearchConditions();
    $criteres->addCondition('id_pers', '=', $this->_compiled->id);
    return $dao->findBy($criteres);
  }
  
}

class DAOpersonne {
  /**
   * Redefinition de la méthode delete par défaut du DAO
   * Cette méthode efface en cascade tous les enregistrements
   * associés à une personne lors de l'effacement de cette
   * personne.
   * @param $id L'id de la personne à effacer 
   */
  function delete($id) {
  	$personne = $this->_compiled->get($id);
  	$daoEmails = CopixDAOFactory::create ('email');
  	$emails = $personne->getEmails();
  	foreach ($emails as $email) {
  		$daoEmails->delete($email->id);
  	}
  	$daoAdresses = CopixDAOFactory::create ('adresse');
  	$adresses = $personne->getAdresses();
  	foreach ($adresses as $adresse) {
  		$daoAdresses->delete($adresse->id);
  	}
  	$this->_compiled->_compiled_delete($id);
  }
}

?>