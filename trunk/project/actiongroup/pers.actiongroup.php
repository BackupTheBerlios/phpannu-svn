<?php
/**
 * Gestion des personnes
 */
class ActionGroupPers extends CopixActionGroup {
   /**
   * Affiche la liste complète des personnes
   */
  function getListeToutesPers (){
      $tpl = & new CopixTpl ();
      $tpl->assign ('TITLE_PAGE', 'Liste de toutes les personnes');
      $tpl->assign ('MAIN', CopixZone::process ('PersListe'));
      return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
   }
  
  /**
   * Cette méthode permet d'afficher le détail d'une personne.
   * La requete doit contenir une variable idPers qui contient
   * l'id de la personne à afficher.
   **/
  function afficherDetail() {
      $tpl = & new CopixTpl ();
      $tpl->assign ('TITLE_PAGE', "Detail d'une personne");
      $tpl->assign ('MAIN', CopixZone::process ('DetailPers', array('idPers' => $this->vars['idPers'])));
      return new CopixActionReturn (COPIX_AR_DISPLAY, $tpl);
  }
  
  /**
   * Cette méthode permet de supprimer une personne et toutes
   * ses informations liées.
   */
  function supprimer() {
  	$idPers = $this->vars['idPers'];
  	if ($idPers == null) {
  		//TODO : Erreur
  	}
  	$DAOPers = & CopixDAOFactory::create('personne');
  	$DAOPers->delete($idPers);
  	return $this->getListeToutesPers();
  }
  
  /**
   * Cette méthode à pour role de sauver en base la description
   * d'une personne. Elle est utilisée pour la création d'une nouvelle 
   * personne.
   * TODO : Renommer + vérifier les types 
   **/
  function sauver() {
    $error = false;
    $tpl = & new CopixTpl ();

    $nom = $this->vars['nom'];
    $prenom = $this->vars['prenom'];
    $dateNaissance = $this->vars['date_naiss'];
      
    $DAOPersonne = & CopixDAOFactory::create ('personne');
    $nouvellePers = CopixDAOFactory::createRecord('personne');
    $nouvellePers->nom = $nom;
    $nouvellePers->prenom = $prenom;
    $nouvellePers->date_naiss = $dateNaissance;
    $DAOPersonne->insert($nouvellePers);
    
    $numberOfMails = $this->vars['numberOfMail'];
    $emails = array();
    $emailCategory = array();
    for ($i = 1; $i <= $numberOfMails; $i++) {
      array_push($emails, $this->vars['email|'.$i]);
      array_push($emailCategory, $this->vars['emailCat|'.$i]);
    }
    $DAOEMail = & CopixDAOFactory::create('email');
    if ($emails != null) {
      for ($i = 0; $i < $numberOfMails; $i++) {
        $emailValeur = $emails[$i];
        $emailCatId = $emails[$i];
        $nouvelEmail = CopixDAOFactory::createRecord('email');
        $nouvelEmail->id_pers =$nouvellePers->id;
        $nouvelEmail->valeur = $emailValeur;
        $nouvelEmail->id_cat = $emailCatId;
        $nouvelEmail->defaut = 0;
        $DAOEMail->insert($nouvelEmail);
      }
    }
    
    $numberOfAdresseLigne = $this->vars['numberOfAdresseLigne'];
    $adresseLigne = array();
    for ($i = 1; $i <= $numberOfAdresseLigne; $i++) {
      array_push($adresseLigne, $this->vars['adresse|'.$i]);
    }
    $cp = $this->vars['cp'];
    $ville = $this->vars['ville'];
    $pays = $this->vars['pays'];
    $DAOAdresse = & CopixDAOFactory::create('adresse');
    $nouvelleAdresse = CopixDAOFactory::createRecord('adresse');
    $nouvelleAdresse->id_pers = $nouvellePers->id;
    $nouvelleAdresse->cp = $cp;
    $nouvelleAdresse->ville = $ville;
    $nouvelleAdresse->pays = $pays;
    $DAOAdresse->insert($nouvelleAdresse);
    
    $DAOAdresseLigne = & CopixDAOFactory::create('adresseligne');
    if ($adresseLigne != null) {
      for ($i = 0; $i < $numberOfAdresseLigne; $i++) {
        $valeurLigne = $adresseLigne[$i];
        $nouvelleLigne = CopixDAOFactory::createRecord('adresseLigne');
        $nouvelleLigne->id_adresse = $nouvelleAdresse->id;
        $nouvelleLigne->valeur = $valeurLigne;
        $DAOAdresseLigne->insert($nouvelleLigne);
      }
    }
  
    $url = new CopixUrl();
    $url->set('action','toutespers');
    
    return new CopixActionReturn (COPIX_AR_REDIRECT, $url->getUrl());
  }
  
  /**
   * Cette méthode permet de vérifier la validité d'un objet
   * personne.
   * Un objet personne est valide si :
   * <ul>
   *    <li>Son nom est remplis</li>
   *    <li>Dans le cas d'un ajout, il n'existe pas déjà une autre personne avec le même couple nom/prénom</li>
   *    <li>La date de naissance doit avoir un format valide</li>
   * </ul>
   * @param $personne L'objet personne à valider
   * @param $errors La liste des erreurs
   * @param $warnings La liste des avertissements
   * @return true si la personne est valide, false si il existe des erreurs
   * TODO : A complèter + créer une classe pour les erreurs ...
   **/
  function verifierValiditePersonne($personne, $errors, $warnings) {
    if ($personne->nom == null || $personne->nom == '') {
      array_push($errors, "Le nom de la personne est obligatoire");
    }
    
  }

}
?>