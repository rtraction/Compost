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
 * upgrade
 */
class upgrade extends Admin_Controller {
	
	function Upgrade(){
		parent::Admin_Controller();
		$this->load->model('Settings_Model');
		$this->data['version'] = floatval($this->Settings_Model->GetSetting('Version'));
	}

	function index(){
		$methods = get_class_methods($this);
		foreach($methods as $method){
			if(preg_match('/from_version_*/', $method)){
				$version = str_replace('from_version_', '', $method);
				$version = $version = str_replace('_', '.', $version);
				$version = floatval($version);
				if($this->data['version'] <= $version){
					$this->data['method'] = $method;
					$this->data['upgrade_to'] = 'Upgrade to version: '.$version;
					break;
				}
			}
		}
		$this->data['pagename'] = 'Upgrades';
		$this->load->view('showupgrade_view', $this->data);
	}
	
	function from_version_0_82(){
		
		if($this->data['version'] >= 0.82){
			redirect(base_url().index_page().'upgrade');
		}

		//remove image extensions in the folders
		set_time_limit(0);
		$comps = glob(FCPATH.'/images/comps/*.*');
		foreach($comps as $f){
			$pathInfo = pathinfo($f);
			//ignore non-images and comp.jpg, nopic.jpg and preloader.gif
			if(in_array(strtolower($pathInfo['extension']), array('jpeg', 'jpg', 'png', 'gif')) && !in_array($pathInfo['filename'], array('comp', 'nopic', 'preloader'))){
				rename($f, str_replace('.', '', $f));
			}
		}

		$clients = glob(FCPATH.'/images/clients/*.*');
		foreach($clients as $f){
			$pathInfo = pathinfo($f);
			//ignore non-images and rtraction.jpg, nopic.jpg, Archive
			if(in_array(strtolower($pathInfo['extension']), array('jpeg', 'jpg', 'png', 'gif')) && !in_array($pathInfo['filename'], array('rtraction', 'nopic', 'Archive'))){
				rename($f, str_replace('.', '', $f));
			}
		}

		//remove the image extensions in the database
		$this->load->library('compostdb');
		$revisions = $this->compostdb->getTable('Revision');

		foreach($revisions as $k=>$r){
			$revisions[$k]['RevisionUrl'] = str_replace('.', '', $r['RevisionUrl']);
		}
		$this->compostdb->setTable('Revision',$revisions);
		
		$this->compostdb->Insert('Setting',array('SettingName'=>'Version','SettingValue'=>'', 'DefaultValue'=>'', 'DataType'=>'uneditable'));
		$this->Settings_Model->SetSettings(array('Version'=>'0.83'));
		redirect(base_url().index_page().'upgrade');
	}
}