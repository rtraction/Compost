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
 * AbstractDb
 * --------
 * AbstractDb is a base class used to make sure that all database classes are the same.
 * Since php4 does not support abstract classes, the functions trigger errors
 * instead if it being an abstract class with abstract functions. This also means that the class
 * only needs to be documented in one place and the rest of the classes inherit the
 * documentation.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @property db
 */
class AbstractDb {

	function AbstractDb(){}

	/**
	 * GetTable retrieves the entire table as an associative array.
	 * @param string $table
	 * @return array
	 */
	function GetTable($table){
		trigger_error('GetTable has not been implemented');
	}

	/**
	 * GetEmptyTable grabs an empty version of the table from the empty database
	 * stored in /database/Empty_Database/
	 * @param string $table
	 * @return array
	 */
	function GetEmptyTable($table){
		trigger_error('GetEmptyTable has not been implemented');
	}

	/**
	 * SetTable fully replaces a table with the contents of $data.
	 * @param string $table
	 * @param array $data
	 * @return array
	 */
	function SetTable($table, $data){
		trigger_error('SetTable has not been implemented');
	}

	/**
	 * Insert adds a row into the database
	 * @param string $table
	 * @param array $data
	 * @return int
	 */
	function Insert($table, $data){
		trigger_error('Insert has not been implemented');
	}

	/**
	 * Delete deletes a row from the database
	 * @param string $table
	 * @param int $id
	 * @return array
	 */
	function Delete($table, $id){
		trigger_error('Delete has not been implemented');
	}

	/**
	 * DeleteWhere deletes a row from the database given the name and value of a field.
	 * @param string $table
	 * @param string $wherefield
	 * @param string $wherevalue
	 * @return boolean
	 */
	function DeleteWhere($table, $wherefield, $wherevalue){
		trigger_error('DeleteWhere has not been implemented');
	}

	/**
	 * Update updates the data of a row in the database, just like in SQL.
	 * @param string $table
	 * @param array $data
	 * @return boolean
	 */
	function Update ($table, $data){
		trigger_error('Update has not been implemented');
	}

	/**
	 * Select retrieves a row based on its ID.
	 * @param string $table
	 * @param int $id
	 * @return array
	 */
	function Select ($table, $id){
		trigger_error('Select has not been implemented');
	}

	/**
	 * SelectWhere retrieves a row(s) based on a field and its value
	 * @param string $table
	 * @param string $wherefield
	 * @param mixed $wherevalue
	 */
	function SelectWhere($table, $wherefield, $wherevalue){
		trigger_error('SelectWhere has not been implemented');
	}
}