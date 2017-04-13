<zing:THtmlBodyComponent class="admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/tables.css" type="text/css" rel="stylesheet" />
<zing:THtmlStyle href="/Zing/Assets/Styles/cms.css" type="text/css" rel="stylesheet" />

<h1>Pages</h1>

<zing:THtmlForm authPerms="CmsPageList" class="zing">
	<zing:THtmlDiv id="divNotify" />
	<zing:THtmlButton authPerms="CmsPageCreate" id="btnAddPage" onClick="addPage" value="Add Page" />
	<zing:THtmlButton authPerms="CmsPageDelete" id="btnDeletePages" onClick="deletePages" value="Delete Selected Pages" />

	<zing:THtmlTable id="tblCmsPages" class="zing" caption="All Pages">
		<zing:THtmlTableColumn class="state" onRender="setPageState" />
		<zing:THtmlTableColumn class="checkbox" authPerms="CmsPageDelete" title="" onRender="insertCheckbox" />
		<zing:THtmlTableColumn class="uri" title="Uri" boundProperty="uri" onRender="insertPreviewLink" />
		<zing:THtmlTableColumn class="page" title="Page">
			<zing:THtmlDiv tag="h2" boundProperty="title" />
			<zing:THtmlDiv>
				<zing:THtmlDiv tag="span" authPerms="CmsPageEdit" onRender="insertEditLink" >(Edit)</zing:THtmlDiv>
			</zing:THtmlDiv>
		</zing:THtmlTableColumn>
		<zing:THtmlTableColumn class="abstract" title="Abstract">
			<zing:THtmlFormattedDiv boundProperty="abstract" />
		</zing:THtmlTableColumn>
	</zing:THtmlTable>
</zing:THtmlForm>