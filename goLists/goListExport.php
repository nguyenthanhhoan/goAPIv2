 <?php
 /**
 * @file        goListExport.php
 * @brief       API to export list
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
*/
 
ini_set('memory_limit', '2048M');
	
	$listid = $astDB->escape($_REQUEST["list_id"]);
	
	if($listid != NULL){
		//$query = mysqli_query($link,"SELECT custom_fields_enabled FROM system_settings;");
		$fetch = $astDB->getOne('system_settings', 'custom_fields_enabled');
		$custom_fields_enabled = $fetch["custom_fields_enabled"];
		$added_custom_SQL  = "";
		$added_custom_SQL2 = "";
		$added_custom_SQL3  = "";
		$added_custom_SQL4 = "";	
	
		if ($custom_fields_enabled > 0) {
			$custom_table = "custom_".$listid;
			//$cllist = mysqli_query($link,"SELECT field_label FROM vicidial_lists_fields WHERE list_id ='$listid';");
			$cllist_query = "SHOW COLUMNS FROM $custom_table;";
			$cllist = $astDB->rawQuery($cllist_query);
			$clcount = $astDB->getRowCount();
			$header_columns = "";
			//$u=0;
			/*
			foreach($cllist->result() as $clrow){
				$column = $clrow->Field;
				if ($column!='lead_id')
					 $header_columns .= ",$column";
			}*/
			
			foreach ($cllist as $clrow) {
				if ($clrow != 'lead_id'){
					$header_columns .= ",".$clrow[0];
				}
			}
			
			if($clcount > 0){
				$added_custom_SQL  = ", $custom_table ct";
				$added_custom_SQL2 = "AND vl.lead_id=ct.lead_id";
				$added_custom_SQL3  = "$custom_table ct";
				$added_custom_SQL4 = "vl.lead_id=ct.lead_id";	
			}
		}
		
		if($added_custom_SQL3 !== "") {
			$stmt = "SELECT vl.lead_id AS lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner{$header_columns}
			FROM vicidial_list vl
			LEFT OUTER JOIN {$added_custom_SQL3} ON {$added_custom_SQL4}
			WHERE vl.list_id='{$listid}';";
		} else {
			$stmt = "SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list WHERE list_id='$listid'; ";
		}
		
		$dllist = $astDB->rawQuery($stmt);
		$header = $astDB->getFieldNames();
		//while($fetch_header = $astDB->getFieldNames()){
		//	$header[] = $fetch_header;
		//}
		/*
		$u=0;
		$count_header = count($header);
		while($fetch_row = mysqli_fetch_row($dllist)){
			$array_fetch = $fetch_row[0];
			$u = $u+1;
			while($u <= $count_header){
				$array_fetch .= "|".$fetch_row[$u];
				$u++;
			}
			$row[] = $array_fetch;
			$array_fetch = "";
			$u = 0;
		}*/
		
		$u=0;
		$x=0;
		$count_header = count($header);
		foreach ($dllist as $fetch_row) {
			$array_fetch = $fetch_row[$header[0]];
			$u = $u+1;
			while($u < $count_header){
				$array_fetch .= "|".utf8_encode($fetch_row[$header[$u]]);
				$u++;
			}
			$explode_array = explode("|",$array_fetch);
			$row[$x] = $explode_array;
			$array_fetch = "";
			$u = 0;
			$x++;
		}
		
		$apiresults = array("result" => "success", "header" => $header, "row" => $row, "query" => $stmt, "query_custom_list" => $custom_table);
	}else{
		$apiresults = array("result" => "Error: List ID not defined");
	}
	

?>