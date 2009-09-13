{if $error}<div class="error">{$error}</div>{/if}
{if $message}<div class="message">{$message}</div>{/if}
<form method="post" action="user-profile/" enctype="multipart/form-data" class="standard-form">
  <div>

	<h3>Registration Information</h3>

	<label for="">First Name:</label>
	<input type="text" name="firstname" id="firstname" value="{$firstname}" /> *<br />

	<label for="lastname">Last Name:</label>
	<input type="text" name="lastname" id="lastname" value="{$lastname}" /> *<br />

	<label for="email">Email Address:</label>
	<input type="text" name="email" id="email" value="{$email}" /> *<br />

	<label for="lregogin">Username:</label>
	<input type="text" name="reglogin" id="reglogin" value="{$reglogin}" /> *<br />

	{**
	<label for="password">Password:</label>
	<input type="password" name="regpassword" id="regpassword" value="{$regpassword}" /> *<br />

	<label for="regpassword2">Confirm Password:</label>
	<input type="password" name="regpassword2" id="regpassword2" value="{$regpassword2}" /> *<br />
	**}
	<label for="reminder">Password Reminder:</label>
	<input type="text" name="reminder" id="reminder" value="{$reminder}" /><br />

  </div>

  <div>

	<h3>Profile Information</h3>

	<label for="website">Website:</label>
    <input type="text" name="website" id="website" value="{$website}" /><br />

    <label for="location">Location:</label>
    <input type="text" name="location" id="location" value="{$location}" /><br />

    <label for="signature">Forum Signature:</label>
    <input type="text" name="signature" id="signature" value="{$signature}" /><br />

    <label for="tagline">Forum Tag:</label>
    <input type="text" name="tagline" id="tagline" value="{$tagline}" /><br />

    <label for="avatar">Forum Avatar:</label>
	<div class="field">
    {if $avatar}<img src="images/200/users/{$avatar}" alt="" /><br />{$avatar}<br />{/if}
      <input type="file" name="avatar" id="avatar" size="26" value="" />
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
    </div><br />

    <label for="submit">&nbsp;</label>
    <input type="submit" name="submit" id="submit" value="Update &gt;&gt;" class="button" onmouseover="this.className='button buttonrollover';" onmouseout="this.className='button'" /><br />

  </div>

</form>

<h3>Change Password</h3>
<p>Passwords can be changed from our <a href="change-password/" rel="nofollow">change password</a> page.</p>
