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

<form action="<?php echo base_url().index_page() ?>login/resend" method="post">
 <dl>
  <dt><label for="username" <?php echo isset($errors['username']) ? "class='red'" : ""; ?>>Username:</label></dt>
  <dd><input validation="required" type="text" name="username" id="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ""; ?>" /></dd>
  <dt><label for="email" <?php echo isset($errors['email']) ? "class='red'" : ""; ?>>Email Address:</label></dt>
  <dd>
   <input validation="required" type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>" id="email" />
   <span class="description">
    This must be the same email address stored in our records.
   </span>
  </dd>
  <dt>&nbsp;</dt>
  <dd>
   <input type="submit" value="Email Me!" id="loginbutton" />
  </dd>
 </dl>
</form>

<?php include 'footer.php'; ?>