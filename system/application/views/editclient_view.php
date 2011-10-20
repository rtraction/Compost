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
<?php echo isset($message) ? $message : ""; ?>
<form enctype="multipart/form-data" method="post" action="<?php echo base_url().index_page() ?>client/save<?php echo isset($client['CompanyId']) ? '/'.$client['CompanyId'] : ''; ?>">
	<dl>
		<dt>
			<label for="ClientName" <?php echo isset($errors['ClientName']) ? "class='red'" : ""; ?>>Client Name:</label>
		</dt>
		<dd>
			<input type="text" validation="required" name="ClientName" id="ClientName" value="<?php echo isset($client['CompanyName']) ? $client['CompanyName'] : ''; ?>" />
		</dd>
		<dt>
			<label for="ClientLogo" <?php echo isset($errors['ClientLogo']) ? "class='red'" : ""; ?>>
				Client Logo:
			</label>
		</dt>
		<dd>
			<input type="file" name="ClientLogo" id="ClientLogo" />
			<span class="description">Optional. Recommended: 85px square image.<br />jpg files only. (Images will be resized to fit this area)</span>
		</dd>
		<dt>
			&nbsp;
			<?php
			if (isset($client['CompanyName'])) {
				echo "<input type='hidden' name='oldname' value='" . $client['CompanyName'] . "' />";
			}
			?>
		</dt>
		<dd>
			<input type="button" value="Cancel" onClick="history.go(-1);" />
			<input type="submit" value="Save" name="submit" />
		</dd>
	</dl>
</form> 
<?php include 'footer.php'; ?>