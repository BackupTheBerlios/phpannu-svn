{if count ($groups)}
<table class="CopixTable">
 <thead>
 <tr>
  <th>{i18n key="copix:profile.dao.CopixGroup.fields.name_cgrp"}</th>
  <th>{i18n key="copix:profile.dao.CopixGroup.fields.description_cgrp"}</th>
  <th>Actions</th>
 </tr>
 </thead>
 <tbody>
 {foreach from=$groups item=Profile}
   <tr  {cycle values=',class="alternate"'}>
      <td>{$Profile->name_cgrp}</td>
      <td>{$Profile->description_cgrp}</td>
      <td><a
      href="{copixurl dest="admin|prepareEdit" id=$Profile->id_cgrp}"><img src="{copixurl}img/tools/update.png" alt="{i18n key=copix:common.buttons.update}" title="{i18n key=copix:common.buttons.update}" /></a><a
      href="{copixurl dest="admin|remove" id=$Profile->id_cgrp}"><img src="{copixurl}img/tools/delete.png"  alt="{i18n key=copix:common.buttons.delete}" title="{i18n key=copix:common.buttons.delete}"/></a></td>
   </tr>
 {/foreach}
 </tbody>
</table>
{else}
 <p>{i18n key="profile.messages.noGroup"}</p>
 <p>{i18n key="profile.messages.canCreateGroupClickingOn"}</p>
{/if}
<p><a href="{copixurl dest="admin|create"}"><img src="{copixurl}img/tools/new.png" alt="{i18n key=copix:common.buttons.new}" title="{i18n key=copix:common.buttons.new}" />{i18n key="profile.buttons.newGroup"}</a></p>