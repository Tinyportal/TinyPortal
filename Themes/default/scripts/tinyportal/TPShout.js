function expandHeaderSmiley(mode, is_guest, sessionId)
{
	// Guests don't have theme options!!
	if (is_guest)
		document.cookie = "expandsmiley=" + (mode ? 1 : 0);
	else
		smf_setThemeOption("expand_header_smiley", mode ? 1 : 0, null, sessionId);

	document.getElementById("expand_smiley").src = mode ? tp_images_url + "/TPcollapse.png" : tp_images_url + "/TPexpand.png";
	document.getElementById("expandHeaderSmiley").style.display = mode ? "" : "none";
	current_header_smiley = mode;
}


function expandHeaderBBC(mode, is_guest, sessionId)
{
	if (is_guest)
		document.cookie = "expandbbc=" + (mode ? 1 : 0);
	else
		smf_setThemeOption("expand_header_bbc", mode ? 1 : 0, null, sessionId);

	document.getElementById("expand_bbc").src = mode ? tp_images_url + "/TPcollapse.png" : tp_images_url + "/TPexpand.png";
	document.getElementById("expandHeaderBBC").style.display = mode ? "" : "none";
	current_header_bbc = mode;
}

function TPupdateShouts(action, shoutboxId, shoutId, shoutLayout)
{

	var params = "";
	var name;
	var shout;
	if (action === "save") {
		name    = $("#tp-shout-name").val();
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
			var error = $($.parseHTML(data)).filter("#shoutError");
			// If there's an error let's display it
			if (error.length > 0) {
				$("#shout_errors").html(error).show();
				$(".tp_shoutframe_" + shoutboxId).fadeIn();
				$("#tp_shout_" + shoutboxId).val(shout);
			} else {
				$("#shout_errors").hide();
				$(".tp_shoutframe_" + shoutboxId).html(data).fadeIn();
				$(".tp_shoutframe_" + shoutboxId).parent().scrollTop(0);
				if (action === "save") {
					$("#tp_shout_" + shoutboxId).val("");
				}
			}
		}
	});
}
