	

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

function TPupdateShouts(action, shoutId)
{
	var params = "shout=" + action;
	var name;
	var shout;
	if (action === "save")
	{
		name = $("#tp-shout-name").val();
		shout = $("#tp_shout").val();
		params = "shout=" + action + "&tp-shout-name=" + name + "&tp_shout=" + shout;
	}
	if (shoutId) {
		params = "shout=" + action + "&s=" + shoutId;
	}
	
	$.ajax({
		type : "POST",
		dataType: "html",
		cache: false,
		url: smf_scripturl + "?action=tpmod;shout",
		data: params + "&" + tp_session_var + "=" + tp_session_id,
		beforeSend: function() {
			$(".tp_shoutframe").hide();
			$("#tp_shout_refresh img").attr("src", tp_images_url + "/ajax.gif");
		},
		complete: function(){
			$("#tp_shout_refresh img").attr("src", tp_images_url + "/TPrefresh.png");
		},
		success: function(data) {
			var error = $($.parseHTML(data)).filter("#shoutError");
			// If there's an error let's display it
			if (error.length > 0) {
				$("#shout_errors").html(error).show();
				$(".tp_shoutframe").fadeIn();
				$("#tp_shout").val(shout);
			} else {
				$("#shout_errors").hide();
				$(".tp_shoutframe").html(data).fadeIn();
				$(".tp_shoutframe").parent().scrollTop(0);
				if (action === "save") {
					$("#tp_shout").val("");
				}
			}
		}
	});
}