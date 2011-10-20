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
	<?php $c=0; foreach ($clients as $client) { $c = $c == 3 ? 1 : $c+1; ?>
		<td>
			<a href="<?php echo base_url().index_page() ?>client/open/<?php echo $client['CompanyId']; ?>">
				<img class="client thumb" src="<?php echo base_url(); ?>images/clients/<?php echo file_exists(APPPATH."../../images/clients/".$client['CompanyName']."") ? $client['CompanyName'] : "nopic.jpg"; ?>" />
			</a>
			<br />
			<em><a href="<?php echo base_url().index_page() ?>client/open/<?php echo $client['CompanyId']; ?>"><?php echo $client['CompanyName']; ?></a></em>
		</td>
	<?php if ($c == 3 && $c < count($clients)) { ?></tr><tr><?php } } ?>
	</tr>
</table>
<?php include 'footer.php'; ?>