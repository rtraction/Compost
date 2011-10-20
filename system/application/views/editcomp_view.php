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
<form enctype="multipart/form-data" action="<?php echo base_url().index_page() ?>comps/save<?php echo isset($formaction) ? "/".$formaction : ""; ?>" method="post">
	<dl>
		<input type="hidden" name="revision" value="<?php echo isset($revision)? $revision : ''?>" />
		<dt>
			<label for="CompName" <?php echo isset($errors['CompName']) ? "class='red'" : ""; ?>>* Name:</label>
		</dt>
		<dd>
			<input validation="required" name="CompName" size="32" maxlength="40" id="CompName" value="<?php if (isset($CompName)){ echo $CompName; } else if(isset($_POST['CompName'])) { echo $_POST['CompName']; } ?>" />
		</dd>
		<dt>
			<label for="CompDescription" <?php echo isset($errors['CompDescription']) ? "class='red'" : ""; ?>>* Description:</label>
		</dt>
		<dd>
			<textarea rows="3" validation="required" cols="24" name="CompDescription" id="CompDescription"><?php if (isset($CompDescription)){ echo $CompDescription; } else if(isset($_POST['CompDescription'])) { echo $_POST['CompDescription']; } ?></textarea>
		</dd>
	</dl>
	<dl id="pageinfo">
		<dt>
			<label for="theRevisionUrl">* Page:</label>
		</dt>
		<dd>
			<input type="file" accept="image/jpeg" id="RevisionUrl" name="RevisionUrl" />
			<input type="hidden" id="theRevisionUrl" name="theRevisionUrl" value="<?php echo $this->input->post('theRevisionUrl', TRUE); ?>" />
			<span class="description <?php echo isset($errors['RevisionUrl']) ? "red" : ""; ?>">image files only, please</span>
			<br />
			<input <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "top left" ? "checked=checked'" : ""; ?> class="hidden" type="radio" title="top left" name="RevisionPageFloat" id="Left" value="top left" /><label title="Page is Fixed Width and Floats Left" class="pagefloat" for="Left"><img <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "top left" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/floatleft.gif" /></label>
			<input <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "top center" ? "checked=checked'" : ""; ?> class="hidden" type="radio" title="top center" name="RevisionPageFloat" id="Centre" value="top center" /><label title="Page is Fixed Width and Centered" class="pagefloat" for="Centre"><img <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "top center" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/floatcentre.gif" /></label>
			<input <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "top right" ? "checked=checked'" : ""; ?> class="hidden" type="radio" title="top right" name="RevisionPageFloat" id="Right" value="top right" /><label title="Page is Fixed Width and Floats Right" class="pagefloat" for="Right"><img <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "top right" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/floatright.gif" /></label>
			<input <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "center top" ? "checked=checked'" : ""; ?> class="hidden" type="radio" title="top center" name="RevisionPageFloat" id="Fluid" value="center top" /><label title="Page is a Fluid Width" class="pagefloat" for="Fluid"><img <?php echo isset($_POST['RevisionPageFloat']) && $_POST['RevisionPageFloat'] == "center top" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/floatfluid.gif" /></label>
			<span id='pagefloattext' class='description <?php echo isset($errors['RevisionPageFloat']) ? "red" : ""; ?>'>Page Position</span>
		</dd>
	</dl>
	<dl  id="bginfo">
		<dt>
			<label for="RevisionBackgroundColour">Background:</label>
		</dt>
		<dd>
			#<input id="RevisionBackgroundColour" class="color" name="RevisionBackgroundColour" value="<?php echo isset($_POST['RevisionBackgroundColour']) ? $_POST['RevisionBackgroundColour'] : 'FFFFFF'; ?>" />
			<span class='description  <?php echo isset($errors['RevisionBackgroundColour']) ? "red" : ""; ?>'>Background Colour</span>
			<br />
			<input type="file" accept="image/jpeg" id="RevisionBackgroundImage" name="RevisionBackgroundImage" />
			<input type="hidden" id="theRevisionBackgroundImage" name="theRevisionBackgroundImage" value="<?php echo isset($_POST['theRevisionBackgroundImage']) ? $_POST['theRevisionBackgroundImage'] : ""; ?>" />
			<span style='margin-top: 5px;' class='description <?php echo isset($errors['RevisionBackgroundImage']) ? "red" : ""; ?>'>Background Image (.jpg  .gif  .png) <a id="removebackground"><img src="<?php echo base_url(); ?>images/x.gif" alt="Clear Background Image" /></a></span>
			<br />
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;top left" ? "checked=checked'" : ""; ?> class="hidden" title="no-repeat;top left" type="radio" name="RevisionBackgroundRepeat" id="TopLeft" value="no-repeat;top left" /><label title="Background is positioned top left" class="bgrepeat" for="TopLeft"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;top left" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/topleft.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;top center" ? "checked=checked'" : ""; ?> class="hidden" title="no-repeat;top center" type="radio" name="RevisionBackgroundRepeat" id="TopCentre" value="no-repeat;top center" /><label title="Background is centered at the top" class="bgrepeat" for="TopCentre"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;top center" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/topcentre.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;top right" ? "checked=checked'" : ""; ?> class="hidden" title="no-repeat;top right" type="radio" name="RevisionBackgroundRepeat" id="TopRight" value="no-repeat;top right" /><label title="Background is positioned top right" class="bgrepeat" for="TopRight"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;top right" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/topright.gif" /></label>
			<br />
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-y;top left" ? "checked=checked'" : ""; ?> class="hidden" title="repeat-x;top left" type="radio" name="RevisionBackgroundRepeat" id="Horizontal1" value="repeat-x;top left" /><label title="Background repeats horizontally from the left" class="bgrepeat" for="Horizontal1"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-x;top left" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/repeathorizontalleft.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-y;top center" ? "checked=checked'" : ""; ?> class="hidden" title="repeat-x;top center" type="radio" name="RevisionBackgroundRepeat" id="Horizontal2" value="repeat-x;top center" /><label title="Background repeats horizontally from the centre" class="bgrepeat" for="Horizontal2"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-x;top center" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/repeathorizontalcentre.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-y;top right" ? "checked=checked'" : ""; ?> class="hidden" title="repeat-x;top right" type="radio" name="RevisionBackgroundRepeat" id="Horizontal3" value="repeat-x;top right" /><label title="Background repeats horizontally from the right" class="bgrepeat" for="Horizontal3"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-x;top right" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/repeathorizontalright.gif" /></label>
			<br />
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-x;top left" ? "checked=checked'" : ""; ?> class="hidden" title="repeat-y;top left" type="radio" name="RevisionBackgroundRepeat" id="Vertical1" value="repeat-y;top left" /><label title="Background repeats vertically from the left" class="bgrepeat" for="Vertical1"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-y;top left" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/repeatverticalleft.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-x;top center" ? "checked=checked'" : ""; ?> class="hidden" title="repeat-y;top center" type="radio" name="RevisionBackgroundRepeat" id="Vertical2" value="repeat-y;top center" /><label title="Background repeats vertically from the centre" class="bgrepeat" for="Vertical2"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-y;top center" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/repeatverticalcentre.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-x;top right" ? "checked=checked'" : ""; ?> class="hidden" title="repeat-y;top right" type="radio" name="RevisionBackgroundRepeat" id="Vertical3" value="repeat-y;top right" /><label title="Background repeats vertically from the right" class="bgrepeat" for="Vertical3"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat-y;top right" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/repeatverticalright.gif" /></label>
			<br />
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat;top left" ? "checked=checked'" : ""; ?> class="hidden" title="repeat;top left" type="radio" name="RevisionBackgroundRepeat" id="Tile1" value="repeat;top left" /><label title="Background is tiled from the left" class="bgrepeat" for="Tile1"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat;top left" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/tileleft.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat;top center" ? "checked=checked'" : ""; ?> class="hidden" title="repeat;top center" type="radio" name="RevisionBackgroundRepeat" id="Tile2" value="repeat;top center" /><label title="Background is tiled from the centre" class="bgrepeat" for="Tile2"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat;top center" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/tilecentre.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat;top right" ? "checked=checked'" : ""; ?> class="hidden" title="repeat;top right" type="radio" name="RevisionBackgroundRepeat" id="Tile3" value="repeat;top right" /><label title="Background is tiled from the right" class="bgrepeat" for="Tile3"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "repeat;top right" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/tileright.gif" /></label>
			<br />
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;bottom left" ? "checked=checked'" : ""; ?> class="hidden" title="no-repeat;bottom left" type="radio" name="RevisionBackgroundRepeat" id="BottomLeft" value="no-repeat;bottom left" /><label title="Background is positioned bottom left" class="bgrepeat" for="BottomLeft"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;bottom left" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/bottomleft.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;bottom center" ? "checked=checked'" : ""; ?> class="hidden" title="no-repeat;bottom center" type="radio" name="RevisionBackgroundRepeat" id="BottomCentre" value="no-repeat;bottom center" /><label title="Background is centered at the bottom" class="bgrepeat" for="BottomCentre"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;bottom center" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/bottomcentre.gif" /></label>
			<input <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;bottom right" ? "checked=checked'" : ""; ?> class="hidden" title="no-repeat;bottom right" type="radio" name="RevisionBackgroundRepeat" id="BottomRight" value="no-repeat;bottom right" /><label title="Background is positioned bottom right" class="bgrepeat" for="BottomRight"><img <?php echo isset($_POST['RevisionBackgroundRepeat']) && $_POST['RevisionBackgroundRepeat'] == "no-repeat;bottom right" ? "style='border: 2px solid black;'" : ""; ?> src="<?php echo base_url(); ?>images/bottomright.gif" /></label>
			<br class="clear" />
			<span id='bgrepeattext' class='description <?php echo isset($errors['RevisionBackgroundRepeat']) ? "red" : ""; ?>'>Background Image Type</span>
		</dd>
	</dl>
	<dl>
		<dt>
			&nbsp;
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="hidden" name="ProjectId" value="<?php echo $ProjectId; ?>" />
		</dt>
		<dd>
		<?php if (!isset($revision) || !$revision) { ?>
			<input id="previewbutton" type="button" name="preview" value="Preview" /><br />&nbsp;<br />
		<?php } ?>
		<input type="button" name="cancel" value="Cancel" onClick="history.go(-1);" />
		<input type="submit" name="submit" value="Save" />
	</dl>
</form>

<div id="pagepreview">
	<img alt="" src="<?php echo base_url(); ?>images/browserhead.gif" />
	<span id="previewname">- <?php echo isset($_POST['CompName']) ? $_POST['CompName'] : "Comp Name"; ?></span>
	<div id="previewbrowser">
		<div id="pagebg" style="<?php
			echo isset($_POST['theRevisionBackgroundImage']) ? "background-image: url(/".$_POST['theRevisionBackgroundImage']."); " : "";
			if (isset($_POST['RevisionBackgroundRepeat'])) {
				$options = explode(";", $_POST['RevisionBackgroundRepeat']);
				echo 'background-repeat: '.$options[0].'; ';
				echo 'background-position: '.$options[1].'; ';
			}
			
			if (isset($_POST['RevisionBackgroundColour'])) { 
				echo 'background-color: #'.$_POST['RevisionBackgroundColour'].'; ';
			}
			if (isset($_POST['RevisionBackgroundImage'])) {
				echo 'background-image: url(/'.$_POST['RevisionBackgroundImage'].'); ';
			}
		?>" >
		<div id="previewpage" style="<?php
			if (isset($_POST['theRevisionUrl'])) {
				echo "background-image: url(/".$_POST['theRevisionUrl']."); ";
			}
			if (isset($_POST['RevisionPageFloat'])) {
				echo "background-position: ".$_POST['RevisionPageFloat']."; ";
			}
			if (isset($_POST['RevisionUrl'])) {
				$rurl = $_POST['RevisionUrl'];
				$_POST['RevisionUrl'] = base_url()."images/comps/".$rurl . "_thumb";
				echo "background-image: url(".$_POST['RevisionUrl']."); ";
			}
			if (isset($_POST)) {
				echo "height: 256px; ";
			}
		?>">
	</div>
	</div>
	</div>
</div>

<?php if (!isset($revision) || !$revision) { ?>
	<div id="bigpreview">
		<div id="bigpreviewpage">
		</div>
	</div>
<?php } ?>
<?php include 'footer.php'; ?>