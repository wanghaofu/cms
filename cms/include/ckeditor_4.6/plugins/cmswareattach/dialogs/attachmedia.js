(function()
{

	var ATTRTYPE_STR = 1,
		ATTRTYPE_BOOLEAN = 2;


	var attributesMap = 
	{
		'autostart' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : '0' },
		'enablecontextmenu' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : 'true' },
		'clicktoplay' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : 'true' },
		'showcontrols' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : '1' },
		'showstatusbar' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : '1' },
		'showdisplay' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : '0' },
		'loop' : { 'type' : ATTRTYPE_BOOLEAN , 'default' : '1' }
	};
	
	var names = [ 'id' , 'src' , 'name' , 'align' , 'title' , 'class' , 'bgcolor' , 'style' , 'type' ];
	for ( var i = 0 ; i < names.length ; i++ )
		attributesMap[ names[i] ] = { 'type' : ATTRTYPE_STR, 'default' : '' };

	function loadValue( embedNode )
	{
		var attribute = attributesMap[ this.id ];
		if ( !attribute )
			return;
		var isCheckbox = ( this instanceof CKEDITOR.ui.dialog.checkbox );
		if ( embedNode.getAttribute( this.id ) )
		{
			value = embedNode.getAttribute( this.id );
			if ( isCheckbox )
				this.setValue( value.toLowerCase() == 'true' || value == '1' );
			else
				this.setValue( value );
			return;
		}
		else if ( isCheckbox )
			this.setValue( attribute[ 'default' ] == 'true' || attribute[ 'default' ] == '1' );
	}

	function commitValue( embedNode, extraStyles )
	{
		var attribute = attributesMap[ this.id ];
		if ( !attribute )
		{
			return;
		}

		var isRemove = ( attribute.type == ATTRTYPE_STR && this.getValue() === '' ),
			isCheckbox = ( this instanceof CKEDITOR.ui.dialog.checkbox );

		value = this.getValue();
		if ( isRemove)
		{
			embedNode.removeAttribute( attribute.name );
		}
		else if (isCheckbox)
		{
			var d = attribute[ 'default' ];
			value = ( d == '1' || d == '0' ) ?  ( value ? '1' : '0' ) : ( value ? 'true' : 'false' );
			embedNode.setAttribute( this.id, value );
		}
		else 
			embedNode.setAttribute( this.id, value );
	}

	CKEDITOR.dialog.add( 'attachmedia', function( editor )
	{

		var previewPreloader, previewAttributePreloader,
			previewId = 'cke_previewEmbed' + CKEDITOR.tools.getNextNumber();
			previewAreaHtml = '<div>' + CKEDITOR.tools.htmlEncode( editor.lang.common.preview ) +'<br>' +
			'<div id="cke_MediaPreviewLoader' + CKEDITOR.tools.getNextNumber() + '" style="display:none"><div class="loading">&nbsp;</div></div>' +
			'<div id="' + previewId + '" class="FlashPreviewBox" style="height:250px;"></div></div>';

		var mediapreset = {
			'video' : editor.config.videopreset || [300,250],
			'audio' : editor.config.audiopreset || [300,68]
		};

		var updatePreview = function( dialog )
		{
			//Don't load before onShow.
			if ( !dialog.preview )
				return 1;

			// Read attributes and update imagePreview;
			dialog.preview.setHtml( '' );
			if ( dialog.getContentElement( 'info', 'src' ).getValue() != '' )
			{
				var extraStyles = {}, extraAttributes = {};
				dialog.commitContent( previewAttributePreloader, extraStyles, extraAttributes );
				try
				{
					previewAttributePreloader.setStyles( extraStyles );
				}catch(e){}
				var previewhtml = [ '<embed enablepositioncontrols="true"' ];
				for( i in attributesMap )
				{
					previewAttributePreloader.getAttribute( i ) && previewhtml.push( ' ' + i + '="' + CKEDITOR.tools.htmlEncode( previewAttributePreloader.getAttribute( i ) ) + '"' );
				}
				previewhtml.push( '></embed>' );
				dialog.preview.setHtml( previewhtml.join( '' ) );
			}
			return 0;
		};
		return {
			title : editor.config.lang_fix.attachmedia,
			minWidth : 420,
			minHeight : 410,
			onShow : function()
			{
				// Clear previously saved elements.
				this.fakeImage = this.embedNode = null;
				previewPreloader = new CKEDITOR.dom.element( 'embed', editor.document );
				previewAttributePreloader = CKEDITOR.dom.element.createFromHtml( '<cke:embed></cke:embed>', editor.document );
				previewAttributePreloader.setAttributes(
					{
						type : 'application/x-mplayer2',
						'enablepositioncontrols': 'true'
					} );
				// Try to detect any embed or object tag that has Flash parameters.
				var fakeImage = this.getSelectedElement();
				if ( fakeImage && fakeImage.data( 'cke-real-element-type' ) && fakeImage.data( 'cke-real-element-type' ) == 'media' )
				{
					this.fakeImage = fakeImage;

					var embedNode = editor.restoreRealElement( fakeImage );
					this.embedNode = embedNode;

					this.setupContent( embedNode, fakeImage );
				}
				else
				{
					var preset = this.getContentElement( 'info', 'preset' );
					preset.getInputElement().show();
					this.getContentElement( 'info', 'width' ).setValue( mediapreset[preset.getValue()][0] );
					this.getContentElement( 'info', 'height' ).setValue( mediapreset[preset.getValue()][1] );
				}
				this.preview = CKEDITOR.document.getById( previewId );
				updatePreview( this );
			},
			onOk : function()
			{
				// If there's no selected object or embed, create one. Otherwise, reuse the
				// selected object and embed nodes.
				var embedNode = null;
				if ( !this.fakeImage )
				{
					embedNode = CKEDITOR.dom.element.createFromHtml( '<cke:embed></cke:embed>', editor.document );
					embedNode.setAttributes(
						{
							type : 'application/x-mplayer2'
						} );
				}
				else
				{
					embedNode = this.embedNode;
				}

				// A subset of the specified attributes/styles
				// should also be applied on the fake element to
				// have better visual effect. (#5240)
				var extraStyles = {}, extraAttributes = {};
				this.commitContent( embedNode, extraStyles, extraAttributes );
				try
				{
					embedNode.setStyles( extraStyles );
				}catch(e){}
				// Refresh the fake image.
				var newFakeImage = editor.createFakeElement( embedNode, 'cke_media', 'media', true );
				newFakeImage.setAttributes( extraAttributes );
				newFakeImage.setStyles( extraStyles );
				if ( this.fakeImage )
				{
					newFakeImage.replace( this.fakeImage );
					editor.getSelection().selectElement( newFakeImage );
				}
				else
					editor.insertElement( newFakeImage );
			},

			onHide : function()
			{
				if ( this.preview )
					this.preview.setHtml( '' );
			},

			contents : [
				{
					id : 'info',
					label : editor.lang.common.generalTab,
					accessKey : 'I',
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									type : 'hbox',
									widths : [ '280px', '110px' ],
									align : 'right',
									children :
									[
										{
											id : 'src',
											type : 'text',
											label : editor.lang.common.url,
											required : true,
											validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.flash.validateSrc ),
											setup : loadValue,
											commit : commitValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											onLoad : function()
											{
												/*
												var dialog = this.getDialog();
												updatePreview = function( src ){
													previewPreloader.setAttribute( 'src', src );
													dialog.preview.setHtml( '<embed height="100%" width="100%" src="'
														+ CKEDITOR.tools.htmlEncode( previewPreloader.getAttribute( 'src' ) )
														+ '" type="application/x-mplayer2"></embed>' );
												};
												// Preview element
												dialog.preview = dialog.getContentElement( 'info', 'preview' ).getElement().getChild( 3 );

												// Sync on inital value loaded.
												this.on( 'change', function( evt ){

														if ( evt.data && evt.data.value )
															updatePreview( evt.data.value );
													} );
												// Sync when input value changed.
												this.getInputElement().on( 'change', function( evt ){
													updatePreview( this.getValue() );
												}, this );
												*/
											}
										},
										{
											type : 'button',
											id : 'browse',
											filebrowser : 'info:src',
											hidden : true,
											// v-align with the 'src' field.
											// TODO: We need something better than a fixed size here.
											style : 'display:inline-block;margin-top:10px;',
											label : editor.config.lang_fix.browseServer
										}
									]
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '25%', '25%', '25%', '25%', '25%' ],
							children :
							[
								{
									type : 'text',
									id : 'width',
									style : 'width:95px',
									label : editor.lang.common.width,
									validate : CKEDITOR.dialog.validate.integer( editor.lang.common.invalidWidth ),
									setup : function( embedNode, fakeImage )
									{
										loadValue.apply( this, arguments );
										if ( fakeImage )
										{
											var fakeImageWidth = parseInt( fakeImage.$.style.width, 10 );
											if ( !isNaN( fakeImageWidth ) )
												this.setValue( fakeImageWidth );
										}
									},
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : function( embedNode, extraStyles )
									{
										commitValue.apply( this, arguments );
										if ( this.getValue() )
											extraStyles.width = this.getValue() + 'px';
									}
								},
								{
									type : 'text',
									id : 'height',
									style : 'width:95px',
									label : editor.lang.common.height,
									validate : CKEDITOR.dialog.validate.integer( editor.lang.common.invalidHeight ),
									setup : function( embedNode, fakeImage )
									{
										loadValue.apply( this, arguments );
										if ( fakeImage )
										{
											var fakeImageHeight = parseInt( fakeImage.$.style.height, 10 );
											if ( !isNaN( fakeImageHeight ) )
												this.setValue( fakeImageHeight );
										}
									},
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : function( embedNode, extraStyles )
									{
										commitValue.apply( this, arguments );
										if ( this.getValue() )
											extraStyles.height = this.getValue() + 'px';
									}
								},
								{
									type : 'text',
									id : 'hSpace',
									style : 'width:95px',
									label : editor.lang.flash.hSpace,
									validate : CKEDITOR.dialog.validate.integer( editor.lang.flash.validateHSpace ),
									setup : loadValue,
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : commitValue
								},
								{
									type : 'text',
									id : 'vSpace',
									style : 'width:95px',
									label : editor.lang.flash.vSpace,
									validate : CKEDITOR.dialog.validate.integer( editor.lang.flash.validateVSpace ),
									setup : loadValue,
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : commitValue
								}
							]
						},

						{
							type : 'vbox',
							children :
							[
								{
									type : 'html',
									id : 'preview',
									style : 'width:95%;',
									html : previewAreaHtml
								}
							]
						},

						{
							type : 'vbox',
							children :
							[
							/*
								{
									type : 'radio',
									id : 'preset',
									height : '20px',
									style : 'width:30%;',
									'default' : 'video',
									onLoad : function()
									{
										var dialog = this.getDialog(),
										applyPreset = function( value ){
											dialog.getContentElement( 'info', 'width' ).setValue( mediapreset[value][0] );
											dialog.getContentElement( 'info', 'height' ).setValue( mediapreset[value][1] );
										};
										this.on( 'change', function( evt ){
											alert(evt.data);
												if ( evt.data && evt.data.value )
													applyPreset( evt.data.value );
											} );
										this.getInputElement().on( 'change', function( evt ){
											alert(evt.data);
											applyPreset( this.getValue() );
										}, this );
										this.getInputElement().$.onpropertychange = function(){alert(11);}
										alert(this.getInputElement().$.innerHTML);
									},
									setup : function(){
										this.getInputElement().hide();
									},
									items : [
										[ editor.config.lang_fix.media.video , 'video', 'video' ],
										[ editor.config.lang_fix.media.audio , 'audio', 'audio' ]
									]
								}
								*/
								{
									type : 'select',
									id : 'preset',
									'default' : 'video',
									onLoad : function()
									{
										var dialog = this.getDialog(),
										applyPreset = function( value ){
											dialog.getContentElement( 'info', 'width' ).setValue( mediapreset[value][0] );
											dialog.getContentElement( 'info', 'height' ).setValue( mediapreset[value][1] );
										};
										this.on( 'change', function( evt ){
											if ( evt.data && evt.data.value )
												applyPreset( evt.data.value );
										} );
										this.getInputElement().on( 'change', function( evt ){
											applyPreset( this.getValue() );
										}, this );
									},
									setup : function(){
										this.getInputElement().hide();
									},
									items : [
										[ editor.config.lang_fix.media.video , 'video' ],
										[ editor.config.lang_fix.media.audio , 'audio' ]
									]
								}
							]
						}
					]
				},
				/* upload is unavailable in media mode
				{
					id : 'Upload',
					hidden : true,
					filebrowser : 'uploadButton',
					label : editor.lang.common.upload,
					elements :
					[
						{
							type : 'file',
							id : 'upload',
							label : editor.lang.common.upload,
							size : 38
						},
						{
							type : 'fileButton',
							id : 'uploadButton',
							label : editor.lang.common.uploadSubmit,
							filebrowser : 'info:src',
							'for' : [ 'Upload', 'upload' ]
						}
					]
				},
				*/
				{
					id : 'properties',
					label : editor.lang.flash.propertiesTab,
					elements :
					[
						{
							type : 'hbox',
							widths : [ '50%', '50%' ],
							children :
							[
								{
									id : 'align',
									type : 'select',
									label : editor.lang.common.align,
									'default' : '',
									style : 'width : 100%;',
									items :
									[
										[ editor.lang.common.notSet , ''],
										[ editor.lang.common.alignLeft , 'left'],
										[ editor.lang.common.alignBottom , 'bottom'],
										[ editor.lang.common.alignMiddle , 'middle'],
										[ editor.lang.common.alignRight , 'right'],
										[ editor.lang.common.alignTop , 'top']
									],
									setup : loadValue,
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : function( embedNode, extraStyles, extraAttributes )
									{
										var value = this.getValue();
										commitValue.apply( this, arguments );
										value && ( extraAttributes.align = value );
									}
								},
								{
									type : 'html',
									html : '<div></div>'
								}
							]
						},
						{
							type : 'fieldset',
							label : CKEDITOR.tools.htmlEncode( editor.config.lang_fix.attachmediavars ),
							children :
							[
								{
									type : 'vbox',
									padding : 0,
									children :
									[
										{
											type : 'checkbox',
											id : 'autostart',
											label : editor.config.lang_fix.media.autostart,
											'default' : false,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										},
										{
											type : 'checkbox',
											id : 'enablecontextmenu',
											label : editor.config.lang_fix.media.enablecontextmenu,
											'default' : true,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										},
										{
											type : 'checkbox',
											id : 'clicktoplay',
											label : editor.config.lang_fix.media.clicktoplay,
											'default' : true,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										},
										{
											type : 'checkbox',
											id : 'showcontrols',
											label : editor.config.lang_fix.media.showcontrols,
											'default' : true,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										},
										{
											type : 'checkbox',
											id : 'showstatusbar',
											label : editor.config.lang_fix.media.showstatusbar,
											'default' : true,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										},
										{
											type : 'checkbox',
											id : 'showdisplay',
											label : editor.config.lang_fix.media.showdisplay,
											'default' : false,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										},
										{
											type : 'checkbox',
											id : 'loop',
											label : editor.config.lang_fix.media.loop,
											'default' : true,
											setup : loadValue,
											onChange : function()
											{
												updatePreview(this.getDialog());
											},
											commit : commitValue
										}
									]
								}
							]
						}
					]
				},
				{
					id : 'advanced',
					label : editor.lang.common.advancedTab,
					elements :
					[
						{
							type : 'hbox',
							widths : [ '45%', '55%' ],
							children :
							[
								{
									type : 'text',
									id : 'id',
									label : editor.lang.common.id,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'text',
									id : 'title',
									label : editor.lang.common.advisoryTitle,
									setup : loadValue,
									commit : commitValue
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '45%', '55%' ],
							children :
							[
								{
									type : 'text',
									id : 'bgcolor',
									label : editor.lang.flash.bgcolor,
									setup : loadValue,
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : commitValue
								},
								{
									type : 'text',
									id : 'class',
									label : editor.lang.common.cssClass,
									setup : loadValue,
									onChange : function()
									{
										updatePreview(this.getDialog());
									},
									commit : commitValue
								}
							]
						},
						{
							type : 'text',
							id : 'style',
							label : editor.lang.common.cssStyle,
							setup : loadValue,
							onChange : function()
							{
								updatePreview(this.getDialog());
							},
							commit : commitValue
						}
					]
				}
			]
		};
	} );
})();
