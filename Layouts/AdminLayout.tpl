<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 	

<html>
	
<zing:THtmlHeadPlaceholder 
	title="Replicated Solutions Content Management System" 
	keywordLoading="5"
	premiumKeywords="web, design, taunton, wiveliscombe, somerset, website, sme, business, internet, traffic, e-commerce, ecommerce, dynamic, static, content, management, system, systems, cms, customer, communicating, accessibility, search, engine, optimisation, seo, usability"
	keywordThreshold="2"
	/>
<zing:THtmlHeadKeywords renderContent="false">
Content Management System
</zing:THtmlHeadKeywords>

<zing:THtmlHeadComponent visible="2">
	<meta name="robots" content="index,follow" />
	<meta name="language" content="en" />
	
</zing:THtmlHeadComponent>

<zing:TYuiLoader require="reset, base, fonts, grids, logger" loadOptional="true" allowRollup="true" onSuccess="YAHOO.util.Event.onAvailable('yui-log', function () { var logreader = new YAHOO.widget.LogReader('yui-log'); });" />

<zing:THtmlBodyPlaceholder>

	<div class="yui-skin-sam">
		<zing:TYuiMenuBar id="menuAdmin" authGuest="false">
			<zing:TYuiMenuBarItem>
				<zing:THtmlLink href="#menuFile">File</zing:THtmlLink>
				<zing:TYuiMenu id="menuFile">
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Open <em class="helptext">Ctrl-O</em></zing:THtmlLink></zing:TYuiMenuItem>
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Close</zing:THtmlLink></zing:TYuiMenuItem>
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Save</zing:THtmlLink></zing:TYuiMenuItem>
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Save As...</zing:THtmlLink></zing:TYuiMenuItem>
				</zing:TYuiMenu>
			</zing:TYuiMenuBarItem>
			<zing:TYuiMenuBarItem>
				<zing:THtmlLink href="#menuEdit">Edit</zing:THtmlLink>
				<zing:TYuiMenu id="menuEdit">
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Cut <em class="helptext">Ctrl-X</em></zing:THtmlLink></zing:TYuiMenuItem>
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Copy <em class="helptext">Ctrl-C</em></zing:THtmlLink></zing:TYuiMenuItem>
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Paste <em class="helptext">Ctrl-V</em></zing:THtmlLink></zing:TYuiMenuItem>
					<zing:TYuiMenuItem><zing:THtmlLink href="#">Undo <em class="helptext">Ctrl-Z</em></zing:THtmlLink></zing:TYuiMenuItem>
				</zing:TYuiMenu>
			</zing:TYuiMenuBarItem>
			<zing:TYuiMenuBarItem authPerms="CmsPageList"><zing:THtmlLink module="Zing/Web/CMS/THtmlPageList">Pages</zing:THtmlLink></zing:TYuiMenuBarItem>
			<zing:TYuiMenuBarItem authPerms="CmsFileList"><zing:THtmlLink module="Zing/Web/CMS/THtmlFileList">Files</zing:THtmlLink></zing:TYuiMenuBarItem>
			<zing:TYuiMenuBarItem authPerms="AuthUserRead"><zing:THtmlLink module="Zing/Web/Auth/THtmlAuthUserList">People</zing:THtmlLink></zing:TYuiMenuBarItem>
			<zing:TYuiMenuBarItem authPerms="AuthGroupEdit"><zing:THtmlLink module="Zing/Web/Auth/THtmlAuthACLAdmin">Permissions</zing:THtmlLink></zing:TYuiMenuBarItem>
			<zing:TYuiMenuBarItem><zing:THtmlLink module="Zing/Web/Auth/Logout">Logout</zing:THtmlLink></zing:TYuiMenuBarItem>
		</zing:TYuiMenuBar>
	</div>

	<div id="doc3" class="yui-t1">
		
		<div id="bd">
			<div id="yui-main">
				<div class="yui-b">
					<zing:THtmlHeadKeywords>
						<zing:TContentPlaceholder />
					</zing:THtmlHeadKeywords>
				</div>
			</div>
		
			<div class="yui-b yui-skin-sam">
				<zing:THtmlCmsNavigation id="nav-main" class="zing-nav-horizontal" />
			</div>
		</div>
			
		<div id="ft">
			<p>Copyright &copy; 2008 by Replicated Solutions Limited.  All Rights Reserved.
		</div>
			
	</div>

	<zing:THtmlGoogleAnalytics />

</zing:THtmlBodyPlaceholder>

</html>