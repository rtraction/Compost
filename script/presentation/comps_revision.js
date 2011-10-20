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
	$(".bgrepeat").click(function(){
		$(".bgrepeat img").css("border", "2px solid white");
		$(this).children("img").css("border", "2px solid black");
		
		var options = $(this).prev("input").attr("title");
		options = options.split(";");
		$("#pagepreview #pagebg").css("background-repeat", options[0]);
		if (options[1] != undefined) {
			$("#pagepreview #pagebg").css("background-position", options[1]);
		}
	});
	
	$(".pagefloat").click(function(){
		$(".pagefloat img").css("border", "2px solid white");
		$(this).children("img").css("border", "2px solid black");
		
		$("#pagepreview #previewpage").css("background-position", $(this).prev("input").attr("title"));
	});
	
	$("#RevisionBackgroundColour").ColorPicker("destroy").ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(this).val(hex);
			$('#pagebg').css('background-color', "#"+hex);
			$(this).ColorPickerHide();
		},
		onClose: function(hsb, hex, rgb, el) {
			$(this).val(hex);
			$('#pagebg').css('background-color', "#"+hex);
			$(this).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function(hsb, hex, rgb) {
			$(this).val(hex);
			$('#pagebg').css('background-color', "#"+hex);
		}
	});
	
	/* Ajax Upload */
	if ($("#RevisionUrl").length > 0) {
		new Ajax_upload('#RevisionUrl', {
			//action: 'upload.php',
			action: base_url+index_page+'ajax/upload/RevisionUrl',
			name: 'RevisionUrl',
			onChange: function(file, extension){
				$("#previewpage").css("background-image", "none");
				$("#pagepreview").addClass("loading");
			},
			onComplete : function(file, response){
				if (response != "fail") {
					response = response.split(";");
					$("#theRevisionUrl").val(response[0]);
					response[1] = parseInt(response[1]) + 100;
					$('#previewpage').css('background-image', 'url('+base_url+response[0]+')');
					//Default Page Position = Centered
					$("input#Centre").attr("checked", "checked");
					$(".pagefloat img").css("border", "2px solid white");
					$("input#Centre").next("label").children("img").css("border", "2px solid black");
					//$("#pagefloattext").text($("input#Centre").next("label").attr("title"));
					$("#pagepreview #previewpage").css("background-position", $("input#Centre").attr("title")).css("height", response[1]+"px");
					$("#theRevisionUrl").next(".description").removeClass("red");
					//Default background pattern = centred at top
					$("input#TopCentre").attr("checked", "checked");
					$(".bgrepeat img").css("border", "2px solid white");
					$("input#TopCentre").next("label").children("img").css("border", "2px solid black");
					$("#bgrepeattext").text($("input#TopCentre").next("label").attr("title"));
					$("#pagepreview #previewpage").css("background-repeat", $("input#TopCentre").attr("title")).css("height", "top center");
				} else {
					$("#theRevisionUrl").next(".description").addClass("red");
				}
				$("#pagepreview").removeClass("loading");
			}
		});
	}
	if ($("#RevisionBackgroundImage").length > 0) {
		new Ajax_upload('#RevisionBackgroundImage', {
			//action: 'upload.php',
			action: base_url+index_page+'ajax/upload/RevisionBackgroundImage',
			name: 'RevisionBackgroundImage',
			onChange: function(file, extension){
				$("#pagepreview").addClass("loading");
			},
			onComplete : function(file, response){
				if (response != "fail") {
					response = response.split(";");
					$("#theRevisionBackgroundImage").val(response[0]);
					$('#pagebg').css('background-image', 'url('+base_url+response[0]+')');
					$("#pagepreview").removeClass("loading");
					if (response[1] == "tile") {
						$("input#Tile1").attr("checked", "checked");
						$(".bgrepeat img").css("border", "2px solid white");
						$("input#Tile1").next("label").children("img").css("border", "2px solid black");
						$("#bgrepeattext").text($("input#Tile1").next("label").attr("title"));
						$("#pagepreview #pagebg").css("background-repeat", "repeat");
						$("#pagepreview #pagebg").css("background-position", "top left");
					} else if (response[1] == "repeat-y") {
						$("input#Horizontal1").attr("checked", "checked");
						$(".bgrepeat img").css("border", "2px solid white");
						$("input#Horizontal1").next("label").children("img").css("border", "2px solid black");
						$("#bgrepeattext").text($("input#Horizontal1").next("label").attr("title"));
						$("#pagepreview #pagebg").css("background-repeat", "repeat-y");
						$("#pagepreview #pagebg").css("background-position", "top left");
					} else { // if (response[1] == "repeat-x") {
						$("input#Vertical1").attr("checked", "checked");
						$(".bgrepeat img").css("border", "2px solid white");
						$("input#Vertical1").next("label").children("img").css("border", "2px solid black");
						$("#bgrepeattext").text($("input#Vertical1").next("label").attr("title"));
						$("#pagepreview #pagebg").css("background-repeat", "repeat-x");
						$("#pagepreview #pagebg").css("background-position", "top left");
					}
				}
			}
		});
	}
	
	$("input#CompName").keyup(function(){
		$('#pagepreview #previewname').text("- "+$(this).val());
	}).keyup();
	
	$("a#removebackground").click(function(){
		$("#theRevisionBackgroundImage").val("");
		$("#pagepreview #pagebg").css("background-image", "");
	});
	
	/* "Create Comp" big preview */
	$("#previewbutton").click(function(){
		//we need to validate
		var valid = true;
		if ($("#theRevisionUrl").val().length == 0) {
			valid = false;
			$("#theRevisionUrl").next(".description").addClass("red");
		}
		//...
		if (valid == true) {
			$("#bigpreview").show();
			var pagebgimage = $("#pagebg").css("background-image");
			$("#bigpreview")
				.css("background-image", pagebgimage)
				.css("background-color", $("#pagebg").css("background-color"))
				.css("background-position", $("#pagebg").css("background-position"))
				.css("background-repeat", $("#pagebg").css("background-repeat"));
			if($("#bigpreview").css("background-color") == "transparent") {
				$("#bigpreview").css("background-color", "white");
			}
			$("#bigpreviewpage")
				.css("background-image", $("#previewpage").css("background-image"))
				.css("background-position", $("#previewpage").css("background-position"))
				.css("background-repeat", $("#previewpage").css("background-repeat"))
				.css("height", "100%")
				.css("width", "100%");
			var pageimage = $("#bigpreviewpage").css("background-image");
			pageimage = pageimage.split("_thumb");
			$("#bigpreviewpage").css("background-image", pageimage[0]+pageimage[1]);
			//scroll to the top of the page
			$.scrollTo("#header", 300, {onAfter:function(){ $("<div class='red'>Do not click the 'back' button!</div>").dialog({title: "Design Preview", buttons: { "Close Preview" : function(){ $("#bigpreview").click(); } }, position: 'center', dialogClass: 'alert'}); } });
		} else {
			$.scrollTo(".red", 300);
		}
	});
	
	$("#bigpreview").click(function(){
		$(this).hide();
		$("*").dialog("close");
		//scroll to the bottom of the page
		$.scrollTo("#footer", 300);
	});
}); //END DOCUMENT.READY