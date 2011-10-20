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
<form action="<?php echo base_url().index_page() ?>project/save<?php echo isset($project['ProjectId']) ? '/'.$project['ProjectId'] : ''; ?>" method="post">
 <dl>
  <dt>
   <label for="ProjectName">Project Name:</label>
  </dt>
  <dd>
   <input validation="required" id="ProjectName" name="ProjectName" value="<?php echo isset($formdata['ProjectName']) ? $formdata['ProjectName'] : ''; ?>" />
  </dd>
 </dl>
 <br style="clear: both;" />
 <h3>
  Permissions
 </h3>
 <p>
  The users below are associated with <?php echo $CompanyName; ?>. Please select which ones you wish to be allowed to view this project.
 </p>
 <ul style="width: 300px" id="permissions_list">
  <?php foreach ($users as $user) { ?>
    <li>
    <input id="<?php echo $user['UserId']; ?>" type="checkbox" name="permit[<?php echo $user['UserId']; ?>]" <?php if (isset($formdata['permit'][$user['UserId']])) { if ($formdata['permit'][$user['UserId']]) { echo "CHECKED"; }} ?> />
  	
     <span><?php echo $user['UserName']; ?></span>
    
  <?php } ?>
 </ul>
 <dl>
  <dt>
   &nbsp;
   <input type="hidden" name="CompanyId" value="<?php echo $CompanyId; ?>" />
  </dt>
  <dd>
   <input type="button" value="Cancel" onClick="history.go(-1)" />
   <input type="submit" value="Save" name="submit" />
  </dd>
 </dl>
</form>
<?php include 'footer.php'; ?>