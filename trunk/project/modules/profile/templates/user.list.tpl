{if count ($users)}
<form action="{copixurl dest="admin|addUser"}" method="POST" name="userList" class="copixForm">
   <table class="CopixTable">
   <thead>
    <tr>
     <th>{i18n key="profile.titleTab.user"}</th><th>{i18n key="profile.titleTab.selection"}</th>
    </tr>
    </thead>
    <tbody>
      {foreach from=$users item=name}
       <tr>
         <td>{$name->login}</td>
         <td><input type="checkbox" class="checkbox" name="selectedUsers[]" value="{$name->login}" /></td>
       </tr>
      {/foreach}
   </tbody>   
   </table>

   <br /><br />

   <input type="submit" value="{i18n key="copix:common.buttons.add"}" />
   <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location = '{copixurl dest="admin|edit"}'" />
</form>
{else}
 <p>{i18n key="profile.message.noCapEnable"}</p>
 <input type="button" value="Retour" onclick="javascript:document.location = '{copixurl dest="admin|edit"}'" />
{/if}