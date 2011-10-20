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
 * Project - project management.
 */
class Project extends Authenticated_Controller {

	/**
	 * /project/
	 * same as /project/listing/
	 */
	function index() {
		$this->listing();
	}

	/**
	 * /project/listing/
	 * Shows all projects in the system. Only for the admin.
	 */
	function listing() {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		//Page Data
		$this->load->model('Projects_model');
		
		$data['projects'] = array();
		//open the project, company and revision files
		$Projects = $this->compostdb->GetTable('Project');
		$Companies = $this->compostdb->GetTable('Company');
		$Revisions = $this->compostdb->GetTable('Revision');
		$Comps = $this->compostdb->GetTable('Comp');
		foreach ($Projects as $key => $project) {
			$comp = $this->Comp_model->GetLatestComp($project['ProjectId'], $Revisions, $Comps);
			$company = $this->Projects_model->GetCompany($project['ProjectId'], $Projects, $Companies);

			if ($comp) {
				$url = explode('.',$comp);
				$project['RevisionUrl'] = '/images/comps/' . $url[0].'_thumb.'.$url[1];
			} else {
				$thumbnail = '/images/comps/nopic.jpg';
				if ($this->_url_exists(base_url().'images/clients/'.$company['CompanyName'])) {
					$thumbnail = '/images/clients/'.$company['CompanyName'];
				}
				$project['RevisionUrl'] = $thumbnail;
			}
			$project['CompanyName'] = $company['CompanyName'];
			if ($project['CompanyId'] != '-1') {
				$data['projects'][] = $project;
			}
		}
		//Page Title
		$data['pagename'] = 'All Projects';
		$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
		//Highlighted Tab
		$data['tab'] = 'projects';
		//sidebar menu
		$data['menu'] = array();
		$data['menu'][] = array(
				'link'	=> 'client/listing',
				'name'	=> 'Create a Project',
				'help'	=> 'project',
		);
		//Page Description
		if (count($data['projects']) > 0) {
			$data['description'] = 'Here are all of the projects in the system.';
		} else {
			$data['description'] = 'There currently are no projects in the system. <a href="'.base_url().index_page().'client/listing">Click here</a> to create one';
		}
		//Sidebar Menu

		//Load the View
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$this->load->view('projectslist_view', $data);
	}
	
	function _url_exists($url){
		if(strstr($url, "http://")) $url = str_replace("http://", "", $url);
		$fp = @fsockopen($url, 80);
		if($fp === false) return false;
		return true;
	}

	/**
	 * /project/options/$ProjectId/
	 * Parameter: $ProjectId - the ID of the project to edit
	 * Displays the Edit Project page for this project.
	 */
	function options($ProjectId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		global $data;
		//Get Project Info
		$this->load->model('Projects_model');
		$this->load->model('Company_model');
		$project = $this->Projects_model->GetProject($ProjectId);
		$CompanyName = $this->Company_model->GetCompany($project['CompanyId']);
		//Page Title
		$data['pagename'] = $project['ProjectName'] . ' Project Options';
		if (file_exists(APPPATH.'../../images/clients/'.$CompanyName)) {
			$data['pagename'] = '<img src="'.base_url().'images/clients/'.$CompanyName.'" />'.$data['pagename'];
		} else {
			$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'projects';
		//Page Description
		$data['description'] = 'Use this page to modify the project.';
		//Sidebar Menu
		$data['menu'] = array();
		$data['menu'][] = array(
				'link'	=> 'project/open/'.$ProjectId,
				'name'	=> 'Return to Designs'
		);
		$data['menu'][] = array(
				'link'	=> 'client/open/'.$project['CompanyId'],
				'name'	=> 'Return to Project List'
		);
		//Page Data
		$data['project'] = $project;
		$data['formdata']['ProjectName'] = $project['ProjectName'];
		$data['CompanyName'] = $CompanyName;
		$data['users'] = $this->Company_model->GetUsers($project['CompanyId']);
		$projectusers = $this->Projects_model->GetUsers($ProjectId);
		foreach ($projectusers as $projectuser) {
			$data['formdata']['permit'][$projectuser['UserId']] = $projectuser;
		}
		$data['CompanyId'] = $project['CompanyId'];
		$data['scripts'] = array('project_options.js');
		//Load the View
		$this->load->view('editproject_view', $data);
	}

	/**
	 * /project/create/[$CompanyId]/
	 * Parameter: $CompanyId (optional) - The ID of the client that this project is attached to.
	 * For admin only. Redirects to client listing if no company id is specified.
	 * Displays the 'Create Project' form for this client.
	 */
	function create($CompanyId=null) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		if ($CompanyId==null) {
			redirect(base_url().index_page().'client/listing');
		}
		global $data;
		//Get Company info
		$this->load->model('Company_model');
		$CompanyName = $this->Company_model->GetCompany($CompanyId);
		$users = $this->Company_model->GetUsers($CompanyId);
		//Page Title
		$data['pagename'] = 'New Project for ' . $CompanyName;
		if (file_exists(APPPATH.'../../images/clients/'.$CompanyName)) {
			$data['pagename'] = '<img src="'.base_url().'images/clients/'.$CompanyName.'" />'.$data['pagename'];
		} else {
			$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'projects';
		//Page Description
		$data['description'] = 'Enter the project name and select any users who will be allowed to view this project.';
		//Sidebar Menu
		$data['menu'] = array();
		$data['menu'][] = array(
				'link'	=> 'client/open/'.$CompanyId,
				'name'	=> 'Return to Project List'
		);
		//Page Data
		$data['CompanyName'] = $CompanyName;
		$data['users'] = $users;
		$data['formdata'] = array();
		$data['formdata']['permit'] = array();
		foreach ($users as $user) {
			$data['formdata']['permit'][$user['UserId']] = 1;
		}
		$data['CompanyId'] = $CompanyId;
		//Load the View
		$this->load->view('editproject_view', $data);
	}

	/**
	 * /project/open/$ProjectId/
	 * Parameter: $ProjectId - the ID of the project to view.
	 * Lists compositions associated with this project.
	 */
	function open($ProjectId) {
		//Get Project Info
		$this->load->model('Projects_model');
		$project = $this->Projects_model->GetProject($ProjectId);
		$company = $this->Projects_model->GetCompany($ProjectId);
		if (!$this->Projects_model->GetPermission($_SESSION['userid'], $project['ProjectId']) && $_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		//Page Title
		$data['pagename'] = $project['ProjectName'] . ' Designs';
		if (file_exists(APPPATH.'../../images/clients/'.$company['CompanyName'])) {
			$data['pagename'] = '<img src="'.base_url().'images/clients/'.$company['CompanyName'].'" />'.$data['pagename'];
		} else {
			$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
		}
		//Highlighted Tab
		if ($project['CompanyId'] != '-1') {
			$data['tab'] = 'projects';
		} else {
			$data['tab'] = 'archive';
		}
		//Page Description
		$data['description'] = 'Click on a design below to view, annotate and comment. The ratings shown are averages.';
		//Sidebar Menu
		$data['menu'] = array();
		if ($project['CompanyId'] != '-1') {
			if ($_SESSION['userrole'] == '-1') {
				$data['menu'][] = array(
						'link'	=> 'comps/create/'.$ProjectId,
						'name'	=> 'Create a Design',
						'help'	=> 'comp',
				);
				$data['menu'][] = array(
						'link'	=> 'project/options/'.$ProjectId,
						'name'	=> 'Project Options'
				);
			}
			$data['menu'][] = array(
					'link'	=> 'client/open/'.$project['CompanyId'],
					'name'	=>	'Return to Project List'
			);
		} else {
			$data['menu'][] = array(
					'link'	=> 'client/open/'.$project['CompanyId'],
					'name'	=>	'Return to Archived Projects'
			);
		}
		if ($_SESSION['userrole'] == '-1') {
			if ($project['CompanyId'] != '-1') {
				$data['menu'][] = array(
						'link'	=> 'project/delete/'.$ProjectId,
						'name'	=> 'Archive this Project',
						'help'	=> 'archive',
				);
			} else if ($project['ProjectId'] != '-1') {
				$data['menu'][] = array(
						'link'	=> 'project/delete/'.$ProjectId,
						'name'	=> 'Delete this Project',
						'help'	=> 'deleteproject',
				);
			}
		}
		//Page Data
		if ($this->uri->segment(4) == 'ad') {
			//Archive Done
			$data['message'] = 'The design was successfully archived.';
		} else if ($this->uri->segment(4) == 'af') {
			//Archive Failed
			$data['message'] = '<span class="red">The design was not archived.</span>';
		}
		$data['comps'] = $this->Projects_model->GetComps($ProjectId);
		if (count($data['comps']) == 0) {
			if ($ProjectId != '-1') {
				$data['description'] = 'There are no designs for this project yet.';
				if ($_SESSION['userrole'] == '-1') {
					$data['description'] .= ' <a href="'.base_url().index_page().'comps/create/'.$ProjectId.'">Click here to create a design</a>.';
				}
			} else {
				//there are no comps in the 'misc. comps' archive project
				$data['description'] = 'There are no archived designs here yet.';
			}
		} else {
			$CompTable = $this->compostdb->GetTable('Comp');
			$RevisionTable = $this->compostdb->GetTable('Revision');
			$RatingTable = $this->compostdb->GetTable('Rating');
			foreach ($data['comps'] as $key => $comp) {
				$data['comps'][$key] = $this->Comp_model->GetComp($comp['CompId'], $CompTable, $RevisionTable, $RatingTable);
				//change the revisionurl to include '_thumb'
				$url = explode('.',$data['comps'][$key]['RevisionUrl']);
				$data['comps'][$key]['RevisionUrl'] = $url[0].'_thumb.'.$url[1];
			}
		}
		//Load the View
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$this->load->view('complist_view', $data);
	}

	/**
	 * /project/save/[$ProjectId]/
	 * Parameter: $ProjectId (optional) - the ID of the project to update
	 * Expected: $_POST
	 * Updates (if $ProjectId is set) or creates a project.
	 */
	function save($ProjectId=null) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		if (!isset($_POST['submit'])) {
			if ($ProjectId) {
				$this->options($ProjectId);
			} else {
				redirect(base_url().index_page().'project/listing');
			}
		} else {
			global $data;
			if ($ProjectId) {
				//Update
				//validate
				if (!$this->_validate($_POST)) {
					//Validation Failed
					$data['error'] = 'Please make sure a project name is entered, and at least one user has access.';
					$data['formdata']['ProjectName'] = $_POST['ProjectName'];
					if (isset($_POST['permit'])) $data['formdata']['permit'] = $_POST['permit'];
					$this->options($ProjectId);
				} else {
					//Validation Success
					$this->load->model('Projects_model');
					//update the project name
					$this->Projects_model->SetProject($ProjectId, $_POST['ProjectName']);
					$users = array();
					foreach ($_POST['permit'] as $userid => $nothing) {
						$users[] = $userid;
					}
					//permit the users for this project
					$this->Projects_model->SetPermission($ProjectId, $users);
					redirect(base_url().index_page().'project/open/'.$ProjectId);
				}
			} else {
				//Create
				//validate
				if (!$this->_validate($_POST)) {
					//Validation Failed
					$data['error'] = 'Please make sure a project name is entered, and at least one user has access.';
					$data['formdata']['ProjectName'] = $_POST['ProjectName'];
					if (isset($_POST['permit'])) $data['formdata']['permit'] = $_POST['permit'];
					$this->create($_POST['CompanyId']);
				} else {
					//Validation Success
					$this->load->model('Projects_model');
					$newprojectid = $this->Projects_model->NewProject($_POST['CompanyId'], $_POST['ProjectName']);
					$users = array();
					foreach($_POST['permit'] as $userid => $nothing) {
						$users[] = $userid;
					}
					//permit the users for this project
					$this->Projects_model->SetPermission($newprojectid, $users);
					redirect(base_url().index_page().'project/open/'.$newprojectid);
				}
			}
		}
	}

	/**
	 * /project/delete/$ProjectId/
	 * Parameter: $ProjectId - the ID of the project to be deleted
	 * Displays the "are you sure?" page for deleting this project.
	 */
	function delete($ProjectId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		//Are You Sure?
		//Get Project Info
		$this->load->model('Projects_model');
		$project = $this->Projects_model->GetProject($ProjectId);
		$company = $this->Projects_model->GetCompany($ProjectId);
		//Page Title
		$data['pagename'] = 'Are You Sure?';
		if (file_exists(APPPATH.'../../images/clients/'.$company['CompanyName'])) {
			$data['pagename'] = '<img src="'.base_url().'images/clients/'.$company['CompanyName'].'" />'.$data['pagename'];
		} else {
			$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'projects';
		//Page Description
		if ($project['CompanyId'] != '-1') {
			$data['description'] = 'Are you sure you want to archive the project <strong><em>'.$project['ProjectName'].'</em></strong>? Any designs associated with this project will go into the archive. This cannot be undone.';
			$data['action'] = 'Archive';
		} else {
			$data['description'] = 'Are you sure you want to permanently delete the project <strong><em>'.$project['ProjectName'].'</em></strong>? Any designs associated with this project will be permenantly deleted. This cannot be undone.';
			$data['action'] = 'Delete';
		}
		//Sidebar Menu

		//Page Data
		$data['formaction'] = '/project/suredelete/'.$ProjectId;
		//Load the View
		$this->load->view('areyousure_view', $data);
	}

	/**
	 * /project/suredelete/$ProjectId/
	 * Parameter: $ProjectId - the ID of the project to be deleted
	 * Deletes a project.
	 */
	function suredelete($ProjectId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url().index_page().'admin');
		}
		$this->load->model('Projects_model');
		$project = $this->Projects_model->GetProject($ProjectId);
		if (isset($_POST['sure'])) {
			//we're sure - delete!
			//deletes it if it is already archived, otherwise this archives it
			$this->Projects_model->DelProject($ProjectId);
		}
		redirect(base_url().index_page().'client/open/'.$project['CompanyId']);
	}

	/**
	 * _validate
	 * Parameter: $post - the $_POST variable
	 * validates the project name and if we have permission to save or not
	 * Returns: True or False.
	 */
	function _validate($post) {
		if (strlen($post['ProjectName']) == 0 || !isset($post['permit'])) {
			return false;
		} else {
			return true;
		}
	}
}

/* End of file project.php */
/* Location: ./system/application/controllers/project.php */