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
$().ready(function(){
	//Create javascript tooltips
	$("label[title], a[title], .star[title]").qtip({
		style: { name: 'blue', tip: true }
	});
	
	/**
	 * rating - turn all '.star' inputs into a rating system
	 */
	$("input.star").rating();
	
	/**
	 * Function: Click event for rating stars
	 * Description: Saves the new rating into the database via an Ajax call.
	 */
	$(".star-rating-live").click(function(){
		$("#ratingdescription").text("The rating shown above is your rating.");
		$.ajax({
			type: 'POST',
			url: base_url+index_page+'/ajax/rate/',
			data: 'compid='+$("#composition").attr("class")+'&rating='+$(this).children('a').text(),
			success: function(msg) {
				//do nothing
			},
			error: function(msg) {
				alert('An error occurred.');
			}
		});
	});
	
	/**
	 * Function: Click event for 'rating cancel' icon
	 * Description: Deletes a rating from the database via an Ajax call.
	 */
	$(".rating-cancel").click(function(){
		$("#ratingdescription").text("The rating shown above is your rating.");
		$.ajax({
			type: 'POST',
			url: base_url+index_page+'/ajax/clear_rating/',
			data: 'compid='+$("#composition").attr("class"),
			success: function(msg) {
				//do nothing
			},
			error: function(msg) {
				alert('An error occurred.');
			}
		});
	});
	
	$("input.color").each(function(){
		var $this = $(this);
		$(this).ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onHide: function() {
				$('#pagebg').css('background-color', '#'+$this.val());
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			},
			onChange: function(hsb, hex, rgb) {
				$this.val(hex);
			}
		}).bind('keyup', function(){
			$('#pagebg').css('background-color', "#"+this.value);
		});
	});
	
	
	/* Form Validation */
	$("form").submit(function(){
		//clear errors
		$(".error").remove();
		var valid = true;
		$("input").each(function(){
			if($(this).attr("validation") == "required" && $(this).val() == "") {
				var id = "";
				id = $(this).attr("id");
				var label = "";
				label = $("label[for="+id+"]").text();
				var length = "";
				length = $("label[for="+id+"]").text().length;
				if(label.charAt(length-1) == ":") {
					label = label.substr(0, length - 1);
				}
				$("<span class='red error'><p>Please enter "+label.toLowerCase()+"</p></span>").insertBefore("form");
				valid = false;
			}
		});
		
		$("select").each(function(){
			if($(this).attr("validation") == "required" && $(this).val() == "")
			{
				var id = "";
				id = $(this).attr("id");
				var label = "";
				label = $("label[for="+id+"]").text();
				var length = "";
				length = $("label[for="+id+"]").text().length;
				if(label.charAt(length-1) == ":")
				{
					label = label.substr(0, length - 1);
				}
				
				$("<span class='red error'><p>Please select "+label.toLowerCase()+"</p></span>").insertBefore("form");
				valid = false;
			}
		});
		
		$("textarea").each(function(){
			if($(this).attr("validation") == "required" && $(this).val() == "")
			{
				var id = "";
				id = $(this).attr("id");
				var label = "";
				label = $("label[for="+id+"]").text();
				var length = "";
				length = $("label[for="+id+"]").text().length;
				if(label.charAt(length-1) == ":")
				{
					label = label.substr(0, length - 1);
				}
				
				$("<span class='red error'><p>Please enter "+label.toLowerCase()+"</p></span>").insertBefore("form");
				valid = false;
			}
		});
		
		return valid;
	});
}); //END DOCUMENT.READY
