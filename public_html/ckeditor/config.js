/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.skin = 'v2';
  
  config.resize_enabled = false;
  
  config.toolbar_Full = [
    ['Source','-','Preview'],
    ['Cut','Copy','PasteText'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    '/',
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','Table','HorizontalRule','SpecialChar'],
    '/',
    ['Format','Font','FontSize'],
    ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks']
  ];
   
  config.toolbar_Basic = [
    ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Link', 'Unlink', 'Image', 'Table', '-', 'SpecialChar']
  ];
   
  config.toolbar_User = [
    ['Bold', 'Italic', 'Underline', 'Strike','-', 'Link', 'Unlink']
  ];

  config.filebrowserBrowseUrl = '/ckeditor/kcfinder/browse.php?type=files';
  config.filebrowserImageBrowseUrl = '/ckeditor/kcfinder/browse.php?type=images';
  config.filebrowserFlashBrowseUrl = '/ckeditor/kcfinder/browse.php?type=flash';
  config.filebrowserUploadUrl = '/ckeditor/kcfinder/upload.php?type=files';
  config.filebrowserImageUploadUrl = '/ckeditor/kcfinder/upload.php?type=images';
  config.filebrowserFlashUploadUrl = '/ckeditor/kcfinder/upload.php?type=flash';
};
