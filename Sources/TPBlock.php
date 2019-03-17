<?php
/**
 * @package TinyPortal
 * @version 2.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) 2019 - The TinyPortal Team
 *
 */

if (!defined('SMF')) {
        die('Hacking attempt...');
}

function TPBlock_init() {{{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

	if(loadLanguage('TPmodules') == false) {
		loadLanguage('TPmodules', 'english');
    }

	if(loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
    }

	// a switch to make it clear what is "forum" and not
	$context['TPortal']['not_forum'] = true;

	// call the editor setup
	require_once($sourcedir. '/TPcommon.php');

	// clear the linktree first
	TPstrip_linktree();

}}}

function editBlock( $blockID ) {{{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

    if(!is_numeric($blockID)) {
        fatal_error($txt['tp-notablock'], false);
    }
    // get one block
    $context['TPortal']['subaction'] = 'editblock';
    $context['TPortal']['blockedit'] = array();
    $request =  $smcFunc['db_query']('', '
        SELECT * FROM {db_prefix}tp_blocks
        WHERE id = {int:blockid} LIMIT 1',
        array('blockid' => $blockID)
    );
    if($smcFunc['db_num_rows']($request) > 0) {
        $row = $smcFunc['db_fetch_assoc']($request);

        $can_edit = !empty($row['editgroups']) ? get_perm($row['editgroups'],'') : false;

        // check permission
        if(allowedTo('tp_blocks') || $can_edit) {
            $ok=true;
        }
        else {
            fatal_error($txt['tp-blocknotallowed'], false);
        }

        $context['TPortal']['editblock'] = array();
        $context['TPortal']['blockedit']['id'] = $row['id'];
        $context['TPortal']['blockedit']['title'] = $row['title'];
        $context['TPortal']['blockedit']['body'] = $row['body'];
        $context['TPortal']['blockedit']['frame'] = $row['frame'];
        $context['TPortal']['blockedit']['type'] = $row['type'];
        $context['TPortal']['blockedit']['var1'] = $row['var1'];
        $context['TPortal']['blockedit']['var2'] = $row['var2'];
        $context['TPortal']['blockedit']['visible'] = $row['visible'];
        $context['TPortal']['blockedit']['editgroups'] = $row['editgroups'];
        $smcFunc['db_free_result']($request);
    }
    else {
        fatal_error($txt['tp-notablock'], false);
    }

    // Add in BBC editor before we call in template so the headers are there
    if($context['TPortal']['blockedit']['type'] == '5') {
        $context['TPortal']['editor_id'] = 'blockbody' . $context['TPortal']['blockedit']['id'];
        TP_prebbcbox($context['TPortal']['editor_id'], strip_tags($context['TPortal']['blockedit']['body']));
    }

    if(loadLanguage('TPortalAdmin') == false) {
        loadLanguage('TPortalAdmin', 'english');
    }
    loadtemplate('TPmodules');

}}}

function saveBlock( $blockID ) {{{
	global $settings, $context, $scripturl, $txt, $user_info, $sourcedir, $boarddir, $smcFunc;

    // save a block?
    if(!is_numeric($blockID)) {
        fatal_error($txt['tp-notablock'], false);
    }
    $request =  $smcFunc['db_query']('', '
        SELECT editgroups FROM {db_prefix}tp_blocks
        WHERE id = {int:blockid} LIMIT 1',
        array('blockid' => $blockID)
    );

    if($smcFunc['db_num_rows']($request) > 0) {
        $row = $smcFunc['db_fetch_assoc']($request);
        // check permission
        if(allowedTo('tp_blocks') || get_perm($row['editgroups'])) {
            $ok = true;
        }
        else {
            fatal_error($txt['tp-blocknotallowed'], false);
        }
        $smcFunc['db_free_result']($request);

        // loop through the values and save them
        foreach ($_POST as $what => $value) {
            if(substr($what, 0, 10) == 'blocktitle') {
                // make sure special charachters can't be done
                $value = strip_tags($value);
                $value = preg_replace('~&#\d+$~', '', $value);
                $val = substr($what,10);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET title = {string:title}
                        WHERE id = {int:blockid}',
                        array('title' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 9) == 'blockbody' && substr($what, -4) != 'mode') {
                // If we came from WYSIWYG then turn it back into BBC regardless.
                if (!empty($_REQUEST[$what.'_mode']) && isset($_REQUEST[$what])) {
                    require_once($sourcedir . '/Subs-Editor.php');
                    $_REQUEST[$what] = html_to_bbc($_REQUEST[$what]);
                    // We need to unhtml it now as it gets done shortly.
                    $_REQUEST[$what] = un_htmlspecialchars($_REQUEST[$what]);
                    // We need this for everything else.
                    $value = $_POST[$what] = $_REQUEST[$what];
                }

                $val = (int) substr($what, 9);

                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET body = {string:body}
                        WHERE id = {int:blockid}',
                        array('body' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 10) == 'blockframe') {
                $val = substr($what, 10);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET frame = {string:frame}
                        WHERE id = {int:blockid}',
                        array('frame' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 12) == 'blockvisible') {
                $val = substr($what, 12);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET visible = {string:vis}
                        WHERE id = {int:blockid}',
                        array('vis' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 9) == 'blockvar1') {
                $val=substr($what, 9);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET var1 = {string:var1}
                        WHERE id = {int:blockid}',
                        array('var1' => $value, 'blockid' => $val)
                        );
            }
            elseif(substr($what, 0, 9) == 'blockvar2') {
                $val = substr($what, 9);
                $smcFunc['db_query']('', '
                        UPDATE {db_prefix}tp_blocks
                        SET var2 = {string:var2}
                        WHERE id = {int:blockid}',
                        array('var2' => $value, 'blockid' => $val)
                        );
            }
        }
        redirectexit('action=tportal;sa=editblock'.$whatID);
    }
    else {
        fatal_error($txt['tp-notablock'], false);
    }

}}}

function TPBlockActions(&$subActions) {{{

   $subActions = array_merge(
        array (
            'showblock'      => array('TPBlock.php', 'showBlock',   array()),
            'editblock'      => array('TPBlock.php', 'editBlock',   array()),
            'deleteblock'    => array('TPBlock.php', 'deleteBlock', array()),
            'saveblock'      => array('TPBlock.php', 'saveBlock',   array()),
        ),
        $subActions
    );


}}}

?>
