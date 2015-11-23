<?php
/**
 * @package TinyPortal
 * @version 1.2
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2015 - The TinyPortal Team
 *
 */

//  SVN Rev67
global $scripturl;

$txt['tp-authorID'] = 'Author';
$txt['tp-created'] = 'Created';
$txt['tp-dladmin'] = 'Administration';
$txt['tp-dldownloads'] = 'Downloads';
$txt['tp-dlexterror'] = 'Currently you can only upload one of the following file formats';
$txt['tp-dlexterror2'] = 'Your file was';
$txt['tp-dlftp'] = 'FTP';
$txt['tp-dlheader1'] = 'Downloads';
$txt['tp-dlheader2'] = 'You can create categories here, edit each one with permissions, names and icons and upload files into them. There is also a ftp screen where you can assign pre-uploaded files to aitem or caegory. Submitted files screen can also be found here, it allow you to either approve or reject(delete) them.';
$txt['tp-dlheader3'] = 'Manage categories and items in the Download manager module';
$txt['tp-dlmaxerror'] = 'The maximum size for a uploaded file is currently';
$txt['tp-dlmaxerror2'] = 'Your file was';
$txt['tp-dlsubmissions'] = 'submissions';
$txt['tp-dltabs1'] = 'Settings';
$txt['tp-dltabs2'] = 'Add category';
$txt['tp-dltabs3'] = 'Upload';
$txt['tp-dltabs4'] = 'Admin';
$txt['tp-dluploadfailure']='The upload was not able to complete. This might happen because it took too long to upload or the file is bigger than the server will allow.<br /><br />Please consult your server administrator for more information. ';
$txt['tp-dluploadnotallowed'] = 'Sorry, uploading files is currently not allowed.';
$txt['tp-downloads'] = 'Downloads';
$txt['tp-id'] = 'ID';
$txt['tp-last_access'] = 'Last access';
$txt['tp-mainpage'] = 'Main';
$txt['tp-name']='Name:';
$txt['tp-needtoregister'] = 'You need to login to enter this section.';
$txt['tp-notallowed'] = 'You are not allowed to access this section.';
$txt['tp-notfound'] = 'The item/section was not found.';
$txt['tp-sortby'] = 'Sort by';
$txt['tp-useredit'] = 'Edit file';

// TPdlmanager template
//  SVN Rev67
$txt['tp-adminonly'] = 'You are not allowed in this section.';
$txt['tp-dlhaverated'] = 'You have rated this item.';
$txt['tp-dlmakeitem2'] = 'Assigning the file:';
$txt['tp-dlnotapprovedyet'] = 'The file is not approved yet.';
$txt['tp-dlnoupload'] = 'Do not upload anything, just create an empty item.';
$txt['tp-dlstatscats'] = 'largest categories';
$txt['tp-dlstatsdls'] = 'most downloaded items';
$txt['tp-dlstatssize'] = 'largest filesize';
$txt['tp-dlstatsviews'] = 'most viewed items';
$txt['tp-dlupload'] = 'Upload';
$txt['tp-dluploadattach'] = 'Attach to an existing file?';
$txt['tp-dluploadfile'] = 'File to upload:';
$txt['tp-dluploadpic'] = 'Additional picture:';
$txt['tp-dluploadtext'] = 'Description (HTML allowed)';
$txt['tp-downloadsection'] = 'Download Section';
$txt['tp-downloadsection2'] = 'File Repository';
$txt['tp-itemdownloads'] = 'Downloads';
$txt['tp-itemlastdownload'] = ' Last accessed';
$txt['tp-maxuploadsize'] = 'Max upload size';
$txt['tp-ratedownload'] = 'Give this download a rating of';
$txt['tp-recentuploads'] = 'Recent files';
$txt['tp-recentuploads2'] = 'Latest file added:';
$txt['tp-search'] = 'Search';
$txt['tp-stats'] = 'Stats';
$txt['tp-warnsubmission'] = ' Currently all uploaded files will need activation by an administrator.';
$txt['tp-searcharea-descr'] = 'Search descriptions';
$txt['tp-searcharea-name'] = 'Search titles';
$txt['tp-dosubmit'] = 'Save';
$txt['tp-dlvisualoptions'] = 'Visual options:';
$txt['tp-dlsearch'] = 'Search in Downloads';
$txt['tp-dlsearchresults'] = 'Downloads search results';

// TPdlmanagerAdmin template
//  SVN Rev67
$txt['tp-adminftp_newfiles'] = 'Files were added successfully. Check out the category they were inserted into.';
$txt['tp-approveno'] = 'No, but permission to upload will still be needed';
$txt['tp-approveyes'] = 'Yes, except the membergroups that can manage downloads.';
$txt['tp-assigncatparent'] = 'Parent category/main category:';
$txt['tp-assignftp'] = 'By using the links behind each file, you can assign individual files. If you check several files, you can assign all of them into a category in one operation. If you write something in the new-category-field, that will be used, if not the dropdown list. Note that this also acts as a parent category when using new-category option.';
$txt['tp-centerbar'] = 'Show upper panel';
$txt['tp-chooseicon'] = '- choose icon -';
$txt['tp-confirm'] = 'Are you sure?';
$txt['tp-confirmdelete'] = 'Are you sure you want to delete?';
$txt['tp-dlaccess'] = 'Allowed membergroups';
$txt['tp-dlallowedsize'] = 'Max upload size';
$txt['tp-dlallowedtypes'] = 'Allowed file extensions';
$txt['tp-dlapprove'] = 'Approval?';
$txt['tp-dlattachloose'] = 'Detach it?';
$txt['tp-dldelete'] = 'Delete?';
$txt['tp-dledit'] = 'Edit/Delete';
$txt['tp-dlfile'] = 'Filename / Uploader';
$txt['tp-dlfilename']='Filename:';
$txt['tp-dlfiles'] = 'Files';
$txt['tp-dlfilesize'] = 'Filesize';
$txt['tp-dlicon'] = 'Icon';
$txt['tp-dlmakeitem'] = 'Assign this';
$txt['tp-dlmorefiles'] = 'Download additional files:';
$txt['tp-dlmorefiles2'] = 'Attach to another item:';
$txt['tp-dlmustapprove'] = 'All uploads must be approved? ';
$txt['tp-dlname'] = 'Name';
$txt['tp-dlparent'] = 'Parent category';
$txt['tp-dlshowlatest'] = 'Show "latest files" on frontpage of Download manager:';
$txt['tp-dlsubmitted'] = 'Approval';
$txt['tp-dluploadcategory'] = 'Category:';
$txt['tp-dluploadicon'] = 'Icon:';
$txt['tp-dluploadtitle'] = 'Title: ';
$txt['tp-dluseformat'] = 'Show filesize in format:';
$txt['tp-dlviews'] = 'Views/Downloads';
$txt['tp-download'] = 'Download';
$txt['tp-ftpstrays'] = 'These files are not assigned in the dl_manager table:';
$txt['tp-leftbar'] = 'Show left panel';
$txt['tp-newcatassign'] = 'New category:';
$txt['tp-nocategory'] = '- no category -';
$txt['tp-noneicon'] = 'None';
$txt['tp-onlyftpstrays'] = 'Only showing files that do not have an entry in the dl manager:';
$txt['tp-rightbar'] = 'Show right panel';
$txt['tp-sayno'] = 'No';
$txt['tp-uploadnewfileexisting'] = 'Upload a new file <br /><span class="smalltext"> (the existing will be replaced!)</span>';
$txt['tp-uploadnewpic'] = 'Upload a new picture<br /><span class="smalltext">(the existing will be replaced)</span>';
$txt['tp-uploadnewpicexisting']='Existing screenshot/picture';
$txt['tp-shortname'] = 'Query title:';

$txt['tp-topbar'] = 'Show top panel';
$txt['tp-bottombar'] = 'Show bottom panel';
$txt['tp-lowerbar'] = 'Show lower panel';
$txt['tp-showtop'] = 'Show header top';
$txt['tp-categories'] = 'Categories';

$txt['tp-dlcreatetopic'] = 'Create new topic?';
$txt['tp-dlcreatetopic_sticky'] = 'Set as sticky?';
$txt['tp-dlcreatetopic_announce'] = 'Announce it?';
$txt['tp-dlchooseboard'] = 'Choose board to post topic in';
$txt['tp-dlusescreenshot'] = 'Use the screenshot instead of icon?';
$txt['tp-dlscreenshotsizes'] = 'Resize screenshot sizes:';
$txt['tp-dlperms'] = 'Permissions';
$txt['tp-dlperms2'] = 'Select and set permissions on all membergroups from one place. "Manage Downloads" give admin rights for all downloads, 
"Upload file" give permission to actually upload, while "create topic" give permission to start a forum topic at the the time of upload.';
$txt['tp-dlcreatetopicboards'] = 'Boards to use for support topics';
$txt['tp-mostpopweek'] = 'Most downloaded this week';
$txt['tp-mostpop'] = 'Most downloaded files ever';
$txt['tp-dluselatest'] = 'Show recent files?';
$txt['tp-dlusefeatured'] = 'Show featured file?';
$txt['tp-dlusestats'] = 'Show most downloaded/weekly?';
$txt['tp-dlintrotext'] = 'Text for introduction';
$txt['tp-dlusecategorytext'] = 'Show category descriptions inside file listings?';
$txt['tp-dlfeatured'] = 'Featured Download';
$txt['tp-dlwysiwyg'] = 'Show text-editor?';

$txt['tp-dlmissingboards'] = 'You have not selected any boards yet. Would you like to go to the <a href="' . $scripturl . '?action=tpmod;dl=adminsettings">settings screen</a>?';

$txt['tp-dlnonint'] = 'Sorry, you attempted to specify a non-integer value!';
$txt['tp-dlfilenotdel'] = 'Unable to delete the actual file, but the item was deleted.';
$txt['tp-dlssnotdel'] = 'Unable to delete the actual screenshot, but the item was deleted.';
$txt['tp-dlnotuploaded'] = 'File was not uploaded.';
$txt['tp-dlnotitle'] = '-no title-';
?>