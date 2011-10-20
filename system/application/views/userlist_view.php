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
<table>
	<tr>
	<?php $c=0; foreach ($users as $user) { $c = $c == 3 ? 1 : $c+1; ?>
		<td>
			<a href="<?php echo base_url().index_page() ?>users/open/<?php echo $user['UserId']; ?>">
				<img src="<?php echo base_url().$user['thumbnail']; ?>" />
			</a>
			<br />
			<em><a href="<?php echo base_url().index_page() ?>users/open/<?php echo $user['UserId']; ?>"><?php echo $user['UserName']; ?></a></em>
		</td>
	<?php if ($c == 3 && $c < count($users)) { ?></tr><tr><?php } } ?>
	</tr>
</table>
<?php include 'footer.php'; ?>