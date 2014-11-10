/**
* 
* 	@version 	1.0.2  November 10, 2014
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
	if(typeof searchApp === 'undefined'){
		getDataBo(defaultVersion,defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
		getDataCh(defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
	} else {
		jQuery('#cPanel').hide();
		jQuery('.searchbuttons').show();
	}
	
});
// Load this after page is fully loaded
jQuery(window).bind("load", function() {
	//jQuery('#t_loader').hide();
	jQuery('#getbible').show();
	if(typeof searchApp === 'undefined'){
		jQuery('.button').show();
		loadTimer1();
	} else {
		jQuery('#button_top').show();
	}
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
			setSearchBook(newSB,preSB);
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
			jQuery('#search_version').val(newSV);
			// update global version
			BIBLE_VERSION = newSV;
		}); 
		
	/*******************************************\
	*			Search Scripture Here			*
	\*******************************************/
	
	// set the criteria for the search
	var crit1 = 1;
	var crit2 = 1;
	var crit3 = 1;
	jQuery('.search_crit1').click(function() {
		crit1 = jQuery(this).attr("value");
		setCrit();
	});
	jQuery('.search_crit2').click(function() {
		crit2 = jQuery(this).attr("value");
		setCrit();
	});
	jQuery('.search_crit3').click(function() {
		crit3 = jQuery(this).attr("value");
		setCrit();
	});
	function setCrit(){
		jQuery('#search_crit').val(crit1+'_'+crit2+'_'+crit3);
	}
	// set the type of search
	jQuery('.search_type').click(function() {
		jQuery('#search_type').val(jQuery(this).attr("value"));
	});
	
	// set the search version
	jQuery('#search_version').val(BIBLE_VERSION);
	// set the search book ref
	jQuery('#search_book').val(BIBLE_BOOK);
	
	
});

// set highlight option on or off
var setHight = false;
function highScripture(){
	var how = searchCrit.split('_');
	if (setHight){
		setHight = false;
		if (how[0] == 3){
			if (how[2] == 2){
				if (how[1] == 1){
					jQuery('#scripture p').unhighlight(searchFor, { caseSensitive: true, wordsOnly: true });
				} else {
					jQuery('#scripture p').unhighlight(searchFor, { caseSensitive: true });
				}
			} else {
				jQuery('#scripture p').unhighlight(searchFor);
			}
		} else {
			var searchwords = searchFor.split(' ');
			var i;
			for (i = 0; i < searchwords.length; ++i) {
				if (how[2] == 2){
					if (how[1] == 1){
						jQuery('#scripture p').unhighlight(searchwords[i], { caseSensitive: true, wordsOnly: true });
					} else {
						jQuery('#scripture p').unhighlight(searchwords[i], { caseSensitive: true });
					}
				} else {
					jQuery('#scripture p').unhighlight(searchwords[i]);
				}
			}
		}
	} else {
		setHight = true;
		if (how[0] == 3){
			if (how[2] == 2){
				if (how[1] == 1){
					jQuery('#scripture p').highlight(searchFor, { caseSensitive: true, wordsOnly: true });
				} else {
					jQuery('#scripture p').highlight(searchFor, { caseSensitive: true });
				}
			} else {
				jQuery('#scripture p').highlight(searchFor);
			}
		} else {
			var searchwords = searchFor.split(' ');
			var i;
			for (i = 0; i < searchwords.length; ++i) {
				if (how[2] == 2){
					if (how[1] == 1){
						jQuery('#scripture p').highlight(searchwords[i], { caseSensitive: true, wordsOnly: true  });
					} else {
						jQuery('#scripture p').highlight(searchwords[i], { caseSensitive: true });
					}
				} else {
					jQuery('#scripture p').highlight(searchwords[i]);
				}
			}
		}
	}
	
}
// set the search book when a book is changed
function setSearchBook(newBook,lastBook){
	var bookNew = newBook.split('__');
	jQuery('#search_book').val(bookNew[2]);
	var bookLast = lastBook.split('__');
	if(jQuery('#search_type').val() == bookLast[2]){
		jQuery('#search_type').val(bookNew[2]);
	}
}
// load the chapter where the search verse is found 
function loadFoundChapter(call, setGlobal){
	loadTimer1();
	//  Call to get scripture
	jQuery('#t_loader').show();
	getData(call, false, true);
	var result = setGlobal.split('__');
	BIBLE_BOOK 			= result[0];
	BIBLE_BOOK_NR 		= result[1];
	BIBLE_CHAPTER 		= result[2];
	BIBLE_LAST_CHAPTER 	= --result[2];
	BIBLE_VERSION		= result[3];
	jQuery('.button').show();
	getDataBo(BIBLE_VERSION,BIBLE_VERSION+'__'+BIBLE_BOOK_NR+'__'+BIBLE_BOOK);
	getDataCh(BIBLE_VERSION+'__'+BIBLE_BOOK_NR+'__'+BIBLE_BOOK);
	jQuery('.searchbuttons').hide();
	jQuery('#button_top').hide();
	jQuery('#versions').val(BIBLE_VERSION);
	jQuery('#cPanel').show();
	// set the search book ref
	jQuery('#search_book').val(BIBLE_BOOK);
	if(BIBLE_LAST_CHAPTER == 0){
		jQuery('#prev').hide();
	}
	gotoTop();
}

// set the found verse page next chapter load text
FoundTheVerse = false;
// Ajax Call to get Data
function getData(request,addTo, Found) {
	if(Found){
		FoundTheVerse = Found;
	}
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
		 } else if (json.type == 'search'){
			setSearch(json,direction);
		 }
		 jQuery('#b_loader').hide();
		 jQuery('#t_loader').hide();
		 if(typeof searchApp === 'undefined' || FoundTheVerse){
		 	jQuery('.navigation').show();
		 }
		 jQuery("#scripture").removeClass('text_loading');
     },
     error:function(){
		 	jQuery('#b_loader').hide();
		 	jQuery('#t_loader').hide();
			if(typeof searchApp === 'undefined' || FoundTheVerse){
				jQuery('.navigation').show();
			}
		 	jQuery('#scripture').removeClass('text_loading');
			if(!addTo && appMode == 1){
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

// Set Search
function setSearch(json,direction){
	var output = '<small>('+json.counter+')</small><br/>';
		jQuery.each(json.book, function(index, value) {
			var book_ref 	= value.book_ref.replace(/\s+/g, '');
			var setCall 	= 'p='+book_ref+value.chapter_nr+"&v="+BIBLE_VERSION;
			var setGlobal 	= book_ref+'__'+value.book_nr+'__'+value.chapter_nr+'__'+BIBLE_VERSION;
			output += '<center><b><a href="javascript:void(0)" onclick="loadFoundChapter(\''+setCall+'\',\''+setGlobal+'\')">'+value.book_name+'&#160;'+value.chapter_nr+'</a></b></center><br/><p class="'+direction+'">';
			jQuery.each(value.chapter, function(index, value) {
				output += '&#160;&#160;<small class="ltr">' +value.verse_nr+ '</small>&#160;&#160;';
				output += value.verse;
				output += '<br/>';
			});
			output += '</p>';
		});
		jQuery('#scripture').html(output);  // <---- this is the div id we update
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
// scroll to top function
function gotoTop(){
	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
}
// show chapter selection
function showChapters(slideup) {
	jQuery('.button').hide();
	jQuery('#chapters').slideDown( "slow" );
	if(slideup){
		gotoTop();
	}
}
//  Call to get scripture
function getScripture(call,setGlobal) {
	jQuery('#t_loader').show();
	var result = setGlobal.split('__');
	BIBLE_BOOK 			= result[0];
	BIBLE_CHAPTER 		= result[1];
	BIBLE_LAST_CHAPTER 	= -- result[1];
	BIBLE_VERSION		= result[2];
	getData(call);
	jQuery('#chapters').slideUp( "slow" );
	jQuery('.button').show();
	gotoTop();
	if(BIBLE_LAST_CHAPTER == 0){
		jQuery('#prev').hide();
	}
}

// Set window scroll
var didScroll = false;
jQuery(window).scroll(function() {
    didScroll = true;
});

// Get next chapter as you scroll down
function loadTimer1(){
	timerInterval_1 = setInterval(function() {
		if ((autoLoadChapter === 1) && didScroll) {
			if (jQuery(window).scrollTop() >= jQuery(document).height() - jQuery(window).height() - 10) {
				nextChapter();
				didScroll = false;
			 }
		}
	}, 250);
}

// get next chapter with next button
function nextChapter(){
	BIBLE_LAST_CHAPTER = BIBLE_CHAPTER;
	BIBLE_CHAPTER++;
	if(appMode == 1){
		addTo = true;
	} else if(appMode == 2){
		addTo = false;
		gotoTop();
		if(BIBLE_CHAPTER > 1){
			jQuery('#prev').show();
		}
	}
	jQuery('.navigation').hide();
	jQuery('#b_loader').show();
	getData('p='+BIBLE_BOOK+BIBLE_CHAPTER+'&v='+BIBLE_VERSION,addTo);
}
// get previous chapter with prev button
function prevChapter(){
	addTo = false;
	gotoTop();
	if(BIBLE_LAST_CHAPTER == 0){
		
	} else {
		jQuery('.navigation').hide();
		jQuery('#b_loader').show();
		getData('p='+BIBLE_BOOK+BIBLE_LAST_CHAPTER+'&v='+BIBLE_VERSION,addTo);
		BIBLE_CHAPTER--;
		BIBLE_LAST_CHAPTER--;
		if(BIBLE_LAST_CHAPTER == 0){
			jQuery('#prev').hide();
		}
	}
}