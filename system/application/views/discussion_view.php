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
<ul id="discussion">
 <?php foreach ($annotations as $annotation) { ?>
  <li id="<?php echo $annotation['AnnotationId']; ?>" <?php echo count($annotation['comments']) > 0 ? "class='expanded'" : ""; ?>>
   <strong><?php echo substr($annotation['AnnotationText'], 0, 15); ?></strong>
   - <?php echo $annotation['author']['UserName']; ?>,
   <span class="grey"><?php echo !empty($annotation['relativedate']) ? "Last Updated ".$annotation['relativedate'] : ""; ?></span>
   <br />
   <div class="clip" title="<?php echo $annotation['AnnotationX']; ?>,<?php echo $annotation['AnnotationY']; ?>">
    <img src="<?php echo base_url(); ?>images/comps/<?php echo $comp['RevisionUrl']; ?>" />
   </div>
   <p>
    <?php echo $annotation['AnnotationText']; ?>
   </p>
   <ul>
   <?php if (count($annotation['comments']) > 0) { ?>
     <?php foreach($annotation['comments'] as $comment) { ?>
      <li>
       <?php if($comment['Unread']) { ?><img src="<?php echo base_url(); ?>images/star_on.gif" /><?php } ?> <strong><?php echo $comment['author']['UserName']; ?></strong>,
       <span class="grey"><?php echo $comment['relativedate']; ?></span>
       <p>
        <input title="This part of the design needs some work." value="1" <?php echo $comment['CommentRating'] == '1' ? 'checked="checked"' : ''; ?> disabled='disabled' name='CommentRating-<?php echo $comment['CommentId']; ?>' type="radio" class="star" />
        <input title="This part of the design is okay." value="2" <?php echo $comment['CommentRating'] == '2' ? 'checked="checked"' : ''; ?> disabled='disabled' name='CommentRating-<?php echo $comment['CommentId']; ?>' type="radio" class="star" />
        <input title="This part of the design is good." value="3" <?php echo $comment['CommentRating'] == '3' ? 'checked="checked"' : ''; ?> disabled='disabled' name='CommentRating-<?php echo $comment['CommentId']; ?>' type="radio" class="star" />
        <input title="This part of the design is really nice!" value="4" <?php echo $comment['CommentRating'] == '4' ? 'checked="checked"' : ''; ?> disabled='disabled' name='CommentRating-<?php echo $comment['CommentId']; ?>' type="radio" class="star" />
        <input title="This part of the design is pure genius!" value="5" <?php echo $comment['CommentRating'] == '5' ? 'checked="checked"' : ''; ?> disabled='disabled' name='CommentRating-<?php echo $comment['CommentId']; ?>' type="radio" class="star" />
        &nbsp; &nbsp;
        <?php echo $comment['CommentBody']; ?>
       </p>
      </li>
     <?php } ?>
   <?php } ?>
    <li class="reply">
     <form action="<?php echo base_url().index_page() ?>discussion/reply/<?php echo $comp['CompId']; ?>" method="post">
      <input title="This part of the design needs some work." value="1" name='CommentRating' type="radio" class="star" />
      <input title="This part of the design is okay." value="2" name='CommentRating' type="radio" class="star" />
      <input title="This part of the design is good." value="3" name='CommentRating' type="radio" class="star" />
      <input title="This part of the design is really nice!" value="4" name='CommentRating' type="radio" class="star" />
      <input title="This part of the design is pure genius!" value="5" name='CommentRating' type="radio" class="star" />
      <span class='description'>&nbsp; Rate this part of the design and leave a comment:</span>
      <textarea cols="47" name="comment"></textarea>
      <input type="hidden" name="annotation" value="<?php echo $annotation['AnnotationId']; ?>" />
      <br />
      <input id="reply" type="submit" value="Reply" name="repy" />
     </form>
    </li>
   </ul>
  </li>
 <?php } ?>
</ul>
<?php include 'footer.php'; ?>