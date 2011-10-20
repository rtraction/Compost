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
 * Admin - handles the administrator dashboard. Non-admins directed here will be
 * redirected to their landing page (probably their project listing).
 */
class Admin extends Admin_Controller {

	/**
	 * /admin/
	 * Takes you to the dashboard, assuming the constructor doesn't throw you away
	 */
	function index() {
		$this->dashboard();
	}

	/**
	 * /admin/dashboard/
	 * This is the admin dashboard. It is 'Recent Activity' by default.
	 */
	function dashboard() {
		//Page Title
		$this->data['pagename'] = 'Recent Activity';
		$this->data['pagename'] = '<img src="' . base_url() . 'images/clients/nopic.jpg" />' . $this->data['pagename'];
		//Highlighted Tab
		$this->data['tab'] = 'dashboard';
		//Page Description
		$this->data['description'] = 'Here\'s what has been going on lately.';
		//Sidebar Menu
		//Page Data
		if ($this->uri->segment(3) == "debug") {
			$this->data['recentitems'] = $this->Log_model->GetLog();
		} else {
			$this->data['recentitems'] = $this->Log_model->GetLog("dash");
		}

		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		//Load the View
		$this->load->view('dashboard_view', $this->data);
	}

	/*
	 * /admin/settings/
	 */

	function settings() {
		$this->data['pagename'] = 'Settings';

		$this->data['description'] = 'Customize the appearance of compost';
		$this->data['tab'] = 'settings';

		$settings = $this->Settings_model->GetListSettings();

		/* $data['settings'] = array();
		  foreach ($settings as $key => $setting) {
		  $data['SettingId'][] = $setting['SettingId'];
		  $data['SettingName'][] = $setting['SettingName'];
		  $data['SettingValue'][] = $setting['SettingValue'];
		  $data['DefaultValue'][] = $setting['DefaultValue'];
		  $data['DataType'][] = $setting['DataType'];

		  $data['settings'][] = $setting;
		  } */

		$this->data['settings'] = $settings;

		$this->data['site_title'] = $this->Settings_model->GetSetting("Site Title");

		$this->load->view('settings_view', $this->data);
	}

	function save() {
		//make sure we submitted the form
		if (!isset($_POST['submit'])) {
			redirect(base_url() . index_page() . 'admin');
		}

		$valid = true;
		if ($valid) {
			$settings = $_POST;

			$files = $_FILES;

			$result = $this->Settings_model->SetSettings($settings);

			//if record is updated, save files
			if ($result) {
				//$result = $this->Settings_model->SaveFiles($files);
			}
		}
		if ($valid == true && $result == true) {
			$this->Settings_model->SaveStylesheet();
			redirect(base_url() . index_page() . 'admin/settings');
		}
	}

	function do_upload() {
		$config['upload_path'] = './images/custom/';
		$config['allowed_types'] = 'gif/jpg/png';
		$config['max_size'] = '100';
		$config['max_width'] = '2048';
		$config['max_height'] = '2048';

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload()) {
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('settings_view', $error);
		} else {
			$data = array('upload_data' => $this->upload->date());
			$this->load->view('settings_view', $data);
		}
	}

}

/* End of file admin.php */
/* Location: ./system/application/controllers/admin.php */