<zing:THtmlBodyComponent class="admin" />
<zing:THtmlStyle href="/Zing/Assets/Styles/forms.css" type="text/css" rel="stylesheet" />

<zing:TCompositeControl id="YuiEditor">
	<zing:THtmlStyle href="/Zing/Assets/Scripts/yui/assets/skins/sam/skin.css" type="text/css" rel="stylesheet" />
	<zing:THtmlScript src="/Zing/Assets/Scripts/yui/yahoo-dom-event/yahoo-dom-event.js" />
	<zing:THtmlScript src="/Zing/Assets/Scripts/yui/element/element-beta-min.js" />
	<zing:THtmlScript src="/Zing/Assets/Scripts/yui/container/container_core-min.js" />
	<zing:THtmlScript src="/Zing/Assets/Scripts/yui/menu/menu-min.js" />
	<zing:THtmlScript src="/Zing/Assets/Scripts/yui/editor/editor-beta-min.js" />
	<zing:THtmlBodyComponent class="yui-skin-sam" />
	<zing:THtmlScript>
		var Dom = YAHOO.util.Dom,
			Event = YAHOO.util.Event,
			Element = YAHOO.util.Element;
			
		var yuiEditor = new YAHOO.widget.Editor('body', {
			handleSubmit: false,
			height: '300px',
			width: '99.99%',
		    dompath: true,
		    animate: true,
			autoHeight: false // incompatible with HTML editing mode
 		});

		var state = 'off';
		
		yuiEditor.on('toolbarLoaded', function () {
			var codeConfig = {
				type: 'push', label: 'Edit HTML Code', value: 'editcode'
			};
			this.toolbar.addButtonToGroup(codeConfig, 'insertitem');
			
			this.toolbar.on('editcodeClick', function() {
				var ta = this.get('element'),
					iframe = this.get('iframe').get('element');
			
				if (state == 'on') {
					state = 'off';
					this.toolbar.set('disabled', false);
					this.setEditorHTML(ta.value);
					if (!this.browser.ie) {
						this._setDesignMode('on');
					}
			
					Dom.removeClass(iframe, 'editor-hidden');
					Dom.addClass(ta, 'editor-hidden');
					this.show();
					this._focusWindow();
				} else {
					state = 'on';
					this.cleanHTML();
					Dom.addClass(iframe, 'editor-hidden');
					Dom.removeClass(ta, 'editor-hidden');
					this.toolbar.set('disabled', true);
					this.toolbar.getButtonByValue('editcode').set('disabled', false);
					this.toolbar.selectButton('editcode');
					this.dompath.innerHTML = 'Editing HTML Code';
					this.hide();
				}
				return false;
			}, this, true);

			this.on('cleanHTML', function(ev) {
				this.get('element').value = ev.html;
			}, this, true);
			
			this.on('afterRender', function() {
				var wrapper = this.get('editor_wrapper');
				wrapper.appendChild(this.get('element'));
//				this.setStyle('width', '100%');
//				this.setStyle('height', '100%');
				this.setStyle('visibility', '');
				this.setStyle('top', '');
				this.setStyle('left', '');
				this.setStyle('position', '');
				this.addClass('editor-hidden');
			}, this, true);
			
		}, yuiEditor, true);
		
		/*
		 * catch the submit and check if we are in code edit mode, and move
		 * edited data back into the edit control before saving.
		 */
		var form = new Element('frmPage');
		form.on('submit', function(ev) {
			if (state == 'on') {
				var ta = yuiEditor.get('element');
				yuiEditor.setEditorHTML(ta.value);				
			}
			yuiEditor.saveHTML();
		});
		
		yuiEditor.render(); 
	</zing:THtmlScript>
	<zing:THtmlStyle>
		#content form.zing div.body {
			margin-left: 0;
		}
		#content form.zing div.yui-editor-container {
			font-size: larger;
		}
		#content form.zing div.yui-editor-editable-container {
			min-height: 300px;
		}
		
		.yui-skin-sam .yui-toolbar-container .yui-toolbar-editcode span.yui-toolbar-icon {
			background-image: url( /Zing/Assets/Images/icons/html_editor.gif );
			background-position: 0 1px;
			left: 5px;
		}
		.yui-skin-sam .yui-toolbar-container .yui-button-editcode-selected span.yui-toolbar-icon {
			background-image: url( /Zing/Assets/Images/icons/html_editor.gif );
			background-position: 0 1px;
			left: 5px;
		}
		
		#content form.zing div.yui-editor-container .editor-hidden {
		    visibility: hidden;
		    top: -9999px;
		    left: -9999px;
		    position: absolute;
		}

		#content form.zing div.yui-editor-container textarea {
		    border: 0;
		    margin: 0;
		    padding: 0;
			font-family: "Lucida Console", "Courier", mono, fixed;
			line-height: 1.4em;
		}

		
	</zing:THtmlStyle>
</zing:TCompositeControl>

<h1><zing:THtmlInnerText id="pageTitle" /> <zing:THtmlLink id="pageLink" class="form-pagelink">(View Page)</zing:THtmlLink></h1>

<zing:THtmlForm id="frmPage" class="zing">
	<zing:THtmlDiv id="divNotify" />
	<fieldset class="field-group">
		<legend>Page Details</legend>

		<zing:THtmlInputCombo id="uri" label="Uri" boundProperty="uri" required="true" help="e.g. /section/page, using lowercase letters, digits, forward slash (/), underscore and hypen" />
		<zing:THtmlInputCombo id="title" label="Title" boundProperty="title" required="true" help="e.g. The title of the page" />
		<zing:THtmlInputCombo id="published" label="Published" boundProperty="published" required="true" help="e.g. The date and time the document will be publically viewable (yyyy-mm-dd hh:mm:ss)" />
		<zing:THtmlInputCombo id="expires" label="Expires" boundProperty="expires" required="true" help="e.g. The date and time the document will no longer be publically viewable (yyyy-mm-dd hh:mm:ss)" />
		<zing:THtmlInputCombo id="weight" label="Weight" boundProperty="weight" required="true" help="e.g. The relative weight of this page compared to others" />
		<zing:THtmlCheckboxGroupCombo id="draft" label="Draft" boundProperty="draft" help="Tick here if this page is a draft, and will not be made publically available" />
		<zing:THtmlCheckboxGroupCombo id="navigation" label="Navigation" boundProperty="navigation" help="Tick here to include this page in the navigation" />
		<zing:THtmlCheckboxGroupCombo id="sitemap" label="Sitemap" boundProperty="sitemap" help="Tick here to include this page in the sitemap" />
		<zing:THtmlCheckboxGroupCombo id="search" label="Search" boundProperty="search" help="Tick here to include this page in site search results" />
	</fieldset>

	<fieldset>
		<legend>Abstract</legend>
		
		<zing:THtmlTextareaCombo id="abstract" rows="5" label="Abstract" boundProperty="abstract" help="An abstract of the page (used in summaries)" />
	</fieldset>

	<fieldset>
		<legend>Body</legend>
		
		<zing:THtmlTextareaCombo id="body" rows="20" cols="40" label="Body" boundProperty="body" help="The full page content" />
	</fieldset>

	<fieldset class="button-group">
		<zing:THtmlButton id="btnSave" onClick="savePage" value="Save Page" />
	</fieldset>

</zing:THtmlForm>
