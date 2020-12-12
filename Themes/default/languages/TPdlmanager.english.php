<?php
// Version: 2.1.0; TPdlmanager

global $scripturl;

//Admin
$txt['tp-dltabs4'] = 'Admin';
$txt['tp-dlsubmitted'] = 'Approval';
$txt['tp-dledit'] = 'Actions';
$txt['tp-dlfile'] = 'Filename / Uploader';
$txt['tp-dlviews'] = 'Views/Downloads';
$txt['tp-dlicon'] = 'Icon';

//Settings
$txt['tp-dltabs1'] = 'Settings';
$txt['tp-showdownload'] = 'Show Downloads';
$txt['tp-dlmanoff'] = 'TPdownloads is NOT active';
$txt['tp-dlmanon'] = 'TPdownloads is active';
$txt['tp-dlallowedtypes'] = 'Allowed file extensions';
$txt['tp-dlallowedsize'] = 'Max upload size';
$txt['tp-dluseformat'] = 'Show filesize in format';
$txt['tp-bytes'] = ' bytes';
$txt['tp-kb'] = ' Kb';
$txt['tp-mb'] = ' Mb';
$txt['tp-gb'] = ' Gb';
$txt['tp-dlusescreenshot'] = 'Use screenshot instead of icon where available';
$txt['tp-dlscreenshotsize1'] = 'Resize screenshot for thumbnail';
$txt['tp-dlscreenshotsize2'] = 'Resize screenshot for featured';
$txt['tp-dlmustapprove'] = 'All uploads must be approved';
$txt['tp-approveyes'] = 'Yes, except the membergroups that can manage downloads.';
$txt['tp-approveno'] = 'No, but permission to upload will still be needed';

$txt['tp-dlcreatetopicboards'] = 'Boards to use for support topics';
$txt['tp-dlwysiwyg'] = 'Show text-editor';
$txt['tp-dlwysiwygdesc'] = 'Changing this setting will change the editor used in the downloads. Please note that when changing the editor the existing introduction text and download descriptions that have been written using a different editor, may need to be updated in future. Change this setting only when you are sure.';
$txt['tp-dlintrotext'] = 'Text for introduction';
$txt['tp-dlusefeatured'] = 'Show featured file on frontpage of Download manager';
$txt['tp-dlfeatured'] = 'Featured Download';
$txt['tp-dluselatest'] = 'Show recent files';
$txt['tp-dlusestats'] = 'Show most downloaded/weekly';
$txt['tp-dlusecategorytext'] = 'Show category descriptions for child categories';
$txt['tp-dlvisualoptions'] = 'Visual options';
$txt['tp-leftbar'] = 'Show left panel';
$txt['tp-rightbar'] = 'Show right panel';
$txt['tp-topbar'] = 'Show top panel';
$txt['tp-centerbar'] = 'Show upper panel';
$txt['tp-lowerbar'] = 'Show lower panel';
$txt['tp-bottombar'] = 'Show bottom panel';
$txt['tp-noneicon'] = 'None';

//Add/Edit Categories
$txt['tp-dltabs2'] = 'Add category';
$txt['tp-dlcatadd'] = 'Add category';
$txt['tp-dlcatedit'] = 'Edit category';
$txt['tp-dlparent'] = 'Parent category';
$txt['tp-shortname'] = 'Short name';
$txt['tp-chooseicon'] = '- choose icon -';
$txt['tp-dlnocategory'] = '- no category -';
$txt['tp-nocats'] = 'No categories found.';
$txt['tp-dlaccess'] = 'Membergroups that can see this category';
$txt['tp-dlviewcat'] = 'View files in this category';

//Upload
$txt['tp-dltabs3'] = 'Upload';
$txt['tp-dlupload'] = 'Upload';
$txt['tp-warnsubmission'] = 'Currently all uploaded files will need activation by an administrator.';
$txt['tp-maxuploadsize'] = 'Max upload size';
$txt['tp-dluploadtitle'] = 'Title';
$txt['tp-dlnotitle'] = '-no title-';
$txt['tp-dluploadcategory'] = 'Category';
$txt['tp-dluploadtext'] = 'Description';
$txt['tp-dluploadfile'] = 'File to upload:';
$txt['tp-dlnoupload'] = 'Do not upload anything, just create an empty item.';
$txt['tp-dlexternalfile'] = 'External URL:';
$txt['tp-dluploadicon'] = 'Icon';
$txt['tp-dluploadpic'] = 'Additional picture for screenshot:';
$txt['tp-dlcreatetopic'] = 'Create new topic';
$txt['tp-dlcreatetopic_sticky'] = 'Set as sticky?';
$txt['tp-dlcreatetopic_announce'] = 'Announce it?';
$txt['tp-dlmissingboards'] = 'You have not selected any boards yet. Would you like to go to the <a href="' . $scripturl . '?action=tportal;sa=download;dl=adminsettings">settings screen</a>?';
$txt['tp-dlchooseboard'] = 'Choose board to post topic in';

$txt['tp-adminonly'] = 'You are not allowed in this section.';

$txt['tp-dluploadnotallowed'] = 'Sorry, uploading files is currently not allowed.';
$txt['tp-dlnotuploaded'] = 'File was not uploaded. Error %s';
$txt['tp-dluploadfailure']='The upload was not able to complete. This might happen because it took too long to upload or the file is bigger than the server will allow.<br><br>Please consult your server administrator for more information. ';
$txt['tp-dlmaxerror'] = 'The maximum size for an uploaded file is currently ';
$txt['tp-dlmaxerror2'] = 'Your file was';
$txt['tp-dlexterror'] = 'Currently you can only upload one of the following file formats';
$txt['tp-dlexterror2'] = 'Your file was';

//Edit file
$txt['tp-useredit'] = 'Edit file';
$txt['tp-dlpreview'] = 'View download';
$txt['tp-dlfilename'] = 'Filename:';
$txt['tp-onlyftpstrays'] = 'Only showing files that do not have an entry in the dl manager:';
$txt['tp-dlfilesize'] = 'Filesize';
$txt['tp-uploadnewfileexisting'] = 'Upload a new file <br><span class="smalltext"> (the existing will be replaced!)</span>';
$txt['tp-uploadnewpicexisting']='Existing screenshot/picture';
$txt['tp-uploadnewpic'] = 'Upload a new picture<br><span class="smalltext">(the existing will be replaced)</span>';
$txt['tp-dlmorefiles2'] = 'Attach to another item:';
$txt['tp-sayno'] = 'No';
$txt['tp-dlnotapprovedyet'] = 'The file is not approved yet.';
$txt['tp-dlmorefiles'] = 'Download additional files:';
$txt['tp-dlattachloose'] = 'Detach it?';
$txt['tp-dldelete'] = 'Delete?';
$txt['tp-confirmdelete'] = 'Are you sure you want to delete?';
$txt['tp-dlfilenotdel'] = 'Unable to delete the actual file, but the item was deleted.';

$txt['tp-dluploadattach'] = 'Attach to an existing file?';

//Submissions
$txt['tp-dlsubmissions'] = 'Submissions';
$txt['tp-nosubmissions'] = 'Currently there are no submissions awaiting approval.';
$txt['tp-dlapprove'] = 'Approval?';

//FTP
$txt['tp-dlftp'] = 'FTP';
$txt['tp-ftpstrays'] = 'These files are not assigned in the dl_manager table:';
$txt['tp-assignftp'] = 'By using the links behind each file, you can assign individual files. If you check several files, you can assign all of them into a category in one operation. If you write something in the new-category-field, that will be used, if not the dropdown list. Note that this also acts as a parent category when using new-category option.';

$txt['tp-dlmakeitem'] = 'Assign this';
$txt['tp-dlmakeitem2'] = 'Assigning the file:';
$txt['tp-newcatassign'] = 'New category';
$txt['tp-assigncatparent'] = 'Parent category/main category:';
$txt['tp-adminftp_newfiles'] = 'Files were added successfully. Check out the category they were inserted into.';

//Main
$txt['tp-recentuploads'] = 'Recent files';
$txt['tp-mostpopweek'] = 'Most downloaded this week';
$txt['tp-mostpop'] = 'Most downloaded files ever';

$txt['tp-categories'] = 'Categories';
$txt['tp-childcategories'] = 'Child categories';
$txt['tp-dlname'] = 'Name';
$txt['tp-dl1file'] = 'File';
$txt['tp-dlfiles'] = 'Files';
$txt['tp-nofiles'] = 'Currently there are no files in this category.';

$txt['tp-sortby'] = 'Sort by';
$txt['tp-id'] = 'ID';
$txt['tp-name']='Name';
$txt['tp-itemdownloads'] = 'Downloads';
$txt['tp-last_access'] = 'Last access';
$txt['tp-created'] = 'Created';
$txt['tp-author_id'] = 'Author';
$txt['tp-authorby'] = 'by';
$txt['tp-itemlastdownload'] = ' Last accessed';
$txt['tp-ratedownload'] = 'Give this download a rating of';
$txt['tp-dlhaverated'] = 'You have rated this item.';

//Stats & Search
$txt['tp-downloadsection'] = 'Download Section';
$txt['tp-stats'] = 'Stats';
$txt['tp-dlstatscats'] = 'largest categories';
$txt['tp-dlstatsviews'] = 'most viewed items';
$txt['tp-dlstatsdls'] = 'most downloaded items';
$txt['tp-dlstatssize'] = 'largest filesize';

$txt['tp-dlsearch'] = 'Search in Downloads';
$txt['tp-search'] = 'Search';
$txt['tp-searcharea-descr'] = 'Search descriptions';
$txt['tp-searcharea-name'] = 'Search titles';
$txt['tp-dlsearchresults'] = 'Downloads search results';

//Strings called from source files
$txt['tp-dladmin'] = 'Administration';
$txt['tp-dldownloads'] = 'Downloads';
$txt['tp-dlheader1'] = 'Downloads';
$txt['tp-mainpage'] = 'Main';
$txt['tp-dlheader2'] = 'You can create categories here, edit each one with permissions, names and icons and upload files into them. There is also a ftp screen where you can assign pre-uploaded files to aitem or caegory. Submitted files screen can also be found here, it allow you to either approve or reject(delete) them.';
$txt['tp-dlheader3'] = 'Manage categories and items in the Download manager module';
$txt['tp-dlssnotdel'] = 'Unable to delete the actual screenshot, but the item was deleted.';
$txt['tp-dlnonint'] = 'Sorry, you attempted to specify a non-integer value!';
$txt['tp-notallowed'] = 'You are not allowed to access this section.';

//General strings
$txt['tp-confirm'] = 'Are you sure?';
$txt['tp-dosubmit'] = 'Save';
$txt['tp-download'] = 'Download';
$txt['tp-nodownload'] = 'No Download';
$txt['tp-downloads'] = 'Downloads';
$txt['tp-downloadss2'] = 'Click here to download the file';
$txt['tp-downloadss3'] = 'This is an empty item';

?>
