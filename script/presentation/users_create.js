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
	$("#CompanyListing").change(function(){
		//get the projects of the newly selected company
		$('.projects').remove();
		//$(this).val() is the companyid
		if ($(this).val().length > 0 && $(this).val() != "-1") {
			$.ajax({
				type: 'POST',
				url: base_url+index_page+'/ajax/getprojects',
				data: 'ClientId='+$(this).val()+'&UserId='+$('input[name=UserId]').val(),
				success: function(msg){
					//msg is in the format 1:Project,3:Project,4:Project,
					//remove the trailing comma
					if (msg.length > 0) {
						msg = msg.substring(0, msg.length - 1);
						var projects = msg.split(',');
						for (var i in projects) {
							var parts = projects[i].split(':');
							//parts[0] = ProjectId, parts[1] = ProjectName
							var checked = '';
							if (parts[2] != undefined && parts[2].length > 0 && parts[2] == "true") {
								checked = 'checked="checked"';
							}
							$('#before_projects').after('<dt class="projects"><label for="'+parts[0]+'">'+parts[1]+'</label><span class="description">Permission</span></dt><dd class="projects"><input id="'+parts[0]+'" type="checkbox" name="permit['+parts[0]+']" '+checked+' /></dd>');
						}
					}
				}
			});
		}
	}).change();
}); //END DOCUMENT.READY