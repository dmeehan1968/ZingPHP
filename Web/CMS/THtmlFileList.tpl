<zing:THtmlBodyComponent class="admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/tables.css" type="text/css" rel="stylesheet" />
<zing:THtmlStyle href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />
<zing:THtmlStyle href="/Zing/Assets/Styles/cms.css" type="text/css" rel="stylesheet" />

<h1>Files</h1>

<form class="zing">
	<zing:THtmlDiv id="divNotify" />
</form>

<zing:THtmlDiv id="divCmsFileList">
	
	<zing:THtmlForm authPerms="CmsFileList" id="frmCmsFileDelete" class="zing">
	
		<zing:THtmlDiv tag="fieldset" id="fsCmsFileList" authPerms="CmsFileCreate">
			<zing:THtmlDiv tag="legend" id="pathTitle" />
			<zing:THtmlPager id="pagerCmsFileList" pageRequestVar="page" itemCount="10">
				<zing:THtmlTable id="tblCmsFileList" class="zing" caption="All Files">
					<zing:THtmlTableColumn class="checkbox" authPerms="CmsFileDelete" title="" onRender="insertCheckbox" />
					<zing:THtmlTableColumn class="filename" title="Filename" boundProperty="filename" onRender="insertPreviewLink" />
					<zing:THtmlTableColumn class="type" title="Type" boundProperty="type" />
					<zing:THtmlTableColumn class="size" title="Size" boundProperty="size" />
					<zing:THtmlTableColumn class="modified" title="Modified" boundProperty="mtime" onRender="formatTime" />
					<zing:THtmlTableColumn class="created" title="Created" boundProperty="ctime" onRender="formatTime" />
					<zing:THtmlTableColumn class="permissions" title="Permissions" boundProperty="perms" />
				</zing:THtmlTable>
			</zing:THtmlPager>
		</zing:THtmlDiv>
	
		<zing:THtmlDiv tag="fieldset" id="fsCmsFileDelete" authPerms="CmsFileDelete">
			<zing:THtmlButton id="btnDeleteFiles" onClick="deleteFiles" value="Delete Selected Files" />
		</zing:THtmlDiv>
	
	</zing:THtmlForm>
	
	<zing:THtmlForm authPerms="CmsFileCreate" id="frmCmsFileUpload" class="zing" enctype="multipart/form-data">
			<zing:THtmlDiv tag="fieldset" id="fsCmsFileUpload" authPerms="CmsFileCreate">
				<legend>Upload File</legend>
			
				<zing:THtmlFileUploadCombo id="upload" maxfilesize="256M" label="Choose a file to upload" type="upload" />
			</zing:THtmlDiv>
		
			<zing:THtmlDiv tag="fieldset" authPerms="CmsFileCreate">
				<zing:THtmlButton id="btnAddFile" onClick="addFile" value="Add File" />
			</zing:THtmlDiv>
		
	</zing:THtmlForm>
	
	<zing:THtmlForm authPerms="CmsFileCreate" id="frmCmsFileCreateFolder" class="zing">
		<zing:THtmlDiv id="divCmsFileCreateFolder" authPerms="CmsFileCreate">
				
			<zing:THtmlDiv tag="fieldset" id="fsCmsFileCreate" authPerms="CmsFileCreate">
				<legend>Create Folder</legend>
			
				<zing:THtmlInputCombo id="folder" label="Folder name" />
			</zing:THtmlDiv>
		
			<zing:THtmlDiv tag="fieldset" authPerms="CmsFileCreate">
				<zing:THtmlButton id="btnAddFolder" onClick="addFolder" value="Add Folder" />
			</zing:THtmlDiv>
		
		</zing:THtmlDiv>
	
	</zing:THtmlForm>
	
	<br class="clear" />

</zing:THtmlDiv>