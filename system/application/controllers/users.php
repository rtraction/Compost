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
 * Users - user management pages and functions
 */
class Users extends Admin_Controller {

	/**
	 * /users/
	 * same as /users/listing/
	 */
	function index() {
		$this->listing();
	}

	/**
	 * /users/create/[$CompanyId]/
	 * Parameter: $CompanyId (optional) - The ID of the client that the new user will be associated with.
	 * Shows the Create User form.
	 */
	function create($CompanyId=null) {

		$this->load->model('Company_model');
		$company = $this->Company_model->GetCompany($CompanyId);
		//Page Title
		$this->data['pagename'] = 'Register User';
		if (file_exists(APPPATH.'../../images/clients/'.$company)) {
			$this->data['pagename'] = '<img src="'.base_url().'images/clients/'.$company.'" />'.$this->data['pagename'];
		} else {
			$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
		}
		//Highlighted Tab
		$this->data['tab'] = 'users';
		//Page Description
		$this->data['description'] = '';
		//Sidebar Menu

		//Page Data
		if ($CompanyId) {
			$this->data['user']['CompanyId'] = $CompanyId;
			$this->data['projects'] = $this->Company_model->GetProjects($CompanyId);
		}

		$this->data['companies'] = $this->Company_model->GetListCompanies();
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$this->data['scripts'] = array('users_create.js');
		//Load the View
		$this->load->view('edituser_view', $this->data);
	}

	/**
	 * /users/listing/[$CompanyId]/
	 * Parameter: $CompanyId (optional) - the ID of the company whose users we will show
	 * Displays a company's users (if $CompanyId is set) or all users in the system.
	 */
	function listing($CompanyId=null) {
		$this->data['menu'] = array();
		$UserTable = $this->compostdb->GetTable('User');
		$CompanyTable = $this->compostdb->GetTable('Company');
		if(isset($CompanyId)) {
			//Listing users for a company
			$this->load->model('Company_model');
			$this->data['users'] = $this->Company_model->GetUsers($CompanyId);
			foreach($this->data['users'] as $key => $user) {
				$company = $this->User_model->GetCompanyName($user['UserId'], $UserTable, $CompanyTable);
				if (file_exists(APPPATH.'../../images/clients/'.$company)) {
					$this->data['users'][$key]['thumbnail'] = '/images/clients/'.$company;
				} else {
					$this->data['users'][$key]['thumbnail'] = '/images/clients/nopic.jpg';
				}
			}
			$company = $this->Company_model->GetCompany($CompanyId);
			$this->data['pagename'] = $company . ' Users';
			if (file_exists(APPPATH.'../../images/clients/'.$company)) {
				$this->data['pagename'] = '<img src="'.base_url().'images/clients/'.$company.'" />'.$this->data['pagename'];
			} else {
				$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
			}
			$this->data['menu'][] = array(
					'link'	=> 'client/open/'. $CompanyId,
					'name'	=> 'Return to '.$company
			);
			$this->data['menu'][] = array(
					'link'	=> 'users/create/'.$CompanyId,
					'name'	=> 'Register a New User',
					'help'	=> 'user',
			);
			//Page Description
			if (count($this->data['users'])) {
				$this->data['description'] = 'These users are associated with '.$company.'.';
			} else {
				$this->data['description'] = 'There are no users associated with '.$company.'. <a href="'.base_url().index_page().'users/create/'.$CompanyId.'">Click here to Register a New User</a>. '.$company.' must have at least one user associated with it before you can create a design.';
			}
		} else {
			//Listing all users in the system
			//Page Title
			$this->data['pagename'] = 'All Users';
			$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
			$this->data['menu'][] = array(
					'link'	=> 'users/create',
					'name'	=> 'Register a New User',
					'help'	=> 'user',
			);
			$this->data['users'] = $this->User_model->GetListUsers();
			foreach($this->data['users'] as $key => $user) {
				$company = $this->User_model->GetCompanyName($user['UserId'], $UserTable, $CompanyTable);
				if (file_exists(APPPATH.'../../images/clients/'.$company)) {
					$this->data['users'][$key]['thumbnail'] = '/images/clients/'.$company;
				} else {
					$this->data['users'][$key]['thumbnail'] = '/images/clients/nopic.jpg';
				}
			}
			//Page Description
			$this->data['description'] = 'These are all of the users in the system.';
		}
		//Highlighted Tab
		$this->data['tab'] = 'users';
		//Sidebar Menu
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		//$data['premenu'] = '<input id="search" value="Search" />';
		//Load the view
		$this->load->view('userlist_view', $this->data);
	}

	/**
	 * /users/open/$UserId/
	 * Parameter: $UserId - The ID of the user to edit
	 * Show the Edit User page.
	 */
	function open($UserId) {
		//User Info
		$this->load->model('Company_model');
		$this->load->model('Projects_model');
		$this->data['user'] = $this->User_model->GetUser($UserId);
		$this->data['company'] = $this->Company_model->GetCompany($this->data['user']['CompanyId']);
		$this->data['projects'] = $this->Company_model->GetProjects($this->data['user']['CompanyId']);
		$this->data['permissions'] = array();
		$Permission = $this->compostdb->GetTable('Permission');
		foreach($this->data['projects'] as $project) {
			if ($this->Projects_model->GetPermission($UserId, $project['ProjectId'], $Permission)) {
				$this->data['permissions'][] = $project['ProjectId'];
			}
		}
		$this->data['companies'] = $this->Company_model->GetListCompanies();
		//Page Title
		$this->data['pagename'] = $this->data['user']['UserName'];
		if (file_exists(APPPATH.'../../images/clients/'.$this->data['company'])) {
			$this->data['pagename'] = '<img src="'.base_url().'images/clients/'.$this->data['company'].'" />'.$this->data['pagename'];
		} else {
			$this->data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$this->data['pagename'];
		}
		//Highlighted Tab
		$this->data['tab'] = 'users';
		//Page Description
		$this->data['description'] = '';
		//Sidebar Menu
		//sidebar actions available to non-admin users
		if ($this->data['user']['CompanyId'] != '-1') {
			$this->data['menu'] = array();
			$this->data['menu'][] = array(
					'link'	=> 'users/listing/'.$this->data['user']['CompanyId'],
					'name'	=> $this->data['company'].' Users'
			);
		}
		if ($UserId != '-1') {
			$this->data['menu'][] = array(
					'link'	=> 'users/delete/'.$UserId,
					'name'	=> 'Delete this User',
					'help'	=> 'deleteuser',
			);
		}
		//Page Data
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('edituser_view', $this->data);
	}

	/**
	 * /users/save/[$UserId]/
	 * Parameter: $UserId (optional) - the ID of the user to update
	 * Expected: $_POST
	 * Updates (if $UserId is set) or creates a new user.
	 */
	function save($UserId = null) {
		//No submit?
		
		if (!isset($_POST['submit'])) {
			if ($UserId) {
				$this->open($UserId);
			} else {
				$this->create();
			}
		} else {
			//for errors
			
			//Updating or Inserting?
			if($UserId) {
				//Update
				//reset the errors
				unset($this->data['errors']);
				//validate
				$this->data['errors'] = $this->_validate($_POST);

				if (count($this->data['errors']) == 0) {
					//Validation Passed!
					$this->load->model('Projects_model');
					if (strlen($_POST['UserPassword']) > 0) {
						//update with new password
						$success = $this->User_model->SetUser($_POST['UserName'], $_POST['UserPassword'], $_POST['UserEmail'], $_POST['CompanyListing'], $UserId);
					} else {
						//update, no password change
						$success = $this->User_model->SetUser($_POST['UserName'], null, $_POST['UserEmail'], $_POST['CompanyListing'], $UserId);
					}
					if ($success) {
						//add the selected permissions
						$projectids = array();
						if (isset($_POST['permit'])) {
							foreach($_POST['permit'] as $ProjectId => $permit) {
								$projectids[] = $ProjectId;
							}
						}
						$this->Projects_model->SetPermit($projectids, $UserId);
						//successfully updated!
						$this->data['message'] = $_POST['UserName'] . ' has been successfully updated.';
					} else {
						//database error!
						$this->data['message'] = '<span class="red">There was an error updating the database.</span>';
					}
					$this->open($UserId);
				} else {
					//Validation Failed!
					$this->data['message'] = '<span class="red">Please fill in all required fields.</span>';
					$this->open($UserId);
				}
			} else {
				//Insert
				//validate
				//reset the errors
				$this->data['errors'] = array();
				// creating new user. Make sure username is unique
				$this->load->model("User_model");
			
				if ($this->User_model->GetUserId($_POST['UserName'])) {					
					$UserNameTaken = 1;
				}
				else{
					$UserNameTaken = 0;
					$this->data['errors'] = $this->_validate($_POST, false);
				}
				if (count($this->data['errors']) == 0 && $UserNameTaken !== 1) {
					//validation passed!
					$this->load->model('Projects_model');
					if ($newid = $this->User_model->SetUser($_POST['UserName'], $_POST['UserPassword'], $_POST['UserEmail'], $_POST['CompanyListing'])) {
						//add the selected permissions
						$projectids = array();
						if(!empty($_POST['permit'])){
							foreach($_POST['permit'] as $ProjectId => $permit) {
								$projectids[] = $ProjectId;
							}
							$this->Projects_model->SetPermit($projectids, $newid);
						}
						//success!
						redirect(base_url().index_page().'users/listing/'.$_POST['CompanyListing']);
					} else {
						//database error!
						$this->data['message'] = '<span class="red">There was an error updating the database.</span>';
					}
				} else {
					//validation failed!
					$this->data['message'] = '<span class="red">Please fill in all required fields.</span>';
					$this->data['user'] = $_POST;
					$this->data['user']['CompanyId'] = $_POST['CompanyListing'];
					if($UserNameTaken === 1){					
						$this->data['message'] = '<span class="red">Username ' . $this->data['user']['UserName'] . ' is not available.</span>';
						$this->data['user']['UserName'] = '';
					}
					$this->create();
				}

			}
		}
	}

	/**
	 * /users/delete/$UserId/
	 * Parameter: $UserId - the ID of the user to delete
	 * Shows the 'are you sure?' page for deleting this user
	 */
	function delete($UserId) {
		//Are You Sure?
		//Get User Info
		$user = $this->User_model->GetUser($UserId);
		//Page Title
		$data['pagename'] = 'Are You Sure?';
		$company = $this->User_model->GetCompanyName($UserId);
		if (file_exists(APPPATH.'../../images/clients/'.$company)) {
			$data['pagename'] = '<img src="'.base_url().'images/clients/'.$company.'" />'.$data['pagename'];
		} else {
			$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'users';
		//Page Description
		$data['description'] = 'Are you sure you want to delete the user <strong><em>'.$user['UserName'].'</em></strong>?';
		//Sidebar Menu

		//Page Data
		$data['formaction'] = '/users/suredelete/'.$UserId;
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('areyousure_view', $data);
	}

	/**
	 * /users/suredelete/$UserId/
	 * Parameter: $UserId - the ID of the user to delete
	 * Deletes a user
	 */
	function suredelete($UserId) {
		$user = $this->User_model->GetUser($UserId);
		if (isset($_POST['sure'])) {
			//we're sure - delete!
			$this->User_model->DelUser($UserId);
		}
		if(!empty($user['CompanyId'])){
			redirect(base_url().index_page().'users/listing/'.$user['CompanyId']);
		} else {
			redirect(base_url().index_page().'users/listing/');
		}
	}

	/**
	 * _validate
	 * Parameters: $post - the $_POST variable
	 * 							$optionalpassword (default: true) - whether or not the password field is optional
	 *								(it is for editing users, but not for creating users).
	 * Returns: array of errors.
	 */
	function _validate($post, $optionalpassword=true) {
		
		$errors = array();
		//validate username
		if (!isset($post['UserName']) || strlen($post['UserName']) < 1) {
			$errors['UserName'] = 1;
		}		
		//validate email
		if (!isset($post['UserEmail']) || strlen($post['UserEmail']) < 1) {
			$errors['UserEmail'] = 1;
		}
		//validate companylisting
		if (!isset($post['CompanyListing']) || strlen($post['CompanyListing']) < 1) {
			$errors['CompanyListing'] = 1;
		}
		//validate password
		if (isset($post['UserPassword']) || isset($post['RetypePassword'])) {
			if (strlen($post['UserPassword']) > 0 || strlen($post['RetypePassword']) > 0) {
				//we *are* updating a password
				if ($_POST['UserPassword'] != $_POST['RetypePassword']) {
					$errors['RetypePassword'] = 1;
				}
				if (strlen($_POST['UserPassword']) < 1) {
					$errors['UserPassword'] = 1;
				}
			}
		}
		if (!$optionalpassword) {
			if (!isset($post['UserPassword']) || strlen($post['UserPassword']) == 0) {
				$errors['UserPassword'] = 1;
			}
		}
		return $errors;
	}

}

/* End of file users.php */
/* Location: ./system/application/controllers/users.php */