CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode' ] },
		{ name: 'clipboard', groups: [ 'undo', 'clipboard' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'basicstyles', groups: [ 'Bold', 'Italic' ]},
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
        { name: 'editing', groups: ['basicstyles'] } 
	];

	config.removeButtons = 'Save,Print,Flash,Flash,SpecialChar,Iframe,ShowBlocks,CreateDiv,Save,NewPage,DocProps,Undo,Redo,Copy,Cut,Styles,Format';
    config.allowedContent = true; 
    config.resize_enabled = false; 
    config.enterMode = CKEDITOR.ENTER_BR;
};  
 