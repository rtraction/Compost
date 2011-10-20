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

/**
 * Phptxtdb
 * --------
 * Phptxtdb is a database substitute, using text files for tables, and lines for
 * rows. Fields are separated by #'s. The first row in each file contains the
 * field definitions.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phptxtdb extends AbstractDb{
	var $_base_path = '';
	function Phptxtdb() {
		parent::AbstractDb();
		$CI =& get_instance();
		$this->_base_path = $CI->config->item('absolute_path');
	}

	function GetTable($table) {
		//open /database/compost/$table.txt
		//TODO: grab the location from config rather than hardcoding it in.
		$file = file($this->_base_path."database/compost/$table.txt");
		//put it into an associative array
		$columns = explode("#", $file[0]);
		$results = array();
		foreach($file as $key => $line) {
			if ($key > 0) {
				$fields = explode("#", $line);
				//don't return the dummy row of -1's!
				$negativefields = array();
				foreach ($fields as $field) {
					if ($field == '-1') {
						$negativefields[] = $field;
					}
				}
				if (count($negativefields) != count($fields)) {
					foreach ($columns as $index => $column) {
						$results[$key][trim($column)] = stripslashes(trim(str_replace("&hash;", "#", $fields[$index])));
					}
				}
			}
		}
		return $results;
	}

	function GetEmptyTable($table) {

		//open /database/Empty_Database/$table.txt
		//TODO: grab the location from config rather than hardcoding it in.
		$file = file($this->_base_path."database/Empty_Database/$table.txt");
		return $file;
	}

	function SetTable($table, $data) {

		//if we've just deleted the last item in the table, we have to make sure the column names stay
		//if data is empty, we need to grab the empty version of the table
		if (empty($data)) {
			$data = $this->GetEmptyTable($table);
			$results = $data;
		} else {
			//if $data[0] exists, we have to move everything up one
			//because we will use [0] for the column names
			if (isset($data[0])) {
				//move everything up one
				$newdata = array();
				$newdata[] = "";
				foreach ($data as $item) {
					$newdata[] = $item;
				}
				unset($newdata[0]);
				$data = $newdata;
			}
			$result = "";
			//columns first
			$totalcolumns = count($data[1]);
			foreach ($data[1] as $column => $whocares) {
				$result .= $column . "#";
			}
			$results[] = $result;
			//turn the data into lines for the file
			foreach ($data as $key => $row) {
				if ($key > 0) {
					$results[] = "";
					foreach ($row as $field) {	
						//need to escape any hashmarks so they don't interfere with the explode function;
						$results[$key] .= str_replace("\n", "", nl2br(str_replace("#", "&hash;", $field))) . "#";
					}
					if (count($row) < $totalcolumns) {
						//need to add enough #'s to fill out the remaining columns
						$difference = $totalcolumns - count($row);
						for($i = 0; $i < $difference; $i++) {
							$results[$key] .= '#';
						}
					}
				}
			}
		}

		//now we have an array of lines
		//open /database/compost/$table.txt for overwriting
		//TODO: grab the location from config rather than hardcoding it in.

		$file = fopen ($this->_base_path."database/compost/$table.txt", "w");

		//waiting until file can be locked for writing (1000 millisecond timeout)
		$startTime = microtime();
		do {
			$canWrite = flock($file, LOCK_EX);
			//if the lock was not obtained, sleep for 0-100 milliseconds, to avoid collision and CPI load
			if(!$canWrite) usleep(round(rand(0, 100)*1000));
		} while ((!$canWrite)and((microtime()-$startTime)<1000));
		//file was locked so we can now write
		if ($canWrite) {
			//write the array $data into the file
			foreach ($results as $key => $result) {
				//chop off the '#' if it is the last character
				if (substr($result, strlen($result)-1, 1) == "#") {
					$result = substr($result, 0, strlen($result)-1);
				}
				fwrite($file,addslashes(trim($result)));
				if ($key < (count($results)-1)) {
					fwrite($file, "\n");
				}
			}

			/* DEBUG: this write takes 10 seconds!
			*/
			//if ($table == "User") sleep(10);
			/*
			*/
		}
		fclose($file); //also unlocks the file
		return true;

	}

	function Insert($table, $data) {
		$thetable = $this->GetTable($table);
		//Get the next usable ID
		$biggest = 0;
		foreach ($thetable as $currtable) {
			if ($currtable[$table.'Id'] > $biggest) {
				$biggest = $currtable[$table.'Id'];
			}
		}
		$newid = $biggest+1;

		//add the new id to the data
		//and sort it so that id is at the top
		$newdata[$table.'Id'] = $newid;
		foreach($data as $key => $item) {
			$newdata[$key] = $item;
		}
		$data = $newdata;

		//add the new data to the table
		$thetable[] = $data;

		//write!
		$this->SetTable($table, $thetable);

		return $newid;
	}

	function Delete($table, $id) {
		$thetable = $this->GetTable($table);
		$newtable = array();
		foreach($thetable as $curr) {
			if ($curr[$table.'Id'] != $id) {
				$newtable[] = $curr;
			}
		}
		//only update if changes have occurred
		if ($thetable != $newtable) {
			return $this->SetTable($table, $newtable);
		} else {
			return false;
		}
	}

	function DeleteWhere($table, $wherefield, $wherevalue) {
		$thetable = $this->GetTable($table);
		$newtable = array();
		foreach ($thetable as $curr) {
			if ($curr[$wherefield] != $wherevalue) {
				$newtable[] = $curr;
			}
		}
		return $this->SetTable($table, $newtable);
	}

	function Update ($table, $data) {
		$thetable = $this->GetTable($table);
		$newtable = array();
		foreach($thetable as $curr) {
			if ($curr[$table."Id"] == $data[$table."Id"]) {
				foreach($curr as $column => $value) {
					if (isset($data[$column])) {
						$empty = false;
						if (empty($data[$column]) && $data[$column] != "0") {
							$empty = true;
						}
						if (!$empty && $value != $data[$column]) {
							$curr[$column] = $data[$column];
						}
					}
				}
			}
			$newtable[] = $curr;
		}
		return $this->SetTable($table, $newtable);
	}

	function Select ($table, $id) {
		//only returns one row
		$thetable = $this->GetTable($table);
		foreach ($thetable as $curr) {
			if ($curr[$table."Id"] == $id) {
				return $curr;
				break;
			}
		}
		return false;
	}

	function SelectWhere($table, $wherefield, $wherevalue) {
		//returns an array of rows
		$thetable = $this->GetTable($table);
		$results = array();
		foreach ($thetable as $curr) {
			if ($curr[$wherefield] == $wherevalue) {
				$results[] = $curr;
			}
		}
		return $results;
	}
}