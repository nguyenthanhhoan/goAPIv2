<?php
/**
 * @file 		goEditUser.php
 * @brief 		API to edit specific User Details
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho <demian@goautodial.com>
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
    
    include_once ("goAPI.php");
 
    // POST or GET Variables		
    $userid = $astDB->escape($_REQUEST['user_id']);
    $user = $astDB->escape($_REQUEST['user']);
    $pass = $astDB->escape($_REQUEST['pass']);
    $full_name = $astDB->escape($_REQUEST['full_name']);
    $phone_login = $astDB->escape($_REQUEST['phone_login']);
    $phone_pass = $pass;
    $user_group = $astDB->escape($_REQUEST['user_group']);
    $email = $astDB->escape($_REQUEST['email']);
    $active = strtoupper($astDB->escape($_REQUEST['active']));
    $hotkeys_active = $astDB->escape($_REQUEST['hotkeys_active']);
    $user_level = $astDB->escape($_REQUEST['user_level']);
    $modify_same_user_level = strtoupper($astDB->escape($_REQUEST['modify_same_user_level']));
    $ip_address = $astDB->escape($_REQUEST['hostname']);
    $goUser = $astDB->escape($_REQUEST['goUser']);
    $voicemail = $astDB->escape($_REQUEST['voicemail']);
    $vdc_agent_api_access = $astDB->escape($_REQUEST['vdc_agent_api_access']);
    $agent_choose_ingroups = $astDB->escape($_REQUEST['agent_choose_ingroups']);
    $vicidial_recording_override = $astDB->escape($_REQUEST['vicidial_recording_override']);
    $vicidial_transfers = $astDB->escape($_REQUEST['vicidial_transfers']);
    $closer_default_blended = $astDB->escape($_REQUEST['closer_default_blended']);
    $agentcall_manual = $astDB->escape($_REQUEST['agentcall_manual']);
    $scheduled_callbacks = $astDB->escape($_REQUEST['scheduled_callbacks']);
    $agentonly_callbacks = $astDB->escape($_REQUEST['agentonly_callbacks']);
    $agent_lead_search_override = $astDB->escape($_REQUEST['agent_lead_search_override']);
    $avatar = $astDB->escape($_REQUEST['avatar']);
    $location = $astDB->escape($_REQUEST['location_id']);    
    $log_user = $session_user;
	
    // Default Values
    $defActive = array("Y","N");
    $defmodify_same_user_level = array("Y","N");	

    // Error Checking
    if (empty($user) && empty($userid) || empty($session_user)) {
		$err_msg = error_handle("40002");
		$apiresults = array("code" => "40002", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for User ID.");
        
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user) || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $userid)){
		$err_msg = error_handle("41006", "user_id or user");
		$apiresults = array("code" => "41006", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Special characters found in user");
        
    } elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
		$err_msg = error_handle("41006", "pass");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in password");
		
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>|=+¬]/', $full_name)){
		$err_msg = error_handle("41006", "full_name");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in full_name");
		
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_login)){
		$err_msg = error_handle("41002", "phone_login");
		$apiresults = array("code" => "41002", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in phone_login");
		
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_pass)){
		$err_msg = error_handle("41004", "phone_pass");
		$apiresults = array("code" => "41004", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Special characters found in phone_pass");
		
	} elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg = error_handle("41006", "active");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
		
	} elseif (!in_array($modify_same_user_level,$defmodify_same_user_level) && $modify_same_user_level != null) {
		$err_msg = error_handle("41006", "modify_same_user_level");
		$apiresults = array("code" => "41006", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Default value for modify_same_user_level is Y or N only.");
		
	} elseif ($user_level < 1 && $user_level!=null || $user_level > 9 && $user_level!= null) {
		$err_msg = error_handle("41002", "user_level");
		$apiresults = array("code" => "41002", "result" => $err_msg);
		//$apiresults = array("result" => "Error: User Level Value should be in between 1 and 9");
		
	} elseif ($VARSERVTYPE == "gofree" && $hotkeys_active != null) {
		$err_msg = error_handle("10004", "hotkeys_active. Hotkeys is disabled");
		$apiresults = array("code" => "10004", "result" => $err_msg);
		//$apiresults = array("result" => "Error: hotkeys is disabled");
		
	} elseif ($modify_same_user_level != null) {
		$err_msg = error_handle("10004", "modify_same_user_level. modify_same_user_level is disabled");
		$apiresults = array("code" => "10004", "result" => $err_msg);
		//$apiresults = array("result" => "Error: modify_same_user_level is disabled");
		
	} elseif (empty($session_user)) {
		$err_msg = error_handle("40002");
		$apiresults = array("code" => "40002", "result" => $err_msg);
		
	} elseif (!empty($location)){
		$result_location = go_check_location($location, $user_group);
		if($result_location < 1){
			$err_msg = error_handle("41006", "location. User group does not exist in the location selected.");
			$apiresults = array("code" => "41006", "result" => $err_msg);
		}
		$updateUserGoArray = array_merge($updateUserGoArray, array("location_id" => $location));
		$insertUserGoArray = array_merge($insertUserGoArray, array("location_id" => $location));
		/*$location_SQL = ", `location_id` = '$location' ";
		$location_COL = ", location_id";
		$location_VAL = ", '$location'";*/						
	} 
	
	$groupId = go_get_groupid($session_user, $astDB);
	
	if (!checkIfTenant($groupId, $astDB)) {
		$astDB->where("user_id", $userid);
	} else {
		$astDB->where("user_id", $userid);
		$astDB->where("user	_group", $groupId);
		//$ulUser = "AND user='$user' AND user_group='$groupId'";
	}
		
	/*if ($userid != NULL) {
		$astDB->where("user_id", $userid);
		$fetch_userInfo = $astDB->getOne("vicidial_users", "user");
		$user = $fetch_userInfo["user"];
	}*/

	if ($active == "N") { $goactive = "0"; } else { $goactive = "1"; }
			
	$updateUserGoArray = array("name" => $user, "fullname" => $full_name, "phone" => $phone_login, "email" => $email, "avatar" => $avatar, "user_group" => $user_group, "role" => $user_level, "status" => $goactive);
	$insertUserGoArray = array("userid" => $userid, "name" => $user, "fullname" => $full_name, "phone" => $phone_login, "avatar" => $avatar, "user_group" => $user_group, "role" => $user_level, "status" => $goactive);
	
	$astDB->where("user_id", $userid);
	$astDB->where("user", DEFAULT_USERS, "NOT IN");
	$fresults = $astDB->getOne("vicidial_users", "user_id,user,full_name,email,user_group,active,user_level,phone_login,phone_pass,voicemail_id,hotkeys_active,vdc_agent_api_access,agent_choose_ingroups,vicidial_recording_override,vicidial_transfers,closer_default_blended,agentcall_manual,scheduled_callbacks,agentonly_callbacks,agent_lead_search_override");
	
	$countCheckResult = $astDB->count; 
	/*$queryUserCheck = " SELECT * FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ulUser ORDER BY user ASC LIMIT 1 "; */
	
	if($countCheckResult > 0) {
		//foreach ($rsltvCheck as $fresults){
		$dataUser = $fresults['user'];
		$data_phone_login = $fresults['phone_login'];
		$dataUserLevel = $fresults['user_level'];
		$dataFullname = $fresults['full_name'];
		$dataUserGroup = $fresults['user_group'];
		$dataActive = $fresults['active'];
		$dataEmail = $fresults['email'];
		$dataHotkeys = $fresults['hotkeys_active'];
		$data_modify_same_user_level = $fresults['modify_same_user_level'];
		$dataVoicemail = $fresults['voicemail'];
		$data_vdc_agent_api_access = $fresults['vdc_agent_api_access'];
		$data_agent_choose_ingroups = $fresults['agent_choose_ingroups'];
		$data_vicidial_recording_override = $fresults['vicidial_recording_override'];
		$data_vicidial_transfers = $fresults['vicidial_transfers'];
		$data_closer_default_blended = $fresults['closer_default_blended'];
		$data_agentcall_manual = $fresults['agentcall_manual'];
		$data_scheduled_callbacks = $fresults['scheduled_callbacks'];
		$data_agentonly_callbacks = $fresults['agentonly_callbacks'];
		$data_agent_lead_search_override = $fresults['agent_lead_search_override'];
		//}
		
		if(empty($phone_login))
			$phone_login = $data_phone_login;
		if(empty($user_level))
			$user_level = $dataUserLevel;
		if(empty($full_name))
			$full_name = $dataFullname;
		if(empty($user_group))
			$user_group = $dataUserGroup;
		if(empty($active))
			$active = $dataActive;
		if(empty($email))
			$email = $dataEmail;
		if(empty($hotkeys_active))
			$hotkeys_active = $dataHotkeys;
		if(empty($modify_same_user_level))
			$modify_same_user_level = $data_modify_same_user_level;
		if(empty($voicemail))
			$voicemail = $dataVoicemail;
		if(empty($vdc_agent_api_access))
			$vdc_agent_api_access = $data_vdc_agent_api_access;
		if(empty($agent_choose_ingroups))
			$agent_choose_ingroups = $data_agent_choose_ingroups;
		if(empty($vicidial_recording_override))
			$vicidial_recording_override = $data_vicidial_recording_override;
		if(empty($vicidial_transfers))
			$vicidial_transfers = $data_vicidial_transfers;
		if(empty($closer_default_blended))
			$closer_default_blended = $data_closer_default_blended;
		if(empty($agentcall_manual))
			$agentcall_manual = $data_agentcall_manual;
		if(empty($scheduled_callbacks))
			$scheduled_callbacks = $data_scheduled_callbacks;
		if(empty($agentonly_callbacks))
			$agentonly_callbacks = $data_agentonly_callbacks;
		if(empty($agent_lead_search_override))
			$agent_lead_search_override = $data_agent_lead_search_override;
			
		if( $modify_same_user_level == "Y") {
			$modify_same_user_level = 0;
		} else {
			$modify_same_user_level = 1;
		}
			
		//# Check User Group if valid
		if($user_group != null){
			$astDB->where("user_group", $user_group);
			$astDB->getOne("vicidial_user_groups", "user_group");
			$countResult = $astDB->count; 
			/*$query = "SELECT user_group FROM vicidial_user_groups WHERE user_group = '$user_group' ORDER BY user_group LIMIT 1;";*/
		}else{
			$err_msg = error_handle("41004", "user_group. Doesn't exist");
			$apiresults = array("code" => "41004", "result" => $err_msg);
		}

		if($countResult <= 0 && $user_group!=null) {
			$err_msg = error_handle("41004", "user_group. Doesn't exist");
			$apiresults = array("code" => "41004", "result" => $err_msg);
		} else {
			$update_array = array("full_name" => $full_name, "user_group" => $user_group, "active" => $active, "hotkeys_active" => $hotkeys_active, "user_level" => $user_level, "vdc_agent_api_access" => $vdc_agent_api_access, "agent_choose_ingroups" => $agent_choose_ingroups, "vicidial_recording_override" => $vicidial_recording_override, "vicidial_transfers" => $vicidial_transfers, "closer_default_blended" => $closer_default_blended, "agentcall_manual" => $agentcall_manual, "scheduled_callbacks" => $scheduled_callbacks, "agentonly_callbacks" => $agentonly_callbacks, "modify_same_user_level" => $modify_same_user_level, "email" => $email, "agent_lead_search_override" => $agent_lead_search_override);
				
			if  ($pass != NULL){
				$fetch_passhash = $astDB->getOne("system_settings", "pass_hash_enabled,pass_key,pass_cost");
				$pass_hash_enabled = $fetch_passhash["pass_hash_enabled"];
				$pass_key = $fetch_passhash["pass_key"];
				$pass_cost = $fetch_passhash["pass_cost"];
				
				// Password Encryption
				//$cwd = $_SERVER['DOCUMENT_ROOT'];
				//$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
				//$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
				$pass_hash = encrypt_passwd($pass, $pass_cost, $pass_key);
				
				/*
				$query_passhash = "select pass_hash_enabled from system_settings";
				*/
				// if($fetch_pass_hash_enabled['pass_hash_enabled'] == "1"){

				if ($pass_hash_enabled > 0){
					$phones_array = array("conf_secret" => $pass, "pass" => "");
					$update_array = array_merge($update_array, array("pass_hash" => $pass_hash, "pass" => "", "phone_pass" => $pass_hash));
					/*
					$pass_query = "`pass_hash` = '$pass_hash', `pass` = '', `phone_pass` = '$pass_hash', ";
					$phonePassQuery = "`pass` = ''";
					*/
					
					$goDB->where("setting", "GO_agent_domain");
					$fetch_value = $astDB->getOne("settings", "value");
					$value = $fetch_value["value"];
					//$queryg = "SELECT value FROM settings WHERE setting='GO_agent_domain';";
					
					$realm = (!is_null($value) || $value !== '') ? $value : 'goautodial.com';
					
					$ha1 = md5("{$phone_login}:{$realm}:{$phone_pass}");
					$ha1b = md5("{$phone_login}@{$realm}:{$realm}:{$phone_pass}");

					$subscriber_array = array("password" => "", "ha1" => $ha1, "ha1b" => $ha1b);
					//$kamPassQuery = "SET `password` = '', `ha1` = '$ha1', `ha1b` = '$ha1b'";
				}else{
					$phones_array = array("conf_secret" => $pass, "pass" => $pass);
					$update_array = array_merge($update_array, array("pass_hash" => $pass_hash, "pass" => $pass, "phone_pass" => $phone_pass));
					/*
					$pass_query = "`pass_hash` = '$pass_hash', `pass` = '$pass', `phone_pass` = '$phone_pass', ";
					$phonePassQuery = "`pass` = '$pass'";
					*/
					$subscriber_array = array("password" => $pass, "ha1" => "", "ha1b" => "");
					//$kamPassQuery = "SET `password` = '$pass', `ha1` = '', `ha1b` = ''";
				}					
				$astDB->where ("extension", $phone_login);
				$queryUpdatePhones = $astDB->update ('phones', $phones_array);
				//$queryUpdatePhones = "UPDATE `phones` SET `conf_secret` = '$pass', $phonePassQuery WHERE `extension` = '$phone_login'";

				$kamDB->where ("username", $phone_login);
				$kamailioq = $kamDB->update ('subscriber', $subscriber_array);
				//$kamailioq = "UPDATE `subscriber` $kamPassQuery WHERE `username` = '$phone_login'";
				}
				
				if($phone_login != NULL){
					$update_array = array_merge($update_array, array("phone_login" => $phone_login));
					//$phonelogin_query = "`phone_login` = '$phone_login', ";
				}

				if($voicemail != null){
					$astDB->where("voicemail_id", $voicemail);
				}

				$astDB->where ("user", $user);
				$queryUpdateUser = $astDB->update('vicidial_users', $update_array);
				/*$queryUpdateUser = "UPDATE `vicidial_users`
									SET $pass_query `full_name` = '$full_name',  $phonelogin_query  `user_group` = '$user_group',  `active` = '$active',
										`hotkeys_active` = '$hotkeys_active',  `user_level` = '$user_level', `vdc_agent_api_access` = '$vdc_agent_api_access', 
										`agent_choose_ingroups` = '$agent_choose_ingroups', `vicidial_recording_override` = '$vicidial_recording_override', 
										`vicidial_transfers` = '$vicidial_transfers', `closer_default_blended` = '$closer_default_blended', `agentcall_manual` = '$agentcall_manual', 
										`scheduled_callbacks` = '$scheduled_callbacks', `agentonly_callbacks` = '$agentonly_callbacks', 
										`modify_same_user_level` = '$modify_same_user_level', `email` = '$email', `agent_lead_search_override` = '$agent_lead_search_override'  $voicemail_query 
									WHERE `user` = '$user'";*/

				$goDB->where("name", $user);
				$fetch_userIDGo = $goDB->getOne("users", "userid");
				$countResultGo = $goDB->count;
				//$queryUserIDGo = "SELECT userid FROM users WHERE userid='$userid'";
				
				if ($countResultGo > 0){
					$goDB->where("name", $user);
					$queryUpdateUserGo = $goDB->update('users', $updateUserGoArray);
					//$queryUpdateUserGo = "UPDATE users SET `name` = '$dataUser', `fullname` = '$full_name', `phone` = '$phone_login', `email` = '$email', `avatar` = '$avatar', `user_group` = '$user_group', `role` = '$user_level', `status` = '$goactive' $location_SQL WHERE name = '$user'";
				} else {
					$queryInsertUserGo = $goDB->insert('users', $insertUserGoArray); // insert record in goautodial.users
					//$queryUpdateUserGo = "INSERT INTO users (userid, name, fullname, phone, email, avatar, user_group, role, status $location_COL) VALUES ('$userid', '$user', '$full_name', '$phone_login', '$email', '$avatar', '$user_group', '$user_level', '$goactive' $location_VAL)";
				}

				$justgovoip_array = array("web_password" => $phone_pass);
				$goDB->where ("carrier_id", $user_group);
				$queryJSIUpdate = $goDB->update ('justgovoip_sippy_info', $justgovoip_array);
				/*
				$queryJSIUpdate = "UPDATE justgovoip_sippy_info SET web_password='$phone_pass' where carrier_id='$user_group'";
				*/
				
				$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified User: $user", $log_group, $queryUpdateUser);
				
				if($queryUpdateUser == false) {
					$err_msg = error_handle("10010");
					$apiresults = array("code" => "10010", "result" => $err_msg);
				} else {
					$apiresults = array("result" => "success");
				}
			}
		} else {
			$err_msg = error_handle("41004", "user or user_id. Doesn't exist");
			$apiresults = array("code" => "41004", "result" => $err_msg);
		}// end

?>
