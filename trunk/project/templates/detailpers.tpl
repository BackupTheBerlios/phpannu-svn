{**
* Template d'affichage du détail d'une personne
* @param $personne Un objet personne représentant la personne à afficher
*}

<table border="1">
  <tr>
    <td width='300'>Nom</td>
    <td <width='300'>{$personne->nom}</td>
  </tr>
  <tr>
    <td >Prénom</td>
    <td>{$personne->prenom}</td>
  </tr>
  <tr>
    <td>Date de naissance</td>
    <td>{$personne->date_naiss}</td>
  </tr>
  <tr>
    <td>Adresses</td>
    <td>
        <table>
        {**{foreach from=$personne->getAdresses() item=adresse}
          {foreach from=$adresse->getLignes() item=ligne}
          <tr>
            <td colspan='2'>{$ligne->valeur}az</td>
          </tr>
          {/foreach}
          <tr>
            <td>{$adresse->cp}</td>
            <td>{$adresse->ville}</td>
          </tr>
          <tr>
            <td colspan='2'>{$adresse->pays}</td>
          </tr>
        {/foreach}**}
        </table>
    </td>
  </tr>
  <tr>
    <td>Emails</td>
    <td>
        <table>      
        {foreach from=$personne->getEmails() item=email}
          <tr>
            <td>{$email->categorie} : </td>
            <td>{$email->valeur}</td>
          </tr>
        {/foreach}
        </table>
      </td>
  </tr>
</table>