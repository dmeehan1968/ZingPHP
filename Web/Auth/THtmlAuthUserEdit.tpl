<zing:THtmlBodyComponent class="admin acl-admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />

<zing:THtmlForm authPerms="AuthUserEdit" id="frmAuthUser" class="zing">
	
	<zing:THtmlDiv tag="h1" id="UserTitle" />

	<zing:THtmlDivNotify id="divNotify" />
	<fieldset class="field-group">
		<legend>User Details</legend>

		<zing:THtmlInputCombo id="username" label="Username" boundProperty="username" required="true" help="Email address" />
		<zing:THtmlInputCombo id="expires" boundProperty="expires" label="Account Expiration Date" type="text" help="Account expiration date, in the format yyyy-mm-dd hh:mm:ss" />
	</fieldset>

	<fieldset class="button-group">
		<span class="default">
			<zing:THtmlButton id="btnSave" onClick="saveUser" value="Save User" />
		</span>
		<zing:THtmlButton id="btnReset" onClick="resetPassword" value="Reset Password" />
	</fieldset>

</zing:THtmlForm>

<zing:THtmlForm authPerms="AuthUserGroupEdit AuthUserRoleEdit AuthUserPermissionEdit" id="frmAuthUserPermissions" class="zing">

	<h1>User Permissions</h1>

	<zing:THtmlDiv id="divNotifyPermissions" />

	<zing:THtmlDiv authPerms="AuthUserGroupEdit" tag="fieldset" id="fsGroups" class="field-group">
		<legend>Groups</legend>

		<zing:THtmlAssignmentGroup id="Groups" label="Groups control broad access rights" assignedLabel="Assigned Groups" availableLabel="Available Groups" size="8" />	
	</zing:THtmlDiv>

	<zing:THtmlDiv authPerms="AuthUserRoleEdit" tag="fieldset"  id="fsRoles" class="field-group">
		<legend>Roles</legend>

		<zing:THtmlAssignmentGroup id="Roles" label="Roles control general access rights" assignedLabel="Assigned Roles" availableLabel="Available Roles" size="8" />	
	</zing:THtmlDiv>

	<zing:THtmlDiv authPerms="AuthUserPermissionEdit" tag="fieldset" id="fsPermissions" class="field-group">
		<legend>Permissions</legend>

		<zing:THtmlAssignmentGroup id="Permissions" label="Permissions control specific access rights" assignedLabel="Assigned Permissions" availableLabel="Available Permissions" size="8" />	
	</zing:THtmlDiv>

	<zing:THtmlDiv authPerms="AuthUserGroupRead AuthUserRoleRead AuthUserPermissionRead" tag="fieldset" id="fsACL" class="field-group">
		<legend>Expanded ACL</legend>
	
		<p>The following shows the expanded rights inherited from the assigned groups, roles and permissions.</p>

		<zing:THtmlPlainTextCombo authPerms="AuthUserGroupRead" class="acl-groups" label="Groups" onRender="insertExpandedGroups" />
		<zing:THtmlPlainTextCombo authPerms="AuthUserRoleRead" class="acl-roles" label="Roles" onRender="insertExpandedRoles" />
		<zing:THtmlPlainTextCombo authPerms="AuthUserPermissionRead" class="acl-permissions" label="Permissions" onRender="insertExpandedPermissions" />
		
	</zing:THtmlDiv>


</zing:THtmlForm>
