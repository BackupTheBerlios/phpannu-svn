<h2>{i18n key='params.moduleSelection'}</h2>
<form action="{copixurl dest="parameters||selectModule"}" method="post" name="moduleSelect">
   <select name="choiceModule">
      <option value="">{i18n key="params.project"}</option>
   {foreach key=cle from=$moduleList item=moduleCaption key=moduleId}
      <option value="{$moduleId}" {if $moduleId==$choiceModule}selected="selected"{/if}>{$moduleCaption}</option>
   {/foreach}
   </select>
   <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
</form>

<h2>{i18n key='params.paramList'}</h2>
{if count ($paramsList)}
<table class="CopixTable" style="margin-right:50px;">
   <thead>
   <tr>
      <th>{i18n key='params.paramsName'}</th>
      <th>{i18n key='params.paramsDefault'}</th>
      <th>{i18n key='params.paramsCurrentValue'}</th>
      <th class="actions">{i18n key='params.paramsOptions'}</th>
   </tr>
   </thead>
   <tbody>
   {foreach from=$paramsList item=params}
      <tr {cycle values=',class="alternate"'}>
         <td>{$params.Caption|escape}</td>
         <td>{$params.Default|escape}</td>
         {if $params.Name==$editParam}
         <form action="{copixurl dest=parameters||valid choiceModule=$choiceModule idFirst=$choiceModule idSecond=$params.Name}" method="post">
            <td><input type="text" name="value" value="{$params.Value|escape}" size="20" /></td>
            <td><input type="image" src="{copixurl}img/tools/valid.png" value="{i18n key="copix:common.buttons.ok"}" /></form><a href="{copixurl dest="parameters||" choiceModule=$choiceModule}"><img src="{copixurl}img/tools/cancel.png" alt="{i18n key="copix:common.buttons.cancel"}" /></a></td>
         {else}
            <td>{$params.Value|escape}</td>
            <td><a href="{copixurl dest="parameters||" choiceModule=$choiceModule editParam=$params.Name}"><img src="{copixurl}img/tools/update.png" alt="{i18n key='params.edit'}" /></a></td>
         {/if}
      </tr>
   {/foreach}
   </tbody>
</table>
{else}
<p>{i18n key='params.noParam'}</p>
{/if}
