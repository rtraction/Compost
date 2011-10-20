<?php
/*
 * This file is part of Compost.
 *
 * Compost is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Compost is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Compost.  If not, see <http://www.gnu.org/licenses/>.
 */

class Log_model extends Model {

	function Log_model()
	{
		parent::Model();
	}

	/**
	 * Logs an event. Be sure to include the LogTimestamp and LogIP.
	 * Returns: True or False
	 */
	function LogEvent($LogEvent, $LogType) {
		$newevent = array(
			'LogEvent' => $LogEvent,
			'LogType' => $LogType,
			'LogTimestamp' => time(),
			'LogIP' => $_SERVER['REMOTE_ADDR']
		);
		return $this->compostdb->Insert('Log', $newevent);
	}
	
	/**
	 * Get the most recent log items.
	 * Returns: Array
	 */
	function GetLog($LogType = null) {
		if ($LogType == null) {
			//Get all log items
			$items = $this->compostdb->GetTable('Log');
		} else {
			//Get log items of a particular type
			$items = $this->compostdb->SelectWhere('Log', 'LogType', $LogType);
		}
		$items = array_reverse($items);
		return $items;
		
	}
}
?>