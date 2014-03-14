/**
* 
* 	@version 	1.0.0 Feb 02, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

		/*******************************************\
		*			Load Scripture Here				*
		\*******************************************/
		
// get the data from the API
jQuery(function() {
	getData(setQuery);
	getDataBo(defaultVersion,defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
	getDataCh(defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
});
// Load this after page is fully loaded
jQuery(window).bind("load", function() {
	jQuery('#t_loader').hide();
	jQuery('#getbible').show();
	jQuery('.button').show();
	loadTimer1();
});
		
jQuery(document).ready(function() {
	
	var preSB;

	jQuery('#books').focus(function () {
		// Store the current value on focus, before it changes
		preSB = this.value;
		}).change(function() {
			var newSB = this.value;
			jQuery('#t_loader').show();
			jQuery('#chapters').slideUp( "slow" );
			jQuery('.button').hide();
			jQuery('#chapters').html('');
			getDataCh(newSB);
			jQuery('#chapters').slideDown( "slow" );
		});
	
	var preSV;
	
	jQuery('#versions').focus(function () {
		// Store the current value on focus, before it changes
		preSV = this.value;
		}).change(function() {
			var newSV = this.value;
			jQuery('#books').hide();
			jQuery('.button').hide();
			jQuery('#chapters').hide();
			jQuery('#books').empty();
			getDataBo(newSV);
		}); 
});




// Ajax Call to get Data
function getData(request,addTo) {
	
	if (typeof appKey !== 'undefined') {
     	request = request+'&appKey='+appKey;
	}
	if(!addTo){
		jQuery('#scripture').addClass('text_loading');
	}
	jQuery.ajax({
     url:jsonUrl,
	 dataType: 'jsonp',
	 data: request,
	 jsonp: 'getbible',
     success:function(json){
		 // set text direction
		 if (json.direction == 'RTL'){
			var direction = 'rtl';
		 } else {
			var direction = 'ltr'; 
		 }
         // check json
		 if (json.type == 'verse'){
			setVerses(json,direction,addTo);
		 } else if (json.type == 'chapter'){
			setChapter(json,direction,addTo);
		 } else if (json.type == 'book'){
			setBook(json,direction,addTo);
		 }
		 jQuery('#b_loader').hide();
		 jQuery('#t_loader').hide();
		 jQuery("#scripture").removeClass('text_loading');
     },
     error:function(){
		 	jQuery('#b_loader').hide();
		 	jQuery('#t_loader').hide();
		 	jQuery('#scripture').removeClass('text_loading');
			if(!addTo){ 
				jQuery('#scripture').html('<h2>No scripture was returned, please try again!</h2>'); // <---- this is the div id we update
			}
		 },
	});
}

// Set Verses
function setVerses(json,direction,addTo){
	var output = '';
		jQuery.each(json.book, function(index, value) {
			output += '<center><b>'+value.book_name+'&#160;'+value.chapter_nr+'</b></center><br/><p class="'+direction+'">';
			jQuery.each(value.chapter, function(index, value) {
				output += '&#160;&#160;<small class="ltr">' +value.verse_nr+ '</small>&#160;&#160;';
				output += value.verse;
				output += '<br/>';
			});
			output += '</p>';
		});
		if(addTo){
			jQuery('#scripture').append(output);
		} else {
			jQuery('#scripture').html(output);  // <---- this is the div id we update
		}
}

// Set Chapter
function setChapter(json,direction,addTo){
	var output = '<center><b>'+json.book_name+'&#160;'+json.chapter_nr+'</b></center><br/><p class="'+direction+'">';
			jQuery.each(json.chapter, function(index, value) {
				output += '&#160;&#160;<small class="ltr">' +value.verse_nr+ '</small>&#160;&#160;';
				output += value.verse;
				output += '<br/>';
			});
			output += '</p>';
			if(addTo){
				jQuery('#scripture').append(output);
			} else {
				jQuery('#scripture').html(output);  // <---- this is the div id we update
			}
}

// Set Book
function setBook(json,direction,addTo){
	var output = '';
		jQuery.each(json.book, function(index, value) {
			output += '<center><b>'+json.book_name+'&#160;'+value.chapter_nr+'</b></center><br/><p class="'+direction+'">';
			jQuery.each(value.chapter, function(index, value) {
				output += '&#160;&#160;<small class="ltr">' +value.verse_nr+ '</small>&#160;&#160;';
				output += value.verse;
				output += '<br/>';
			});
			output += '</p>';
		});
		if(addTo){
			jQuery('#scripture').append(output);
		} else {
			jQuery('#scripture').html(output);  // <---- this is the div id we update
		}
}

		/*******************************************\
		*			  Control Pannel				*
		\*******************************************/

// Ajax Call to get chapter
function getDataCh(call) {
	if (typeof cPanelUrl !== 'undefined') {
		getUrl = cPanelUrl+"index.php?option=com_getbible&task=bible.chapter&format=json";     	
	} else {
		getUrl = "index.php?option=com_getbible&task=bible.chapter&format=json";
	}
	jQuery('#scripture').addClass('text_loading');
	var result = call.split('__');
	var Get = 'v='+result[0]+'&nr='+result[1];
	jQuery.ajax({
		type: "GET",
		dataType: "jsonp",
		url: getUrl,
		data: Get,
		// the name of the callback parameter, as specified by the getBible API
		jsonp: "callback"
	})
	.done(function( json ) {
		var output = '<div style="display: inline-block;">';
		jQuery.each(json, function(index, element) {
			var setGlobal = result[2]+'__'+element+'__'+result[0];
			output += '<div style="float: left; margin: 5px;"><button  class="uk-button uk-button-small" type="button" href="javascript:void(0)" onclick="getScripture(\'p='+result[2]+element+'&v='+result[0]+'\', \''+setGlobal+'\')"><span style="display: block; width: 30px;">'+element+'</span></button></div>';
        });
		output += '</div>';
		jQuery('#t_loader').hide();
		jQuery('#chapters').html(output);  // <---- this is the div id we update
	});
}

// Ajax Call to get Books
function getDataBo(version, first) {
	jQuery('#books').hide();
	jQuery('#f_loader').show();
	if (typeof cPanelUrl !== 'undefined') {
		getUrl = cPanelUrl+"index.php?option=com_getbible&task=bible.books&format=json";     	
	} else {
		getUrl = "index.php?option=com_getbible&task=bible.books&format=json";
	}
	jQuery('#scripture').addClass('text_loading');
	jQuery.ajax({
		type: "GET",
		dataType: "jsonp",
		url: getUrl,
		// the name of the callback parameter, as specified by the getBible API
		jsonp: "callback",
		data: { v: version }
	})
	.done(function( json ) {
			var op = new Option('- Select Book -', '');
			/// jquerify the DOM object 'o' so we can use the html method
			jQuery(op).html('- Select Book -');
			jQuery('#books').append(op);
		jQuery.each(json, function() {
				str = this.ref.replace(/\s+/g, '');
				$value = version+'__'+this.book_nr+'__'+str;
				var op = new Option(this.book_name, $value);
				/// jquerify the DOM object 'o' so we can use the html method
				jQuery(op).html(this.book_name);
				jQuery('#books').append(op);
        });
			if(first){
				jQuery('#books').val(first);
			}
			jQuery('#f_loader').hide();
			jQuery('#books').show();
	});
}

// show chapter selection
function showChapters(slideup) {
	jQuery('.button').hide();
	jQuery('#chapters').slideDown( "slow" );
	if(slideup){
		jQuery("html, body").animate({ scrollTop: 0 }, "slow");
	}
}
//  Call to get scripture
function getScripture(call,setGlobal) {
	jQuery('#t_loader').show();
	var result = setGlobal.split('__');
	BIBLE_BOOK 		= result[0];
	BIBLE_CHAPTER 	= result[1];
	BIBLE_VERSION	= result[2];
	getData(call);
	jQuery('#chapters').slideUp( "slow" );
	jQuery('.button').show();
	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
}

// Set window scroll
var didScroll = false;
jQuery(window).scroll(function() {
    didScroll = true;
});

// Get next chapter as you scroll down
function loadTimer1(){
	timerInterval_1 = setInterval(function() {
		if ( didScroll ) {
			if (jQuery(window).scrollTop() >= jQuery(document).height() - jQuery(window).height() - 10) {
				jQuery('#b_loader').show();
				BIBLE_CHAPTER++;
				getData('p='+BIBLE_BOOK+BIBLE_CHAPTER+'&v='+BIBLE_VERSION,true);
				didScroll = false;
			 }
		}
	}, 250);
}