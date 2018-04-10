<?php
	include("include/session.php");
	
	class Process
	{
		/* Class constructor */
		function Process(){
			global $session;
			/* User submitted login form */
			if(isset($_POST['sublogin'])){
				$this->procLogin();
			}
			/* User submitted registration form */
			else if(isset($_POST['subjoin'])){
				$this->procRegister();
			}
			/* User submitted forgot password form */
			else if(isset($_POST['subforgot'])){
				$this->procForgotPass();
			}
			/* User submitted edit account form */
			else if(isset($_POST['subedit'])){
				$this->procEditAccount();
			}
			else if(isset($_POST['subConfirm'])){
				$this->procSendConfirm();
			}
			else if(isset($_POST['login_with_hash'])){
				$this->procHashLogin($_POST['hash']);
			}
			else if(isset($_POST['addeventA'])){
				$this->procAddA();
			}
			else if(isset($_POST['addeventB'])){
				$this->procAddB();
			}
			else if(isset($_POST['addeventC'])){
				$this->procAddC();
			}
			else if(isset($_POST['choosesemester'])){
				$this->procSemester();
			}
			else if(isset($_POST['choosecourse'])){
				$this->procCourse();
			}
			else if(isset($_POST['choosecrn'])){
				$this->procCRN();
			}
			else if(isset($_POST['studentLogin'])){
				$this->procStudentLogin();
			}
			else if(isset($_POST['editeventA'])){
				$this->procEditA();
			}
			else if(isset($_POST['editeventB'])){
				$this->procEditB();
			}
			else if(isset($_POST['editeventC'])){
				$this->procEditC();
			}
			else if(isset($_POST['deleteEvent'])){
				$this->procDeleteEvent();
			}
			/**
				* The only other reason user should be directed here
				* is if he wants to logout, which means user is
				* logged in currently.
			*/
			else if($session->logged_in){
				$this->procLogout();
			}
			/**
				* Should not get here, which means user is viewing this page
				* by mistake and therefore is redirected.
			*/
			else{
				header("Location: index.php");
			}
		}
		
		/**
			* procLogin - Processes the user submitted login form, if errors
			* are found, the user is redirected to correct the information,
			* if not, the user is effectively logged in to the system.
		*/
		function procLogin(){
			global $session, $form;
			/* Login attempt */
			$_POST = $session->cleanInput($_POST);
			$retval = $session->login($_POST['user'], $_POST['pass'], isset($_POST['remember']));
			
			/* Login successful */
			if($retval){
				header("Location: index.php");
			}
			/* Login failed */
			else{
				$_SESSION['value_array'] = $_POST;
				$_SESSION['error_array'] = $form->getErrorArray();
				header("Location: login.php");
			}
		}
		
		/**
			* procLogout - Simply attempts to log the user out of the system
			* given that there is no logout form to process.
		*/
		function procLogout(){
			global $session;
			$retval = $session->logout();
			header("Location: index.php");
		}
		
		/**
			* procRegister - Processes the user submitted registration form,
			* if errors are found, the user is redirected to correct the
			* information, if not, the user is effectively registered with
			* the system and an email is (optionally) sent to the newly
			* created user.
		*/
		function procRegister(){
			global $session, $form;
			$_POST = $session->cleanInput($_POST);
			/* Convert username to all lowercase (by option) */
			if(ALL_LOWERCASE){
				$_POST['user'] = strtolower($_POST['user']);
			}
			/* Registration attempt */
			$retval = $session->register($_POST['user'], $_POST['pass'], $_POST['email'], $_POST['name']);
			
			/* Registration Successful */
			if($retval == 0){
				$_SESSION['reguname'] = $_POST['user'];
				$_SESSION['regsuccess'] = true;
				header("Location: ".$session->referrer);
			}
			/* Error found with form */
			else if($retval == 1){
				$_SESSION['value_array'] = $_POST;
				$_SESSION['error_array'] = $form->getErrorArray();
				header("Location: ".$session->referrer);
			}
			/* Registration attempt failed */
			else if($retval == 2){
				$_SESSION['reguname'] = $_POST['user'];
				$_SESSION['regsuccess'] = false;
				header("Location: ".$session->referrer);
			}
		}
		/**
			* procEditAccount - Attempts to edit the user's account
			* information, including the password, which must be verified
			* before a change is made.
		*/
		function procEditAccount(){
			global $session, $form;
			$_POST = $session->cleanInput($_POST);
			/* Account edit attempt */
			$retval = $session->editAccount($_POST['curpass'], $_POST['newpass'], $_POST['email'], $_POST['name']);
			
			/* Account edit successful */
			if($retval){
				$_SESSION['useredit'] = true;
				header("Location: ".$session->referrer);
			}
			/* Error found with form */
			else{
				$_SESSION['value_array'] = $_POST;
				$_SESSION['error_array'] = $form->getErrorArray();
				header("Location: ".$session->referrer);
			}
		}
		
		
		function procHashLogin($hash){
			global $session, $database;
			if(substr($hash,0,1) === "#"){
				$hash = substr($hash,1);
			}
			
			$user_info = $database->getUserInfoFromHash($hash);
			
			if($user_info['hash_generated'] < (time() - (60*60*24*3))){
				// if the hash was generated more than 3 days ago, the hash is invalid.
				// let's invalidate and refuse the hash.
				$database->updateUserField($user_info['username'], 'hash', $session->generateRandID());
				$database->updateUserField($user_info['username'], 'hash_generated', time());
				return false;
			}
			
			if($user_info['username'] && $user_info['user_id']){  
				$_SESSION['username'] = $user_info['username'];
				$_SESSION['user_id'] = $user_info['user_id'];
				$session->checkLogin();
				die("Logging In...");
				} else {
				die();
			}
		}
		/*
			
			The following add functions are for add event form. It sends the values to the session function matching
		*/
		
		function procAddA(){
			global $session, $form;
			if (isset($_POST['repeat']) && $_POST['repeat']==1){
				$retval = $session->addEventAA($_POST['title'], $_POST['type'], $_POST['course'], $_POST['seats'], $_POST['notes'], $_POST['date'], $_POST['starttime'], $_POST['endtime'], $_POST['repeat'], $_POST['repeatm'], $_POST['repeatt'], $_POST['repeatw'], $_POST['repeatth'], $_POST['repeatf'], $_POST['re']);
				} else {
				$retval = $session->addEventA($_POST['title'], $_POST['type'], $_POST['course'], $_POST['seats'], $_POST['notes'], $_POST['date'], $_POST['starttime'], $_POST['endtime']);
			}
		}
		
		function procAddB(){
			global $session, $form;
			if (isset($_POST['repeat']) && $_POST['repeat']==1){
				$retval = $session->addEventBA($_POST['title'], $_POST['type'], $_POST['course'], $_POST['crn'], $_POST['seats'], $_POST['notes'], $_POST['date'], $_POST['starttime'], $_POST['endtime'], $_POST['repeat'], $_POST['repeatm'], $_POST['repeatt'], $_POST['repeatw'], $_POST['repeatth'], $_POST['repeatf'], $_POST['re']);
				} else {
				$retval = $session->addEventB($_POST['title'], $_POST['type'], $_POST['course'], $_POST['crn'], $_POST['seats'], $_POST['notes'], $_POST['date'], $_POST['starttime'], $_POST['endtime']);			
			}
		}
		function procAddC(){
			global $session, $form;
			if (isset($_POST['repeat']) && $_POST['repeat']==1){
				$retval = $session->addEventCA($_POST['title'], $_POST['type'], $_POST['course'], $_POST['crn'], $_POST['seats'], $_POST['notes'],  $_POST['dateStart'], $_POST['dateEnd'], $_POST['room'], $_POST['series'], $_POST['repeat'], $_POST['repeatm'], $_POST['repeatt'], $_POST['repeatw'], $_POST['repeatth'], $_POST['repeatf'], $_POST['re'], $_POST['conflict']);
				} else {
				$retval = $session->addEventC($_POST['title'], $_POST['type'], $_POST['course'], $_POST['crn'], $_POST['seats'], $_POST['notes'],  $_POST['dateStart'], $_POST['dateEnd'], $_POST['room'], $_POST['series'], $_POST['conflict']);			
			}
			header("Location: index.php");
			
			
		}
		function procSemester(){
			global $session, $form;
			$_POST = $session->cleanInput($_POST);
			$retval = $session->chooseSemester($_POST['cwid'], $_POST['semester']);
		}
		function procCourse(){
			global $session, $form;
			$retval = $session->chooseCourse($_POST['cwid'], $_POST['course'], $_POST['sem']);
		}
		function procCrn(){
			global $session, $form;
			$retval = $session->chooseCrn($_POST['crn']);
		}
		function procStudentLogin(){
			global $session, $form;
			$retval = $session->studentLogin($_POST['CWID']);
		}			

		function procEditA(){
			global $session, $form;
			$retval = $session->editEventA($_POST['title'], $_POST['type'], $_POST['seats'], $_POST['notes'], $_POST['date'], $_POST['starttime'], $_POST['endtime'], $_POST['eventid']);
		}
		
		function procEditB(){
			global $session, $form;
			$retval = $session->editEventB($_POST['title'], $_POST['type'], $_POST['seats'], $_POST['notes'], $_POST['dateStart'], $_POST['dateEnd'], $_POST['room'], $_POST['conflict'], $_POST['eventid']);
			//header("Location: editevent.php?e=".$_POST['eventid']);
		}
		
		
		function procEditC(){
			global $session, $form;
			$retval = $session->editEventC($_POST['eventid'], $_POST['notes']);
			header("Location: editevent.php?e=".$_POST['eventid']);
		}
		
		function procDeleteEvent(){
			global $session, $form;
			$retval = $session->deleteEvent($_POST['eventid']);
			header("Location: index.php");
		}
		
		
		
	};
	
	
	/* Initialize process */
	$process = new Process;
	
?>
