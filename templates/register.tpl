{if $error}<div class="error">{$error}</div>{/if}
{if $message}<div class="message">{$message}</div>{/if}
{if $success}{include file="login.tpl"}
{else}
{jojoHook hook="register_before_form"}
<form method="post" action="register/" id="register-form">
{if $redirect}<input type="hidden" name="redirect" id="redirect" value="{$redirect}" />{/if}
  <div id="register-form">
    <h3>Registration Information</h3>

      {jojoHook hook="register_top"}
      <label for="firstname">First Name:</label>
	  <input type="text" name="firstname" id="firstname" value="{$firstname}" size="25" /> *<br />

      <label for="lastname">Last Name:</label>
	  <input type="text" name="lastname" id="lastname" value="{$lastname}" size="25" /> *<br />

	  <label for="email">Email Address:</label>
	  <input type="text" name="email" id="email" value="{$email}" size="25" /> *<br />

      <label for="reglogin">Username:</label>
	  <input type="text" name="reglogin" id="reglogin" value="{$reglogin}" size="25" /> *<br />

      <label for="regpassword">Password:</label>
	  <input type="password" name="regpassword" id="regpassword" value="{$regpassword}" size="25" onkeypress="checkPassword(this.value)" /> *<br />

      <label for="regpassword2">Confirm Password:</label>
	  <input type="password" name="regpassword2" id="regpassword2" value="{$regpassword2}" size="25" /> *<br />

	  <label>Password Strength:</label>
      <div style="float: left; border: 1px solid #888; background: #fff; width: 80px; padding: 1px;"><div id="progressBar" style=" height: 20px; width: 0;"></div></div><br />

      <label for="reminder">Password Reminder:</label>
	  <input type="text" name="reminder" id="reminder" value="{$reminder}" size="25" /> *<br />

	  {jojoHook hook="register_bottom"}

	  <label for="submit"></label><input class="button" type="submit" name="submit" id="submit" value="Register" />
	  <div class="clear"></div>
  </div>

  {*<div id="register-form">
    <h3>Profile Information (optional)</h3>
    <label for="website">Website:</label>
	<input type="text" name="website" id="website" value="{$website}" size="25" /><br />

    <label for="location">Location:</label>
	<input type="text" name="location" id="location" value="{$location}" size="25" /><br />

    <label for="signature">Forum Signature:</label>
	<input type="text" name="signature" id="signature" value="{$signature}" size="25" /><br />

    <label for="tagline">Forum Tag:</label>
	<input type="text" name="tagline" id="tagline" value="{$tagline}" size="25" /><br />


  </div>*}
</form>
<div class="clear"></div>

{/if}