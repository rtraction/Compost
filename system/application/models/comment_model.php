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

class Comment_model extends Model {

	function Comment_model()
	{
		parent::Model();
	}

	/**
	 * Creates a comment associated with the specific annotation given
	 * Returns: The new CommentId
	 */
	function NewComment($AnnotationId , $UserId , $CommentBody , $CommentTimestamp, $CommentRating ) {
		$newcomment = array(
			'AnnotationId' => $AnnotationId,
			'UserId' => $UserId,
			'CommentBody' => $CommentBody,
			'CommentTimestamp' => $CommentTimestamp,
			'CommentRating' => $CommentRating,
		);
		
		//Log it
		$annotation = $this->Annotation_model->GetAnnotation($AnnotationId);
		$user = $this->User_model->GetUser($UserId);
		$comp = $this->Comp_model->GetComp($annotation['CompId']);
		$company = $this->Comp_model->GetCompanyName($annotation['CompId']);
		$CompanyId = $this->Comp_model->GetCompany($annotation['CompId']);
		$this->Log_model->LogEvent("<strong>".$user['UserName']."</strong> commented on a note for <a href='".base_url().index_page()."discussion/open/".$annotation['CompId']."#".$AnnotationId."'><strong>".$comp['CompName']."</strong></a> <a href='".base_url().index_page()."client/open/".$CompanyId."'>[".$company."]</a>", "dash");
		
		
		return $this->compostdb->Insert('Comment', $newcomment);
	}
	
	/**
	 * Deletes a comment from the database
	 * Returns: True or False
	 */
	function DelComment($CommentId) {
		return $this->compostdb->Delete('Comment', $CommentId);
	}
}
?>