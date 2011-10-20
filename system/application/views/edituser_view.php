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
<form action="<?php echo base_url().index_page() ?>users/save<?php echo isset($user['UserId']) ? '/'.$user['UserId'] : ''; ?>" method="post">
 <h3>
  User Information
 </h3>
 <dl>
  <dt>
   <label for="UserName" <?php echo isset($errors['UserName']) ? "class='red'" : ""; ?>>Username:</label>
  </dt>
  <dd>
   <input validation="required" name="UserName" id="UserName" value="<?php echo isset($user['UserName']) ? $user['UserName'] : ''; ?>" />
  </dd>
  <dt>
   <label for="UserEmail" <?php echo isset($errors['UserEmail']) ? "class='red'" : ""; ?>>Email Address:</label>
  </dt>
  <dd>
   <input validation="required" name="UserEmail" id="UserEmail" value="<?php echo isset($user['UserEmail']) ? $user['UserEmail'] : ''; ?>" />
  </dd>
  <dt>
   <label for="CompanyListing" <?php echo isset($errors['CompanyListing']) ? "class='red'" : ""; ?>>Client:</label>
  </dt>
  <dd>
   <select validation="required" name="CompanyListing" id="CompanyListing">
   	<option value="">- Select -</option>
   	<option value="-1">Administrator</option>
    <?php foreach ($companies as $client) { if ($client['CompanyId'] != "-1") { ?>
     <option <?php echo isset($user) && $client['CompanyId'] == $user['CompanyId'] ? "SELECTED" : ""; ?> value="<?php echo $client['CompanyId']; ?>"><?php echo $client['CompanyName']; ?></option>
    <?php }} ?>
   </select>
  </dd>
  <?php if (substr($pagename, strlen($pagename)-13, 13) != "Register User") { ?>
   </dl>
   <br class="clear" />
   <h3>Optional</h3>
   <dl>
  <?php } ?>
  <dt>
   <label for="UserPassword" <?php echo isset($errors['UserPassword']) ? "class='red'" : ""; ?>><?php if ($pagename != "Register User") { ?>New <?php } ?>Password:</label>
  </dt>
  <dd>
   <input <?php echo (substr($pagename, strlen($pagename)-13, 13) == "Register User")? 'validation="required"' : ''; ?> type="password" name="UserPassword" id="UserPassword" />
  </dd>
  <dt>
   <label for="RetypePassword" <?php echo isset($errors['RetypePassword']) ? "class='red'" : ""; ?>>Retype:</label>
  </dt>
  <dd id='before_projects'>
   <input <?php echo strip_tags($pagename) == "Register User" ? 'validation="required"' : ''; ?> type="password" name="RetypePassword" id="RetypePassword" />
  </dd>
  <?php if (isset($projects) && count($projects) > 0) { ?>
   <?php foreach($projects as $project) { ?>
    <dt class='projects'><label for="<?php echo $project['ProjectId']; ?>"><?php echo $project['ProjectName']; ?></label><span class='description'>Permission</span></dt>
    <dd class='projects'><input id="<?php echo $project['ProjectId']; ?>" type="checkbox" <?php echo (isset($permissions) && in_array($project['ProjectId'], $permissions)) || !isset($permissions) ? 'checked="checked"' : '' ?> name="permit[<?php echo $project['ProjectId']; ?>]" /></dd>
   <?php } ?>
  <?php } ?>
  <dt>
   &nbsp;
   <input type="hidden" name="UserId" value="<?php echo isset($user['UserId']) ? $user['UserId'] : ''; ?>" />
  </dt>
  <dd>
   <input type="button" value="Cancel" onClick="history.go(-1)" />
   <input type="submit" value="Save" name="submit" />
 </dl>
</form>
<?php include 'footer.php'; ?>