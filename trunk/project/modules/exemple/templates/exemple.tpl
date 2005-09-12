<div style="border:1px dashed grey;width:50%">
<h3>{i18n key="exemple.hello"} <strong>{$nom}</strong></h3>

<p>{i18n key="exemple.examplezone"}</p>
<form action="{$url}" method="get">
<fieldset><legend>{i18n key="exemple.typeAName"}</legend>

{foreach from=$params item=valeur key=cle}
<input type="hidden" name="{$cle}" value="{$valeur}" />
{/foreach}

<label>{i18n key="exemple.name"} : <input type="text" name="nom" /></label>
<input type="submit" value="{i18n key="exemple.validate"}" />
</fieldset>

</form>
</div>

<p><a href="index.php">{i18n key="exemple.backToHomePage"}</a></p>
