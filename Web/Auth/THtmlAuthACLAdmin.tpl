<zing:THtmlBodyComponent class="admin acl-admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />
<zing:THtmlStyle href="/Zing/Assets/Styles/auth.css" type="text/css" rel="stylesheet" />

<h1>Permissions</h1>

<zing:THtmlForm class="zing" >
	<zing:THtmlDiv id="divNotify" />

	<zing:THtmlButton id="btnRefreshSessionACL" onClick="onRefreshSessionACL" value="Refresh Session ACL" />

</zing:THtmlForm>
	
<zing:AclAdminGroup authPerms="AuthGroupRead" id="groups" legend="Groups" singular="Group" plural="Groups" objectClass="AuthGroup" module="Zing/Web/Auth/THtmlAuthGroupEdit" parameterName="group_id" property="id" />

<zing:AclAdminGroup authPerms="AuthRoleRead" id="roles" legend="Roles" singular="Role" plural="Roles" objectClass="AuthRole"  module="Zing/Web/Auth/THtmlAuthRoleEdit" parameterName="role_id" property="id" />

<zing:AclAdminGroup authPerms="AuthPermissionRead" id="permissions" legend="Permissions" singular="Permission" plural="Permissions" objectClass="AuthPerm" />

<br class="clear" />
	
