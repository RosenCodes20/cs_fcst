/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{

config.skin = 'kama';

config.fontSize_sizes = '8/8px;9/9px;10/10px;11/11px;12/12px;13/13px;14/14px;15/15px;16/16px;17/17px;18/18px;19/19px;20/20px;21/21px;22/22px;23/23px;24/24px;25/25px;26/26px;28/28px;36/36px;48/48px;72/72px';

config.toolbar = 'Full';

// Don't change default language. Filemanager supports only english

config.language = 'en';

config.width = "660";
config.height = "280";

config.filebrowserBrowseUrl = 'scripts/filemanager/index.html';
config.filebrowserUploadUrl = '';
 
config.toolbar_Full =
[
    ['Source','-','Image','Maximize','Preview','-'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord'],
    ['Undo','Redo','-','Find','-','SelectAll','RemoveFormat',
    'Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','NumberedList','BulletedList'],
    '/',
    ['Font','FontSize'],
    ['TextColor','BGColor'],
    ['ShowBlocks'],
    ['Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
    ['Outdent','Indent'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink']
];

};

