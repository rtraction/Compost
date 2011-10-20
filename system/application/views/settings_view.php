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

include 'header.php'; ?>
<?php echo isset($message) ? $message : ''; ?>

<form enctype="multipart/form-data" action="<?php echo base_url().index_page() ?>admin/save<?php echo isset($formaction) ? "/".$formaction : ""; ?>" method="post">
	<table>
	 <tr>
	 <?php foreach($settings as $setting) { ?>
	  <td>
	  	<label for="<?php echo $setting['SettingName']; ?>"><?php echo $setting['SettingName']; ?></label>
	  </td><td>
	    <?php 
	    switch($setting['DataType'])
	    {
	    	case 'color':
	    		echo '# <input title="Color value in hexidecimal" value="' . $setting['SettingValue'] . '" id="' . $setting['SettingName'] . '" name="' . $setting['SettingName'] . '"';
	    		echo ' type="text" class="color" />';
	    		break;
	    	case 'font':
	    		echo '<select title="Websafe font" value="' . $setting['SettingValue'] . '" id="' . $setting['SettingName'] . '" name="' . $setting['SettingName'] . '"';
	    		echo ' class="font" >';
	    		$fonts = array(
					'Arial,Helvetica,sans-serif',
					'Arial Black,Gadget,sans-serif',
					'Courier New,Courier,monospace',
					'Georgia,serif',
					'Impact,Charcoal,sans-serif',
					'Palatino Linotype,Book Antiqua,Palatino,serif',
					'Tahoma,Geneva,sans-serif',
					'Times New Roman,Times,serif',
					'Trebuchet MS,Helvetica,sans-serif',
					'Verdana,Geneva,sans-serif' );
	    		foreach($fonts as $font)
	    		{
	    			echo '<option value="' . $font . '"';
	    			if ($font == $setting['SettingValue'])
	    			{
	    				echo ' selected="selected"';
	    			}
	    			echo '>' . $font . '</option>';
	    		}
	    		echo '</select>';
	    		break;
	    	case 'file':
	    		echo '<div class="filename">' . $setting['SettingValue'] . '</div>';
	    		echo '<input type="hidden" id="hidden_' . $setting['SettingName'] . '"';
	    		echo '<input value="' . $setting['SettingValue'] . '" id="' . $setting['SettingName'] . '" name="' . $setting['SettingName'] . '"';
	    		echo ' type="file" />';
	    		break;
			case 'uneditable':
				echo $setting['SettingValue'];
				break;
	    	case 'string':
	    	default:
	    		echo '<input title="Setting value" value="' . $setting['SettingValue'] . '" id="' . $setting['SettingName'] . '" name="' . $setting['SettingName'] . '"';
	    		echo ' type="text" />';
	    		break;
	    }
	    ?>
	  </td>
	  <td>
		<?php
			switch($setting['DataType']){
				case 'uneditable':
					break;
				default:
					echo '<input type="reset" value="Use Default" onclick="javascript:document.getElementById(\''. $setting['SettingName'].').value = '. $setting['DefaultValue'] .'\';return false;" name="Reset" />';
					break;
			}
		?>
	  	
	  </td>
	 </tr>
	 <?php } ?>
	 <tr>
	 	<td>&nbsp;</td>
	 	<td colspan="3">		
		 	<input type="button" name="cancel" value="Cancel" onClick="history.go(-1);" />
			<input type="submit" name="submit" value="Save" />
	 	</td>
	 </tr>
	</table>
</form>
<?php include 'footer.php'; ?>