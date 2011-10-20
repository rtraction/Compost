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
<br />
<strong class="floatleft"><?php echo $_SESSION['userrole'] == "-1" ? 'Design Rating' : 'Rate this Design'; ?>: &nbsp;</strong>
<div class="floatleft">
<input title="This design is mildly interesting." value="1" <?php echo $_SESSION['userrole'] == "-1" ? 'disabled="disabled"' : ''; echo $comp['Rating'] >= '1' && $comp['Rating'] < 2 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
<input title="This design has some good features." value="2" <?php echo $_SESSION['userrole'] == "-1" ? 'disabled="disabled"' : ''; echo $comp['Rating'] >= '2' && $comp['Rating'] < 3 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
<input title="This design is really nice." value="3" <?php echo $_SESSION['userrole'] == "-1" ? 'disabled="disabled"' : ''; echo $comp['Rating'] >= '3' && $comp['Rating'] < 4 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
<input title="This design is great!" value="4" <?php echo $_SESSION['userrole'] == "-1" ? 'disabled="disabled"' : ''; echo $comp['Rating'] >= '4' && $comp['Rating'] < 5 ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
<input title="This design is THE ONE." value="5" <?php echo $_SESSION['userrole'] == "-1" ? 'disabled="disabled"' : ''; echo $comp['Rating'] == '5' ? 'checked="checked"' : ''; ?> name='rating-<?php echo $comp['CompId']; ?>' type="radio" class="star" />
</div>
<br class="clear" />
<span id="ratingdescription" class="description">
<?php if (isset($_SESSION['userrole']) && $_SESSION['userrole'] == '-1') {
 echo 'The scale above shows the average of '.$comp['TotalRatings'].' ratings.';
} else {
 echo 'The rating shown above is your rating.';
}?>
</span>
<br class="clear" />
<div id="composition" class="<?php echo $comp["CompId"]; ?>">
 <div id="toolbar">
  <a title="Fullscreen Preview" id="fullscreen" target="_blank" href="<?php echo base_url().index_page() ?>fullscreen/open/<?php echo $comp['CompId']; ?>/">Fullscreen Preview</a>
  <a title="Show/Hide Notes" id="toggle">Show/Hide Notes</a>
  <a title="Drag this to add a note!" id="newannotation">Create a Note</a>
 </div>
 
 <img id="thecomp" src="<?php echo base_url(); ?>images/comps/<?php echo $comp['RevisionUrl']; ?>" />
 <?php
  //load in the existing annotations here
  $count = 0;
  foreach ($annotations as $annotation) { $count++; ?>
   <div style='position: abolute; top: <?php echo $annotation["AnnotationY"]; ?>px; left: <?php echo $annotation["AnnotationX"]; ?>px;'
    class='annotation <?php echo $annotation["UserId"] == "-1" ? "" : " client"; ?> author_<?php echo $annotation["UserId"]; ?> AnnotationId_<?php echo $annotation["AnnotationId"]; ?>' id='<?php echo $count; ?>' onClick='annotationClick("<?php echo $count; ?>")'>
    <?php echo count($annotation['Comments']) > 0 ? "<span class='number'>".count($annotation['Comments'])."</span>" : ""; ?>
    <p class="view_ann"><?php echo nl2br($annotation["AnnotationText"]); ?></p>
    <a href='<?php echo base_url().index_page() ?>discussion/open/<?php echo $comp['CompId']; ?>#<?php echo $annotation['AnnotationId']; ?>'><span class='comments'><?php echo count($annotation["Comments"]); ?></span> comments - Click to discuss</a>
    <a class='ok' onClick='closeannotations(<?php echo $count; ?>, <?php echo $annotation['AnnotationId']; ?>);'><img src='<?php echo base_url(); ?>images/check.gif' />OK</a>
    <?php if ($_SESSION["userid"] == -1 || ($_SESSION['userid'] == $annotation['UserId'] && count($annotation["Comments"]) == 0)) { ?>
    <a class='delete' onClick='deleteannotation("<?php echo $annotation['AnnotationId']; ?>");'>Delete</a>
    <a class='edit' onClick='editannotation(<?php echo $count; ?>, <?php echo $annotation['AnnotationId']; ?>);'>Edit</a>
	<?php } ?>
   </div>
 <?php } ?>
 
</div>

<?php include 'footer.php'; ?>
