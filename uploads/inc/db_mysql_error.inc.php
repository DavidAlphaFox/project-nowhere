<?php

if(!defined('IN_NOWHERE')) {
	exit('Access Denied');
}

$timestamp = time();
$errmsg = '';

$dberror = $this->error();
$dberrno = $this->errno();

if($dberrno == 1114) {

?>
<html>
<head>
<title>Max Onlines Reached</title>
</head>
<body bgcolor="#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" width="600" align="center" height="85%">
  <tr align="center" valign="middle">
    <td>
    <table cellpadding="10" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 9px">
    <tr>
      <td valign="middle" align="center" bgcolor="#EBEBEB">
        <br><b style="font-size: 10px">Site onlines reached the upper limit</b>
        <br><br><br>Sorry, the number of online visitors has reached the upper limit.
        <br>Please wait for someone else going offline or visit us in idle hours.
        <br><br>
      </td>
    </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
<?

	function_exists('chobits_exit') ? chobits_exit() : exit();

} else {

	if($message) {
		$errmsg = "<b>Chobits Said</b>: $message\n\n";
	}
	if(isset($GLOBALS['_DSESSION']['nw_user'])) {
		$errmsg .= "<b>User</b>: ".htmlspecialchars($GLOBALS['_DSESSION']['nw_user'])."\n";
	}
	$errmsg .= "<b>Time</b>: ".gmdate("Y-n-j g:ia", $timestamp + ($GLOBALS['timeoffset'] * 3600))."\n";
	$errmsg .= "<b>Script</b>: ".$GLOBALS['PHP_SELF']."\n\n";
	if($sql) {
		$errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."\n";
	}
	$errmsg .= "<b>Error</b>:  $dberror\n";
	$errmsg .= "<b>Errno.</b>:  $dberrno";

	echo "</table></table></table></table></table>\n";
	echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
	echo nl2br(str_replace($GLOBALS['tablepre'], '[Table]', $errmsg));

	if($GLOBALS['adminemail']) {
		$errlog = array();
		if(@$fp = fopen(NOWHERE_ROOT.'./data/dberror.log', 'r')) {
			while((!feof($fp)) && count($errlog) < 20) {
				$log = explode("\t", fgets($fp, 50));
				if($timestamp - $log[0] < 86400) {
					$errlog[$log[0]] = $log[1];
				}
			}
			fclose($fp);
		}

		if(!in_array($dberrno, $errlog)) {
			$errlog[$timestamp] = $dberrno;
			@$fp = fopen(NOWHERE_ROOT.'./forumdata/dberror.log', 'w');
			@flock($fp, 2);
			foreach(array_unique($errlog) as $dateline => $errno) {
				@fwrite($fp, "$dateline\t$errno");
			}
			@fclose($fp);
			if(function_exists('errorlog')) {
				errorlog('MySQL', basename($GLOBALS['_SERVER']['PHP_SELF'])." : $dberror - ".GlobalCore::cutstr($sql, 120), 0);
			}

			if($GLOBALS['dbreport']) {
				echo "<br><br>An error report has been dispatched to our administrator.";
			}

		} else {
			echo '<br><br>Similar error report has beed dispatched to administrator before.';
		}

	}
	echo '</p>';

	function_exists('chobits_exit') ? chobits_exit() : exit();

}

?>