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
<form action="<?php echo base_url().index_page().$formaction; ?>" method="post">
  <input type="button" name="notsure" value="Cancel" onClick="history.go(-1)" />
  <input type="submit" name="sure" value="Yes, <?php echo isset($action) ? $action : 'Delete'; ?>" />
</form>
<?php include 'footer.php'; ?>