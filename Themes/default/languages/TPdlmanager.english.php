<?php
// Version: 3.0.1; TPdlmanager

global $scripturl;

//Admin
$txt['tp-dltabs4'] = 'Admin';
$txt['tp-dlsubmitted'] = 'Approval';
$txt['tp-dledit'] = 'Actions';
$txt['tp-dlfile'] = 'Filename / uploader';
$txt['tp-dlviews'] = 'Views / downloads';
$txt['tp-dlicon'] = 'Icon';

//Settings
$txt['tp-dltabs1'] = 'Settings';
$txt['tp-showdownload'] = 'Show TPdownloads';
$txt['tp-dlmanoff'] = 'TPdownloads is NOT active';
$txt['tp-dlmanon'] = 'TPdownloads is active';
$txt['tp-dlallowedtypes'] = 'Allowed file extensions';
$txt['tp-dlallowedsize'] = 'Max upload size';
$txt['tp-dluseformat'] = 'Show filesize in format';
$txt['tp-bytes'] = ' bytes';
$txt['tp-kb'] = ' Kb';
$txt['tp-mb'] = ' Mb';
$txt['tp-gb'] = ' Gb';
$txt['tp-dlscreenshot'] = 'Screenshot';
$txt['tp-dlusescreenshot'] = 'Use screenshot instead of icon where available';
$txt['tp-dlscreenshotsize1'] = 'Resize screenshot for thumbnail';
$txt['tp-dlscreenshotsize2'] = 'Resize screenshot for featured';
$txt['tp-dlmustapprove'] = 'All uploads must be approved';
$txt['tp-approveyes'] = 'Yes, except the membergroups that can manage TPdownloads.';
$txt['tp-approveno'] = 'No, but permission to upload will still be needed';

$txt['tp-dlcreatetopicboards'] = 'Boards to use for support topics';
$txt['tp-dlwysiwyg'] = 'Show text editor';
$txt['tp-dlwysiwygdesc'] = 'Changing this setting will change the editor used in the downloads. Please note that when changing the editor the existing introduction text and download descriptions that have been written using a different editor, may need to be updated in future. Change this setting only when you are sure.';
$txt['tp-dlintrotext'] = 'Text for introduction';
$txt['tp-dlusefeatured'] = 'Show featured file on main page of TPdownloads';
$txt['tp-dlfeatured'] = 'Featured download';
$txt['tp-dluselatest'] = 'Show recent files';
$txt['tp-dlusestats'] = 'Show most downloaded/weekly';
$txt['tp-dlusecategorytext'] = 'Show category descriptions for child categories';
$txt['tp-dllimitlength'] = 'Characters to display per download on category page';
$txt['tp-dlvisualoptions'] = 'Visual options';
$txt['tp-leftbar'] = 'Show left panel';
$txt['tp-rightbar'] = 'Show right panel';
$txt['tp-topbar'] = 'Show top panel';
$txt['tp-centerbar'] = 'Show center panel';
$txt['tp-lowerbar'] = 'Show lower panel';
$txt['tp-bottombar'] = 'Show bottom panel';
$txt['tp-noneicon'] = 'None';

//Admin
$txt['tp-helpdownload1'] = 'These are your main download categories with the number of files each category holds. Selecting a category will bring you to the list of files for that category. The actions icons allow you to view the category page or directly edit the category settings.';
$txt['tp-helpdownload2'] = 'This page shows all list of all files in the chosen category. Selecting a download will bring you the the edit file page. If a category holds any sub-categories these will be listed first.';

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
$txt['tp-warnsubmission'] = 'All uploaded files will need activation by an administrator.';
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
$txt['tp-dlmissingboards'] = 'You have not selected any boards yet.';
$txt['tp-dlmissingboards2'] = 'Select on or more boards in the settings screen?';
$txt['tp-dlchooseboard'] = 'Choose board to post topic in';

$txt['tp-adminonly'] = 'You are not allowed in this section.';

$txt['tp-dluploadnotallowed'] = 'Sorry, uploading files is currently not allowed.';
$txt['tp-dlnotuploaded'] = 'File was not uploaded. Error %s';
$txt['tp-dluploadfailure'] = 'The upload was not able to complete: you may have forgotten to specify a file for the upload. It might also happen because the file you specified took too long to upload or is bigger than the server will allow.';
$txt['tp-dlmaxerror'] = 'The maximum size for an uploaded file is ';
$txt['tp-dlexterror'] = 'You can only upload one of the following file formats';
$txt['tp-dlfileerror'] = 'The file no longer exists on the server';
$txt['tp-dlerrorfile'] = 'Your file was';

//Edit file
$txt['tp-useredit'] = 'Edit file';
$txt['tp-dlpreview'] = 'View download';
$txt['tp-dlfilename'] = 'Filename:';
$txt['tp-dlfilesize'] = 'Filesize';
$txt['tp-uploadnewfileexisting'] = 'Upload a new file <br><span class="smalltext"> (the existing file will be replaced.)</span>';
$txt['tp-uploadnewpicexisting']='Existing screenshot/image';
$txt['tp-uploadnewpic'] = 'Upload a new image<br><span class="smalltext">(supported extensions: jpg, gif or png. The existing image will be replaced.)</span>';
$txt['tp-dlmorefiles2'] = 'Link to existing download';
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
$txt['tp-helpdlsubmissions'] = 'Downloads that have been submitted by members need to be approved by an administrator and assigned to a category.';
$txt['tp-nosubmissions'] = 'There are no submissions awaiting approval.';
$txt['tp-dlapprove'] = 'Approval?';

//FTP
$txt['tp-dlftp'] = 'FTP';
$txt['tp-ftpstrays'] = 'Assign new files in the tp_dlmanager table';
$txt['tp-ftpfolder'] = 'FTP folder';
$txt['tp-ftpfolderdesc'] = 'Using an FTP client you can upload multiple files to the tp-downloads folder on your server. This is the folder used. If you write something in the "New category" field, a new category will be automatically created with that name. Otherwise the file(s) will be assigned to the category selected in the dropdown list. Note that this also acts as a parent category when using new-category option.';
$txt['tp-assignftp'] = 'Here you can add unassigned files to TPdownloads: by using the links behind each file, you can select individual files to directly create a download item. If you wish to process multiple files at once you can use the checkboxes.';
$txt['tp-noftpstrays'] = 'There are no files that are not assigned in the tp_dlmanager table.';

$txt['tp-dlmakeitem'] = 'Process';
$txt['tp-dlmakeitem2'] = 'Assigning the file';
$txt['tp-newcatassign'] = 'New category';
$txt['tp-assigncatparent'] = 'Assign to / Parent category -> ';
$txt['tp-adminftp_nonewfiles'] = 'No files were added: no category specified.';
$txt['tp-adminftp_newfiles'] = 'Files were added successfully. ';
$txt['tp-adminftp_newfilescat'] = 'Check out the category they were inserted into.';
$txt['tp-adminftp_newfile'] = 'File was added successfully. ';
$txt['tp-adminftp_newfileview'] = 'Check out the new download.';
$txt['tp-submitftp'] = 'Process selected';

//Main
$txt['tp-recentuploads'] = 'Recent files';
$txt['tp-mostpopweek'] = 'Most downloaded this week';
$txt['tp-mostpop'] = 'Most downloaded files ever';

$txt['tp-categories'] = 'Categories';
$txt['tp-childcategories'] = 'Child categories';
$txt['tp-dlname'] = 'Name';
$txt['tp-dl1file'] = 'File';
$txt['tp-dlfiles'] = 'Files';
$txt['tp-nofiles'] = 'There are no files in this category.';

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
$txt['tp-downloadsection'] = 'Download section';
$txt['tp-stats'] = 'Stats';
$txt['tp-dlstatscats'] = 'largest categories';
$txt['tp-dlstatsviews'] = 'most viewed items';
$txt['tp-dlstatsdls'] = 'most downloaded items';
$txt['tp-dlstatssize'] = 'largest filesize';

$txt['tp-dlsearch'] = 'Search in TPdownloads';
$txt['tp-search'] = 'Search';
$txt['tp-searcharea-descr'] = 'Search descriptions';
$txt['tp-searcharea-name'] = 'Search titles';
$txt['tp-dlsearchresults'] = 'Downloads search results';

//Strings called from source files
$txt['tp-dladmin'] = 'Administration';
$txt['tp-dldownloads'] = 'Downloads';
$txt['tp-dlheader1'] = 'Downloads';
$txt['tp-mainpage'] = 'Main';
$txt['tp-dlheader2'] = 'You can create categories here, edit each one with permissions, names and icons and upload files into them. There is also a ftp screen where you can assign pre-uploaded files to a download item or category. Submitted files screen can also be found here, it allows you to either approve or reject (delete) them.';
$txt['tp-dlheader3'] = 'Manage categories and items in TPdownloads';
$txt['tp-dlssnotdel'] = 'Unable to delete the actual screenshot, but the item was deleted.';
$txt['tp-dlnonint'] = 'Sorry, you attempted to specify a non-integer value!';
$txt['tp-notallowed'] = 'You are not allowed to access this section.';

//General strings
$txt['tp-confirm'] = 'Are you sure?';
$txt['tp-dosubmit'] = 'Save';
$txt['tp-download'] = 'Download';
$txt['tp-nodownload'] = 'No download';
$txt['tp-downloads'] = 'Downloads';
$txt['tp-downloadss2'] = 'Click here to download the file';
$txt['tp-downloadss3'] = 'This is an empty item';

?>
