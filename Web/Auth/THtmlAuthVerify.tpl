<zing:THtmlBodyComponent class="admin auth-admin" id="auth-verify-account" />
<h1>Account Verification</h1>

<zing:THtmlStyle id="cssForms" href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />

<zing:THtmlForm id="frmVerifyAccount" class="zing">
	<zing:THtmlDivNotify id="divNotify" />
	
	<fieldset class="field-group">

		<p>Welcome <zing:THtmlInnerText id="username" />,<p>
		
		<p>You are about to verify your account.  To do so, you must create a password.</p>
	
		<p>Please enter the same password into both fields to that we can be sure it has been entered consistently.</p>
	
		<zing:THtmlInputCombo id="password1" type="password" label="Password" required="true" />
		<zing:THtmlInputCombo id="password2" type="password" label="Repeat Password" required="true" />
		
	</fieldset>

	<fieldset class="button-group">
		<span class="default">
			<zing:THtmlButton id="btnVerify" onClick="onVerify" value="Verify Account" />
			or <a href="/">Cancel</a>
		</span>
	</fieldset>

</zing:THtmlForm>

<zing:THtmlDiv id="divPostVerify" visible="false">
	<h2>Congratulations!</h2>

	<p><strong>You have now successfully verified your account.</strong></p>

	<zing:THtmlDiv tag="p" authGuest="true">You can continue to access the site, and access secure portions of the site
	after logging in, using the username (<zing:THtmlInnerText boundProperty="username" />)
	and password supplied</zing:THtmlDiv>

	<zing:THtmlDiv tag="p" authGuest="false">You are now logged onto the site and can access the
	additional functionality that your account profile provides.</zing:THtmlDiv>

	<zing:THtmlLink onPreRender="setHomepageAddress">Continue to Site Homepage</zing:THtmlLink>

</zing:THtmlDiv>

<zing:THtmlDiv id="divInvalidVerificationCode" class="notification notification-error" visible="false">
	<h2>Invalid Verification Code</h2>

	<p>You are attempting to verify an account that does not exist, or that has already been verified.</p>

	<p>Please check that the web address that you have used is complete (your email client may have
	truncated or split the address).</p>

	<p>If you have already verified this account using this same web address, you should now be able to
	login to the site using the details you previously provided.</p>

	<p>If you continue to have difficulty logging into the site or verifying your account, please
	contact the site administrator.</p>

	<zing:THtmlLink onPreRender="setHomepageAddress">Home</zing:THtmlLink>
</zing:THtmlDiv>
