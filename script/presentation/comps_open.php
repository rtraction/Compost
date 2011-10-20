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
	 * Function: For each annotation
	 * Description: Enable dragging for annotations that we have permission to drag.
	 */
	$('.annotation').each(function(){
		var thisannotation = $(this);
		//start drag IF we are allowed to
		//get the annotationid and userid
		var classes = $(this).attr("class");
		var AnnotationId;
		var UserId;
		classes = classes.split(" ");
		
		for (var i in classes) {
			if (classes[i].length > 1) {
				var pair = classes[i].split("_");
				if (pair[0] == "author") {
					UserId = pair[1];
				}
				if (pair[0] == "AnnotationId") {
					AnnotationId = pair[1];
				}
			}
		}
		//enable dragging if we are allowed
		<?php if (isset($_SESSION['userid'])) { ?>
			if (UserId == <?php echo $_SESSION["userid"]; ?> || <?php echo $_SESSION["userid"]; ?> == -1) {
				var origpos = thisannotation.position();
				$(this).draggable(draggableannotation(thisannotation, UserId, AnnotationId));
			}
		<?php } ?>
	});
	
	/**
	 * count - number of annotations
	 */
	var count = $(".annotation").size();
	
	/**
	 * Function: For each annotation
	 * Description: 
	 */
	//what is the biggest one?
	$(".annotation").each(function(){
		var thisid = $(this).attr("id");
		if (parseFloat(thisid) > count) { count = thisid; }
	});
	
	var draggableoptions = {
		cursorAt: { left: 25, top: 22 },
		helper: function(){
			return $('<img id="annotationcursor" src="'+base_url+'images/annotation.png" />');
		},
		start: function(event, ui) {
			$(".annotation").show("slow");
		},
		stop: function(event, ui){
			count++;
			//get the position of the #composition div
			var divpos = $("#composition").offset(); 
			$("<div class='annotation author_<?php echo $_SESSION['userid']; ?><?php if($_SESSION["userrole"] != -1) { echo " client"; } ?>' id='"+count+"' onClick='annotationClick("+count+")'><a class='ok' onClick='closeannotations("+count+");'><img src='"+base_url+"images/check.gif' />OK</a><a class='delete'>Delete</a><a class='edit'>Edit</a></div>")
				.css("position", "absolute")
				.css("top", event.pageY - 20 - divpos.top)
				.css("left", event.pageX- 28 - divpos.left)
				.appendTo("#composition")
				.draggable({
					stop: function(){
					}
				});
			//open up the new annotation
			annotationClick(count);
		}
	}
	$("#thecomp").draggable(draggableoptions);
	$("#newannotation").draggable(draggableoptions);
	$("#toggle").click(function(){
		$(".annotation").toggle('slow');
		dragging = false;
	});
	<?php if($_SESSION["userrole"] == -1) {?>
		//edit page name
		$("h2").hover(function(){
			//on
			compname_mouseover($(this));
		}, function(){
			//off
			compname_mouseout($(this));
		});
		$("h2").click(function(){
			if($("#thecomp").length > 0) {
				compname_edit($(this));
			}
		});
		//edit comp description
		$("#description").hover(function(){
			//on
			compdescription_mouseover($(this));
		}, function(){
			//off
			compdescription_mouseout($(this));
		});
		$("#description").click(function(){
			if($("#thecomp").length > 0) {
				compdescription_edit($(this));
			}
		});
	<?php } ?>
	
	$("#revisionhistory").change(function(){
		//$(this).val() is the revision id
		var RevisionId = $(this).val();
		if (RevisionId == "- Revision History -") {
			RevisionId = $("#revisionhistory option[value="+$(this).val()+"]").next("option").attr("value");
		}
		$("a#fullscreen").attr("href", base_url+"fullscreen/open/"+$("#composition").attr("class")+"/"+RevisionId);
		$.ajax({
			type: 'POST',
			url: base_url+index_page+'/ajax/get_revision',
			data: 'RevisionId='+RevisionId,
			success: function(msg){
				//msg is the revisionurl
				if(msg.length > 0) {
					$('#thecomp').attr('src', base_url+'images/comps/'+msg);
				}
			}
		});
	});
	
	//Move Comp from Archive
	$('#movecomp').change(function(){
		if ($(this).val() != undefined && $(this).val() != "" && $(this).val() != "- Assign To Project -") {
			$.ajax({
				type: 'POST',
				url: base_url+index_page+'/ajax/movecomp',
				data: 'CompId='+$("#composition").attr("class")+'&ProjectId='+$(this).val(),
				success: function(msg){
					//successfully moved. redirect to the project
					window.location = "/project/open/"+msg;
				}
			});
		}
	});
}); //END DOCUMENT.READY

/**
 * Function: draggableannotation
 * Parameters:
 *	thisannotation - the annotation object being dragged
 *	UserId - the ID of the user doing the dragging
 *	AnnotationId - the ID of the annotation being dragged.
 * Description: This function defines the draggableness of an annotation. Upon dropping,
 *	an ajax request is made to save the current position.
 * Returns: not really sure
 */
function draggableannotation(thisannotation, UserId, AnnotationId) {
	return {
		distance: 20,
		start: function(event, ui) {
		},
		stop: function(event, ui) {
			//call the save function
			var divpos = $("#composition").offset(); 
			var x = ui.absolutePosition.left - divpos.left;
			var y = ui.absolutePosition.top - divpos.top;
			$.ajax({
				type: "POST",
				url: base_url+index_page+"/ajax/annotation_move",
				data: "UserId="+UserId+"&AnnotationId="+AnnotationId+"&x="+x+"&y="+y,
				success: function(msg){
					if (msg == "fail") {
						//didn't save
						//spring back to original position
						thisannotation.animate({left:origpos.left, top:origpos.top }, 100);
					}
				}
			});
		}
	}
}

function compname_mouseover(compname){
	if ($("#thecomp").length > 0) {
		compname.css('border', '1px solid #ccc').css('padding', '0px');
	}
}
function compname_mouseout(compname){
	compname.css('border', 'none').css('padding', '1px');
}

function compdescription_mouseover(compdescription){
	if ($("#thecomp").length > 0) {
		compdescription.css('border', '1px solid #ccc').css('padding', '0px');
	}
}
function compdescription_mouseout(compdescription){
	compdescription.css('border', 'none').css('padding', '1px');
}

function compname_keydown(e){
	if (e.keyCode == 13) {
		//enter was pressed
		$("#pressenter").remove();
		var theh2 = $("<h2 onClick='compname_edit($(this));' onmouseout='compname_mouseout($(this));' onmouseover='compname_mouseover($(this));'></h2>");
		var compname = $("input.compname");
		theh2.text(compname.val());
		compname.replaceWith(theh2);
		//AJAX save the new name
		$.ajax({
			type: "POST",
			url: base_url+index_page+"/ajax/edit_compname",
			data: "compid="+$("#composition").attr("class")+"&name="+compname.val(),
			success: function(msg){
				//do nothing
			},
			error: function(msg){
				alert("An error occurred.");
			}
		});
	} else if (e.keyCode == 27) {
		//escape was pressed
		compname_cancel();
	}
}

function compdescription_keydown(e){
	if (e.keyCode == 13) {
		//enter was pressed
		$("#pressenter").remove();
		var thedescription = $("<p id='description' onClick='compdescription_edit($(this));' onmouseout='compdescription_mouseout($(this));' onmouseover='compdescription_mouseover($(this));'></p>");
		var compdescription = $("input.compdescription");
		thedescription.text(compdescription.val());
		compdescription.replaceWith(thedescription);
		//AJAX save the new description
		$.ajax({
			type: "POST",
			url: base_url+index_page+"/ajax/edit_compdescription",
			data: "compid="+$("#composition").attr("class")+"&description="+compdescription.val(),
			success: function(msg){
				//do nothing
			},
			error: function(msg){
				alert("An error occurred.");
			}
		});
	} else if (e.keyCode == 27) {
		//escape was pressed
		compdescription_cancel();
	}
}

function compname_cancel(){
	var theh2 = $("<h2 onClick='compname_edit($(this));' onmouseout='compname_mouseout($(this));' onmouseover='compname_mouseover($(this));'></h2>");
	var compname = $("input.compname");
	$("#pressenter").remove();
	theh2.text(compname.attr("title"));
	compname.replaceWith(theh2);
}

function compdescription_cancel(){
	var thedescription = $("<p id='description' onClick='compdescription_edit($(this));' onmouseout='compdescription_mouseout($(this));' onmouseover='compdescription_mouseover($(this));'></p>");
	var compdescription = $("input.compdescription");
	$("#pressenter").remove();
	thedescription.text(compdescription.attr("title"));
	compdescription.replaceWith(thedescription);
}

function compname_edit(compname){
	var theinput = $("<input class='compname' maxlength='40' onblur='compname_cancel()' onKeyDown='compname_keydown(event)' title=\""+compname.text().replace(/\"/g, "''")+"\" value=\""+compname.text().replace(/\"/g, "''")+"\" /><div id='pressenter' class='description'>Press 'enter' to save changes</div>");
	compname.replaceWith(theinput);
	theinput.select();
}

function compdescription_edit(compdescription){
	var theinput = $("<input class='compdescription' onblur='compdescription_cancel()' onKeyDown='compdescription_keydown(event)' title=\""+compdescription.text().replace(/\"/g, "''")+"\" value=\""+compdescription.text().replace(/\"/g, "''")+"\" /><div id='pressenter' style='margin-top: -29px' class='description'>Press 'enter' to save changes</div>");
	compdescription.replaceWith(theinput);
	if (ie == 1) {
		$("#pressenter").css("margin-top", "-24px").css("margin-bottom", "10px").css("margin-right", "300px");
	}
	theinput.select();
}

function annotationClick(idnum){
	var annotation = $("#"+idnum);
	if (!annotation.hasClass("recentlyclosed")) {
		$(".expanded").removeClass("expanded");
		var paragraph = $("#"+idnum+" p");
		annotation.addClass("expanded");
		
		//lose the dragability
		//annotation.draggable('disable');
		//if there is no text, create a textbox
		if (paragraph.html() == null) {
			$("<p class='edit_new'><textarea onkeydown='textareakeydown(event, "+idnum+")' cols='40'></textarea></p>").prependTo(annotation);
			$("#"+idnum+" a.delete").text("");
			$("#"+idnum+" a.edit").text("");
			$("#"+idnum+" textarea").focus();
			$("#"+idnum+" .ok").before("<a class='cancel' onClick='closeannotation("+idnum+");'>CANCEL</a>");
		}
	} else {
		$(".recentlyclosed").removeClass("recentlyclosed");
	}
}

function textareakeydown(event, idnum) {
	if(event.keyCode == 27) {
		//cancel
		closeannotation(idnum);
	}
}

function closeannotation(idnum){
	$("#"+idnum).remove();
}
function cancelcreate(idnum){
	$("#"+idnum).remove();
}

function closeannotations(idnum) {
	//save any textareas with text
	annotation = $("#"+idnum);
	//re-enable the draggable
	annotation.draggable('enable');
	paragraph = $("#"+idnum+" p");
	textarea = $("#"+idnum+" p textarea");
	
	if (textarea.val() != "" && textarea.val() != null) {
		var ptext = textarea.val();
		paragraph.html(ptext.replace(/\n/g, '<br />'));
		textarea.remove();
	}
	if (textarea.val() != null && textarea.val() != "") {
		//save it to the database
		var pos = annotation.position();
		var annotationtext = paragraph.html();
		if($("#"+idnum+" p.edit_ann")[0]) {
			var classes = $("#"+idnum).attr('class').split("AnnotationId_"); //get the classes
			var annid = classes[1].split(" ")[0];
			$("#"+idnum+" p").removeClass("edit_ann");
			$("#"+idnum+" p").addClass("view_ann");
	 		$.ajax({
				type: "POST",
				url: base_url+index_page+"/ajax/set_annotation",
				data: "annid="+annid+"&text="+annotationtext+"&compid="+$("#composition").attr("class")+"&x="+pos.left+"&y="+pos.top,
				success: function(msg){
					//msg should be True or False
				}
			});
			$("#"+idnum+" p").removeClass("edit_ann");
			$("#"+idnum+" p").addClass("view_ann");
		} else {
			$("#"+idnum+" .cancel").remove();
			$("#"+idnum+" a.delete").text("Delete");
			$("#"+idnum+" a.edit").text("Edit");
			$.ajax({
				type: "POST",
				url: base_url+index_page+"/ajax/save_annotation",
				data: "id="+idnum+"&text="+annotationtext+"&compid="+$("#composition").attr("class")+"&x="+pos.left+"&y="+pos.top,
				success: function(msg){
					//msg should be the AnnotationId of the new annotation
					$("#"+idnum).addClass("AnnotationId_"+msg);
					$("#"+idnum+" a.ok").before(
						"<a class='discussit' href='"+base_url+"discussion/open/"+$("#composition").attr("class")+"#"+msg+"'><span class='comments'>0</span> comments - Click to discuss</a>"
					);
					//  $("#"+idnum+" a.delete").attr("onClick", "deleteannotation("+msg+");");
					// $("#"+idnum+" a.edit").attr("onClick", "editannotation("+idnum+", "+msg+");");
					$("#"+idnum+" a.delete").bind("click", function(){ deleteannotation(msg); });
					$("#"+idnum+" a.edit").bind("click", function(){ editannotation(idnum, msg); });
				}
			});
			$("#"+idnum+" p").removeClass("edit_new");
			$("#"+idnum+" p").addClass("view_ann");
		}
	}
	//close all open annotations
	$(".expanded").addClass("recentlyclosed");
	$(".expanded").removeClass("expanded");
}

function editannotation(idnum, annid){
	//get a copy of whatever is in the box
	var paragraph = $("#"+idnum+" p");
	var pText = paragraph.html();
	if(!paragraph.hasClass("edit_ann")) {
		//replace it with an editable textbox containing the previous annotation
		var styleP = pText.replace(/<br>/gi, '\r\n');
		paragraph.replaceWith("<p class='edit_ann'><textarea wrap='on' onkeydown='textareakeydown(event, "+idnum+")' cols='40'>"+styleP+"</textarea></p>");
	}
}

function deleteannotation(idnum) {
	//are you sure?
	if (confirm("Are you sure you want to delete this annotation, and all comments associated with it?")) {
		annotation = $(".annotation.AnnotationId_"+idnum);
		annotation.remove();
		$.ajax({
			type: "POST",
			url: base_url+index_page+"/ajax/delete_annotation",
			data: "id="+idnum+"&UserId=<?php echo $_SESSION['userid']; ?>",
			success: function(msg){
				
			}
		});
	}
}