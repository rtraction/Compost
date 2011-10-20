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

class User_model extends Model {

	function User_model() {
		parent::Model();
	}

	/**
	 * Retrieves a user from the database
	 * @param int $UserId
	 * @param array $UserTable
	 * @return array
	 */
	function GetUser($UserId, $UserTable = null) {
		if ($UserTable == null) {
			return $this->compostdb->Select('User', $UserId);
		} else {
			foreach ($UserTable as $curr) {
				if ($curr['UserId'] == $UserId) {
					return $curr;
					break;
				}
			}
		}
	}

	/**
	 * Retrieves a user's id from the database
	 * @param string $UserName
	 * @return mixed int or false
	 */
	function GetUserId($UserName) {
		$user = $this->compostdb->SelectWhere('User', 'UserName', $UserName);
		if (count($user)) {
			return $user[0]['UserId'];
		} else {
			return false;
		}
	}

	/**
	 * Determines whether or not the credentials given are valid
	 * @param string $UserName
	 * @param string $UserPassword
	 * @return boolean
	 */
	function GetAuthentication($UserName, $UserPassword) {
		$users = $this->compostdb->SelectWhere('User', 'UserName', $UserName);
		if (isset($users[0])) {
			$user = $users[0];
			if ($user['UserPassword'] == md5($UserPassword)) {
				return $user['CompanyId'];
			} else {
				//Log it
				$this->Log_model->LogEvent("Login attempt failed ('" . $UserName . "' found)", "user");
				return false;
			}
		} else {
			$this->Log_model->LogEvent("Login attempt failed ('" . $UserName . "' not found)", "user");
			return false;
		}
	}

	/**
	 * Retrieves the company that the user is associated with
	 * @param int $UserId
	 * @return int
	 */
	function GetCompany($UserId) {
		$user = $this->compostdb->Select('User', $UserId);
		return $user['CompanyId'];
	}

	/**
	 * Get the company that the user is associated with
	 * @param int $UserId
	 * @param array $UserTable
	 * @param array $CompanyTable
	 * @return string the name of the company
	 */
	function GetCompanyName($UserId, $UserTable = null, $CompanyTable = null) {
		if ($UserTable == null) {
			$user = $this->compostdb->Select('User', $UserId);
		} else {
			foreach ($UserTable as $curr) {
				if ($curr['UserId'] == $UserId) {
					$user = $curr;
					break;
				}
			}
		}
		if ($CompanyTable == null) {
			$company = $this->compostdb->Select('Company', $user['CompanyId']);
		} else {
			foreach ($CompanyTable as $curr) {
				if ($curr['CompanyId'] == $user['CompanyId']) {
					$company = $curr;
					break;
				}
			}
		}
		return $company['CompanyName'];
	}

	/**
	 * Inserts a user into the database, unless UserId is set - then it updates the user in the database
	 * if UserPassword is not set and UserId is set, then don't change the password
	 *
	 * @param string $UserName
	 * @param string $UserPassword
	 * @param string $UserEmail
	 * @param int $CompanyId
	 * @param int $UserId
	 * @return mixed The new user's UserId, or true/false if updating
	 */
	function SetUser($UserName, $UserPassword=null, $UserEmail, $CompanyId, $UserId = null) {
		$newuser = array(
			'CompanyId' => $CompanyId,
			'UserName' => $UserName,
			'UserEmail' => $UserEmail
		);
		if ($UserPassword != null) {
			$newuser['UserPassword'] = md5($UserPassword);
		}
		if (isset($UserId)) {
			//update
			$newuser['UserId'] = $UserId;
			return $this->compostdb->Update('User', $newuser);
		} else {
			//insert
			return $this->compostdb->Insert('User', $newuser);
		}
	}

	/**
	 * If the username/email combo checks out, assigns a new randomly generated password to the user
	 * @param string $UserName
	 * @param string $UserEmail
	 * @return mixed string / boolean
	 */
	function SetPassword($UserName, $UserEmail) {
		//Get the user from the database
		$user = $this->compostdb->SelectWhere('User', 'UserName', $UserName);

		if (count($user) == 1 && $user[0]['UserEmail'] == $UserEmail) {
			//Generate a new password
			$newpassword = $this->_generatePassword();
			//save the new password
			$user[0]['UserPassword'] = md5($newpassword);
			if ($this->compostdb->Update('User', $user[0])) {
				return $newpassword;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Retrieves a list of all users in the database
	 * @return array of the user info
	 */
	function GetListUsers() {
		return $this->compostdb->GetTable('User');
	}

	/**
	 * Delete a user from the database
	 * @param int $UserId
	 * @return boolean
	 */
	function DelUser($UserId) {
		$this->compostdb->DeleteWhere('Permission', 'UserId', $UserId);
		return $this->compostdb->Delete('User', $UserId);
	}

	/**
	 * Retrieves all projects that a user is allowed to view
	 * @param int $UserId
	 * @return array of projects
	 */
	function GetProjects($UserId) {
		//Get the project id's from Permission using UserId
		$permissions = $this->compostdb->SelectWhere('Permission', 'UserId', $UserId);
		$projects = array();
		foreach ($permissions as $permission) {
			$projects[] = $this->compostdb->Select('Project', $permission['ProjectId']);
		}
		return $projects;
	}

	/**
	 * Generate a new random password
	 * @param int $length
	 * @param boolean $strength
	 * @access private
	 * @return string
	 */
	function _generatePassword($length=9, $strength=0) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= 'AEUY';
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

}

?>