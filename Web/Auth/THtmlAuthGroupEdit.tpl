<zing:THtmlBodyComponent class="admin acl-admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />

<zing:THtmlForm id="frmAuthGroupEdit" class="zing">

	<zing:THtmlDiv tag="h1" id="GroupTitle" />

	<zing:THtmlDiv id="divNotify" />

	<zing:THtmlDiv tag="fieldset"  id="fsRoles" class="field-group">
		<legend>Roles</legend>

		<zing:THtmlAssignmentGroup id="Roles" label="Roles control general access rights" assignedLabel="Assigned Roles" availableLabel="Available Roles" size="8" />	
	</zing:THtmlDiv>

	<zing:THtmlDiv tag="fieldset" id="fsPermissions" class="field-group">
		<legend>Permissions</legend>

		<zing:THtmlAssignmentGroup id="Permissions" label="Permissions control specific access rights" assignedLabel="Assigned Permissions" availableLabel="Available Permissions" size="8" />	
	</zing:THtmlDiv>

	<zing:THtmlDiv tag="fieldset" id="fsACL" class="field-group">
		<legend>Expanded ACL</legend>
	
		<p>The following shows the expanded rights inherited from the assigned roles and permissions.</p>

		<zing:THtmlPlainTextCombo class="acl-permissions" label="Permissions" onRender="insertExpandedPermissions" />
		
	</zing:THtmlDiv>


</zing:THtmlForm>
