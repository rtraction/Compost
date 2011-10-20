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
 * Help - Generate a help page
 */
class Help extends MY_Controller {

	/**
	 * /login/
	 * Displays the login form.
	 */
	function index()
	{ 
		//Page Title
		$data['pagename'] = "Help";
		//Highlighted Tab
		$data['tab'] = 'help';
		//Load the View
		$data['site_title'] = $this->Settings_model->GetSetting("Site Title");
		$this->load->view('help_view', $data);
	}
	
}

/* End of file login.php */
/* Location: ./system/application/controllers/help */