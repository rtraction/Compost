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
 * Comps - everything to do with managing compositions.
 */
class Comps extends Authenticated_Controller {

	/**
	 * /comps/create/$ProjectId/
	 * Parameter: $ProjectId - The ID of the project that this comp will be associated with.
	 * Shows the 'create composition' form.
	 */
	function create($ProjectId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		$this->load->model('Projects_model');
		$company = $this->Projects_model->GetCompanyName($ProjectId);
		//Page Title
		$data['pagename'] = 'New Design';
		if (file_exists(APPPATH . '../../images/clients/' . $company)) {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $company . '" />' . $data['pagename'];
		} else {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'clients';
		//Page Description
		$data['description'] = 'Upload a design below to start collecting feedback.';
		//Sidebar Menu
		//Page Data
		$data['ProjectId'] = $ProjectId;
		$data['revision'] = false;
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$data['scripts'] = array('comps_revision.js');
		//Load the View
		$this->load->view('editcomp_view', $data);
	}

	/**
	 * /comps/open/$CompId/
	 * Parameter: $CompId - The ID of the composition to show.
	 * Displays a composition.
	 */
	function open($CompId) {
		//Comp Info
		$this->load->model('Annotation_model');
		$this->load->model('Projects_model');
		$this->load->model('Company_model');
		$comp = $this->Comp_model->GetComp($CompId);
		if (!isset($comp['CompName'])) {
			redirect(base_url() . index_page() . 'admin');
		}
		if (!$this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) && $_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}

		//Highlighted Tab
		$CompanyId = $this->Comp_model->GetCompany($CompId);
		if ($CompanyId != '-1') {
			$data['tab'] = 'projects';
		} else {
			$data['tab'] = 'archive';
		}
		//Page Description
		$data['description'] = '<em>' . $comp['CompDescription'] . '</em>';
		//Sidebar Menu
		$data['premenu'] = '<select id="revisionhistory"><option>- Revision History -</option>';
		//Get a list of revisions for this comp
		$revisions = $this->Comp_model->GetRevisions($CompId);
		rsort($revisions);
		foreach ($revisions as $revision) {
			$name = explode('_', $revision['RevisionUrl']);
			$name = $name[1];
			$displayname = $revision['RevisionDate'] . ' : ' . $name; //$comp['CompName'];
			if (strlen($displayname) > 10) {
				$displayname = substr($displayname, 0, 20) . '...';
			}
			$data['premenu'] .= '<option value="';
			$data['premenu'] .= $revision['RevisionId'];
			$data['premenu'] .= '">';
			$data['premenu'] .= $displayname;
			$data['premenu'] .= '</option>';
		}
		$data['premenu'] .= '</select>';
		if ($_SESSION['userrole'] == '-1' && $CompanyId == '-1') {
			$data['premenu'] .= '<br />&nbsp;<br /><select name="movecomp" id="movecomp">';
			$data['premenu'] .= '<option>- Assign To Project -</option>';
			$clients = $this->Company_model->GetListCompanies();
			$ProjectsTable = $this->compostdb->GetTable("Project");
			foreach ($clients as $client) {
				$projects = $this->Company_model->GetProjects($client['CompanyId'], $ProjectsTable);
				if (count($projects) > 0) {
					$data['premenu'] .= '<optgroup label="' . $client['CompanyName'] . '">';
					foreach ($projects as $project) {
						$data['premenu'] .= '<option value="' . $project['ProjectId'] . '">' . $project['ProjectName'] . '</option>';
					}
					$data['premenu'] .= '</optgroup>';
				}
			}
			$data['premenu'] .= '</select>';
		}

		$data['menu'] = array();

		$data['menu'][] = array(
			'link' => 'project/open/' . $comp['ProjectId'],
			'name' => 'Return to Design List'
		);
		if ($_SESSION['userrole'] == '-1' && $CompanyId != '-1') {
			$data['menu'][] = array(
				'link' => 'comps/revision/' . $comp['CompId'],
				'name' => 'Create a Revision',
				'help' => 'revision',
			);
			$data['menu'][] = array(
				'link' => 'comps/archive/' . $comp['CompId'],
				'name' => 'Archive this Design',
				'help' => 'archive',
			);
		}
		if ($_SESSION['userrole'] == '-1' && $CompanyId == '-1') {
			$data['menu'][] = array(
				'link' => 'comps/delete/' . $comp['CompId'],
				'name' => 'Delete Permanently',
				'help' => 'deletecomp',
			);
		}


		//Page Title
		$data['pagename'] = $comp['CompName'];
		$data['pagenameTag'] = $this->Comp_model->GetCompanyName($CompId);

		//Page Data
		$data['comp'] = $comp;
		$AnnotationTable = $this->compostdb->GetTable('Annotation');
		$ReadTable = $this->compostdb->GetTable('Read');
		$CommentTable = $this->compostdb->GetTable('Comment');
		$data['annotations'] = $this->Comp_model->GetAnnotations($CompId, $AnnotationTable);
		foreach ($data['annotations'] as $key => $annotation) {
			$data['annotations'][$key]['Unread'] = $this->Annotation_model->GetUnread($annotation['AnnotationId'], $_SESSION['userid'], $AnnotationTable, $ReadTable, $CommentTable);
			$data['annotations'][$key]['Comments'] = $this->Annotation_model->GetComments($annotation['AnnotationId'], $CommentTable);
		}
		if ($_SESSION['userrole'] != '-1') {
			//don't show the average rating, instead show YOUR rating
			$data['comp']['Rating'] = $this->Comp_model->GetUserRating($CompId, $_SESSION['userid']);
		}

		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$data['scripts'] = array('comps_open.php');
		//Load the View
		$this->load->view('comp_view', $data);
	}

	/**
	 * /comps/save/[$CompId]/
	 * Parameter: $CompId (optional) - The ID of the composition to update.
	 * Expected: $_POST
	 * Updates (if $CompId is set) or creates a new composition.
	 */
	function save($compId=null) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		//make sure we submitted the form
		if (!isset($_POST['submit'])) {
			if ($compId) {
				$this->open($compId);
			} else {
				redirect(base_url() . index_page() . 'project/listing');
			}
		}
		//validate
		$valid = true;
		$data['error'] = '';
		$compName = $_POST['CompName'];
		if (strlen($compName) == 0) {
			$valid = false;
			$data['errors']['CompName'] = true;

			if (isset($data['error'])) {
				$data['error'] = $data['error'] . "\nName required.";
			} else {
				$data['error'] = "Name required.";
			}
		}
		$compDescription = $_POST['CompDescription'];
		if (strlen($compDescription) == 0) {
			$valid = false;
			$data['errors']['CompDescription'] = true;
			if (isset($data['error'])) {
				$data['error'] = $data['error'] . "<br/>Description required.";
			} else {
				$data['error'] = "Description required.";
			}
		}
		$theRevisionUrl = $_POST['theRevisionUrl'];
		if (strlen($theRevisionUrl) == 0) {
			$valid = false;
			$data['errors']['RevisionUrl'] = true;
			$data['error'] .= "<br />Revision  URL required.";
		}
		$revision = $_POST['revision'];
		$revisionPageFloat = $this->input->post('RevisionPageFloat');
		$revisionBackgroundColour = $this->input->post('RevisionBackgroundColour');
		$revisionBackgroundRepeat = $this->input->post('RevisionBackgroundRepeat');
		$theRevisionBackgroundImage = $this->input->post('theRevisionBackgroundImage');
		if (strlen($revision) == 0) {
			if (strlen($revisionPageFloat) == 0) {
				$valid = false;
				$data['errors']['RevisionPageFloat'] = true;
				$data['error'] = "\nPage float position required.";
			}
			if (strlen($revisionBackgroundColour) == 0) {
				$valid = false;
				$data['errors']['RevisionBackgroundColour'] = true;
				$data['error'] = "\nBackground colour required.";
			}
			if (strlen($revisionBackgroundRepeat) == 0) {
				$valid = false;
				$data['errrors']['RevisionBackgroundRepeat'] = true;
				$data['error'] = "Background repeat pattern required";
			}
		}
		if ($valid) {
			$filename = explode("/", $theRevisionUrl);
			$filename = $filename[count($filename) - 1];
			$filename = explode("_thumb", $filename);
			$filename = $filename[0] . $filename[1];
			//creates a new comp if needed, and a new revision.

			$ProjectId = $_POST['ProjectId'];
			$compId = $this->Comp_model->NewComp($ProjectId, $compName, $compDescription, $filename, $compId, $revisionBackgroundColour, $theRevisionBackgroundImage, $revisionBackgroundRepeat, $revisionPageFloat);
			//redirect to the new composition(or revision)
			redirect(base_url() . index_page() . 'comps/open/' . $compId);
		} else {
			//invalid!
			if ($_SESSION['userrole'] != '-1') {
				redirect(base_url() . index_page() . 'admin');
			}
			$ProjectId = $_POST['ProjectId'];
			$this->load->model('Projects_model');
			$company = $this->Projects_model->GetCompanyName($ProjectId);
			//Page Title
			if ($compId == null) {
				$data['pagename'] = 'New Design';
			} else {
				$data['pagename'] = 'New Revision';
			}
			if (file_exists(APPPATH . '../../images/clients/' . $company)) {
				$data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $company . '" />' . $data['pagename'];
			} else {
				$data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $data['pagename'];
			}
			//Highlighted Tab
			$data['tab'] = 'clients';
			//Page Description
			$data['description'] = 'Upload a design below to start collecting feedback.';
			//Sidebar Menu
			//Page Data
			$data['ProjectId'] = $ProjectId;
			$data['site_title'] = $this->Settings_model->GetSetting("Site Title");

			//Load the View
			$this->load->view('editcomp_view', $data);
		}
	}

	/**
	 * /comps/archive/$CompId/
	 * Parameter: $CompId - the composition ID that is to be archived.
	 * Displays the 'are you sure?' page for archiving this composition.
	 */
	function archive($CompId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		//Are You Sure?
		//Get Comp Info
		$comp = $this->Comp_model->GetComp($CompId);
		$this->load->model('Projects_model');
		$project = $this->Projects_model->GetProject($comp['ProjectId']);
		$projectname = $project['ProjectName'];
		$this->load->model('Company_model');
		$companyname = $this->Company_model->GetCompany($project['CompanyId']);
		//Page Title
		$data['pagename'] = 'Are You Sure?';
		if (file_exists(APPPATH . '../../images/clients/' . $companyname)) {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $companyname . '" />' . $data['pagename'];
		} else {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'archive';
		//Page Description
		$data['description'] = 'Are you sure you want to archive the design <strong><em>' . $comp['CompName'] . '</em></strong>? It will be removed from the project <strong><em>' . $projectname . '</em></strong> and the company <strong><em>' . $companyname . '</em></strong>.';
		//Sidebar Menu
		//Page Data
		$data['action'] = 'Archive';
		$data['formaction'] = '/comps/surearchive/' . $CompId;
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('areyousure_view', $data);
	}

	/**
	 * /comps/surearchive/$CompId/
	 * Parameter: $CompId - the ID of the composition to be archived
	 * Archives the composition. Redirects back to the original project.
	 */
	function surearchive($CompId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		//archive the composition
		$comp = $this->Comp_model->GetComp($CompId);
		//do we have permissions to do this?
		//project id of the comp
		$ProjectId = $comp['ProjectId'];
		//we are allowed to do this
		if ($this->Comp_model->SetArchiveComp($CompId)) {
			//redirect to the original project
			redirect(base_url() . index_page() . 'project/open/' . $ProjectId . '/ad');
		} else {
			//problems!
			redirect(base_url() . index_page() . 'project/open/' . $ProjectId . '/af');
		}
	}

	/**
	 * /comps/delete/$CompId
	 * Parameter - $CompId - the ID of the composition to be deleted.
	 * Shows the 'are you sure?' page for deleting this composition.
	 */
	function delete($CompId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		//Are You Sure?
		//Get Comp Info
		$comp = $this->Comp_model->GetComp($CompId);
		$this->load->model('Projects_model');
		$project = $this->Projects_model->GetProject($comp['ProjectId']);
		$projectname = $project['ProjectName'];
		$this->load->model('Company_model');
		$companyname = $this->Company_model->GetCompany($project['CompanyId']);
		//Page Title
		$data['pagename'] = 'Are You Sure?';
		if (file_exists(APPPATH . '../../images/clients/' . $companyname)) {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/' . $companyname . '" />' . $data['pagename'];
		} else {
			$data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $data['pagename'];
		}
		//Highlighted Tab
		$data['tab'] = 'archive';
		//Page Description
		$data['description'] = 'Are you sure you want to delete the design <strong><em>' . $comp['CompName'] . '</em></strong>? This cannot be undone!';
		//Sidebar Menu
		//Page Data
		$data['action'] = 'Delete';
		$data['formaction'] = '/comps/suredelete/' . $CompId;
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('areyousure_view', $data);
	}

	/**
	 * /comps/suredelete/$CompId/
	 * Parameter: $CompId - the ID of the composition to be deleted.
	 * Deletes the composition and redirects to the owning project
	 */
	function suredelete($CompId) {
		if ($_SESSION['userrole'] != '-1') {
			redirect(base_url() . index_page() . 'admin');
		}
		//delete the composition
		$comp = $this->Comp_model->GetComp($CompId);

		//project id of the comp
		$ProjectId = $comp['ProjectId'];

		//we are allowed to do this
		if ($this->Comp_model->DelComp($CompId)) {
			//redirect to the original project
			redirect(base_url() . index_page() . 'project/open/' . $ProjectId . '/ad');
		} else {
			//problems!
			redirect(base_url() . index_page() . 'project/open/' . $ProjectId . '/af');
		}
	}

	/**
	 * /comps/revision/$CompId
	 * Parameter: The ID of the composition
	 * Expected: $_POST
	 * Creates a revision for the composition.
	 */
	function revision($CompId) {
		global $data;
		//Page Data
		$data['revision'] = true;
		$data['comp'] = $this->Comp_model->GetComp($CompId);
		$_POST = $data['comp'];
		$_POST['theRevisionBackgroundImage'] = $data['comp']['RevisionBackgroundImage'];
		$data['CompName'] = $data['comp']['CompName'];
		$data['ProjectId'] = $data['comp']['ProjectId'];
		$data['formaction'] = $CompId;
		//Page Title
		$data['pagename'] = 'Create a Revision of ' . $data['comp']['CompName'];
		//Page Description
		$data['description'] = 'You can make changes to the design below.';
		//Highlighted Tab
		$data['tab'] = 'projects';
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$data['scripts'] = array('comps_revision.js');
		//Load The View
		$this->load->view('editcomp_view', $data);
	}

}

/* End of file comp.php */
/* Location: ./system/application/controllers/comp.php */
