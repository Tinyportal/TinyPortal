function expandHeaderSmiley(mode, shoutbox_id, is_guest, sessionId)
{
	// Guests don't have theme options!!
	if (is_guest)
		document.cookie = "expandsmiley" + shoutbox_id + "=" + (mode ? 1 : 0);
	else
		smf_setThemeOption("expand_header_smiley_" + shoutbox_id, mode ? 1 : 0, null, sessionId);

	document.getElementById("expand_smiley_" + shoutbox_id).src = mode ? tp_images_url + "/TPcollapse.png" : tp_images_url + "/TPexpand.png";
	document.getElementById("expandHeaderSmiley_" + shoutbox_id).style.display = mode ? "" : "none";
	current_header_smiley = mode;
}


function expandHeaderBBC(mode, shoutbox_id, is_guest, sessionId)
{
	if (is_guest)
		document.cookie = "expandbbc_" + shoutbox_id + "=" + (mode ? 1 : 0);
	else
		smf_setThemeOption("expand_header_bbc_" + shoutbox_id, mode ? 1 : 0, null, sessionId);

	document.getElementById("expand_bbc_" + shoutbox_id).src = mode ? tp_images_url + "/TPcollapse.png" : tp_images_url + "/TPexpand.png";
	document.getElementById("expandHeaderBBC_" + shoutbox_id).style.display = mode ? "" : "none";
	current_header_bbc = mode;
}

function TPupdateShouts(action, shoutboxId, shoutId, shoutLayout, shoutAvatar)
{
	var params = "";
	var name;
	var shout;
	
	if (action === "save") {
		name    = $("#tp-shout-name_" + shoutboxId).val();
		shout   = $("#tp_shout_" + shoutboxId).val();
		params  = "&tp-shout-name=" + name + "&tp_shout=" + shout;		
	}

	if (shoutboxId || !(0 === shoutboxId.length)) {
		params = params.concat("&b=" + shoutboxId);
	}

	if (shoutId) {
		params = params.concat("&s=" + shoutId);
	}

    if(shoutLayout || !(0 === shoutLayout.length)) {
        params = params.concat("&l=" + shoutLayout);
    }
    if(shoutAvatar || !(0 === shoutAvatar.length)) {
        params = params.concat("&a=" + shoutAvatar);
    }

	$.ajax({
		type : "POST",
		dataType: "html",
		cache: false,
        async: false,
		url: smf_scripturl + "?action=tpshout;shout=" + action,
		data: params + "&" + tp_session_var + "=" + tp_session_id,
        error: function (request, status, error) {
            console.log(request.responseText);
        },
		beforeSend: function() {
		},
		complete: function(){
		},
		success: function(data) {
			var error = $($.parseHTML(data)).filter("#shoutError_" + shoutboxId);
			// If there's an error let's display it
			if (error.length > 0) {
				$("#shout_errors_" + shoutboxId).html(error).show();
				$(".tp_shoutframe.tp_shoutframe_" + shoutboxId).fadeIn();
				$("#tp_shout_" + shoutboxId).val(shout);
			} else {
				$("#shout_errors_" + shoutboxId).hide();
				$(".tp_shoutframe.tp_shoutframe_" + shoutboxId).html(data).fadeIn();
				$(".tp_shoutframe.tp_shoutframe_" + shoutboxId).parent().scrollTop(0);
//				$(".tp_shoutframe.tp_shoutframe_" + shoutboxId).parent().scrollTop($(document).height());
				if (action === "save") {
					$("#tp_shout_" + shoutboxId).val("");
					document.getElementById("tp_shout_" + shoutboxId).focus();
					$("#tp_shout_" + shoutboxId).setCursorPosition(0,0);
				}
			}
		}
	});
}

function replaceShoutText(shout_text, shout_handle) {
	var cursorPosition = $("#" + shout_handle).prop("selectionStart");
	var shoutTextArea = document.getElementById(shout_handle).value;
	var whiteSpace = cursorPosition != 0 && shoutTextArea.charAt(cursorPosition-1) == " " ? " " : "";
	var newShoutTextArea = shoutTextArea.substr(0,cursorPosition) + shout_text + whiteSpace + shoutTextArea.substr(cursorPosition, (shoutTextArea.length-1));
	document.getElementById(shout_handle).style.outline = "0.2em";
	setTimeout(function(){
		document.getElementById(shout_handle).style.outline = "0.2em";
		document.getElementById(shout_handle).value = newShoutTextArea;
	}, 100);
	document.getElementById(shout_handle).focus();
	$("#" + shout_handle).setCursorPosition(cursorPosition+shout_text.length);
	return false;
};

function surroundShoutText(bbcBefore, bbcAfter, shout_handle) {
	var cursorPosition = $("#" + shout_handle).prop("selectionStart");
	var shoutTextArea = document.getElementById(shout_handle).value;
	var whiteSpace = cursorPosition != 0 && shoutTextArea.charAt(cursorPosition-1) == " " ? " " : "";
	var newShoutTextArea = shoutTextArea.substr(0,cursorPosition) + bbcBefore + bbcAfter + whiteSpace + shoutTextArea.substr(cursorPosition, (shoutTextArea.length-1));
	document.getElementById(shout_handle).style.outline = "0.2em";
	setTimeout(function(){
		document.getElementById(shout_handle).style.outline = "0.2em";
		document.getElementById(shout_handle).value = newShoutTextArea;
		$("#" + shout_handle).setCursorPosition((cursorPosition+bbcBefore.length));
	}, 100);
	document.getElementById(shout_handle).focus();
	return false;
};

function tpShoutFocusTextArea(shoutboxTextArea) {
	if (document.getElementById(shoutboxTextArea)) {
		document.getElementById(shoutboxTextArea).focus();
	}
	return false;
}

$.fn.setCursorPosition = function(pos) {
  this.each(function(index, elem) {
    if (elem.setSelectionRange) {
      elem.setSelectionRange(pos, pos);
    } else if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  });
  return this;
};