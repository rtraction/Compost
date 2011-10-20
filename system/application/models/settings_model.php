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

class Settings_model extends Model {

	function Settings_model()
	{
		parent::Model();
	}
	
	/**
	 * Retrieves all settings
	 * Returns: Array of Associative Arrays
	 */
	function GetListSettings() {
		return $this->compostdb->GetTable('Setting');
	}

	/**
	 * Updates all settings
	 * Returns: True or False
	 */
	function SetSettings($settings) {
		$success = true;
		foreach($settings as $key=>$setting)
		{
			$key = str_replace("_", " ", $key);
			$result = $this->compostdb->SelectWhere("Setting", "SettingName", $key);
			if ($result)
			{
				//normally there would only be one row but loop to be certain
				foreach($result as $thisSetting)
				{
					//update only the fields that have changed
					if ($thisSetting["SettingValue"] != $setting)
					{
						$thisSetting["SettingValue"] = $setting;
						$updated = $this->compostdb->Update("Setting", $thisSetting);
						if ($updated == false)
						{
							$success = false;
						}
					}
				}
			} else {
				$thisSetting['SettingName'] = $key;
				$thisSetting['SettingValue'] = $setting;
				$thisSetting['DataType'] = 'float';
				$this->compostdb->insert('Setting', $thisSetting);
			}
		}
		return $success;
	}
	
	/**
	 * Retrieves a user's id from the database
	 * Returns: int
	 */
	function GetSetting($SettingName) {
		$SettingName = str_replace("_", " ", $SettingName);
		$setting = $this->compostdb->SelectWhere('Setting', 'SettingName', $SettingName);

		if (count($setting)) {
			return $setting[0]['SettingValue'];
		} else {
			return false;
		}
	}

	/*
	 * Saves the settings as a stylesheet
	 */
	function SaveStylesheet($filename = "custom.css")
	{
		$filepath = '/style/';
		$filename = getcwd() . $filepath . $filename;
		$settings = $this->compostdb->GetTable('Setting');
		
		$cssArray = array();
		
		foreach($settings as $thisSetting)
		{
			$val = $thisSetting["SettingValue"];
			switch($thisSetting["SettingName"])
			{
				case "Title Font":
					//main title font
					$cssArray[] = '#header h1 { font-family: ' . $val . '; }';
					break;
				case "Title Colour":
					//main title colour
					$cssArray[] = '#header h1 { color: #' . $val . '; }';
					break;
				case "Heading Font":
					//h2 heading font
					$cssArray[] = 'h2 { font-family: ' . $val . '; }';
					break;
				case "Heading Colour":
					//h2 heading colour
					$cssArray[] = 'h2 { color: #' . $val . '; }';
					break;
				case "Body Background Image":
					//body background
					$cssArray[] = '#wrap { background-image: url(../images/' . $val . '); }';
					break;
				case "Header Background Colour":
					//page header background
					$cssArray[] = 'body { background-color: #' . $val . '; }';
					break;
				case "Header Background Image":
					//page header background
					$cssArray[] = 'body { background-image: url(../images/' . $val . '); }';
					break;
				case "Body Colour":
					//body text colour
					$cssArray[] = 'body { color: #' . $val . '; }';
					break;
				case "Menu Background Image":
					//menu bg image
					$cssArray[] = '#menu { background-image: url(../images/' . $val . '); }';
					break;
				case "Menu Background Colour":
					//menu bg colour
					$cssArray[] = '#menu { background-color: #' . $val . '; }';
					break;
				case "Menu Text Colour":
					$cssArray[] = '#menu li a { color: #' . $val . '; }';
					break;
				case "Menu Active Text Colour":
					$cssArray[] = '#menu li.current a, #menu li a:hover { color: #' . $val . '; }';
					break;
			}
		}

		if ((file_exists($filename) && is_writable($filename)) || !file_exists($filename))
		{
			if (!$handle = fopen($filename, 'w'))
			{
				$error = "Error opening custom stylesheet ($filename)";
				exit;	
			} else {
				//write array to css file
				$cssOutput = implode("\n", $cssArray);
				
				fwrite($handle, $cssOutput);
				fclose($handle);
			}
		} else {
			$error = "Unable to create custom stylesheet ($filename)";
			exit;
		}
	}
}
?>