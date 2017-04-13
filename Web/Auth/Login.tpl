<zing:THtmlDiv tag="h1" id="loginTitle"></zing:THtmlDiv>
<zing:THtmlBodyComponent class="login" />

<zing:THtmlStyle id="cssForms" href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />

<zing:THtmlForm id="frmLogin" class="zing">
	<zing:THtmlDivNotify id="divNotify" />
	<zing:THtmlInput id="referer" type="hidden"  />
	<zing:THtmlInputCombo id="username" label="Username" required="true" help="Your username, for example, email address" />
	<zing:THtmlInputCombo id="password" type="password" label="Password" required="true" />

	<fieldset class="button-group">
		<span class="default">
			<zing:THtmlButton id="btnLogin" onClick="doLogin" value="Login" />
		</span>
		For forgotten/lost passwords, enter username and <zing:THtmlButton id="btnResetPassword" onClick="doReset" value="Reset Password" />
	</fieldset>

</zing:THtmlForm>