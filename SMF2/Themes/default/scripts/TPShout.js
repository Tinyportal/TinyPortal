	

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
	var param = action;
	if (param == "save")
	{
		var name = $j("#tp-shout-name").val();
		var shout = $j("#tp_shout").val();
		$j("#tp_shout").val("");
		param = param + ";tp-shout-name="+name+";tp_shout="+shout;
	}
	if (shoutId)
		param = param + ";s=" + shoutId;
	$j.ajax({
		type : "GET",
		dataType: "html",
		cache: false,
		url: smf_scripturl + "?action=tpmod;shout=" + param + ";" + tp_session_var + "=" + tp_session_id,
		beforeSend: function() {
			$j("#tp_shout_refresh img").attr("src", tp_images_url + "/ajax.gif");
		},
		complete: function(){
			$j("#tp_shout_refresh img").attr("src", tp_images_url + "/TPrefresh.png");
		},					
		success: function(data) {
			shoutHtml = $j(".tp_shoutframe", $j(data)).html();
			$j(".tp_shoutframe").html(shoutHtml);
			$j(".tp_shoutframe").parent().scrollTop(0);
		}
	});
}