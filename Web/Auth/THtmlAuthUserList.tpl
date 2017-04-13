<zing:THtmlBodyComponent class="admin acl-admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/tables.css" type="text/css" rel="stylesheet" />
<zing:THtmlStyle href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />
<zing:THtmlStyle href="/Zing/Assets/Styles/auth.css" type="text/css" rel="stylesheet" />

<h1>People</h1>

<zing:THtmlForm class="zing">
	<zing:THtmlDivNotify id="divNotify" />
	<zing:THtmlButton authPerms="AuthUserCreate" id="btnAddUser" onClick="addUser" value="Add User" />
	<zing:THtmlButton authPerms="AuthUserDelete" id="btnDeleteUsers" onClick="deleteUsers" value="Delete Selected Users" />

	<zing:THtmlTable authPerms="AuthUserRead" id="tblAuthUsers" class="zing" caption="All Users">
		<zing:THtmlTableColumn authPerms="AuthUserDelete" class="checkbox" title="" onRender="insertCheckbox" />
		<zing:THtmlTableColumn class="username" title="Username" boundProperty="username" onRender="insertUserLink" />
		<zing:THtmlTableColumn class="status" title="Status" onRender="insertStatus" />
		<zing:THtmlTableColumn class="expires" title="Expires" boundProperty="expires" />
		<zing:THtmlTableColumn authPerms="AuthUserGroupRead" class="groups" title="Groups" onRender="insertUserGroups" />
		<zing:THtmlTableColumn authPerms="AuthUserRoleRead" class="roles" title="Roles" onRender="insertUserRoles" />
		<zing:THtmlTableColumn authPerms="AuthUserPermissionRead" class="perms" title="Permissions" onRender="insertUserPermissions" />
	</zing:THtmlTable>
</zing:THtmlForm>