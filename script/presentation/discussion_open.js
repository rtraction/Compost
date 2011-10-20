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
$(document).ready(function(){
	/**
	 * css: resize the annotation images
	 */
	$('.clip img').css('width', 731/1.2+'px');
	
	/**
	 * make the annotation clips draggable
	 */
	$('.clip img').draggable();
	
	/**
	 * Function: for each annotation clipping
	 * Description: Position the annotation clips according to their x,y title attribute
	 */
	$('.clip').each(function(){
		var pos = $(this).attr('title').split(',');
		pos[0] -= 266 - 38;
		pos[1] -= 455 - 76; //page diff and annotation size diff
		var newx = ((-pos[0])/1.2) -124;//+ 100;
		var newy = ((-pos[1])/1.2) -276;//+ 80;
		$(this).children('img').css('top', newy+'px');
		$(this).children('img').css('left', newx+'px');
	});
}); //END DOCUMENT.READY