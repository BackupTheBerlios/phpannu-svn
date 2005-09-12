{**
* Select available groups.
*}
<h2>{i18n key="profile.title.availableGroup"}</h2>
   {if count ($groups)}
    <table class="CopixTable">
    <thead>
      <tr>
       <th>{i18n key="profile.titleTab.name"}</th>
       <th>{i18n key="profile.titleTab.actions"}</th>
      </tr>
     </thead>
     <tbody>
      {foreach from=$groups item=group key=groupId}
         <tr  {cycle values=',class="alternate"'}>
          <td>{$group->name_cgrp}</td>
          <td><input type="button" value="{i18n key="copix:common.buttons.select"}" onclick="javascript:document.location.href='{copixurl appendFrom=$select id_cgrp=$group->id_cgrp}'" />
          </td>
         </tr>
      {/foreach}
     </tbody>
    </table>
   {/if}
<input type="button" onclick="document.location.href='{$back}'" value="{i18n key=copix:common.buttons.back}" />