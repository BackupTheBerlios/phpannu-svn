{**
* LoginZone template.
*
* We expect #login_zone to be defined in the css stylesheet.
* We expect .connectButton to be defined in the css styleSheet.
*}
{if $user eq null}
  <b>{i18n key=auth.titlePage.login}</b>
  <form action="index.php?module=auth&action=in" method="post" id="loginForm" class="copixForm" >
  <input type="hidden" name="auth_url_return" value="{$url_return}" />
  {i18n key=auth|auth.login}
  <input type="text" name="auth_login" id="auth_login" size="9" />&nbsp;
  {i18n key=auth|auth.password}
  <input type="password" name="auth_password" id="auth_password" size="9" />&nbsp;
  <input type="submit" value="{i18n key=auth|auth.buttons.login}" />&nbsp;
  <a href="index.php?module=auth&action=newUserForm" >{i18n key=auth|auth.message.addProfile}</a>
  </form>
{else}
	<b>{$user->login}</b>
   <a href="index.php?module=auth&action=out">{i18n key=auth|auth.buttons.logout}</a>
{/if}
