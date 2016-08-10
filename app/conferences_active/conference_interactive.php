<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	James Rose <james.o.rose@gmail.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('conference_interactive_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get and prepare the conference name
	$conference_name = check_str(trim($_REQUEST["c"]));
	$conference_name_full = $conference_name.'-'.$_SESSION['domain_name'];
	$conference_display_name = str_replace("-", " ", $conference_name);
	$conference_display_name = str_replace("_", " ", $conference_display_name);

//show the header
	require_once "resources/header.php";
	echo "<script src=\"".PROJECT_PATH."/resources/jquery/jquery-ui-1.9.2.min.js\"></script>\n";
?><script type="text/javascript">
function loadXmlHttp(url, id) {
	var f = this;
	f.xmlHttp = null;
	/*@cc_on @*/ // used here and below, limits try/catch to those IE browsers that both benefit from and support it
	/*@if(@_jscript_version >= 5) // prevents errors in old browsers that barf on try/catch & problems in IE if Active X disabled
	try {f.ie = window.ActiveXObject}catch(e){f.ie = false;}
	@end @*/
	if (window.XMLHttpRequest&&!f.ie||/^http/.test(window.location.href))
		f.xmlHttp = new XMLHttpRequest(); // Firefox, Opera 8.0+, Safari, others, IE 7+ when live - this is the standard method
	else if (/(object)|(function)/.test(typeof createRequest))
		f.xmlHttp = createRequest(); // ICEBrowser, perhaps others
	else {
		f.xmlHttp = null;
		 // Internet Explorer 5 to 6, includes IE 7+ when local //
		/*@cc_on @*/
		/*@if(@_jscript_version >= 5)
		try{f.xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");}
		catch (e){try{f.xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");}catch(e){f.xmlHttp=null;}}
		@end @*/
	}
	if(f.xmlHttp != null){
		f.el = document.getElementById(id);
		f.xmlHttp.open("GET",url,true);
		f.xmlHttp.onreadystatechange = function(){f.stateChanged();};
		f.xmlHttp.send(null);
	}
}

loadXmlHttp.prototype.stateChanged=function () {
if (this.xmlHttp.readyState == 4 && (this.xmlHttp.status == 200 || !/^http/.test(window.location.href)))
	//this.el.innerHTML = this.xmlHttp.responseText;
	document.getElementById('ajax_reponse').innerHTML = this.xmlHttp.responseText;
}

var requestTime = function() {
	var url = 'conference_interactive_inc.php?c=<?php echo trim($_REQUEST["c"]); ?>';
	new loadXmlHttp(url, 'ajax_reponse');
	// setInterval(function(){new loadXmlHttp(url, 'ajax_reponse');}, 1222);
	setInterval(function(){new loadXmlHttp(url, 'ajax_reponse');}, 1222);
	//initInviteDialog();
}

if (window.addEventListener) {
	window.addEventListener('load', requestTime, false);
}
else if (window.attachEvent) {
	window.attachEvent('onload', requestTime);
}

function send_cmd(url) {
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET",url,false);
	xmlhttp.send(null);
	document.getElementById('cmd_reponse').innerHTML=xmlhttp.responseText;
}

var record_count = 0;

function inviteUser(conferenceName) {
	//alert("this is the invite extension:" + conferenceName);
	var invite_type = $("input[name='invite_type']:checked").val();
	var invite_number = $("#invite_user_number").val();

	if(invite_number == "") {
		return;
	}
	if(invite_type == "extension") {
		var command = 'conference_exec.php?cmd=conference&name=' + conferenceName 
			+ "&data=invite_ext&number=" + invite_number;
	} else {
		var command = 'conference_exec.php?cmd=conference&name=' + conferenceName 
			+ "&data=invite_phone&number=" + invite_number;
	}
	send_cmd(command);	
}

function invitePhone(conferenceName) {
	alert("this is the invite phone:" + conferenceName);	
	
}

</script>

<?php

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
echo "	<tr>\n";
echo "	<td align='left'>";
echo "		<b>".$text['label-interactive']."</b><br><br>\n";
echo "		".$text['description-interactive']."\n";
echo "	</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "	<td align='left'>";
// $invite_user_ext = '<input class="formfld" type="number" name="invite_user_ext"'
// 		.'autocomplete="off" maxlength="255" min="0" step="1" value="" style="width: 70px;">';
// echo $invite_user_ext;
// $action_invite_user = "	<input type='button' class='btn' title=\"".$text['button-invite-user-title']
// 		."\" onclick=\"inviteExtension('".$conference_name."');\" value='".$text['button-invite-user']."'>\n";
// echo "	$action_invite_user";

echo "<input type=\"radio\" name=\"invite_type\" value=\"extension\" checked=\"checked\"/> ".$text['button-invite-user-extension'];
echo "<input type=\"radio\" name=\"invite_type\" value=\"phone\" /> ".$text['button-invite-user-phone'];

$invite_user_number = '<input class="formfld" type="number" id="invite_user_number"'
		.'autocomplete="off" maxlength="255" min="0" step="1" value="" style="width: 150px;">';
echo $invite_user_number;
$action_invite_user = "	<input type='button' class='btn' title=\"".$text['button-invite-user-title']
."\" onclick=\"inviteUser('".$conference_name_full."');\" value='".$text['button-invite-user']."'>\n";
echo "	$action_invite_user";
echo "	</td>\n";
echo "	</tr>\n";
echo "	<tr>\n";
echo "	<td align=\"left\">\n";
echo "		<br>\n";
echo "		<div id=\"ajax_reponse\"></div>\n";
echo "		<div id=\"time_stamp\" style=\"visibility:hidden\">".date('Y-m-d-s')."</div>\n";
echo "	</td>";
echo "	</tr>";
echo "</table>";



//show the header
	require_once "resources/footer.php";
	

?>