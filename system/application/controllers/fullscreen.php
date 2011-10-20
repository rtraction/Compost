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
 * Fullscreen - shows a full preview of the composition.
 */
class Fullscreen extends Authenticated_Controller{
	
	/**
	 * /fullscreen/open/$CompId/
	 * Parameter: $CompId - the ID of the composition being viewed.
	 * Displays a full sized composition in a new window, with the correct position and background properties.
	 */
	function open($CompId) {
		//Required Models
		$this->load->model('Projects_model');
		//Page Data
		$data['comp'] = $this->Comp_model->GetComp($CompId);
		//Permissions?
		if (!$this->Projects_model->GetPermission($_SESSION['userid'], $data['comp']['ProjectId']) && $_SESSION['userrole'] != '-1') {
			//Go back to the dashboard
			redirect(base_url().index_page().'admin');
		}
		//Page Title
		$data['pagename'] = $data['comp']['CompName'];
		//No need for Highlighted Tab, because we aren't showing the header this time.
		//No need for sidebar menu either.
		//Do we need to load a different revision?
		
		$RevisionId = $this->uri->segment(4);
		if ($RevisionId != null) {
			$revision = $this->Comp_model->GetRevision($RevisionId);
			if ($revision['CompId'] == $CompId) {
				foreach($revision as $key => $value) {
					$data['comp'][$key] = $value;	
				}
			}
		}
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$this->load->view('fullscreen_view', $data);
	}
}
/* End of file fullscreen.php */
/* Location: ./system/application/controllers/fullscreen.php */