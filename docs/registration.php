<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$_user_location	= 'public';

define('AT_INCLUDE_PATH', 'include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_POST['cancel'])) {
	header('Location: ./about.php');
	exit;
} else if (isset($_POST['submit'])) {
	/* email check */
	if ($_POST['email'] == '') {
		$msg->addError('EMAIL_MISSING');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$", $_POST['email'])) {
		$msg->addError('EMAIL_INVALID');
	}
	$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$_POST[email]'",$db);
	if (mysql_num_rows($result) != 0) {
		$msg->addError('EMAIL_EXISTS');
	}

	/* login name check */
	if ($_POST['login'] == '') {
		$msg->addError('LOGIN_NAME_MISSING');
	} else {
		/* check for special characters */
		if (!(eregi("^[a-zA-Z0-9_]([a-zA-Z0-9_])*$", $_POST['login']))) {
			$msg->addError('LOGIN_CHARS');
		} else {
			$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."members WHERE login='$_POST[login]'",$db);
			if (mysql_num_rows($result) != 0) {
				$msg->addError('LOGIN_EXISTS');
			} else {
				$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$_POST[login]'",$db);
				if (mysql_num_rows($result) != 0) {
					$msg->addError('LOGIN_EXISTS');
				}
			}
		}
	}

	/* password check:	*/
	if ($_POST['password'] == '') { 
		$msg->addError('PASSWORD_MISSING');
	} else {
		// check for valid passwords
		if ($_POST['password'] != $_POST['password2']){
			$msg->addError('PASSWORD_MISMATCH');
		}
	}
		
	$_POST['login'] = strtolower($_POST['login']);

	//check date of birth
	$mo = intval($_POST['month']);
	$day = intval($_POST['day']);
	$yr = intval($_POST['year']);

	/* let's us take (one or) two digit years (ex. 78 = 1978, 3 = 2003) */
	if ($yr < date('y')) { 
		$yr += 2000; 
	} else if ($yr < 1900) { 
		$yr += 1900; 
	} 

	$dob = $yr.'-'.$mo.'-'.$day;

	if ($mo && $day && $yr && !checkdate($mo, $day, $yr)) {	
		$msg->addError('DOB_INVALID');
	} else if (!$mo || !$day || !$yr) {
		$dob = '0000-00-00';
		$yr = $mo = $day = 0;
	}

	if (!$msg->containsErrors()) {
		if (($_POST['website']) && (!ereg("://",$_POST['website']))) { 
			$_POST['website'] = "http://".$_POST['website']; 
		}
		if ($_POST['website'] == 'http://') { 
			$_POST['website'] = ''; 
		}
		$_POST['postal'] = strtoupper(trim($_POST['postal']));

		$_POST['password'] = $addslashes($_POST['password']);
		$_POST['website'] = $addslashes($_POST['website']);
		$_POST['first_name'] = $addslashes($_POST['first_name']);
		$_POST['last_name'] = $addslashes($_POST['last_name']);
		$_POST['address'] = $addslashes($_POST['address']);
		$_POST['postal'] = $addslashes($_POST['postal']);
		$_POST['city'] = $addslashes($_POST['city']);
		$_POST['province'] = $addslashes($_POST['province']);
		$_POST['country'] = $addslashes($_POST['country']);
		$_POST['phone'] = $addslashes($_POST['phone']);

		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
			$status = AT_STATUS_UNCONFIRMED;
		if (defined('AUTO_APPROVE_INSTRUCTORS') && AUTO_APPROVE_INSTRUCTORS) {
			$status = AT_STATUS_INSTRUCTOR;
		} else {
			$status = AT_STATUS_STUDENT;
		}
		$now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.

		/* insert into the db. (the last 0 for status) */
		$sql = "INSERT INTO ".TABLE_PREFIX."members VALUES (0,'$_POST[login]','$_POST[password]','$_POST[email]','$_POST[website]','$_POST[first_name]','$_POST[last_name]', '$dob', '$_POST[gender]', '$_POST[address]','$_POST[postal]','$_POST[city]','$_POST[province]','$_POST[country]', '$_POST[phone]',$status,'', '$now','$_SESSION[lang]',0)";
		$result = mysql_query($sql, $db);
		$m_id	= mysql_insert_id($db);
		if (!$result) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$msg->addError('DB_NOT_UPDATED');
			$msg->printAll();
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}

		if ($_POST['pref'] == 'access') {
			$_SESSION['member_id'] = $m_id;
			save_prefs();
		}

		if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
			$msg->addFeedback('REG_THANKS_CONFIRM');

			$code = substr(md5($_POST['email'] . $now . $m_id), 0, 10);
			$confirmation_link = $_base_href . 'confirm.php?id='.$m_id.SEP.'m='.$code;

			/* send the email confirmation message: */
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			$mail = new ATutorMailer();

			$mail->From     = EMAIL;
			$mail->AddAddress($_POST['email']);
			$mail->Subject = SITE_NAME . ' - ' . _AT('email_confirmation_subject');
			$mail->Body    = _AT('email_confirmation_message', SITE_NAME, $confirmation_link);

			$mail->Send();

		} else {
			$msg->addFeedback('REG_THANKS');
		}

		require(AT_INCLUDE_PATH.'header.inc.php');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}

unset($_SESSION['member_id']);
unset($_SESSION['valid_user']);
unset($_SESSION['login']);
unset($_SESSION['is_admin']);
unset($_SESSION['course_id']);
unset($_SESSION['is_guest']);

/*****************************/
/* template starts down here */

$onload = 'document.form.login.focus();';

$savant->assign('languageManager', $languageManager);

$savant->display('registration.tmpl.php');

?>