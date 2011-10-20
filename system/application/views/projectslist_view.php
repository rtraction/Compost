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
<table>
	<tr>
	<?php $c=0; foreach($projects as $project) { $c = $c == 3 ? 1 : $c+1; ?>
		<td>
			<a class='thumb' href="<?php echo base_url().index_page() ?>project/open/<?php echo $project['ProjectId']; ?>">
				<img class="project thumb" src="<?php echo base_url().$project['RevisionUrl']; ?>" />
			</a>
			<br />
			<a href="<?php echo base_url().index_page() ?>project/open/<?php echo $project['ProjectId']; ?>"><?php echo $project['ProjectName']; ?></a>
		</td>
	<?php if ($c == 3 && $c < count($projects)) { ?></tr><tr><?php }} ?>
	</tr>
</table>
<?php include 'footer.php'; ?>