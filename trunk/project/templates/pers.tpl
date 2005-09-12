<a href="{copixurl dest=||ajouterpers}">Ajouter</a>
<table border="1">
<tr>
  <td width='40'>Nom</td>
  <td width='40'>Prénom</td>
  <td width='60'>Emails</td>
</tr>
{foreach from=$listePers item=objPers}
<tr>
    <td><a href='{copixurl dest=||detailpers idPers=$objPers->id}'>{$objPers->nom}</a></td>
    <td>{$objPers->prenom}</td>
    <td>
      <table>     
      {foreach from=$objPers->getEmails() item=objEmail}
        <tr>
          <td>{$objEmail->valeur}</td>
          <td>{$objEmail->categorie}</td>
        </tr>
      {/foreach}
      </table>
    </td>
    <td><a href='{copixurl dest=||supprpers idPers=$objPers->id}'>Supprimer</td>
</tr>
{/foreach}
</table>
