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

function TPupdateShouts(action, shoutId)
{

    alert(action);
    
	var params = "";
	var name;
	var shout;
	if (action === "save") {
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
		url: smf_scripturl + "?action=tpshout;shout=" + action,
		data: params + "&" + tp_session_var + "=" + tp_session_id,
        statusCode: {
            404: function() {
                alert( "page not found" );
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        },
		beforeSend: function() {
            alert('before');
            return false;
		},
		complete: function(){
            alert('complete');
		},
		success: function(data) {
            alert('Successfully called');
            alert(data);
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
