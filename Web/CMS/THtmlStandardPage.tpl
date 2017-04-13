<zing:THtmlBodyComponent onPreRender="setBodyId" />
<zing:THtmlPageTitle order="false" onPreRender="insertPageTitle" />
<zing:THtmlHeadComponent visible="2">
	<meta name="description" content="<zing:THtmlAttributeText boundProperty="abstract" />" />
</zing:THtmlHeadComponent>

<zing:THtmlHeadKeywords>
	<zing:TCompositeControl id="body" boundProperty="body" />	
</zing:THtmlHeadKeywords>

<div class="clear">&nbsp;</div>

<zing:THtmlDiv class="admin-edit" authPerms="CmsPageEdit">
	<zing:THtmlLink module="Zing/Web/CMS/THtmlStandardPageEdit" onPreRender="setEditLink">Edit Page</zing:THtmlLink>
</zing:THtmlDiv>

