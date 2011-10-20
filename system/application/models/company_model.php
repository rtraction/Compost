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

class Company_model extends Model {


	/**
	 * Creates a company with the specified name
	 * Returns: The new CompanyId
	 */
	function NewCompany($CompanyName) {
		$newcompany = array(
			'CompanyName' => $CompanyName
		);
		//create a user for this company
		$this->load->model('User_model');
		$CompanyId = $this->compostdb->Insert('Company', $newcompany);
		$this->User_model->SetUser(str_replace(" ", "", $CompanyName), substr(md5(time()), 0, 5), "", $CompanyId);
		return $CompanyId;
	}
	
	/**
	 * Deletes a company from the database. Moves all comps to the 'archive' company
	 * Returns: True or False
	 */
	function DelCompany($CompanyId) {
		//First, move all company projects to the 'archive' company
		$projects = $this->compostdb->SelectWhere('Project', 'CompanyId', $CompanyId);
		foreach($projects as $project) {
			$project['CompanyId'] = '-1';
			if (!$this->compostdb->Update('Project', $project)) { return false; }
		}
		//Delete the users associated with this company
		if ($this->compostdb->DeleteWhere('User', 'CompanyId', $CompanyId)) {
			//Delete the company
			return $this->compostdb->Delete('Company', $CompanyId);
		} else { return false; }	
	}
	
	/**
	 * Retrieves all projects assigned to a specific company
	 * Returns: Array of Associative Arrays
	 */
	function GetProjects($CompanyId,$Projects = null) {
		if($Projects == null) {
			return $this->compostdb->SelectWhere('Project', 'CompanyId', $CompanyId);
		} else {
			$results = array();
			foreach ($Projects as $curr) {
				if ($curr['CompanyId'] == $CompanyId) {
					$results[] = $curr;
				}
			}
			return $results;
		}
	}
	
	/**
	 * Retrieves the name of a company
	 * Returns: String
	 */
	function GetCompany($CompanyId) {
		$company = $this->compostdb->Select('Company', $CompanyId);
		if(empty($company)){
			return null;
		} else {
			return $company['CompanyName'];
		}
	}
	
	/**
	 * Retrieves a list of all companies
	 * Returns: Array of Associative Arrays
	 */
	function GetListCompanies() {
		return $this->compostdb->GetTable('Company');
	}
	
	/**
	 * Retrieves a list of all users associated with a specific company
	 * Returns: Associative Array
	 */
	function GetUsers($CompanyId) {
		return $this->compostdb->SelectWhere('User', 'CompanyId', $CompanyId);
	}
	
	/**
	 * Updates a the specified company's name.
	 * Returns: True or False
	 */
	function SetCompany($CompanyId, $CompanyName) {
		$newcompany = array(
			'CompanyId' 	=> $CompanyId,
			'CompanyName'	=> $CompanyName
		);
		return $this->compostdb->Update('Company', $newcompany);
	}

}
?>