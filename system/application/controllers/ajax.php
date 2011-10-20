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
 * Ajax - the methods in this file are all called by ajax requests.
 * Each method uses the $_POST array.
 */
class Ajax extends MY_Controller {

	/**
	 * /ajax/
	 * does nothing for now
	 */
	function index() {
		
	}

	/**
	 * /ajax/save_annotation/
	 * Expected: $_POST('compid', 'text', 'x', 'y')
	 * Description: Saves changes to an annotation, or saves a new annotation.
	 * Returns: True or False
	 */
	function save_annotation() {
		//load the Annotation database model
		$this->load->model('Annotation_model');
		$this->load->model('Projects_model');
		//$AnnotationId = $_POST['id'];
		$AnnotationText = $_POST['text'];
		$CompId = $_POST['compid'];
		$AnnotationX = $_POST['x'];
		$AnnotationY = $_POST['y'];
		$UserId = $_SESSION['userid'];
		$comp = $this->Comp_model->GetComp($CompId);
		if ($this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) || $_SESSION['userrole'] == '-1') {
			echo $this->Annotation_model->NewAnnotation($CompId, $UserId, $AnnotationX, $AnnotationY, $AnnotationText);
		}
	}

	/**
	 * /ajax/set_annotation/
	 * Expected: $_POST('annid', 'annotationtext', 'compid', 'x', 'y')
	 * Description: Saves changes to an annotation
	 * Returns: True or False
	 */
	function set_annotation() {
		//load the Annotation database model
		$this->load->model('Annotation_model');
		$this->load->model('Projects_model');
		$annotation = $this->Annotation_model->GetAnnotation($_POST['annid']);

		$comp = $this->Comp_model->GetComp($_POST['compid']);

		if ($this->Projects_model->GetAnnotationPermission($_SESSION['userid'], $_POST['annid']) || $_SESSION['userrole'] == '-1') {
			echo $this->Annotation_model->SetAnnotation($_POST['annid'], $_POST['x'], $_POST['y'], $_POST['text']);
		}
	}

	/**
	 * /ajax/annotation_move/
	 * Expected: $_POST('UserId', 'AnnotationId', 'x', 'y')
	 * Description: Requested after an annotation is dragged and dropped. The new coordinates are saved into the database.
	 * Returns: True or False, or 'fail'
	 */
	function annotation_move() {
		//this might be insecure - $_POST['UserId'] HAS to equal the userid in the database,
		//otherwise someone is trying to hack
		$this->load->model('Annotation_model');
		$this->load->model('Projects_model');
		$annotation = $this->Annotation_model->GetAnnotation($_POST['AnnotationId']);
		$comp = $this->Comp_model->GetComp($annotation['CompId']);
		if ($this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) || $_SESSION['userrole'] == '-1') {
			if ($annotation['UserId'] == $_POST['UserId']) {
				//save new coords
				echo $this->Annotation_model->SetAnnotation($annotation['AnnotationId'], $_POST['x'], $_POST['y'], $annotation['AnnotationText']);
			} else {
				echo 'fail';
			}
		} else {
			echo 'fail';
		}
	}

	/**
	 * /ajax/get_annotation_position/
	 * Expected: $_POST('AnnotationId')
	 * Description: Retrieves the x and y coordinates of the annotation.
	 * Returns: 'x,y'
	 */
	function get_annotation_position() {
		//get the x,y of the annotation
		$this->load->model('Annotation_model');
		$this->load->model('Projects_model');
		$annotation = $this->Annotation_model->GetAnnotation($_POST['AnnotationId']);
		$comp = $this->Comp_model->GetComp($annotation['CompId']);
		if ($this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) || $_SESSION['userrole'] == '-1') {
			echo $annotation['AnnotationX'] . ',' . $annotation['AnnotationY'];
		}
	}

	/**
	 * /ajax/delete_annotation
	 * Expected: $_POST('id', 'UserId')
	 * Description: Deletes an annotation from the database. You have to be the owner of the annotation or the admin.
	 * Returns: True or False, or 'fail'
	 */
	function delete_annotation() {
		$this->load->model('Annotation_model');
		$this->load->model('Projects_model');
		//get the annotation
		$annotation = $this->Annotation_model->GetAnnotation($_POST['id']);
		$comp = $this->Comp_model->GetComp($annotation['CompId']);
		if ($this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) || $_SESSION['userrole'] == '-1') {
			//did this user make it? or are we admin?
			if ($annotation['UserId'] == $_POST['UserId'] || $_SESSION['userrole'] == '-1') {
				//delete it
				echo $this->Annotation_model->DelAnnotation($_POST['id']);
			} else {
				//don't delete it
				echo 'fail';
			}
		} else {
			echo 'fail';
		}
	}

	/**
	 * /ajax/edit_compname/
	 * Expected: $_POST('compid', 'name')
	 * Description: Updates a composition's name in the database.
	 * Returns: nothing.
	 */
	function edit_compname() {
		if ($_SESSION['userrole'] == '-1') {
			//expecting two $_POST vars, if there are more or less, don't update
			if (count($_POST) == 2) {
				$CompId = $_POST['compid'];
				$CompName = $_POST['name'];
				//make sure we are allowed to do this
				//get the comp's project id
				$comp = $this->Comp_model->GetComp($CompId);
				$ProjectId = $comp['ProjectId'];
				//see if we have permissions for that projectid, or if we are admin
				$projects = $this->User_model->GetProjects($_SESSION['userid']);
				foreach ($projects as $project) {
					$projectids[] = $project['ProjectId'];
				}
				//we are allowed to update
				$this->Comp_model->SetCompName($CompId, $CompName);
			}
		}
	}

	/**
	 * /ajax/edit_compdescription/
	 * Expected: $_POST('compid', 'description')
	 * Description: Updates a composition's description in the database.
	 * Returns: nothing.
	 */
	function edit_compdescription() {
		if ($_SESSION['userrole'] == '-1') {
			//expecting two $_POST vars, if there are more or less, don't update
			if (count($_POST) == 2) {
				$CompId = $_POST['compid'];
				$CompDescription = $_POST['description'];
				//make sure we are allowed to do this
				//get the comp's project id
				$comp = $this->Comp_model->GetComp($CompId);
				$ProjectId = $comp['ProjectId'];
				//see if we have permissions for that projectid, or if we are admin
				$projects = $this->User_model->GetProjects($_SESSION['userid']);
				foreach ($projects as $project) {
					$projectids[] = $project['ProjectId'];
				}

				//we are allowed to update
				$this->Comp_model->SetCompDescription($CompId, $CompDescription);
			}
		}
	}

	/**
	 * /ajax/rate/
	 * Expected: $_POST('compid', 'rating')
	 * Description: Updates or creates a user rating of a composition in the database.
	 * Returns: True or False.
	 */
	function rate() {
		//$_POST['compid'], $_POST['rating'], $_SESSION['userid']
		//make sure we have priv's for this comp first
		if (isset($_POST['compid']) && isset($_POST['rating']) && isset($_SESSION['userid'])) {
			//does $_SESSION['userid'] have priveleges to use $_POST['compid'] ?
			$this->load->model('Projects_model');
			$CompId = $_POST['compid'];
			$comp = $this->Comp_model->GetComp($CompId);
			//project id of the comp
			$ProjectId = $comp['ProjectId'];

			// normally we would check for $_SESSION['userrole']==-1, but in this case
			// admins can't vote :)
			if ($this->Projects_model->GetPermission($_SESSION['userid'], $ProjectId)) {
				//we can use this project
				echo $this->Comp_model->SetRateComp($CompId, $_SESSION['userid'], $_POST['rating']);
			}
		}
	}

	/**
	 * /ajax/clear_rating/
	 * Expected: $_POST('compid')
	 * Description: Removes the current user's rating of the composition in the database.
	 * Returns: True or False.
	 */
	function clear_rating() {
		//$_POST['compid'], $_SESSION['userid']
		//make sure we have priv's for this comp first
		if (isset($_POST['compid']) && isset($_SESSION['userid'])) {
			//does $_SESSION['userid'] have priveleges to use $_POST['compid'] ?
			$this->load->model('Projects_model');
			$CompId = $_POST['compid'];
			$comp = $this->Comp_model->GetComp($CompId);
			//project id of the comp
			$ProjectId = $comp['ProjectId'];

			// normally we would check for $_SESSION['userrole']==-1, but in this case
			// admin's can't vote :)
			if ($this->Projects_model->GetPermission($_SESSION['userid'], $ProjectId)) {
				//we can use this project
				echo $this->Comp_model->SetRateClear($CompId, $_SESSION['userid']);
			}
		}
	}

	/**
	 * /ajax/getprojects/
	 * Expected: $_POST('ClientId', 'UserId')
	 * Description: Retrieves a list of projects for a client (and wheter or not the current user has access)
	 * Returns: 'ProjectId:ProjectName:HasAccess,ProjectId:ProjectName:HasAccess,...'
	 */
	function getprojects() {
		if ($_SESSION['userrole'] == '-1') {
			$this->load->model('Company_model');
			$this->load->model('Projects_model');
			$ProjectsTable = $this->compostdb->GetTable('Project');
			$projects = $this->Company_model->GetProjects($_POST['ClientId'], $ProjectsTable);
			foreach ($projects as $project) {
				echo $project['ProjectId'] . ':' . $project['ProjectName'];
				if (isset($_POST['UserId']) && $_POST['UserId'] != 'undefined' && strlen($_POST['UserId']) > 0) {
					if ($this->Projects_model->GetPermission($_POST['UserId'], $project['ProjectId'], $ProjectsTable)) {
						echo ':true';
					} else {
						echo ':false';
					}
				}
				echo ',';
			}
		}
	}

	/**
	 * /ajax/get_revision/
	 * Expected: $_POST('RevisionId')
	 * Description: Retrieves the url of a specific revision.
	 * Returns: 'RevisionUrl'
	 */
	function get_revision() {
		$this->load->model('Projects_model');
		//we have the revisionid
		//get the compid
		$comp = $this->Comp_model->GetCompFromRevision($_POST['RevisionId']);
		//do we have permission to get this revision?
		if ($this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) || $_SESSION['userrole'] == '-1') {
			//we want to give the revision url

			$revisions = $this->Comp_model->GetRevisions($comp);
			foreach ($revisions as $revision) {
				if ($revision['RevisionId'] == $_POST['RevisionId']) {
					echo $revision['RevisionUrl'];
					break;
				}
			}
		}
	}

	/**
	 * /ajax/upload/$type
	 * Parameter: $type - either 'RevisionUrl' or 'RevisionBackgroundImage'
	 * Expected: $_FILES($type)
	 * Description: Uploads an image to the server.
	 * Returns: Dimensions and repeat rules.
	 */
	function upload($type) {
		$valid = true;
		//can we do this?
		if (isset($_SESSION['userrole']) && $_SESSION['userrole'] == '-1') {
			if ($type == 'RevisionUrl' || $type == 'RevisionBackgroundImage') {
				$target_path = $complocation = $this->config->item("absolute_path") . 'images/comps/';
				$target_location = 'images/comps/';
				//upload the file
				if (!$valid = validate_image_upload($type)) {
					echo 'fail';
					exit;
				}
				$filename = date('d-m-Y-G-i-s') . '_' . basename($_FILES[$type]['name']);
				$filename = str_replace(" ", "_", $filename);
				$filename = str_replace("(", "", $filename);
				$filename = str_replace(")", "", $filename);				
				$filename = implode('', explode('.', $filename, substr_count($filename, '.')));
				
				$target_path .= $filename;
				$target_location .= $filename;
				$filename = str_replace('.', '_thumb.', $filename);
				$thumb_location = 'images/comps/' . $filename;				
				
				if (move_uploaded_file($_FILES[$type]['tmp_name'], $target_path)) {
					$result = $target_location;
					//file upload success
					if ($type == "RevisionUrl") {
						$result = $thumb_location;
						//create the thumbnail
						move_uploaded_file($_FILES[$type]['tmp_name'], $target_path);
						$config['image_library'] = 'gd2';
						$config['source_image'] = $target_path;
						$config['create_thumb'] = TRUE;
						$config['maintain_ratio'] = TRUE;
						$config['width'] = 237;
						$config['height'] = 303;
						$this->load->library('image_lib', $config);
						$this->image_lib->resize();
					}
					list($w, $h) = getimagesize($target_path);
					if ($type == "RevisionBackgroundImage") {
						if ($w == $h) {
							//tile it
							$result .= ";tile";
						} else if ($w < $h) {
							//repeat-vertical
							$result .= ";repeat-x";
						} else if ($h < $w) {
							//repeat-horizontal
							$result .= ";repeat-y";
						}
					} else {
						$result .= ";" . ($h * .2);
					}
					if ($valid == true) {
						echo $result;
					} else {
						echo "fail";
					}
				} else {
					//file upload fail
					echo 'fail';
				}
			} else {
				echo "fail";
			}
		} else {
			echo "fail";
		}
	}

	/**
	 * /ajax/movecomp
	 * Expected: $_POST('ProjectId', 'CompId')
	 * Description: Moves a composition to a project.
	 * Returns: 'ProjectId'
	 */
	function movecomp() {
		//$_POST['CompId'] to $_POST['ProjectId']
		if ($_SESSION['userrole'] == '-1') {
			if (isset($_POST['CompId']) && isset($_POST['ProjectId'])) {
				$this->Comp_model->SetProject($_POST['CompId'], $_POST['ProjectId']);
				echo $_POST['ProjectId'];
			}
		}
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
