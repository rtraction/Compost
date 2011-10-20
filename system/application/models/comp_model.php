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

class Comp_model extends Model {

	function Comp_model()
	{
		parent::Model();
		$this->load->model("Projects_model");
	}

	/**
	 * Creates a comp with the given name and image associated with the given project. If CompId is set, then it creates a revision instead of a new comp.
	 * Returns: The CompId
	 */
	function NewComp($ProjectId , $CompName , $CompDescription , $RevisionUrl , $CompId=null, $RevisionBackgroundColour=null, $RevisionBackgroundImage=null, $RevisionBackgroundRepeat=null, $RevisionPageFloat=null) {
		$newcomp = array(
			'ProjectId'							=>	$ProjectId,
			'CompName'							=>	$CompName,
			'CompDescription'				=>	$CompDescription,
			'CompHidden'						=>	'0'
		);
		
		
		$returnComp = false;
		$verb = "";
		if (isset($CompId)) {
			$newcomp['CompId'] = $CompId;
			$this->compostdb->Update('Comp', $newcomp);
			$verb = "updated";
		} else {
			//create the comp and get the new CompId
			$CompId = $this->compostdb->Insert('Comp', $newcomp);
			$verb = "created";
			$returnComp = true;
		}
		$newrevision = array(
			'CompId'			=>	$CompId,
			'RevisionUrl'	=>	$RevisionUrl,
			'RevisionDate' =>	date('Y-m-d'),
			'RevisionBackgroundColour' 	=> 	$RevisionBackgroundColour,
			'RevisionBackgroundImage' 	=>	$RevisionBackgroundImage,
			'RevisionBackgroundRepeat'	=> 	$RevisionBackgroundRepeat,
			'RevisionPageFloat'					=>	$RevisionPageFloat
		);
		$RevisionId = $this->compostdb->Insert('Revision', $newrevision);
		
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$company = $this->Projects_model->GetCompany($ProjectId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> ".$verb." <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$CompName."</strong></a> <a href='".base_url().index_page()."client/open/".$company['CompanyId']."'>[".$company['CompanyName']."]</a>", "dash");
		
		return $CompId;
	}
	
	/**
	 * Updates a composition's name
	 * Returns: True or False
	 */
	function SetCompName($CompId, $CompName) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> renamed \"".$comp['CompName']."\" to <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$CompName."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		$update = array(
			'CompId'	=>	$CompId,
			'CompName'	=> $CompName
		);
		return $this->compostdb->Update('Comp', $update);
	}
	
	/**
	 * Updates a composition's description
	 * Returns: True or False
	 */
	function SetCompDescription($CompId, $CompDescription) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> updated the description of <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		$update = array(
			'CompId'	=>	$CompId,
			'CompDescription'	=> $CompDescription
		);
		return $this->compostdb->Update('Comp', $update);
	}
	
	/**
	 * Deletes a comp from the database permanently
	 * Returns: True or False
	 */
	function DelComp($CompId) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> deleted \"".$comp['CompName']."\" to <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$CompName."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		//get the comp info
		$comp = $this->compostdb->Select('Comp', $CompId);
		$annotations = $this->compostdb->SelectWhere('Annotation', 'CompId', $CompId);
		foreach ($annotations as $annotation) {
			$comments = $this->compostdb->SelectWhere('Comment', 'AnnotationId', $annotation['AnnotationId']);
			foreach ($comments as $comment) {
				$this->compostdb->DeleteWhere('Read', 'CommentId', $comment['CommentId']);
			}
			$this->compostdb->DeleteWhere('Comment', 'AnnotationId', $annotation['AnnotationId']);
		}
		$this->compostdb->DeleteWhere('Annotation', 'CompId', $CompId);
		$this->compostdb->DeleteWhere('Revision', 'CompId', $CompId);
		$this->compostdb->DeleteWhere('Rating', 'CompId', $CompId);
		return $this->compostdb->Delete('Comp', $CompId);
	}
	
	/**
	 * Moves a comp to the 'archive' company (-1)
	 * Returns: True or False
	 */
	function SetArchiveComp($CompId) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> archived \"".$comp['CompName']."\" to <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$CompName."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		
		//update the comp's projectid to -1 (archive project, part of the archive company)
		$update = array(
			'CompId'	=>	$CompId,
			'ProjectId'	=> '-1'
		);
		return $this->compostdb->Update('Comp', $update);
	}
	
	/**
	 * Move a comp to the specified project
	 * Returns: True or False
	 */
	function SetProject($CompId, $ProjectId) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> moved \"".$comp['CompName']."\" to <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$CompName."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		
		$update = array(
			'CompId'	=> $CompId,
			'ProjectId'	=> $ProjectId,
		);
		return $this->compostdb->Update('Comp', $update);
	}
	
	/**
	 * Retrieves all annotations associated with the comp
	 * Returns: Array of Associative Arrays
	 */
	function GetAnnotations($CompId, $Annotation = null) {
		if ($Annotation == null) {
			return $this->compostdb->SelectWhere('Annotation', 'CompId', $CompId);
		} else {
			$results = array();
			foreach ($Annotation as $curr) {
				if ($curr['CompId'] == $CompId) {
					$results[] = $curr;
				}
			}
			return $results;
		}
	}
	
	/**
	 * Retrieves the description and name of the comp, as well as the revisionurl of the latest revision
	 * Returns: Associative Array
	 */
	function GetComp($CompId, $CompTable = null, $RevisionTable = null, $RatingTable = null) {
		if ($CompTable == null) {
			$comp = $this->compostdb->Select('Comp', $CompId);
		} else {
			foreach($CompTable as $curr) {
				if ($curr['CompId'] == $CompId) {
					$comp = $curr;
					break;
				}
			}
		}
		if ($RevisionTable == null) {
			$revisions = $this->compostdb->SelectWhere('Revision', 'CompId', $CompId);
		} else {
			$revisions = array();
			foreach($RevisionTable as $curr) {
				if ($curr['CompId'] == $CompId) {
					$revisions[] = $curr;
				}
			}
		}
		$biggestid = 0;
		$latestrevision = array();
		foreach ($revisions as $revision) {
			if ($revision['RevisionId'] > $biggestid) {
				$latestrevision = $revision;
				$biggestid = $revision['RevisionId'];
			}
		}
		$comp['RevisionUrl'] = $latestrevision['RevisionUrl'];
		$comp['RevisionBackgroundColour'] = $latestrevision['RevisionBackgroundColour'];
		$comp['RevisionBackgroundImage'] = $latestrevision['RevisionBackgroundImage'];
		$comp['RevisionBackgroundRepeat'] = $latestrevision['RevisionBackgroundRepeat'];
		$comp['RevisionPageFloat'] = $latestrevision['RevisionPageFloat'];
		$comp['Rating'] = $this->GetAverageRating($CompId, $RatingTable);
		$comp['TotalRatings'] = $this->GetTotalRatings($CompId, $RatingTable);
		return $comp;
	}
	
	/**
	 * Gets the company ID of the company
	 * Returns: INT
	 */
	function GetCompany($CompId) {
		$comp = $this->compostdb->Select('Comp', $CompId);
		$project = $this->compostdb->Select('Project', $comp['ProjectId']);
		return $project['CompanyId'];
	}
	
	/**
	 * Gets the company Name of the comp's company
	 * Returns: STRING
	 */
	function GetCompanyName($CompId) {
		$comp = $this->compostdb->Select('Comp', $CompId);
		$project = $this->compostdb->Select('Project', $comp['ProjectId']);
		$company = $this->compostdb->Select('Company', $project['CompanyId']);
		return $company['CompanyName'];
	}
	
	/**
	 * Sets a comp to hidden - still visible to others but not on the 'active comps' page.
	 * Returns: True or False
	 */
	function SetHideComp($CompId) {
		$update = array(
			'CompId'			=> $CompId,
			'CompHidden'	=> '1'
		);
		return $this->compostdb->Update('Comp', $update);
	}
	
	/**
	 * Sets a comp to NOT hidden
	 * Returns: True or False
	 */
	function SetShowComp($CompId) {
		$update = array(
			'CompId'			=> $CompId,
			'CompHidden'	=> '0'
		);
		return $this->compostdb->Update('Comp', $update);
	}
	
	/**
	 * Inserts a Rating into the database. Overwrites a current Rating of the same comp by the same user.
	 * Returns: True or False
	 */
	function SetRateComp($CompId , $UserId , $Rating) {
		
		//Log it
		$user = $this->User_model->GetUser($UserId);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> rated <a href='".base_url().index_page()."comps/open/".$comp['CompId']."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		
		$newrating = array(
			'CompId' => $CompId,
			'UserId' => $UserId,
			'Rating' => $Rating
		);
		
		//get all ratings of comp CompId
		$ratings = $this->compostdb->SelectWhere('Rating', 'CompId', $CompId);
		
		//get the rating that is from user UserId
		foreach ($ratings as $rating) {
			if ($rating['UserId'] == $UserId) {
				//if it exists, update it
				$newrating['RatingId'] = $rating['RatingId'];
				return $this->compostdb->Update('Rating', $newrating);
			}
		}
		//if it doesn't exist, insert it
		return $this->compostdb->Insert('Rating', $newrating);
	}
	
	/**
	 * Clears a Rating from the database.
	 * Returns: True or False
	 */
	function SetRateClear($CompId , $UserId) {
		//Log it
		$user = $this->User_model->GetUser($UserId);
		$comp = $this->GetComp($CompId);
		$CompanyId = $this->GetCompany($CompId);
		$CompanyName = $this->GetCompanyName($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</a> reset their rating of <a href='".base_url().index_page()."comps/open/".$comp['CompId']."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$CompanyName."]</a>", "dash");
		
		
		//get all ratings of comp CompId
		$ratings = $this->compostdb->SelectWhere('Rating', 'CompId', $CompId);
		
		//get the rating that is from user UserId
		foreach ($ratings as $rating) {
			if ($rating['UserId'] == $UserId) {
				//if it exists, delete it
				return $this->compostdb->Delete('Rating', $rating['RatingId']);
			}
		}
		//if it doesn't exist, do nothing
		return false;
	}
	
	/**
	 * Retrieves a list of revisions for the given comp.
	 * Returns: Array of Associative Arrays
	 */
	function GetRevisions($CompId) {
		return $this->compostdb->SelectWhere('Revision', 'CompId', $CompId);
	}
	
	/**
	 * Retrieves a revision
	 * Returns: String
	 */
	function GetRevision($RevisionId){
		$revision = $this->compostdb->Select('Revision', $RevisionId);
		return $revision;
	}
	
	/**
	 * Retrieves the CompId of a revision
	 * Returns: id
	 */
	function GetCompFromRevision($RevisionId) {
		$revision = $this->compostdb->Select('Revision', $RevisionId);
		return $revision['CompId'];
	}
	
	/**
	 * Retrieves the url of the latest updated comp for the given project.
	 * Returns: Url
	 */
	function GetLatestComp($ProjectId, $Revisions = null, $Comps = null) {
		//Get all revisions
		if ($Revisions == null) {
			$Revisions = $this->compostdb->GetTable('Revision');
		}
		//Count backwards
		if ($Comps == null) {
			$Comps = $this->compostdb->GetTable('Comp');
		}
		for($i=count($Revisions); $i>0; $i--) {
			//Is this comp for our project?
			foreach ($Comps as $curr) {
				if ($curr["CompId"] == $Revisions[$i]['CompId']) {
					$comp = $curr;
					break;
				}
			}
			if ($comp['ProjectId'] == $ProjectId) {
				return $Revisions[$i]['RevisionUrl'];
			}
		}
		return false;
	}
	
	/**
	 * Retrieves the $limit latest updated compositions in the system
	 * Returns: Array of Associative Arrays
	 */
	function GetRecentComps($limit) {
		$Revision = $this->compostdb->GetTable('Revision');
		$biggest = array();
		foreach ($Revision as $curr) {
			$biggest[$curr['CompId']] = 0;
		}
		foreach($Revision as $curr) {
			if ($curr['RevisionId'] > $biggest[$curr['CompId']]) {
				$biggest[$curr['CompId']] = $curr['RevisionId'];
			}
		}
		
		arsort($biggest);
		
		$revisions = array();
		$total = 1;
		foreach ($biggest as $compid => $revid) {
			if ($total <= $limit) {
				$newone = array();
				$newone['Comp'] = $this->compostdb->Select('Comp', $compid);
				$newone['Revision'] = $this->compostdb->Select('Revision', $revid);
				$newone['Project'] = $this->compostdb->Select('Project', $newone['Comp']['ProjectId']);
				$newone['Rating'] = $this->GetAverageRating($compid);
				$revisions[] = $newone;
				$total ++;
			} else { break; }
		}
		return $revisions;
	}
	
	/**
	 * Retrieves all compositions
	 * Returns: Associative Array (CompId => RevisionUrl )
	 */
	function GetListComps() {
		return $this->compostdb->GetTable('Comp');
	}
	
	/**
	 * Retrieves the average rating
	 * Returns: Integer between 0 and 5, inclusive
	 */
	function GetAverageRating($CompId, $RatingTable = null) {
		//Get all ratings for this comp
		if ($RatingTable == null) {
			$ratings = $this->compostdb->SelectWhere('Rating', 'CompId', $CompId);
		} else {
			$ratings = array();
			foreach($RatingTable as $curr) {
				if ($curr['CompId'] == $CompId) {
					$ratings[] = $curr;
				}
			}
		}
		$total = 0;
		$number = count($ratings);
		if ($number == 0) { return 0; }
		foreach ($ratings as $rating) {
			$total += $rating['Rating'];
		}
		$average = $total / $number;
		return $average;
	}
	
	/**
	 * Retrieves the total number of ratings for this comp
	 * Returns: Integer
	 */
	function GetTotalRatings($CompId, $RatingTable = null) {
		//Get all ratings for this comp
		if ($RatingTable == null) {
			$ratings = $this->compostdb->SelectWhere('Rating', 'CompId', $CompId);
		} else {
			$ratings = array();
			foreach($RatingTable as $curr) {
				if ($curr['CompId'] == $CompId) {
					$ratings[] = $curr;
				}
			}
		}
		return count($ratings);
	}
	
	/**
	 * Retrieves a user's rating of this comp
	 * Returns: Integer
	 */
	function GetUserRating($CompId, $UserId) {
		$ratings = $this->compostdb->SelectWhere('Rating', 'CompId', $CompId);
		foreach ($ratings as $rating) {
			if ($rating['UserId'] == $UserId) {
				return $rating['Rating'];
			}
		}
	}
}
?>