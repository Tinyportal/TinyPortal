	

function expandHeaderSmiley(mode, is_guest, sessionId)
{
	// Guests don't have theme options!!
	if (is_guest)
		document.cookie = "expandsmiley=" + (mode ? 1 : 0);
	else
		smf_setThemeOption("expand_header_smiley", mode ? 1 : 0, null, sessionId);

	document.getElementById("expand_smiley").src = mode ? tp_images_url + "/TPcollapse.gif" : tp_images_url + "/TPexpand.gif";
	document.getElementById("expandHeaderSmiley").style.display = mode ? "" : "none";
	current_header_smiley = mode;
}


function expandHeaderBBC(mode, is_guest, sessionId)
{
	if (is_guest)
		document.cookie = "expandbbc=" + (mode ? 1 : 0);
	else
		smf_setThemeOption("expand_header_bbc", mode ? 1 : 0, null, sessionId);

	document.getElementById("expand_bbc").src = mode ? tp_images_url + "/TPcollapse.gif" : tp_images_url + "/TPexpand.gif";
	document.getElementById("expandHeaderBBC").style.display = mode ? "" : "none";
	current_header_bbc = mode;
}

var $j = jQuery.noConflict();
function TPupdateShouts(action, shoutId)
{
	var params = "shout=" + action;
	var name;
	var shout;
	if (action === "save")
	{
		name = $j("#tp-shout-name").val();
		shout = $j("#tp_shout").val();
		params = "shout=" + action + "&tp-shout-name=" + name + "&tp_shout=" + shout;
	}
	if (shoutId) {
		params = "shout=" + action + "&s=" + shoutId;
	}
	
	$j.ajax({
		type : "POST",
		dataType: "html",
		cache: false,
		url: smf_scripturl + "?action=tpmod;shout",
		data: params + "&" + tp_session_var + "=" + tp_session_id,
		beforeSend: function() {
			$j(".tp_shoutframe").hide();
			$j("#tp_shout_refresh img").attr("src", tp_images_url + "/ajax.gif");
		},
		complete: function(){
			$j("#tp_shout_refresh img").attr("src", tp_images_url + "/TPrefresh.png");
		},
		success: function(data) {
			var error = $j($j.parseHTML(data)).filter("#shoutError");
			// If there's an error let's display it
			if (error.length > 0) {
				$j("#shout_errors").html(error).show();
				$j(".tp_shoutframe").fadeIn();
				$j("#tp_shout").val(shout);
			} else {
				$j("#shout_errors").hide();
				$j(".tp_shoutframe").html(data).fadeIn();
				$j(".tp_shoutframe").parent().scrollTop(0);
				if (action === "save") {
					$j("#tp_shout").val("");
				}
			}
		}
	});
}