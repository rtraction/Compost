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
 * Client - All pages for managing clients
 */
class Client extends Authenticated_Controller {

	/**
	 * /client/
	 * Calls /client/listing/
	 */
	function index() {
		$this->listing();
	}

	/**
	 * /client/listing/
	 * Lists all clients in the system.
	 */
	function listing() {
		if ($_SESSION['userrole'] != ADMIN_USER_ROLE) {
			redirect(base_url() . index_page() . 'admin');
		}

		//Highlighted Tab
		if ($this->uri->segment(3) == 'admin') {
			//Page Title
			$this->data['pagename'] = 'Dashboard';
			$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $this->data['pagename'];
			//highlighted tab
			$this->data['tab'] = 'dashboard';
			//Page Description
			$this->data['description'] = 'There are no designs in the system. All clients are shown below. Click on a client to view projects and users.';
		} else {
			//Page Title
			$this->data['pagename'] = 'Clients';
			$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $this->data['pagename'];
			//highlighted tab
			$this->data['tab'] = 'clients';
			//Page Description
			$this->data['description'] = 'All clients are shown below. Click on a client to view projects and users.';
		}

		//Sidebar Menu
		//$data['premenu'] = '<input id="search" value="Search" />';
		$this->data['menu'] = array();
		$this->data['menu'][] = array(
			'link' => 'client/create',
			'name' => 'Create a New Client',
			'help' => 'client',
		);
		//Page Data
		$this->load->model('Company_model');
		$this->data['clients'] = $this->Company_model->GetListCompanies();
		if (count($this->data['clients']) <= 1) {
			$this->data['description'] = 'There are currently no clients in the system. <a href="' . base_url() . index_page() . 'client/create">Click here to create a new client</a>.';
		}
		foreach ($this->data['clients'] as $key => $client) {
			if ($client['CompanyId'] == '-1') {
				unset($this->data['clients'][$key]);
				break;
			}
		}

		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('clientlist_view', $this->data);
	}

	/**
	 * /client/create/
	 * Displays the "create a client" form.
	 */
	function create() {
		if ($_SESSION['userrole'] != ADMIN_USER_ROLE) {
			redirect(base_url() . index_page() . 'admin');
		}
		//Page Title
		$this->data['pagename'] = 'Create Client';
		$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $this->data['pagename'];
		//Highlighted Tab
		$this->data['tab'] = 'clients';
		//Page Description
		$this->data['description'] = 'Use the form below to create a new client.';
		//Sidebar Menu
		//Page Data
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('editclient_view', $this->data);
	}

	/**
	 * /client/edit/$CompanyId/
	 * Parameter: $CompanyId - the ID of the client to edit
	 * Shows the 'edit client' form, prepopulated with this particular client's information.
	 */
	function edit($CompanyId) {
		if ($_SESSION['userrole'] != ADMIN_USER_ROLE) {
			redirect(base_url() . index_page() . 'admin');
		}
		//Company Info
		$this->load->model('Company_model');
		$this->data['client']['CompanyName'] = $this->Company_model->GetCompany($CompanyId);
		//Page Title
		$this->data['pagename'] = $this->data['client']['CompanyName'];
		if (file_exists(APPPATH . '../../images/clients/' . $this->data['client']['CompanyName'])) {
			$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $this->data['client']['CompanyName'].'" />' . $this->data['pagename'];
		} else {
			$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $this->data['pagename'];
		}
		//Highlighted Tab
		$this->data['tab'] = 'clients';
		//Page Description
		$this->data['description'] = "Use this page to edit a client's name and logo.";
		//Sidebar Menu
		//Page Data
		$this->data['client']['CompanyId'] = $CompanyId;
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('editclient_view', $this->data);
	}

	/**
	 * /client/open/$CompanyId/
	 * Parameter: $CompanyId - The ID of the company to show
	 * Shows all projects associated with this client.
	 */
	function open($CompanyId) {
		if ($_SESSION['userrole'] != $CompanyId && $_SESSION['userrole'] != ADMIN_USER_ROLE) {
			//not allowed here, so show our company instead
			redirect(base_url() . index_page() . 'admin');
		}
		//Get Company Info
		$this->load->model('Company_model');
		$CompanyName = $this->Company_model->GetCompany($CompanyId);
		$users = $this->Company_model->GetUsers($CompanyId);
		//Page Title
		$data['pagename'] = $CompanyName . ' Projects';
		if (file_exists(APPPATH . '../../images/clients/' . $CompanyName)) {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $CompanyName . '" />' . $data['pagename'];
		} else {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $data['pagename'];
		}
		//Highlighted Tab
		if ($CompanyId == '-1') {
			$data['tab'] = 'archive';
		} else if ($_SESSION['userrole'] == ADMIN_USER_ROLE) {
			$data['tab'] = 'projects';
		} else {
			$data['tab'] = 'dashboard';
		}
		//Page Description
		$projects = $this->Company_model->GetProjects($CompanyId);
		$data['projects'] = array();
		if ($_SESSION['userrole'] != ADMIN_USER_ROLE) {
			//get the list of projects we have permissions for
			$this->load->model('Projects_model');
			$PermissionsTable = $this->compostdb->GetTable("Permission");
			foreach ($projects as $project) {
				//only add the ones we are allowed to view
				if ($this->Projects_model->GetPermission($_SESSION['userid'], $project['ProjectId'], $PermissionsTable)) {
					$data['projects'][] = $project;
				}
			}
		} else {
			//we're admin, so add all projects
			$data['projects'] = $projects;
		}
		if (count($data['projects']) == 0) {
			$data['description'] = 'There are no available projects associated with ' . $CompanyName . ' for you yet.';
			if ($_SESSION['userrole'] == ADMIN_USER_ROLE) {
				$data['description'] = 'There are no projects associated with ' . $CompanyName . ' yet. <a href="' . base_url() . index_page() . 'project/create/' . $CompanyId . '">Click here</a> to create one';
			}
		} else {
			$data['description'] = 'All projects for <em>' . $CompanyName . '</em> are shown below.';
		}

		//Sidebar Menu
		if ($CompanyId != '-1' && $_SESSION['userrole'] == ADMIN_USER_ROLE) {
			$data['menu'] = array();
			if (count($users) > 0) {
				$data['menu'][] = array(
					'link' => 'project/create/' . $CompanyId,
					'name' => 'Create a New Project',
					'help' => 'project',
				);
			} else {
				//You have to create a user before creating a project
				$data['description'] = 'There are no projects associated with ' . $CompanyName . ' yet. To create a project, you must first <a href="' . base_url() . index_page() . 'users/create/' . $CompanyId . '">create a user</a> for ' . $CompanyName . '.';
			}
			$data['menu'][] = array(
				'link' => 'users/listing/' . $CompanyId,
				'name' => 'Manage Users',
				'help' => 'users',
			);
			$data['menu'][] = array(
				'link' => 'client/edit/' . $CompanyId,
				'name' => 'Edit Client',
				'help' => 'client',
			);
			$data['menu'][] = array(
				'link' => 'client/delete/' . $CompanyId,
				'name' => 'Delete this Client',
			);
		}
		//Page Data
		$CompsTable = $this->compostdb->GetTable("Comp");
		$RevisionsTable = $this->compostdb->GetTable("Revision");
		foreach ($data['projects'] as $key => $project) {
			//change the revisionurl to include '_thumb'
			$comp = $this->Comp_model->GetLatestComp($project['ProjectId'], $RevisionsTable, $CompsTable);
			if ($comp) {
				$url = explode('.', $comp);
				$data['projects'][$key]['RevisionUrl'] = '/images/comps/' . $url[0] . '_thumb.' . $url[1];
			} else {
				$thumbnail = '/images/comps/nopic.jpg';
				//if (file_exists(APPPATH.'../../images/clients/'.$CompanyName.'.jpg')) {
				if ($this->_url_exists(base_url() . 'images/clients/' . $CompanyName)) {
					$thumbnail = '/images/clients/' . $CompanyName;
				}

				$data['projects'][$key]['RevisionUrl'] = $thumbnail;
			}
		}

		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('projectslist_view', $data);
	}

	function _url_exists($url) {
		if (strstr($url, "http://"))
			$url = str_replace("http://", "", $url);
		$fp = @fsockopen($url, 80);
		if ($fp === false)
			return false;
		return true;
	}

	/**
	 * /client/save/[$CompanyId]
	 * Parameter $CompanyId (optional) - the ID of the company to update
	 * Expected: $_POST
	 * Updates (if $CompanyId is set) or creates a new client.
	 */
	function save($CompanyId = null) {
		if ($_SESSION['userrole'] != ADMIN_USER_ROLE) {
			redirect(base_url() . index_page() . 'admin');
		}
		//No submit?
		if (!isset($_POST['submit'])) {
			$this->open($CompanyId);
		} else {
			$errors = array();
			if ($CompanyId) {
				//Edit Client
				//validate
				if ($this->_validate($_POST, $errors)) {
					//Validation Passed
					$this->load->model('Company_model');
					if ($this->Company_model->SetCompany($CompanyId, $_POST['ClientName'])) {
						//Success!
						//the upload location
						$target_path = $complocation = 'images/clients/' . $_POST['ClientName'];
						$thumb = 'images/clients/' . $_POST['ClientName'] . '_thumb';
						if (isset($_FILES['ClientLogo']) && strlen($_FILES['ClientLogo']['name']) > 0) {
							move_uploaded_file($_FILES['ClientLogo']['tmp_name'], $target_path);
							$config['image_library'] = 'gd2';
							$config['source_image'] = $target_path;
							$config['create_thumb'] = TRUE;
							$config['maintain_ratio'] = TRUE;
							$config['width'] = 85;
							$config['height'] = 85;
							$this->load->library('image_lib', $config);
							$this->image_lib->resize();
							unlink($target_path);
							rename($thumb, $target_path);
						} else {
							//we've changed the name but not the logo
							//rename the oldname.jpg to newname.jpg to keep the logo!
							if ($_POST['oldname'] != $_POST['ClientName']) {
								//only if the file exists
								rename('images/clients/' . $_POST['oldname'], $target_path);
							}
						}

						

						redirect(base_url() . index_page() . 'client/open/' . $CompanyId);
					} else {
						//Database error
						$this->data['message'] = '<span class="red">There was an error updating to the database.</span>';
						$this->edit($CompanyId);
					}
				} else {
					//Validation Failed
					$this->data['message'] = '<span class="red">';
					if ($errors['ClientName']) {
						$this->data['message'] .= 'Please enter a client name to continue. ';
					}
					if ($errors['ClientLogo']) {
						$this->data['message'] .= 'Make sure you choose a valid image for the logo.' . get_file_upload_error($_FILES['ClientLogo']['error']);
					}
					$this->data['message'] .= '</span>';
					$this->edit($CompanyId);
				}
			} else {
				//Create Client
				//validate

				if ($this->_validate($_POST, $errors)) {
					//Validation Passed
					$this->load->model('Company_model');
					$ClientId = $this->Company_model->NewCompany($_POST['ClientName']);
					if ($ClientId) {
						//Create Success
						if (isset($_FILES['ClientLogo'])) {
							//upload the logo
							$target_path = $complocation = 'images/clients/' . $_POST['ClientName'] . "";
							$thumb = $complocation = 'images/clients/' . $_POST['ClientName'] .'_thumb'. "";
							//rename it to be the client's name
							move_uploaded_file($_FILES['ClientLogo']['tmp_name'], $target_path);
							$config['image_library'] = 'gd2';
							$config['source_image'] = $target_path;
							$config['create_thumb'] = TRUE;
							$config['maintain_ratio'] = TRUE;
							$config['width'] = 85;
							$config['height'] = 85;
							$this->load->library('image_lib', $config);
							$this->image_lib->resize();
							unlink($target_path);
							rename($thumb, $target_path);
						}
						$this->open($ClientId);
					} else {
						$this->data['error'] = 'There was an error inserting to the database.';
						$this->create();
					}
				} else {
					//Validation Failed
					$this->data['error'] = 'Please enter a client name to continue.';
					$this->create();
				}
			}
		}
	}

	/**
	 * /client/delete/$CompanyId/
	 * Parameter: The ID of the client to delete
	 * Displays the 'are you sure?' page for deleting this client.
	 */
	function delete($CompanyId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		//Are You Sure?
		//Get Company Info
		$this->load->model('Company_model');
		$company = $this->Company_model->GetCompany($CompanyId);
		//Page Title
		$this->data['pagename'] = 'Are You Sure?';
		if (file_exists(APPPATH . '../../images/clients/' . $company)) {
			$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $company . '" />' . $this->data['pagename'];
		} else {
			$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $this->data['pagename'];
		}
		//Highlighted Tab
		$this->data['tab'] = 'clients';
		//Page Description
		$this->data['description'] = 'Are you sure you want to delete the client <strong><em>' . $company . '</em></strong>?';
		//Sidebar Menu
		//Page Data
		$this->data['formaction'] = '/client/suredelete/' . $CompanyId;
		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('areyousure_view', $this->data);
	}

	/**
	 * /client/suredelete/$CompanyId/
	 * Parameter: $CompanyId - the ID of the client to delete
	 * Deletes a client from the database. All projects are moved to the Archive.
	 */
	function suredelete($CompanyId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		if (isset($_POST['sure'])) {
			//we're sure - delete!
			$this->load->model('Company_model');
			$this->Company_model->DelCompany($CompanyId);
		}
		redirect(base_url() . index_page() . 'client/listing');
	}

	/**
	 * _validate
	 * Parameters: $post - the $_POST variable
	 * 							&$errors - the error list to modify
	 * Description: Validates the client name and logo.
	 * Returns: True or False. Also modifies &$errors.
	 */
	function _validate($post, &$errors) {
		//validate client name here
		$errors['ClientName'] = false;
		$errors['ClientLogo'] = false;
		$valid = true;
		if (!isset($post['ClientName']) || strlen($post['ClientName']) == 0) {
			$errors['ClientName'] = true;
			$valid = false;
		}
		//validate file upload here
		if(!validate_image_upload('ClientLogo')){
			$errors['ClientLogo'] = true;
			$valid = false;
		}
		if ($valid) {
			return true;
		} else {
			return false;
		}
	}

}

/* End of file client.php */
/* Location: ./system/application/controllers/client.php */