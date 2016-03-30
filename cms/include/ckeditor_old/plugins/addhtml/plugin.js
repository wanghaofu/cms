CKEDITOR.plugins.add( 'addhtml',
{
	beforeInit : function( editor )
	{
		editor.ui.addHandler( CKEDITOR.UI_ADDHTML, CKEDITOR.ui.addhtml.handler );
	}
});

CKEDITOR.UI_ADDHTML = 'addhtml';


CKEDITOR.ui.addhtml = function( definition )
{
	CKEDITOR.tools.extend( this, definition,
		// Set defaults.
		{
			title		: definition.label,
			className	: definition.className || ( definition.command && 'cke_addhtml_' + definition.command ) || '',
			click		: definition.click || function( editor )
				{
					editor.execCommand( definition.command );
				},
			execute : definition.execute || function(){}
		});
	this._ = {};
};


CKEDITOR.ui.addhtml.handler =
{
	create : function( definition )
	{
		return new CKEDITOR.ui.addhtml( definition );
	}
};

CKEDITOR.ui.addhtml._ =
{
	instances : [],

	keydown : function( index, ev )
	{

	},

	focus : function( index, ev )
	{

	}
};

( function()
{
CKEDITOR.ui.addhtml.prototype =
{
	canGroup : true,

	render : function( editor, output )
	{
		var env = CKEDITOR.env,
			id = this._.id = CKEDITOR.tools.getNextId(),
			html = this.html,
			command = this.command, // Get the command name.
			clickFn,
			index;
			
		this._.editor = editor;
			
		var instance =
		{
			id : id,
			editor : editor,
			focus : function()
			{
			}
		};
		
		output.push(html);

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


CKEDITOR.ui.prototype.addHTML = function( name, definition )
{
	this.add( name, CKEDITOR.UI_ADDHTML, definition );
};

CKEDITOR.on( 'reset', function()
	{
		CKEDITOR.ui.addhtml._.instances = [];
	});
