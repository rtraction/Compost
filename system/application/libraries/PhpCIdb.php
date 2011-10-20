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

class PhpCIdb extends AbstractDb{
	/**
	 *
	 * @var CI_DB
	 */
	var $db;
	function PhpCIdb() {
		parent::AbstractDb();
		//load the database settings from the config file
		//uses $this->db for convenience
		$CI =& get_instance();
		$CI->load->database();
		$this->db = &$CI->db;
		$this->base_path = $CI->config->item('absolute_path');
	}

	function GetTable($table) {
		$query = $this->db->get($table);
		$key = $table.'Id';
		$results = array();
		foreach ($query->result() as $row)
		{
			$results[$row->$key] = (array)$row;
		}
		return $results;
	}

	function GetEmptyTable($table) {
		$file = file($this->base_path."database/Empty_Database/$table.txt");
		return $file;
	}

	function SetTable($table, $data) {
		$this->db->truncate($table);
		foreach($data as $d){
			$this->insert($table, $d);
		}
		return true;
	}

	function Insert($table, $data) {
		$this->db->insert($table, $data);
		$newId = $this->db->insert_id();
		return $newId;
	}

	function Delete($table, $id) {
		$this->db->where($table.'Id', $id);
		$this->db->delete($table);
		return true;
	}

	function DeleteWhere($table, $wherefield, $wherevalue) {
		$this->db->where($wherefield, $wherevalue);
		$this->db->delete($table);
		return true;
	}

	function Update ($table, $data) {
		$this->db->where($table.'Id', $data[$table.'Id']);
		$this->db->update($table, $data);
		return true;
	}

	function Select ($table, $id) {
		$this->db->where($table.'Id', $id);
		$query = $this->db->get($table);
		$row = $query->result();
		if($row){
			$row = $row[0];
		}
		return (array)$row;
	}

	function SelectWhere($table, $wherefield, $wherevalue) {
		$this->db->where($wherefield, $wherevalue);
		$query = $this->db->get($table);
		$results = array();
		foreach ($query->result() as $row)
		{
			$results[] = (array)$row;
		}
		return $results;
	}
}

/* End of file PhpCIdb.php */
/* Location: ./system/application/libraries/PhpCIdb.php */