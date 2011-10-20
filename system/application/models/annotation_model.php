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

class Annotation_model extends Model {
	
	function Annotation_model()
	{
		parent::Model();
	}
	
	/**
	 * Creates an annotation associated with the given comp
	 * Returns: The new AnnotationId
	 */
	function NewAnnotation($CompId, $UserId, $AnnotationX , $AnnotationY , $AnnotationText) {
		$newannotation = array(
			'CompId' => $CompId,
			'UserId' => $UserId,
			'AnnotationX' => $AnnotationX,
			'AnnotationY' => $AnnotationY,
			'AnnotationText' => $AnnotationText
		);
		//Log it
		$user = $this->User_model->GetUser($UserId);
		$comp = $this->Comp_model->GetComp($CompId);
		$company = $this->Comp_model->GetCompanyName($CompId);
		$CompanyId = $this->Comp_model->GetCompany($CompId);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> put a note on <a href='".base_url().index_page()."comps/open/".$CompId."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$company."]</a>", "dash");
		
		//Create the Annotation
		return $this->compostdb->Insert('Annotation', $newannotation);
	}
	
	/**
	 * Deletes an annotation from the database
	 * also deletes all comments with this annotation id
	 * Returns: True or False
	 */
	function DelAnnotation($AnnotationId) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$annotation = $this->GetAnnotation($AnnotationId);
		$comp = $this->Comp_model->GetComp($annotation['CompId']);
		$company = $this->Comp_model->GetCompanyName($annotation['CompId']);
		$CompanyId = $this->Comp_model->GetCompany($annotation['CompId']);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> deleted a note from <a href='".base_url().index_page()."comps/open/".$annotation['CompId']."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$company."]</a>", "dash");
		
		//are there comments?
		$comments = $this->compostdb->SelectWhere('Comment', 'AnnotationId', $AnnotationId);
		if (count($comments) > 0) {
			$this->compostdb->DeleteWhere('Comment', 'AnnotationId', $AnnotationId);
		}
		
		return $this->compostdb->Delete('Annotation', $AnnotationId);
	}
	
	/**
	 * Updates a specific annotation
	 * Returns: True or False
	 */
	function SetAnnotation($AnnotationId , $AnnotationX , $AnnotationY , $AnnotationText) {
		//Log it
		$user = $this->User_model->GetUser($_SESSION['userid']);
		$annotation = $this->GetAnnotation($AnnotationId);
		$comp = $this->Comp_model->GetComp($annotation['CompId']);
		$company = $this->Comp_model->GetCompanyName($annotation['CompId']);
		$CompanyId = $this->Comp_model->GetCompany($annotation['CompId']);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> updated a note on <a href='".base_url().index_page()."comps/open/".$annotation['CompId']."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$company."]</a>", "dash");

		//If a field is an empty string (''), it will not get updated.
		$update['AnnotationId'] = $AnnotationId;
		$update['AnnotationX'] = $AnnotationX;
		$update['AnnotationY'] = $AnnotationY;
		$update['AnnotationText'] = $AnnotationText;
		$this->compostdb->Update('Annotation', $update);
		return true;
	}
	
	/**
	 * Retrieves the comments for this annotation
	 * Returns: Array of Associative Arrays
	 */
	function GetComments ($AnnotationId, $Comment = null) {
		if ($Comment == null) {
			$Comment = $this->compostdb->GetTable('Comment');
		}
		$comments = array();
		foreach ($Comment as $currComment) {
			if ($currComment['AnnotationId'] == $AnnotationId) {
				$comments[] = $currComment;
			}
		}
		return $comments;
	}
	
	/**
	 * Retrieves the information for displaying the annotation, namely, the X, Y, and Text values
	 * Returns: Associative Array
	 */
	function GetAnnotation ($AnnotationId) {
		return $this->compostdb->Select('Annotation', $AnnotationId);
	}
	
	/**
	 * Retrieves the unread comments in this annotation
	 * Returns: Associative Array
	 */
	function GetUnread($AnnotationId = null , $UserId, $annotations = null, $Read = null, $Comment = null) {
		$annotationids = array();
		if (strlen($AnnotationId) > 0) {
			//just the one annotation	
			$annotationids[] = $AnnotationId;
		} else {
			//all annotations!	
			if ($annotations == null) {
				$annotations = $this->compostdb->GetTable('Annotation');
			}
			foreach($annotations as $a) {
				$annotationids[] = $a['AnnotationId'];	
			}
		}
		$unreadcomments = array();
		foreach($annotationids as $AId) {
			$comments = $this->GetComments($AId, $Comment);
			if ($Read == null) {
				$Read = $this->compostdb->GetTable('Read');
			}
			//of all of the $comments, which ones has user $UserId not read?
			$readcomments = array();
			foreach($comments as $comment) {
				foreach($Read as $currRead) {
					if ($currRead['CommentId'] == $comment['CommentId']) {
						$readcomments[] = $comment;
						break;
					}
				}
			}
			
			//find out which ones are in $comments but not in $readcomments
			foreach ($comments as $comment) {
				$in=false;
				foreach ($readcomments as $readcomment) {
					if ($comment['CommentId'] == $readcomment['CommentId']) {
						$in=true;
					}
				}
				if ($in==false) {
					$unreadcomments[] = $comment;
				}
			}
		}
		return $unreadcomments;
	}
	
	/**
	 * Sets all comments within an annotation to 'read' for this user
	 * Returns: True or False
	 */
	function SetReadComments($AnnotationId , $UserId) {
		$comments = $this->GetComments($AnnotationId);
		$Read = $this->compostdb->GetTable('Read');
		
		//What is the highest ReadId?
		$biggest = 0;
		foreach ($Read as $currRead) {
			if ($currRead['ReadId'] > $biggest) {
				$biggest = $currRead['ReadId'];
			}
		}
		$ReadId = $biggest + 1;
		
		//Add each comment into the Read table
		foreach ($comments as $comment) {
			//check to see if it's not already in there
			$in = false;
			foreach ($Read as $currRead) {
				if ($comment['CommentId'] == $currRead['CommentId'] && $currRead['UserId'] == $UserId) {
					$in = true;
				}
			}
			if($in == false) {
				$Read[] = array(
					'ReadId' => $ReadId,
					'CommentId' => $comment['CommentId'],
					'UserId' => $UserId
				);
				$ReadId ++;
			}
		}
		if (count($Read)) {
			$this->compostdb->SetTable('Read', $Read);
		}
		return true;
	}
}
?>