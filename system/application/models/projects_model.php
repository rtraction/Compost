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

class Projects_model extends Model {
	
	/**
	 * Retrieves all projects
	 * Returns: Array of Associative Arrays
	 */
	function GetListProjects() {
		return $this->compostdb->GetTable('Project');
	}
	
	/**
	 * Creates a project in the database, associated with the given company.
	 * Returns: The new ProjectId
	 */
	function NewProject($CompanyId , $ProjectName) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$company = $this->compostdb->Select('Company', $CompanyId);
		$CompanyName = $company['CompanyName'];
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> created the project <a href='".base_url().index_page()."client/open/".$CompanyId."'><strong>".$ProjectName."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		$newproject = array(
			'CompanyId'		=> $CompanyId,
			'ProjectName'	=> $ProjectName
		);
		return $this->compostdb->Insert('Project', $newproject);
	}
	
	/**
	 * Archives a project from the database. All associated comps get associated with the 'archive' company.
	 * Returns: True or False
	 */
	function DelProject($ProjectId) {
		//Get the Comps
		$project = $this->compostdb->Select('Project', $ProjectId);
		if ($project['CompanyId'] != '-1') {
			//Archive the project
			$project['CompanyId'] = '-1';
			return $this->compostdb->Update('Project', $project);
		} else {
			//it's already archived, permanently delete the project and all comps
			$comps = $this->compostdb->SelectWhere('Comp', 'ProjectId', $ProjectId);
			foreach ($comps as $comp) {
				
				$this->compostdb->DeleteWhere('Rating', 'CompId', $comp['CompId']);
				//get all annotations to delete
				$annotations = $this->compostdb->SelectWhere('Annotation', 'CompId', $comp['CompId']);
				//delete all comments for all annotations for this comp
				foreach($annotations as $annotation) {
					//delete the 'read' entries for each comment
					$comments = $this->compostdb->SelectWhere('Comment', 'AnnotationId', $annotation['AnnotationId']);
					foreach($comments as $comment) {
						//delete all 'read' entries for this comment
						$this->compostdb->DeleteWhere('Read', 'CommentId', $comment['CommentId']);
					}
					//now delete the comment
					$this->compostdb->DeleteWhere('Comment', 'AnnotationId', $annotation['AnnotationId']);
				}
				//now delete all annotations for this comp
				$this->compostdb->DeleteWhere('Annotation', 'CompId', $comp['CompId']);
				//delete all revisions for this comp
				$this->compostdb->DeleteWhere('Revision', 'CompId', $comp['CompId']);
				//now delete this comp
				$this->compostdb->Delete('Comp', $comp['CompId']);
			}
			//delete all permissions for this project
			$this->compostdb->DeleteWhere('Permission', 'ProjectId', $ProjectId);
			//Log it
			$user = $this->User_model->GetUser($_SESSION['userid']);
			$company = $this->GetCompany($ProjectId);
			$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> deleted the project <strong>\"".$project['ProjectName']."\"</strong> <a href='".base_url().index_page()."client/open/".$compnay['CompanyId']."'>[".$company['CompanyName']."]</a>", "dash");
			
			return $this->compostdb->Delete('Project', $ProjectId);
		}
	}
	
	/**
	 * Retrieves the company associated with the given project.
	 * Returns: Associative Array
	 */
	function GetCompany($ProjectId, $Projects = null, $Companies = null) {
		if ($Projects == null) {
			$project = $this->compostdb->Select('Project', $ProjectId);
		} else {
			foreach ($Projects as $curr) {
				if ($curr["ProjectId"] == $ProjectId) {
					$project = $curr;
					break;
				}
			}
		}
		$CompanyId = $project['CompanyId'];
		if ($Companies == null) {
			return $this->compostdb->Select('Company', $CompanyId);
		} else {
			foreach ($Companies as $curr) {
				if ($curr["CompanyId"] == $CompanyId) {
					return $curr;
					break;
				}
			}
		}
	}
	
	/**
	 * Retrieves the company associated with the given project.
	 * Returns: Associative Array
	 */
	function GetCompanyName($ProjectId) {
		$project = $this->compostdb->Select('Project', $ProjectId);
		$CompanyId = $project['CompanyId'];
		$company = $this->compostdb->Select('Company', $CompanyId);
		return $company['CompanyName'];
	}
	
	/**
	 * Retrieves the project name
	 * Returns: string
	 */
	function GetProject($ProjectId) {
		return $this->compostdb->Select('Project', $ProjectId);
	}
	
	/**
	 * Retrieves all comps associated with the given project
	 * Returns: Array of Associative Arrays
	 */
	function GetComps($ProjectId) {
		return $this->compostdb->SelectWhere('Comp', 'ProjectId', $ProjectId);
	}
	
	/**
	 * Updates a project's name
	 * Returns: True or False
	 */
	function SetProject($ProjectId , $ProjectName) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$company = $this->GetCompany($ProjectId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> updated the project <a href='".base_url().index_page()."client/open/".$ProjectId."'><strong>".$ProjectName."</strong></a> <a href='".base_url().index_page()."client/open/".$company['CompanyId']."'>[".$company['CompanyName']."]</a>", "dash");
		
		$newproject = array(
			'ProjectId'		=> $ProjectId,
			'ProjectName'	=> $ProjectName
		);
		return $this->compostdb->Update('Project', $newproject);
	}
	
	/**
	 * permits the users access to the project. ALL other users lose access to this project 
	 * Returns: True or False
	 */
	function SetPermission($ProjectId , $userids) {
		//Delete all permissions for $ProjectId
		if ($this->compostdb->DeleteWhere('Permission', 'ProjectId', $ProjectId)) {
			//Insert the new permissions
			$newpermission = array();
			foreach ($userids as $UserId) {
				$newpermission = array(
					'ProjectId'	=> $ProjectId,
					'UserId'		=> $UserId
				);
				$this->compostdb->Insert('Permission', $newpermission);
			}
			return true;
		} else { return false; }
	}
	
	/**
	 * permits one user access to a project
	 * Returns: True or False
	 */
	function SetPermit($projectids, $UserId) {
		//delete the old permissions
		$this->compostdb->DeleteWhere('Permission', 'UserId', $UserId);
		$newpermission = array();
		foreach($projectids as $ProjectId) {
			$newpermission['ProjectId'] = $ProjectId;
			$newpermission['UserId'] = $UserId;
			$this->compostdb->Insert('Permission', $newpermission);
		}
	}
	
	/**
	 * Retrieves the users who are allowed to view this project
	 * Returns: Associative Array (userid => username )
	 */
	function GetUsers($ProjectId) {
		$userids = $this->compostdb->SelectWhere('Permission', 'ProjectId', $ProjectId);
		$users = array();
		foreach($userids as $user) {
			$users[] = $this->compostdb->Select('User', $user['UserId']);
		}
		return $users;
	}
	
	/**
	 * Determines whether or not a user is permitted to view this project
	 * Returns: True or False
	 */
	function GetPermission($UserId, $ProjectId, $Permission = null) {
		//Get all projects that this user has permission for
		if ($Permission == null) {
			$permissions = $this->compostdb->SelectWhere('Permission', 'UserId', $UserId);
		} else {
			$permissions = array();
			foreach ($Permission as $curr) {
				if ($curr["UserId"] == $UserId) {
					$permissions[] = $curr;
				}
			}
		}
		foreach ($permissions as $permission) {
			if ($permission['ProjectId'] == $ProjectId) {
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * Determines whether or not a user is permitted to modify this annotation
	 * Returns: True or False
	 */
	function GetAnnotationPermission($UserId, $AnnId) {
		$annotation = $this->Annotation_model->GetAnnotation($AnnId);		
		
		if($UserId == $annotation['UserId'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
}