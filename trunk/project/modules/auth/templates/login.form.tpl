{**
* LoginZone template.
*}
{if $failed}
<p>{i18n key="auth.failedToLogin"}</p>
{/if}

{if $user eq null}
  <form action="{copixurl dest="auth||in"}" method="post" id="loginForm">
      <fieldset>
      <table>
       <tr>
        <th>{i18n key=auth|auth.login}</th>
        <td><input type="text" name="login" id="login" size="9" value="{$login}" /></td>
       </tr>
       <tr>
        <th>{i18n key=auth|auth.password}</th>
        <td><input type="password" name="password" id="password" size="9" /></td>
       </tr>
       {if $showRememberMe}
       <tr>
        <th>{i18n key=auth|auth.rememberMe}</th>
        <td><input type="checkbox" name="rememberMe" id="rememberMe" value="1" /></td>
       </tr>
       {/if}
       </table>
       <input type="submit" value="{i18n key="auth.buttons.login"}" />
       </fieldset>
   </form>
   {if $showLostPassword}
      <a href="{copixurl dest="auth||lostPasswordAsk"}">{i18n key="auth.lostPasswordAsk"}</a>
   {/if}
{else}
	<h2>{$user->login}</h2>
	<p>
     <a href="{copixurl dest="auth||out"}">{i18n key=auth|auth.buttons.logout}</a>
	</p>
{/if}