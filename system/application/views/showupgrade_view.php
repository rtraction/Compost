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

<div> Current Version: <?php echo $version ?> </div>


<?php
if(isset($upgrade_to)){
	?>
<span class="red">
	<a href="<?php echo base_url() . index_page() . 'upgrade/'.$method; ?>"><?php echo $upgrade_to; ?></a>
</span>
<?php
} else {
	echo 'No Upgrades for your current version.';
}
?>

<?php include 'footer.php'; ?>