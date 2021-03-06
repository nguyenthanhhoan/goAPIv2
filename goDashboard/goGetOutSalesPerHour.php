<?php
 /**
 * @file 		goGetOutSalesPerHour.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Warren Ipac Briones  <warren@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

    $groupId = go_get_groupid($session_user, $astDB);

    if (checkIfTenant($groupId, $goDB)) {
        $ul='';
    } else {
        if($groupId !== "ADMIN")
			$ul = "and vlog.user_group = '$groupId'";
		else
			$ul = "";
    }

	$NOW = date("Y-m-d");
	$query_date =  date('Y-m-d H');
	$status = "SALE";
	$date = "vlog.call_date BETWEEN '$query_date:00:00' AND '$query_date:59:59'";
	$query="select count(*) as getOutSalesPerHour FROM vicidial_log as vlog LEFT JOIN vicidial_list as vl ON vlog.lead_id=vl.lead_id WHERE vlog.status='SALE' $ul and $date";
    $fresults = $astDB->rawQuery($query);
    //$fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success", "query" => $date), $fresults );
	
	
?>
