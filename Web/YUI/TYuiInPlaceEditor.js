YAHOO.namespace('zing');

(function() {

	YAHOO.zing.Editor = {
		
		getAncestorByTemplate: function(root, template) {
			while (root && (root = root.parentNode)) {
				if (template.acceptNode(root) == NodeFilter.FILTER_ACCEPT) {
					return root;
				}
			}
			return null;
		},
		
		getNodesByTemplate: function(range, template) {
			var nodes = [];
			var walker = document.createTreeWalker(range.commonAncestorContainer, NodeFilter.SHOW_ALL, new YAHOO.zing.RangeFilter(range, template), false);
			
			while ((node = walker.nextNode())) {
				nodes.push(node);
			}
							
			if (nodes.length) {
				return nodes;
			}
			
			return null;
		},
		
		surroundContents: function(range, wrapper, oCfg) {
			if (!oCfg) oCfg = {};
			var root = range.commonAncestorContainer;
			if (root.nodeType == 3) {
				root = root.parentNode;
			}
			var walker = document.createTreeWalker(root, NodeFilter.SHOW_ALL,
								{
									acceptNode: function(node) {
										return NodeFilter.FILTER_ACCEPT;
									}
								},
								false);
			var inRange = false;
			var rangeStarted = false;
			var subRange = document.createRange();
			var node;
			
			while ((node = walker.nextNode())) {
				if (node == range.startContainer) {
					inRange = true;
				}
				
				if (inRange) {
					switch (oCfg.filter.acceptNode(node)) {
					case NodeFilter.FILTER_ACCEPT:
						if (!rangeStarted) {
							subRange.setStart(node, node == range.startContainer ? range.startOffset : 0);
							rangeStarted = true;
						}
						subRange.setEnd(node, node.length);
						break;
					case NodeFilter.FILTER_SKIP:
						break;
					default:
						if (rangeStarted) {
							subRange.surroundContents(wrapper.cloneNode(true));
							rangeStarted = false;
						}
					}
				}
				
				if (node == range.endContainer) {
					subRange.setEnd(range.endContainer, range.endOffset);
					break;
				}

				if (	rangeStarted
						&& oCfg.breakOnBlockExit
						&& oCfg.filter.acceptNode(node.parentNode) != NodeFilter.FILTER_ACCEPT
						&& node == node.parentNode.lastChild) {
					subRange.surroundContents(wrapper.cloneNode(true));
					walker.nextNode();
					walker.lastChild();
					rangeStarted = false;
				}
			}
			
			if (rangeStarted) {
				subRange.surroundContents(wrapper.cloneNode(true));
			}

		},
		
		unWrap: function(node) {
			if (node.nodeType != 1) return;
			while (node.firstChild) {
				Dom.insertBefore(node.firstChild, node);
			}
			node.parentNode.removeChild(node);
		}
	};
	
	YAHOO.zing.RangeFilter = function(range, filter) {
		this._filter = filter;
		this._started = false;
		this._range = range;
	}
	
	YAHOO.zing.RangeFilter.prototype = {
		acceptNode: function(node) {
			valid = NodeFilter.FILTER_REJECT;
			if (node == this._range.startContainer) {
				this._started = true;
			}
			
			if (this._started && this._filter.acceptNode(node)) {
				valid = NodeFilter.FILTER_ACCEPT;
			}

			if (node == this._range.endContainer) {
				this._started = false;
			}
			return valid;
		}
	}
	
	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang,
		Event = YAHOO.util.Event,
		Editor = YAHOO.zing.Editor;
	
	YAHOO.zing.EditorCommand = function() {
		
	}
	
	YAHOO.zing.EditorCommand.prototype = {

		execute: function() {
		}
		
	}
	
	YAHOO.zing.TemplateNodeFilter = function(template) {
		this._template = template;
	}
	
	YAHOO.zing.TemplateNodeFilter.prototype = {
		acceptNode: function(node) {
			if (node.nodeType != this._template.nodeType) return false;
			if (node.tagName != this._template.tagName) return false;
			if (this._template.style) {
				for(var i = 0 ; i < this._template.style.length ; i++) {
					var property = this._template.style[i];
					if (Dom.getStyle(node, property) != Dom.getStyle(this._template, property)) {
						return NodeFilter.FILTER_REJECT;
					}
				}
			}
			return NodeFilter.FILTER_ACCEPT;
		}
	}
	
	YAHOO.zing.Strong = function() {
		YAHOO.zing.Strong.superclass.constructor.call(this);

		this.template =  document.createElement('strong');
		this.wrapConfig = { allowMultiple: true, breakOnBlockEntry: true, breakOnBlockExit: true };
			
	}
	
	YAHOO.lang.extend(YAHOO.zing.Strong, YAHOO.zing.EditorCommand, {
		
		execute: function(range) {
		
			var filter = new YAHOO.zing.TemplateNodeFilter(this.template);			
			var startAncestor = Editor.getAncestorByTemplate(range.startContainer, filter);
			var endAncestor = Editor.getAncestorByTemplate(range.endContainer, filter);
			var innerNodes = Editor.getNodesByTemplate(range, filter)
			this.wrapConfig.filter = { acceptNode: function(node) {
					if (node.nodeType == 1) {
						switch(document.defaultView.getComputedStyle(node,null)['display']) {
							case 'block':
							case 'inline-block':
								return NodeFilter.FILTER_REJECT;
						}
					}
					if (node.nodeType == 3 && (node.data == "\n\n" || YAHOO.lang.trim(node.data) == '')) {
						return NodeFilter.FILTER_SKIP;
					}
					return NodeFilter.FILTER_ACCEPT;
				}
			};
			
			// simple case - no matching ancestor or inner nodes
			
			if (!startAncestor && !endAncestor && !innerNodes) {

				Editor.surroundContents(range, this.template.cloneNode(true), this.wrapConfig);

				return true;
			}
			
			// range includes matching child nodes
			
			if (!startAncestor && !endAncestor && innerNodes) {
				
				// unwrap the children

				innerNodes.each(function(node) { Editor.unWrap(node) });
				
				// wrap the whole range
				
				Editor.surroundContents(range, this.template.cloneNode(true), this.wrapConfig);
			
				return true;
			}
			
			// start and end are within the same ancestor
			
			if (startAncestor == endAncestor) {
			
				// remove a portion of an existing wrapper
				
				var startNode = { node: startAncestor.firstChild, offset: range.startOffset };
				var endNode = { node: startAncestor.lastChild, offset: range.endOffset };
			
				Editor.unWrap(startAncestor);
				
				// reapply the region before the current
				
				var initialRange = range.cloneRange();
				
				initialRange.setStart(startNode.node, 0);
				initialRange.setEnd(startNode.node, startNode.offset);
				if ( ! initialRange.collapsed) {
					Editor.surroundContents(initialRange, this.template.cloneNode(true), this.wrapConfig);
				}
				
				// reapply the region after the current
				
				var finalRange = range.cloneRange();
				
				finalRange.setStart(endNode.node, endNode.offset);
				finalRange.setEnd(endNode.node, endNode.length);
				if ( ! finalRange.collapsed) {
					Editor.surroundContents(finalRange, this.template.cloneNode(true), this.wrapConfig);
				}
				
				return true;
			}
			
			// range starts in an existing ancestor but ends in another one
			
			if (startAncestor && endAncestor && startAncestor != endAncestor) {
			
				var startNode = startAncestor.firstChild;
				var endNode = endAncestor.lastChild;
				
				Editor.unWrap(startAncestor);
				Editor.unWrap(endAncestor);
				
				range.setStartBefore(startNode);
				range.setEndAfter(endNode);
				Editor.surroundContents(range, this.template.cloneNode(true), this.wrapConfig);
				return true;
				
			}
			
			// range starts before existing ancestor
			
			if (!startAncestor && endAncestor) {
			
				var endNode = endAncestor.lastChild;
				
				Editor.unWrap(endAncestor);
				
				range.setEndAfter(endNode);
				Editor.surroundContents(range, this.template.cloneNode(true), this.wrapConfig);
				return true;
			}
			
			// range extends after current ancestor
			
			if (startAncestor && !endAncestor) {
			
				var startNode = startAncestor.firstChild;
				
				Editor.unWrap(startAncestor);
				
				range.setStartBefore(startNode);
				Editor.surroundContents(range, this.template.cloneNode(true), this.wrapConfig);
				return true;
			}
			
			return false;
		
		}
		
	});
	
}());

(function() {

	var Dom = YAHOO.util.Dom,
		Lang = YAHOO.lang,
		Event = YAHOO.util.Event;
	
	YAHOO.zing.Toolbar = function(el, attrs) {
		
		if (Lang.isString(el)) {
			el = Dom.get(el);
			if (! el) {
				el = document.createElement('div');
			}
		}

		if (!el.id) {
			el.id = Dom.generateId();
		}
		
		YAHOO.zing.Toolbar.superclass.constructor.call(this, el, attrs);
		
	}
	
	YAHOO.lang.extend(YAHOO.zing.Toolbar, YAHOO.util.Element,
		{
			SCOPE_TEXT: 1,
			SCOPE_ELEMENT: 2,
			SCOPE_MIXED: 3,
			
			DOM_ELEMENT_NODE: 1,
			DOM_TEXT_NODE: 3,
			
			render: function(el) {
				if (el) {
					this.set('container', el);
				}
				
				var p = this.get('panel');
				
				p.cfg.applyConfig(this.get('panelConfig'));
				p.cfg.queueProperty('context', this.get('context'));
				/**
				 * Potential fix for Firefox bug that causes editing caret to
				 * disappear if toolbar is dragged before the caret is placed in
				 * the editable region
				 */
				if (YAHOO.env.ua.gecko) {
					p.dragEvent.subscribe(function(type, args, me) {
						if (args[0] == 'endDrag') {
							/* refresh focus on the editable element */
							me.blur();
							me.focus();
						}
					}, this.get('context')[0]);
				}
				/**
				 * Config must be refreshed immediately after rendering to
				 * adjust context
				 */
//				Lang.later(10, p.cfg, function() { this.refresh(); });
				Event.onContentReady(p.id, function() { this.refresh(); }, p.cfg, true);
				
				p.setHeader('Tools');
				p.setBody('');
				p.render(this.get('element'));

				var toolbar = new YAHOO.widget.Toolbar(p.body, {
					buttonType: 'advanced',
					draggable: false,
					buttons: [
						{ group: 'textstyle', label: 'Font Style',
							buttons: [
								{ type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold',
									command: new YAHOO.zing.Strong
								},
								{ type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic',
									command: new YAHOO.zing.Strong
								},
								{ type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline',
									command: new YAHOO.zing.Strong
								},
								{ type: 'separator' },
								{ type: 'color', label: 'Font Color', value: 'forecolor', disabled: true },
								{ type: 'color', label: 'Background Color', value: 'backcolor', disabled: true }
							]
						},
						{ type: 'separator' },
						{ group: 'alignment', label: 'Alignment',
							buttons: [
								{ type: 'push', label: 'Align Left CTRL + SHIFT + [', value: 'justifyleft' },
								{ type: 'push', label: 'Align Center CTRL + SHIFT + |', value: 'justifycenter' },
								{ type: 'push', label: 'Align Right CTRL + SHIFT + ]', value: 'justifyright' },
								{ type: 'push', label: 'Justify', value: 'justifyfull' }
							]
						},
						{ type: 'separator' },
						{ group: 'indentlist', label: 'Lists',
							buttons: [
								{ type: 'push', label: 'Create an Unordered List', value: 'insertunorderedlist' },
								{ type: 'push', label: 'Create an Ordered List', value: 'insertorderedlist' }
							]
						},
						{ type: 'separator' },
						{ group: 'insertitem', label: 'Insert Item',
							buttons: [
								{ type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
								{ type: 'push', label: 'Insert Image', value: 'insertimage' }
							]
						}
					]							
					});
			
				toolbar.on('buttonClick', this._handleButtonClick, this, true);
							
				this.addClass(this.get('skin'));
				this.appendTo(this.get('container'));
			},
			
			_handleButtonClick: function(ev) {
				try {
					YAHOO.log(ev.button.value);
					var sel = window.getSelection();
					var range = sel.getRangeAt(0);
					ev.button.command.execute(range);
				} catch (e) {
					YAHOO.log(e,'error', 'TYuiInPlaceEditor');
				}
			},
			
			initAttributes: function(oConfigs) {
				YAHOO.zing.Toolbar.superclass.initAttributes.call(this, oConfigs);
				
				this.setAttributeConfig('container', { writeOnce: true, value: document.body });
				
				this.setAttributeConfig('skin', { value: 'yui-skin-sam' });
				
				this.setAttributeConfig('context', { value: [ document.body, 'tl', 'tl' ] });
				
				this.setAttributeConfig('panel',
						{
							writeOnce: true,
							value: new YAHOO.widget.Panel(Dom.generateId())													
						});

				this.setAttributeConfig('panelConfig',
					{
						value: 
						{
							close: false,
							draggable: true,
							dragOnly: true,
							constraintoviewport: true,
							visible: true
						}
					});
			},
			
			destroy: function() {
				this.get('panel').destroy();
				this.get('container').removeChild(this.get('element'));
			}
		});	
})();

YAHOO.register('Toolbar', YAHOO.zing.Toolbar, {version: '0.1', build: '1'});

(function() {

	var Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event,
		Element = YAHOO.util.Element;

	YAHOO.zing.TYuiInPlaceEditor = function(el) {
		// constructor
		try {

			this.element = Dom.get(el);
			if (this.element.id == '') {
				this.element.id = Dom.generateId();
			}
			YAHOO.log('ID: ' + this.element.id);
			
			Event.onContentReady(this.element.id, this.init, this, true);
			
		} catch (e) {
			YAHOO.log(e);
		}

	};
	 
	YAHOO.zing.TYuiInPlaceEditor.prototype = {
		
		oConfig: {
			hoverBorderSize: 8,
			hoverBorderPadding: 5,
			hoverBorderColor: 'silver',
			editingBorderColor: 'green',
			toolbarSkin: 'yui-skin-sam'
		},
			
		oStatus: {
			isHovering: false,
			isEditing: false,
			origBorder: null,
			origMargin: null,
			origPadding: null,
			origOverflow: null
		},
		
		init: function(ev) {
			try {

				Event.on(this.element, 'mouseover', this._handleMouseOver, this, true);
				
			} catch (e) {
				YAHOO.log(e,'error', 'TYuiInPlaceEditor');
			}
		},
		
		_nullIfEmpty: function(val) {
			if (YAHOO.lang.isString(val) && YAHOO.lang.trim(val) == '') {
				return null;
			}
			return val;
		},
		
		/**
		 * Turn on the editing highlights
		 */
		
		enableEditingHighlight: function() {
			this.oStatus.isHovering = true;
			this.oStatus.origBorder = this._nullIfEmpty(this.element.style.border);
			this.oStatus.origMargin = this._nullIfEmpty(this.element.style.margin);
			this.oStatus.origPadding = this._nullIfEmpty(this.element.style.padding);
			this.element.style.border = this.oConfig.hoverBorderSize + 'px solid ' + this.oConfig.hoverBorderColor;
			this.element.style.margin = - (this.oConfig.hoverBorderSize + this.oConfig.hoverBorderPadding) + 'px';
			this.element.style.padding = this.oConfig.hoverBorderPadding + 'px';
			YAHOO.log('Editing Hightlights enabled for ' + this.element.tagName + '#' + this.element.id);
		},
		
		/**
		 * Turn off the editing highlights
		 */
		
		disableEditingHighlight: function() {
			this.oStatus.isHovering = false;
			this.element.style.border = this.oStatus.origBorder;
			this.element.style.margin = this.oStatus.origMargin;
			this.element.style.padding = this.oStatus.origPadding;
			YAHOO.log('Editing Highlights disabled for ' + this.element.tagName + '#' + this.element.id);
		},
		
		enableEditingControls: function() {
			this.oStatus.isEditing = true;
			this.element.contentEditable = true;
			this.element.style.borderColor = this.oConfig.editingBorderColor;
			this.oStatus.origOverflow = this._nullIfEmpty(this.element.style.overflow);
			this.element.style.overflow = 'hidden';
			
			this.toolbar = new YAHOO.zing.Toolbar('toolbar',
								{
									skin: this.oConfig.toolbarSkin,
									context: [ this.element, 'tl', 'tr' ]
								});
			this.toolbar.render();

			document.execCommand('insertbronreturn', false, false);
			document.execCommand('stylewithcss', false, true);
			YAHOO.log('Editing enabled for ' + this.element.tagName + '#' + this.element.id);
		},

		disableEditingControls: function() {
			this.oStatus.isEditing = false;
			this.element.contentEditable = false;
			this.element.style.borderColor = this.oConfig.hoverBorderColor;
			this.element.style.overflow = this.oStatus.origOverflow;
			this.element.style.overflow = 'visible';
			this.toolbar.destroy();
			YAHOO.log('Editing disabled for ' + this.element.tagName + '#' + this.element.id);
		},
		
		/**
		 * Event handler for initial mouse over event.  If not already in a
		 * hovering state, enable highlighting and track further events
		 */
		
		_handleMouseOver: function(ev) {
			try {
				if (this.oStatus.isHovering == false) {
					this.enableEditingHighlight();
					Event.on(this.element, 'mouseout', this._handleMouseOut, this, true);
					Event.on(document.body, 'mousedown', this._handleMouseDown, this, true);
				}
				
			} catch(e) {
				YAHOO.log(e,'error', 'TYuiInPlaceEditor');
			}		
		},
		
		/**
		 * Event handler for mouse out during initial highlighting.  Disable
		 * highlighting and stop tracking events
		 */
		
		_handleMouseOut: function(ev) {
			try {
				if (! Dom.isAncestor(ev.currentTarget, ev.relatedTarget)) {
					this.disableEditingHighlight();
					Event.removeListener(this.element, 'mouseout', this._handleMouseOut);
				}
			} catch (e) {
				YAHOO.log(e,'error', 'TYuiInPlaceEditor');
			}
		},
		
		_handleMouseDown: function(ev) {
			try {
				if (! Dom.isAncestor(this.element, ev.target) && ! Dom.isAncestor(this.toolbar.get('element'), ev.target)) {
					this.disableEditingControls();
					this.disableEditingHighlight();
					Event.removeListener(document.body, 'mousedown', this._handleMouseDown);
				} else {
					if (!this.oStatus.isEditing && ev.button == 2) {
						Event.removeListener(this.element, 'mouseout', this._handleMouseOut);
						this.enableEditingControls();
						Event.preventDefault(ev);
					}
				}
			} catch (e) {
				YAHOO.log(e, 'error', 'TYuiInPlaceEditor');
			}
		}
		
	}
})();

YAHOO.register('TYuiInPlaceEditor', YAHOO.zing.TYuiInPlaceEditor, { version: "0.0.1", build: "1" });
