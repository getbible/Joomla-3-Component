/**
* 
* 	@version 	1.0.6  January 06, 2015
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

// get the data from the Page
jQuery(document).ready(function() {
    set(Cpanel); 
});

// Set the Cpanel to page
function set(Cpanel) {
	var output = '<ul>';
	jQuery.each(Cpanel.versions, function(index, translation) {
			//output += '<li>';
			//output += Cpanel.translation.version_name;
			//output += '</li>';
	});
	output += '</ul>';
}