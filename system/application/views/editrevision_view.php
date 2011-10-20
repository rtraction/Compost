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
    <dt>
      <label for="RevisionUrl" <?php echo isset($errors['RevisionUrl']) ? "class='red'" : ""; ?>>Upload:</label>
    </dt>
    <dd>
      <input type="file" accept="image/jpeg" id="RevisionUrl" name="RevisionUrl" />
      <span class='description'>image files only, please</span>
    </dd>
    <dt>
      &nbsp;
      <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
      <input type="hidden" name="CompId" value="<?php echo $comp['CompId']; ?>" />
    </dt>
    <dd>
      <input type="button" name="cancel" value="Cancel" onClick="history.go(-1);" />
      <input type="submit" name="submit" value="Save" />
  </dl>
</form>
<?php include 'footer.php'; ?>