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
 * CompostDB
 * --------
 * CompostDB is a database class, it selects the appropriate database type
 * (text or as defined in the database config) and executes the commands as is appropriate.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompostDb extends AbstractDb{
	/**
	 *
	 * @var AbstracttDb
	 */
	var $db;
	function CompostDb() {
		parent::AbstractDb();
		$CI =& get_instance();
		if($CI->config->item('use_txt_db')){
			$CI->load->library('phptxtdb');
			$this->db = &$CI->phptxtdb;
		} else {
			$CI->load->library('phpcidb');
			$this->db = &$CI->phpcidb;
		}
	}

	function GetTable($table) {
		return $this->db->GetTable($table);
	}

	function GetEmptyTable($table) {
		return $this->db->GetEmptyTable($table);
	}

	function SetTable($table, $data) {
		return $this->db->SetTable($table, $data);
	}

	function Insert($table, $data) {
		return $this->db->Insert($table,$data);
	}

	function Delete($table, $id) {
		return $this->db->Delete($table, $id);
	}

	function DeleteWhere($table, $wherefield, $wherevalue) {
		return $this->db->DeleteWhere($table, $wherefield, $wherevalue);
	}

	function Update ($table, $data) {
		return $this->db->Update($table, $data);
	}

	function Select ($table, $id) {
		return $this->db->Select($table, $id);
	}

	function SelectWhere($table, $wherefield, $wherevalue) {
		return $this->db->SelectWhere($table, $wherefield, $wherevalue);
	}
}