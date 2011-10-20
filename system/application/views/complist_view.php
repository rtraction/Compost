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
        <?php $c=0;
foreach($comps as $comp) {
    $c = $c == 3 ? 1 : $c+1; ?>
        <td>
            <a class='thumb' href="<?php echo base_url().index_page(); ?>comps/open/<?php echo $comp['CompId']; ?>" title="<?php echo str_replace('"', "''", $comp['CompDescription']); ?>">
                <img class="thumb" src="<?php echo base_url(); ?>images/comps/<?php echo $comp['RevisionUrl']; ?>" />
            </a>
            <br />
    <?php echo $comp['CompName']; ?>
            <br />
            <input title="This design is mildly interesting." disabled="disabled" value="1" <?php echo $comp['Rating'] >= '1' && $comp['Rating'] < 2 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
            <input title="This design has some good features." disabled="disabled" value="2" <?php echo $comp['Rating'] >= '2' && $comp['Rating'] < 3 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
            <input title="This design is really nice." disabled="disabled" value="3" <?php echo $comp['Rating'] >= '3' && $comp['Rating'] < 4 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
            <input title="This design is great!" disabled="disabled" value="4" <?php echo $comp['Rating'] >= '4' && $comp['Rating'] < 5 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
            <input title="This design is THE ONE." disabled="disabled" value="5" <?php echo $comp['Rating'] == '5' ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
            <br />
        </td>
    <?php if ($c == 3 && $c < count($comps)) { ?></tr><tr><?php }
}
?>
    </tr>
</table>
<?php include 'footer.php'; ?>