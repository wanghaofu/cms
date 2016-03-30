/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.plugins.add( 'checkbox',
{
	beforeInit : function( editor )
	{
		editor.ui.addHandler( CKEDITOR.UI_CHECKBOX, CKEDITOR.ui.checkbox.handler );
	}
});

/**
 * Checkbox UI element.
 * @constant
 * @example
 */
CKEDITOR.UI_CHECKBOX = 'checkbox';

/**
 * Represents a checkbox UI element. This class should not be called directly. To
 * create new checkbox use {@link CKEDITOR.ui.prototype.addCheckbox} instead.
 * @constructor
 * @param {Object} definition The checkbox definition.
 * @example
 */
CKEDITOR.ui.checkbox = function( definition )
{
	// Copy all definition properties to this object.
	CKEDITOR.tools.extend( this, definition,
		// Set defaults.
		{
			title		: definition.label,
			className	: definition.className || ( definition.command && 'cke_checkbox_' + definition.command ) || '',
			click		: definition.click || function( editor )
				{
					editor.execCommand( definition.command );
				},
			execute : definition.execute || function(){}
		});

	this._ = {};
};

/**
 * Transforms a checkbox definition in a {@link CKEDITOR.ui.checkbox} instance.
 * @type Object
 * @example
 */
CKEDITOR.ui.checkbox.handler =
{
	create : function( definition )
	{
		return new CKEDITOR.ui.checkbox( definition );
	}
};

/**
 * Handles a checkbox click.
 * @private
 */
CKEDITOR.ui.checkbox._ =
{
	instances : [],

	keydown : function( index, ev )
	{
		var instance = CKEDITOR.ui.checkbox._.instances[ index ];

		if ( instance.onkey )
		{
			ev = new CKEDITOR.dom.event( ev );
			return ( instance.onkey( instance, ev.getKeystroke() ) !== false );
		}
	},

	focus : function( index, ev )
	{
		var instance = CKEDITOR.ui.checkbox._.instances[ index ],
			retVal;

		if ( instance.onfocus )
			retVal = ( instance.onfocus( instance, new CKEDITOR.dom.event( ev ) ) !== false );

		// FF2: prevent focus event been bubbled up to editor container, which caused unexpected editor focus.
		if ( CKEDITOR.env.gecko && CKEDITOR.env.version < 10900 )
			ev.preventBubble();
		return retVal;
	}
};

( function()
{
	var keydownFn = CKEDITOR.tools.addFunction( CKEDITOR.ui.checkbox._.keydown, CKEDITOR.ui.checkbox._ ),
		focusFn = CKEDITOR.tools.addFunction( CKEDITOR.ui.checkbox._.focus, CKEDITOR.ui.checkbox._ );

CKEDITOR.ui.checkbox.prototype =
{
	canGroup : true,

	/**
	 * Renders the checkbox.
	 * @param {CKEDITOR.editor} editor The editor instance which this checkbox is
	 *		to be used by.
	 * @param {Array} output The output array to which append the HTML relative
	 *		to this checkbox.
	 * @example
	 */
	render : function( editor, output )
	{
		var env = CKEDITOR.env,
			id = this._.id = CKEDITOR.tools.getNextId(),
			classes = this.className,
			command = this.command, // Get the command name.
			checked = this.checked,
			clickFn,
			index;

		this._.editor = editor;

		var instance =
		{
			id : id,
			checkbox : this,
			editor : editor,
			focus : function()
			{
				var element = CKEDITOR.document.getById( id );
				element.focus();
			},
			execute : function()
			{
				this.checkbox.click( editor );
				this.checkbox.execute( this );
			}
		};

		instance.clickFn = clickFn = CKEDITOR.tools.addFunction( instance.execute, instance );

		instance.index = index = CKEDITOR.ui.checkbox._.instances.push( instance ) - 1;

		var name=id+"_"+this.name;
		// Indicate a mode sensitive checkbox.
		if ( this.modes && 0)
		{
			var modeStates = {};
			editor.on( 'beforeModeUnload', function()
				{
					modeStates[ editor.mode ] = this._.state;
				}, this );

			editor.on( 'mode', function()
				{
					var mode = editor.mode;
					// Restore saved checkbox state.
					this.setState( this.modes[ mode ] ?
						modeStates[ mode ] != undefined ? modeStates[ mode ] :
							CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED );
				}, this);
		}
		else if ( command )
		{
			// Get the command instance.
			command = editor.getCommand( command );

			if ( command )
			{
				command.on( 'state', function()
					{
						this.setState( command.state );
					}, this);

			}
		}

		

		
		output.push('<span class="cke_checkbox ' , classes , '" id="' , id ,'">');

		if(this.additional != null){
			 output.push(this.additional);
		}

		output.push(
				'<div class="cke_left" '+
				(CKEDITOR.env.ie ? '' : 'style="padding-top:4px;"') +
				'>' +
				'<input type="checkbox" ' + 
				' name="' , name ,'" id="' , name ,'" ' , (checked ? "checked=checked":"") , 
				' /></div>' +
				'<div class="cke_left" >' +
				'<a class="cke_checkbox_label"' +
				'href="javascript:void(\''+ ( this.label || '' ).replace( "'", '' )+ '\')" ',
				'title="', this.label, '" ' +
				'onclick="document.getElementById(\'' , name , '\').checked=!document.getElementById(\'' , name , '\').checked;',
				'CKEDITOR.tools.callFunction(', clickFn, ',this);return false;">'+
				'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', this.label,
				'</a></div>'+
			'</span>' );

		if ( this.onRender ){
			this.onRender();
		}

		return instance;
	},

	setState : function( state )
	{
		if ( this._.state == state )
			return false;

		this._.state = state;

		var element = CKEDITOR.document.getById( this._.id );

		if ( element )
		{
			element.setState( state );
			state == CKEDITOR.TRISTATE_DISABLED ?
				element.setAttribute( 'aria-disabled', true ) :
				element.removeAttribute( 'aria-disabled' );

			state == CKEDITOR.TRISTATE_ON ?
				element.setAttribute( 'aria-pressed', true ) :
				element.removeAttribute( 'aria-pressed' );

			return true;
		}
		else
			return false;
	}
};

})();

/**
 * Adds a checkbox definition to the UI elements list.
 * @param {String} The checkbox name.
 * @param {Object} The checkbox definition.
 * @example
 * editorInstance.ui.addCheckbox( 'MyBold',
 *     {
 *         label : 'My Bold',
 *         command : 'bold'
 *     });
 */
CKEDITOR.ui.prototype.addCheckbox = function( name, definition )
{
	this.add( name, CKEDITOR.UI_CHECKBOX, definition );
};

CKEDITOR.on( 'reset', function()
	{
		CKEDITOR.ui.checkbox._.instances = [];
	});
