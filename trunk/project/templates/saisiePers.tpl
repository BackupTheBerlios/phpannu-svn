{**
Saisie d'une nouvelle personne
**}
<script language="JavaScript" type="text/javascript">
var nombreDeLigneEMail = 1;
var nombreDeLigneAdresse = 1;

function nouvelleLigneEmail() {literal}{{/literal}
 var tbody = document.getElementById("tableEMail").getElementsByTagName("tbody")[0];
 var row = document.createElement("tr");
 
 var cell_2 = document.createElement("td");
 cell_2.setAttribute("vAlign","top");
 
 var ctrl_select = document.createElement("select");
 ctrl_select.setAttribute("name", "emailCat|"+nombreDeLigneEMail);
  {foreach from=$listeEmailCat item=emailCat}
   var ctrl_option{$emailCat->id} = document.createElement("option");
   ctrl_option{$emailCat->id}.text = "{$emailCat->valeur}";
   ctrl_option{$emailCat->id}.value = "{$emailCat->id}";
   try {literal}{{/literal}
    ctrl_select.add(ctrl_option{$emailCat->id}, null); // standards compliant; doesn't work in IE
   {literal}}{/literal}
   catch(ex) {literal}{{/literal}
    ctrl_select.add(ctrl_option{$emailCat->id}); // IE only
   {literal}}{/literal}
 {/foreach}
 
 cell_2.appendChild(ctrl_select);
  
 var cell_1 = document.createElement("td"); 
 cell_1.setAttribute("vAlign","top");
 
 var ctrl_1 =  document.createElement("input");
 ctrl_1.setAttribute("type","text");
 ctrl_1.setAttribute("name","email|"+nombreDeLigneEMail);
 ctrl_1.setAttribute("size","40");
 ctrl_1.setAttribute("value","");
 
 cell_1.appendChild(ctrl_1);
 
 row.appendChild(cell_2);
 row.appendChild(cell_1);
 
 tbody.appendChild(row);
 
 document.getElementById("numberOfMail").value = nombreDeLigneEMail; 
 nombreDeLigneEMail = nombreDeLigneEMail + 1;

{literal}}{/literal}

function nouvelleLigneAdresse() {literal}{{/literal}
 var tbody = document.getElementById("tableAdresseLigne").getElementsByTagName("tbody")[0];
 var row = document.createElement("tr");
 
 var cell_1 = document.createElement("td"); 
 cell_1.setAttribute("colspan","2");
 cell_1.setAttribute("vAlign","top");
 
 var ctrl_1 =  document.createElement("input");
 ctrl_1.setAttribute("type","text");
 ctrl_1.setAttribute("name","adresse|"+nombreDeLigneAdresse);
 ctrl_1.setAttribute("size","60");
 ctrl_1.setAttribute("value","");
 
 cell_1.appendChild(ctrl_1);
 
 row.appendChild(cell_1);
 
 tbody.appendChild(row);
 
 document.getElementById("numberOfAdresseLigne").value = nombreDeLigneAdresse; 
 nombreDeLigneAdresse = nombreDeLigneAdresse + 1;

{literal}}{/literal}

</script>
<form name='personne' action='{copixurl dest=||sauverpers}' method="post">
  <table id="tableSaisie">
    <tr>
      <td>Nom : </td>
      <td><input name='nom' type='text' size='40'/></td>
    </tr>
    <tr>
      <td>Prénom : </td>
      <td><input name='prenom' type='text' size='40'/></td>
    </tr>
    <tr>
      <td>Date de naissance : </td>
      <td>{calendar name="date_naiss" value=""}</td>
    </tr>
    <tr>
      <td colspan='2'>Adresse : </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table id="tableAdresseLigne">
          <tbody>
          </tbody>
        </table>
        <input name='numberOfAdresseLigne' id='numberOfAdresseLigne' type='hidden' value='0'/>
      </td>
    </tr>
    <tr>
      <td colspan='2'><input type="button" value="Ajouter une ligne" name="ajouterLigneAdresse" onclick="nouvelleLigneAdresse()"></td>
    </tr>
    <tr>
      <td>Code postale :</td>
      <td><input name='cp' type='text' size='10'/></td>
    </tr>
    <tr>
      <td>Ville :</td>
      <td><input name='ville' type='text' size='40'/></td>
    </tr>
    <tr>
      <td>Pays :</td>
      <td><input name='pays' type='text' size='40'/></td>
    </tr>
    <tr>
      <td>E-Mail : </td>
      <td>
        <table id="tableEMail">
          <tbody>
          </tbody>
        </table>
        <input name='numberOfMail' id='numberOfMail' type='hidden' value='0'/>
      </td>
    </tr>
    <tr>
      <td colspan='2'><input type="button" value="Ajouter un email" name="ajouterLigneEMail" onclick="nouvelleLigneEmail()"></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input type="submit" value="Sauver"></td>
    </tr>
  </table>
</form>
<script language="JavaScript" type="text/javascript">
nouvelleLigneEmail();
nouvelleLigneAdresse();
</script>
