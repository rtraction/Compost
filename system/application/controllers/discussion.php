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
 * Discussion - manages the discussion of a composition, organized into annotations and their comments
 */
class Discussion extends Authenticated_Controller{

	/**
	 * /discussion/open/$CompId/
	 * Parameter: $CompId - the ID of the composition being discussed
	 * Shows the annotations and their comments of this composition.
	 */
	function open($CompId)
	{
		if (isset($CompId)) {
			//load the discussion page for comp $CompId
			//Comp Info
			$this->load->model('Annotation_model');
			$this->load->model('Comment_model');
			$this->load->model('Projects_model');
			$comp = $this->Comp_model->GetComp($CompId);
			if (!$this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) && $_SESSION['userrole'] != '-1') {
				redirect(base_url().index_page().'admin');
			}
			$company = $this->Comp_model->GetCompanyName($CompId);
			//Page Title
			$data['pagename'] = 'Discussion: <em>' . $comp['CompName'] . '</em>';
			if (file_exists(APPPATH.'../../images/clients/'.$company)) {
				$data['pagename'] = '<img src="'.base_url().'images/clients/'.$company.'" />'.$data['pagename'];
			} else {
				$data['pagename'] = '<img src="'.base_url().'images/clients/nopic.jpg" />'.$data['pagename'];
			}
			//Highlighted Tab
			$data['tab'] = 'projects';
			//Page Description
			$data['description'] = '';
			//Sidebar Menu
			$image = explode('.', $comp['RevisionUrl']);
			$compthumb = $image[0] . '_thumb.' . $image[1];
			$data['menu'] = array();
			$data['menu'][] = array(
				'link' => 'comps/open/'.$CompId,
				'name' => '<img src="'.base_url().'images/comps/'.$compthumb.'" />'
			);
			$data['menu'][] = array(
				'link' => 'comps/open/'.$CompId,
				'name' => 'Back to '.$comp['CompName']
			);
			//Page Data
			$data['comp'] = $comp;
			$data['annotations'] = $this->Comp_model->GetAnnotations($CompId);
			$AnnotationTable = $this->compostdb->GetTable('Annotation');
			$ReadTable = $this->compostdb->GetTable('Read');
			$CommentTable = $this->compostdb->GetTable('Comment');
			$UserTable = $this->compostdb->GetTable('User');
			foreach ($data['annotations'] as $key => $annotation) {
				//get the unread comments
				$unread = $this->Annotation_model->GetUnread($annotation['AnnotationId'], $_SESSION['userid'], $AnnotationTable, $ReadTable, $CommentTable);
				//set comments as read
				$this->Annotation_model->SetReadComments($annotation['AnnotationId'], $_SESSION['userid']);
				$data['annotations'][$key]['comments'] = $this->Annotation_model->GetComments($annotation['AnnotationId'], $CommentTable);
				$data['annotations'][$key]['author'] = $this->User_model->GetUser($annotation['UserId'],$UserTable);
				//get the most recently updated comment
				$latestdate = null;
				foreach ($data['annotations'][$key]['comments'] as $key2 => $comment) {
					//comment author
					$data['annotations'][$key]['comments'][$key2]['author'] = $this->User_model->GetUser($comment['UserId'],$UserTable);
					//comment date
					$data['annotations'][$key]['comments'][$key2]['relativedate'] = $this->relativedate->getRelativeDate($comment['CommentTimestamp']);
					if ($comment['CommentTimestamp'] > $latestdate) {
						$latestdate = $comment['CommentTimestamp'];
					}
					$data['annotations'][$key]['comments'][$key2]['Unread'] = false;
					if (count($unread) > 0) {
						foreach ($unread as $key4 => $ur) {
							if ($ur['CommentId'] == $data['annotations'][$key]['comments'][$key2]['CommentId']) {
								//this comment is previously unread!
								$data['annotations'][$key]['comments'][$key2]['Unread'] = true;
								break;
							}
						}
					}
				}
				$data['annotations'][$key]['relativedate'] = $this->relativedate->getRelativeDate($latestdate);
			}
			
			$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
			$data['scripts'] = array('discussion_open.js');
			//Load the View
			$this->load->view('discussion_view', $data);
		} else {
			redirect(base_url().index_page().'admin');
		}
	}
	
	/**
	 * /discussion/reply/$CompId/
	 * Parameter: $CompId - The ID of the composition being discussed.
	 * Expected: $_POST
	 * Post a reply to an annotation.
	 */
	function reply($CompId){
		//create a comment
		$AnnotationId = $_POST['annotation'];
		$UserId = $_SESSION['userid'];
		$CommentBody = $_POST['comment'];
		if (isset($_POST['CommentRating'])) {
			$CommentRating = $_POST['CommentRating'];
		} else {
			$CommentRating = 0;	
		}
		$this->load->model('Comment_model');
		$this->load->model('Projects_model');
		$comp = $this->Comp_model->GetComp($CompId);
		if ($this->Projects_model->GetPermission($_SESSION['userid'], $comp['ProjectId']) || $_SESSION['userrole'] == '-1') {
			$CommentId = $this->Comment_model->NewComment($AnnotationId , $UserId , $CommentBody , time(), $CommentRating );
			redirect(base_url().index_page().'discussion/open/'.$CompId.'#'.$AnnotationId);
		} else {
			redirect(base_url().index_page().'admin');
		}
	}
}

/* End of file discussion.php */
/* Location: ./system/application/controllers/discussion.php */