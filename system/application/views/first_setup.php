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
<form action="<?php echo base_url().index_page() ?>login/setup" method="post">
    <dl>
		<h3>Site Information</h3>
        <dt><label for="Site Title">Site Title:</label></dt>
        <dd><input validation="required" type="text" validation="required" maxlength="40" name="Site Title" id="Site Title" value="<?php echo isset($_POST['Site_Title']) ? $_POST['Site_Title'] : ""; ?>" /></dd>
		<h3>Administrator Information</h3>
		<dt><label for="UserName" <?php echo isset($errors['UserName']) ? "class='red'" : ""; ?>>Username:</label></dt>
		<dd><input validation="required" name="UserName" id="UserName" value="<?php echo isset($_POST['UserName']) ? $_POST['UserName'] : ""; ?>" /></dd>
		<dt><label for="UserEmail" <?php echo isset($errors['UserEmail']) ? "class='red'" : ""; ?>>Email Address:</label></dt>
		<dd><input validation="required" name="UserEmail" id="UserEmail" value="<?php echo isset($_POST['UserEmail']) ? $_POST['UserEmail'] : ""; ?>" /></dd>
		<dt><label for="UserPassword" <?php echo isset($errors['UserPassword']) ? "class='red'" : ""; ?>>Password:</label></dt>
		<dd><input validation="required" type="password" name="UserPassword" id="UserPassword" /></dd>
		<dt><label for="RetypePassword" <?php echo isset($errors['RetypePassword']) ? "class='red'" : ""; ?>>Retype Password:</label></dt>
		<dd><input validation="required" type="password" name="RetypePassword" id="RetypePassword" /></dd>
		<dt><input type="hidden" name="UserId" value="-1" /></dt>
		<dd>
			<input type="hidden" name="referrer" id="referrer" value="<?php echo $this->session->userdata('redirect_url') ?>" />
			<input id="loginbutton" type="submit" value="Proceed" />
    </dl>
</form>

<?php include 'footer.php'; ?>