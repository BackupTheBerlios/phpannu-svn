{literal}
<script type="text/javascript">
//<![CDATA[
function doUrl (pUrl) {
   var myForm = document.groupEdit;
   myForm.action = pUrl;
   myForm.submit ();
   return false;
}
//]]>
</script>
{/literal}

<form name="groupEdit" action="{copixurl dest="admin|valid"}" method="post" class="copixForm">
   <h2>{i18n key="profile.title.groupInformation"}</h2>
<fieldset>
   <table>
    <tr>
     <th>{i18n key=copix:profile.dao.CopixGroup.fields.name_cgrp}</th>
     <td><input type="text" name="name_cgrp" value="{$group->name_cgrp|escape:html}" size="48" /></td>
    </tr>
    <tr>
     <th>{i18n key=copix:profile.dao.CopixGroup.fields.description_cgrp}</th>
     <td><textarea name="description_cgrp" cols="40" rows="5">{$group->description_cgrp|escape:html}</textarea></td>
    </tr>
    <tr>
     <th>{i18n key=copix:profile.dao.CopixGroup.fields.all_cgrp}</th>
     <td><input type="checkbox" class="checkbox" name="all_cgrp" {if $group->all_cgrp}checked="checked"{/if} /></td>
    </tr>
    <tr>
     <th>{i18n key=copix:profile.dao.CopixGroup.fields.known_cgrp}</th>
     <td><input type="checkbox" class="checkbox" name="known_cgrp" {if $group->known_cgrp}checked="checked"{/if} /></td>
    </tr>
   </table>
</fieldset>
   <h2>{i18n key="profile.title.userInGroup"}</h2>
   {if count ($group->_users)}
      {* there are some users in the group*}
<fieldset>
      <table class="CopixTable">
      <thead>
       <tr>
        <th>{i18n key="copix:profile.dao.CopixUser.fields.login_cusr"}</th>
        <th class="actions">{i18n key="copix:common.actions.title"}</th>
       </tr>
       </thead>
       <tbody>
       {foreach from=$group->_users item=user}
       <tr  {cycle values=',class="alternate"'}>
        <td>{$user}</td>
        <td>
        <a href="#" onclick="javascript:doUrl('{copixurl dest="users|admin|prepareEdit" login=$user}')"><img src="{copixurl}img/tools/show.png" alt="{i18n key="copix:common.buttons.show"}" /></a>
        <a href="{copixurl dest="admin|removeUser" user=$user}" onclick="return doUrl ('{copixurl dest="admin|removeUser" user=$user}')"><img src="{copixurl}img/tools/delete.png"  alt="{i18n key=copix:common.buttons.delete}" title="{i18n key=copix:common.buttons.delete}" /></a>
        </td>
       </tr>
       {/foreach}
       </tbody>
      </table>
   {else}
     {* No users in the group *}
     <p>{i18n key="profile.message.noUser"}</p>
   {/if}
   <br />
   <a href="{copixurl dest="admin|update" then="userList"}" onclick="return doUrl ('{copixurl dest="admin|update" then="userList"}')"><img src="{copixurl}img/tools/add.png" alt="{i18n key="copix:common.buttons.new"}" />Ajouter un utilisateur</a>
</fieldset>
   <h2>{i18n key="profile.title.groupCapabilities"}</h2>
<fieldset>
   <p>
   {i18n key=copix:profile.dao.CopixGroup.fields.isadmin_cgrp}
   <input type="checkbox" class="checkbox" name="isadmin_cgrp" {if $group->isadmin_cgrp}checked="checked"{/if} />
   </p>
   {if count ($group->_capabilities)}
      {* there are capabilities in the group *}
      {foreach from=$arCapabilities item=capability}
      <a href="{copixurl dest="admin|capabilitiesList" capability=$capability->name_ccpb}">{$capability->description_ccpb}</a>
      <br />
      {/foreach}
   {else}
      <p>{i18n key="profile.message.noCapabilities"}</p>
   {/if}

   <br />
   <a href="{copixurl dest="admin|update" then="capabilitiesKind"}" onclick="return doUrl ('{copixurl dest="admin|update" then="capabilitiesKind"}')"><img src="{copixurl}img/tools/add.png" alt="{i18n key="copix:common.buttons.new"}" />{i18n key="profile.messages.addProfile"}</a>
</fieldset>
   <br />
   <p class="validButtons">
   <input type="submit" value="{i18n key="copix:common.buttons.save"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location.href='{copixurl dest="admin|"}'" />
   </p>
</form>
