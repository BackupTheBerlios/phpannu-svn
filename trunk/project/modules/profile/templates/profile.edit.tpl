{**
* profile le profil a éditer.
* capacitiesDescriptions
*}
{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.profileEdit;
   myForm.action = pUrl;
   myForm.submit ();
}
//]]>
</script>
{/literal}
<form name="profileEdit" action="{copixurl dest="profile||valid"}" method="post" class="copixForm">
   <h2>Informations sur le profil</h2>
   <table>
    <tr>
     <th>{i18n key="profile.titleTab.name"}</th>
     <td><input type="text" name="name_cpro" value="{$profile->name|escape:html}" size="50" /></td>
    </tr>
    <tr>
     <th>{i18n key="profile.titleTab.description"}</th>
     <td><textarea name="description_cpro" cols="37">{$profile->description|escape:html}</textarea></td>
    </tr>
   </table>
   
   <h2>{i18n key="profile.title.userInProfil"}</h2>
   {if count ($profile->_users)}
      <table>
       <tr>
        <th>{i18n key="profile.titleTab.user"}</th>
        <th>{i18n key="profile.titleTab.actions"}</th>
       </tr>

       {foreach from=$profile->_users item=user}
       <tr>
        <td>{$user}</td>
        <td><a href="{copixurl dest="profile||removeUser" user=$user}"><img src="{copixurl}img/tools/delete.gif" /></a></td>
       </tr>
       {/foreach}
      </table>
   {else}
   <p>{i18n key="profile.message.noUserInProfil"}</p>
   {/if}
   <a href="#" onclick="doUrl ('{copixurl dest="profile||update" then="userList"}')"><img src="{copixurl}img/tools/add.gif" />Ajouter un utilisateur</a>

   <h2>{i18n key="profile.title.profile"}</h2>
   {if count ($profile->_capabilities)}
      <table>
       <tr>
        <th>{i18n key="profile.titleTab.user"}</th>
        <th>{i18n key="profile.titleTab.actions"}</th>
       </tr>

       {foreach from=$profile->_capabilities item=capability key=$path}
       <tr>
        <td>{$path}</td>
        <td><a
            href="{copixurl dest="profile||removeCapacity" cap=$path}"><img src="{copixurl}img/tools/delete.gif" /></a></td>
       </tr>
       {/foreach}
      </table>
   {else}
   <p>{i18n key="profile.message.noAuthInProfil"}</p>
   {/if}
   <a href="#" onclick="doUrl ('{copixurl dest="profile||update" then="capacitiesList"}')"><img src="{copixurl}img/tools/add.gif" />Ajouter un droit</a>

   <br /><br />
   <input type="submit" value="{i18n key="copix:common.buttons.save"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location='{copixurl dest="profile||"}'" />

</form>