<?php

##########################################################################
# Copyright 2013, Philip Ewels (phil.ewels@babraham.ac.uk)               #
#                                                                        #
# This file is part of Labrador.                                         #
#                                                                        #
# Labrador is free software: you can redistribute it and/or modify       #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# Labrador is distributed in the hope that it will be useful,            #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with Labrador.  If not, see <http://www.gnu.org/licenses/>.      #
##########################################################################

/*
	auth.php
	User authentication
	Allows user registration, login and password resetting
	Requires e-mail validation
	Uses cookies to remember logged in status
*/

if(!isset($msg)){
	$msg = array();
}
if(!isset($error)){
	$error = false;
}

//////////////////////////
// LOGIN LINK
//////////////////////////
function labrador_login_link() {
	global $user;
	global $admin;
	if($user) {
		?>
		<li class="dropdown authlink">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Logged in as <?php echo $user['firstname'].' '.$user['surname']; ?> <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="index.php?my_projects">My Projects</a></li>
			<?php if($admin){ ?>
				<li><a href="index.php?assigned_projects">Assigned To Me</a></li>
				<li><a href="index.php?unassigned">Not Assigned</a></li>
			<?php } ?>
				<li><a data-toggle="modal" href="#change_password_modal">Change Password</a></li>
				<li><a href="index.php?a=logout">Log Out</a></li>
			</ul>
		</p>
		<?php
	} else {
		?>
		<li class="authlink">
			<a data-toggle="modal" href="#register_modal">Log In / Register</a>
		</li>
		<?php
	}
}

//////////////////////////
// LOGIN / REGISTER MODAL
//////////////////////////
function labrador_login_modal() {
	global $user;
	global $groups;
	// If we're on the homepage, strip the GET values to avoid munging reverification etc
	$url = $_SERVER['REQUEST_URI'];
	if(isset($_GET['a'])){
		$url = 'index.php';
	}
	if(!$user) { ?>	
	<!-- Register / Login Modal -->
	<div id="register_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="register_modal_label" aria-hidden="true">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3 id="register_modal_label">Register / Log In</h3>
		</div>
		<div class="modal-body">
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#login_tabpane" data-toggle="tab">Log In</a></li>
					<li><a href="#register_tabpane" data-toggle="tab">Register</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="login_tabpane">
						<form class="form-horizontal" action="<?php echo $url; ?>" method="post">
							<div class="control-group">
								<label class="control-label" for="login_email">E-mail Address</label>
								<div class="controls">
									<input type="text" name="login_email" id="login_email">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="login_password">Password</label>
								<div class="controls">
									<input type="password" name="login_password" id="login_password">
								</div>
							</div>
							
							<div class="modal-footer">
								<small class="help-block pull-left">Labrador uses cookies. <a href="http://www.whatarecookies.com/enable.asp" target="_blank">Click here for more info</a>.</small>
								<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
								<input type="submit" class="btn btn-success" name="login_submit" id="login_submit"  value="Log In">
							
								<div class="forgotten_password">
									<p>Forgotten your password? Enter your e-mail address above and <input class="btn btn-link" type="submit" name="forgotten_password" value="click here"> to reset it.</p>
									<p>Problems with e-mail verification? <a data-toggle="modal" data-dismiss="modal" href="#email_verification_modal">Manually enter code</a>.</p>
								</div>
							
							</div>
							
							
						</form>
					</div>
					<div class="tab-pane" id="register_tabpane">
						<form class="form-horizontal" action="<?php echo $url; ?>" method="post">
							<div class="control-group">
								<label class="control-label" for="register_firstName">First Name</label>
								<div class="controls">
									<input type="text" name="register_firstName" id="register_firstName">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="register_lastName">Surname</label>
								<div class="controls">
									<input type="text" name="register_lastName" id="register_lastName">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="register_email">E-mail Address</label>
								<div class="controls">
									<input type="email" name="register_email" id="register_email">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="register_group">Group</label>
								<div class="controls">
									<select name="register_group" id="register_group">
									<?php
									foreach($groups as $name => $key){
										echo '<option>'.$name.'</option>';
									}
									?>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="register_password">Password</label>
								<div class="controls">
									<input type="password" name="register_password" id="register_password">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="register_password_confirm">Confirm Password</label>
								<div class="controls">
									<input type="password" name="register_password_confirm" id="register_password_confirm">
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
								<input type="submit" class="btn btn-success" name="register_submit" id="register_submit" value="Register">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- E-mail Verification Modal -->
	<div id="email_verification_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="email_verification_modal_label" aria-hidden="true">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3 id="email_verification_modal_label">Verify E-mail Address</h3>
		</div>
		<div class="modal-body">
			<form class="form-horizontal" action="index.php" method="get">
				<input type="hidden" name="a" value="verify">
				<div class="control-group">
					<label class="control-label" for="verify_email">E-mail Address</label>
					<div class="controls">
						<input type="email" name="email" id="verify_email">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="verify_code">Verification Code</label>
					<div class="controls">
						<input type="text" name="vstr" id="verify_code">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
					<input type="submit" class="btn btn-success" id="verify_code_submit"  value="Verify E-mail Address">
				</div>
			</form>
		</div>
	</div>

<?php } else { ?>	
	<!-- Change Password Modal -->
	<div id="change_password_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="change_password_modal_label" aria-hidden="true">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3 id="change_password_modal_label">Change Password</h3>
		</div>
		<div class="modal-body">
			<form class="form-horizontal" action="<?php echo $url; ?>" method="post">
				<div class="control-group">
					<label class="control-label" for="login_password">Current Password</label>
					<div class="controls">
						<input type="password" name="login_password" id="login_password">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="new_password">New Password</label>
					<div class="controls">
						<input type="password" name="new_password" id="new_password">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
					<input type="submit" class="btn btn-success" name="change_password" id="change_password"  value="Change Password">
				</div>
			</form>
		</div>
	</div>

<?php
	}
}

//////////////////////////
// ADMIN CHECK
//////////////////////////
function is_admin(){
	global $user, $administrators;
	if(in_array($user['email'], array_keys($administrators))){
		return true;
	} else {
		return false;
	}
}


//////////////////////////
// COOKIE LOGIN CHECK
//////////////////////////
if(isset($_COOKIE['email']) && isset($_COOKIE['authstring'])){
	// Note - password is stored in the cookie hashed and salted, not plain text
	cookie_login($user, $msg, $_COOKIE['email'], $_COOKIE['authstring']);
} else {
	$user = false;
	$admin = false;
}

function cookie_login(&$user, &$msg, $email, $authstring){
	global $admin;
	$admin = false;
	
	$sql = sprintf("SELECT * FROM `users` WHERE `email` = '%s' AND `authstring` = '%s' AND `verification` = ''",
						mysql_real_escape_string($email),
						mysql_real_escape_string($authstring));
	$user_q = mysql_query($sql);
	if(mysql_num_rows($user_q) !== 1){
		$user = false;
		// login failed - remove cookies
		setcookie ("email", "", time() - 3600);
		setcookie ("authstring", "", time() - 3600);
		//$error = true; $msg[] = 'Log in failed.';
	} else {
		$user = mysql_fetch_array($user_q);
		// Is admin?
		if(is_admin()){
			$admin = true;
		}
		//$msg[] = '<strong>Success!</strong> Log in succeeded.';
	}
}

//////////////////////////
// USER LOGIN SUBMIT
//////////////////////////
// user login - using CRYPT_SHA512 for hashing
if (!defined('CRYPT_SHA512')){ die('CRYPT_SHA512 not available for hashing passwords'); }
if (!$user && isset($_POST['login_submit']) && $_POST['login_submit'] == 'Log In'){
	if (filter_var($_POST['login_email'], FILTER_VALIDATE_EMAIL)) {
		$login_q = mysql_query(sprintf("SELECT * FROM `users` WHERE `email` = '%s'", mysql_real_escape_string($_POST['login_email'])));
		if(mysql_num_rows($login_q) !== 1){
			$error = true;
			$msg[] = 'E-mail address or password incorrect.';
		} else {
			$user = mysql_fetch_array($login_q);
			if($user['verification'] == ''){
				if (crypt($_POST['login_password'], $user['password']) == $user['password']) {
					$authstring = bin2hex(openssl_random_pseudo_bytes(128));
					if(!mysql_query("UPDATE `users` SET `authstring` = '$authstring', `last_login` = '".time()."' WHERE `id` = '".$user['id']."'")){
						$error = true;
						$msg[] = "Couldn't update authstring: ".mysql_error();
					} else {
						$msg[] = '<strong>Logged In.</strong> Hi there '.$user['firstname'];
						// Cookie lasts a week
						setcookie("email", $user['email'], time()+3600*24*7);
						setcookie("authstring", $authstring, time()+3600*24*7);
						// Is admin?
						if(is_admin()){
							$admin = true;
						}
					}
				} else {
					$error = true;
					$msg[] = 'E-mail address or password incorrect';
					$user = false;
				}
			} else {
				$error = true;
				$msg[] = 'You need to verify your e-mail address before you can log in.';
				$user = false;
			}
		}
	} else {
		$error = true;
		$msg[] = 'E-mail address looks invalid - '.$_POST['login_email'];
	}
	
}

//////////////////////////
// USER LOG OUT
//////////////////////////
if($user && isset($_GET['a']) && $_GET['a'] == 'logout' && !isset($_POST['login_submit'])){
	setcookie ("email", "", time() - 3600);
	setcookie ("authstring", "", time() - 3600);
	$user = false;
	$msg[] = '<strong>You have logged out.</strong>';
}


//////////////////////////
// USER REGISTRATION
//////////////////////////
$email_headers = "From: $support_email\r\nReply-To: $support_email\r\nContent-type: text/plain; charset=utf-8\r\nX-Mailer: PHP/" . phpversion();

if(!$user && isset($_POST['register_submit']) && $_POST['register_submit'] == 'Register'){
	$firstname = trim($_POST['register_firstName']);
	$surname = trim($_POST['register_lastName']);
	$email = trim($_POST['register_email']);
	$group = trim($_POST['register_group']);
	// $6$ specifies CRYPT_SHA512. rounds=5000 means hash 5000 times. more = more secure, more slow
	$salt = '$6$rounds=5000$' . uniqid();
	$password = crypt($_POST['register_password'], $salt);
	$authstring = bin2hex(openssl_random_pseudo_bytes(128));
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$verificationString = '';
	for ($i = 0; $i < 8; $i++) {
	    $verificationString .= $characters[rand(0, strlen($characters) - 1)];
	}
	
	$error = false;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$msg[] = 'E-mail address looks invalid - '.$email;
		$error = true;
	}
	if(mysql_num_rows(mysql_query(sprintf("SELECT `id` FROM `users` WHERE `email` = '%s'",
		mysql_real_escape_string($email)))) > 0){
		$msg[] = 'Somebody has already registered with the e-mail address <strong>'.$email.'</strong>.';
		$error = true;
	}
	if(strlen($_POST['register_password']) < 5){
		$msg[] = 'Password must be at least 6 characters.';
		$error = true;
	}
	if($_POST['register_password'] != $_POST['register_password_confirm']){
		$msg[] = 'The two passwords you typed did not match.';
		$error = true;
	}
	if(strlen($firstname) < 2 || strlen($surname) < 2 || strlen($email) < 4){
		$msg[] = 'Name and e-mail address are mandatory.';
		$error = true;
	}
	
	if(!$error){
		
		if(mysql_query(sprintf("INSERT INTO `users` (`email`, `firstname`, `surname`, `group`, `password`, `authstring`, `verification`, `registered`, `last_login`)
		 				VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d')",
						mysql_real_escape_string($email),
						mysql_real_escape_string($firstname),
						mysql_real_escape_string($surname),
						mysql_real_escape_string($group),
						mysql_real_escape_string($password),
						mysql_real_escape_string($authstring),
						mysql_real_escape_string($verificationString),
						time(),
						time() ) ) ){
			$msg[] = "<strong>Success!</strong> You are now registered. Please check your inbox to verify your e-mail address.";
			mail($_POST['register_email'], '[Labrador] Registration', "Hi $firstname,

You have successfully registered on the Labrador website: $labrador_url

Before you can use the site, you need to confirm your e-mail address. Please open the following link to do so:
$labrador_url?a=verify&email=$email&vstr=$verificationString

Alternatively, you can click 'Verify E-mail' in the login window and enter the code $verificationString

You will then be able to log in with your registered e-mail address ($email) and the password that you just set.
			
If you have any queries, please e-mail $support_email

--
This is an automated e-mail sent from Labrador
$labrador_url
", $email_headers);
		} else {
			$error = true;
			$msg[] = "Couldn't save registration to database: ".mysql_error();
		}
	}
}


//////////////////////////
// VERIFY E-MAIL ADDRESS
//////////////////////////
if(!$user && isset($_GET['a']) && $_GET['a'] == 'verify' && isset($_GET['email']) && isset($_GET['vstr'])){
	$login_q = mysql_query(sprintf("SELECT * FROM `users` WHERE `email` = '%s'", mysql_real_escape_string($_GET['email'])));
	if(mysql_num_rows($login_q) !== 1){
		$error = true;
		$msg[] = 'E-mail address for verification not found.';
	} else {
		$user_verification = mysql_fetch_array($login_q);
		if($user_verification['verification'] == ''){
			$error = true;
			$msg[] = "This account has already had it's e-mail address verified.";
		} else {
			if($user_verification['verification'] == $_GET['vstr']){
				if(!mysql_query("UPDATE `users` SET `verification` = '' WHERE `id` = '".$user_verification['id']."'")){
					$error = true;
					$msg[] = "Couldn't update verification string: ".mysql_error();
				} else {
					$msg[] = '<strong>E-mail address verified.</strong> Thanks! You can now <a data-toggle="modal" href="#register_modal">Log In</a>.';
				}
			} else {
				$error = true;
				$msg[] = "E-mail address verification code incorrect.";
			}
		}
	}
}



//////////////////////////
// CHANGE PASSWORD
//////////////////////////
if($user && isset($_POST['change_password']) && $_POST['change_password'] == 'Change Password'){
	if(strlen($_POST['new_password']) > 5){
		if (crypt($_POST['login_password'], $user['password']) == $user['password']) {
			// $6$ specifies CRYPT_SHA512. rounds=5000 means hash 5000 times. more = more secure, more slow
			$salt = '$6$rounds=5000$' . uniqid();
			$new_password = crypt($_POST['new_password'], $salt);
			$authstring = bin2hex(openssl_random_pseudo_bytes(128));
			if(!mysql_query("UPDATE `users` SET `password` = '$new_password', `authstring` = '$authstring' WHERE `id` = '".$user['id']."'")){
				$error = true;
				$msg[] = "Couldn't update password: ".mysql_error();
			} else {
				$msg[] = '<strong>Password Changed</strong>';
				// Cookie lasts a week
				setcookie("email", $user['email'], time()+3600*24*7);
				setcookie("authstring", $authstring, time()+3600*24*7);
				mail($user['email'], '[Labrador] Password Changed', "Hi ".$user['firstname'].",

Someone (hopefully you) has just reset the password for ".$user['email']." on the Labrador website.

If this was you, that's great - everything is fine. If it wasn't, please e-mail $support_email immediately.

Thanks!

--
This is an automated e-mail sent from Labrador
$labrador_url
", $email_headers);
			}
		} else {
			$error = true;
			$msg[] = 'Old password check failed.';
		}
	} else {
		$error = true;
		$msg[] = 'New password must be at least six characters long.';
	}
}



//////////////////////////
// FORGOTTEN PASSWORD
//////////////////////////
if(!$user && isset($_POST['forgotten_password']) && $_POST['forgotten_password'] == 'click here'){
	if (filter_var($_POST['login_email'], FILTER_VALIDATE_EMAIL)) {
		$pass_q = mysql_query(sprintf("SELECT * FROM `users` WHERE `email` = '%s'", mysql_real_escape_string($_POST['login_email'])));
		if(mysql_num_rows($pass_q) !== 1){
			$msg[] = '<strong>Password Reset.</strong> A new password has been sent to '.$_POST['login_email'];
			mail($_POST['login_email'], '[Labrador] Password Reset', "Hi there,

Someone has just asked to reset their password on the Labrador website. They provided this e-mail address to recover the password, but there is no user associated with this e-mail address on the website.

If this was you, this means that you are not yet registered on Labrador. If it wasn't, you can safely ignore this email. You can register at $labrador_url

If you have any queries, please e-mail $support_email

--
This is an automated e-mail sent from Labrador
$labrador_url
", $email_headers);
		} else {
			$np_user = mysql_fetch_array($pass_q);
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ($i = 0; $i < 8; $i++) {
			    $randomString .= $characters[rand(0, strlen($characters) - 1)];
			}
			$salt = '$6$rounds=5000$' . uniqid();
			$dbpass = crypt($randomString, $salt);
			$authstring = bin2hex(openssl_random_pseudo_bytes(128));
			$sql = "UPDATE `users` SET `password` = '$dbpass', `authstring` = '$authstring' WHERE `id` = '".$np_user['id']."'";
			if(mysql_query($sql)){
				$msg[] = '<strong>Password Reset.</strong> A new password has been sent to '.$np_user['email'];
				mail($np_user['email'], '[Labrador] Password Reset', "Hi ".$np_user['firstname'].",

A password reset has just been requested on the Labrador website for this e-mail address. Your new password is: $randomString

You can now log in with this password at $labrador_url - click your name in the top right followed by 'Change Password' to update it to something more memorable.

If you did not request this password change, please contact $support_email immediately

--
This is an automated e-mail sent from Labrador
$labrador_url
", $email_headers);
			} else {
				$error = true;
				$msg[] = "Couldn't update pass and authstring: ".mysql_error();
			}
		}
	} else {
		$error = true;
		$msg[] = 'E-mail address looks invalid - '.$_POST['forgotten_password_email'];
	}
}

?>