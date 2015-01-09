/**
* 
* 	@version 	1.0.6  January 06, 2015
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

	/*******************************************\
	*			Load Scripture Here				*
	\*******************************************/

// the Globals
var BIBLE_BOOK 			= 0;
var BIBLE_CHAPTER 		= 0;
var BIBLE_LAST_CHAPTER 	= 0;
var BIBLE_VERSION 		= 0;
var defaultVersion 		= 0;
var defaultVers			= 0;
var defaultBook 		= 0;
var defaultBookNr 		= 0;
var defaultChapter 		= 0;
var setQuery 			= 0;

// set highlight array
var alpha 			= "abcdefghijklmnopqrstuvwxyz";
var highlightsArray = alpha.split("");

// get the data from the API
jQuery(function() {
	// load defaults
	getDefaults(defaultRequest, defaultKey);
	appFeatures(1);
	setTextSize();
	// check if highlights are in sync
	checkHighLightSync();
		
});
// Load this after page is fully loaded
jQuery(window).bind("load", function() {
	//jQuery('#t_loader').hide();
	jQuery('#getbible').show();
	if(searchApp != 1){
		jQuery('.button_chapters').show();
		if(autoLoadChapter === 1){
			loadTimer1();
		}
	} else {
		jQuery('#button_top').show();
	}		
});
		
jQuery(document).ready(function() {
	
	var preSB;

	jQuery('.books').focus(function () {
		// Store the current value on focus, before it changes
		preSB = this.value;
		}).change(function() {
			var newSB = this.value;
			jQuery('#t_loader').show();
			jQuery('#chapters').slideUp( "slow" );
			// jQuery('.button_chapters').hide();
			jQuery('#chapters').html('');
			getDataCh(newSB);
			jQuery('#chapters').slideDown( "slow" );
			setSearchBook(newSB,preSB);
		});
	
	var preSV;
	var activePassage;
	
	jQuery('.versions').focus(function () {
		// Store the current value on focus, before it changes
		preSV 			= this.value;
		}).change(function() {
			activePassage 	= jQuery(".books option:selected").val();
			var newSV = this.value;
			jQuery('.books').hide();
			// jQuery('.button_chapters').hide();
			jQuery('#chapters').hide();
			jQuery('.books').empty();
			getDataBo(newSV, activePassage, 1);
			jQuery('.search_version').val(newSV);
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
		jQuery('.search_crit').val(crit1+'_'+crit2+'_'+crit3);
	}
	// set the type of search
	jQuery('.search_type_select').click(function() {
		jQuery('.search_type').val(jQuery(this).attr("value"));
	});
	
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
	jQuery('.search_book').val(bookNew[2]);
	var bookLast = lastBook.split('__');
	if(jQuery('.search_type').val() == bookLast[2]){
		jQuery('.search_type').val(bookNew[2]);
	}
}
// load the chapter where the search verse is found 
function loadFoundChapter(call, setGlobal){
	if(autoLoadChapter === 1){
		loadTimer1();
	}
	//  Call to get scripture
	jQuery('#t_loader').show();
	var result = setGlobal.split('__');
	BIBLE_BOOK 			= result[0];
	BIBLE_BOOK_NR 		= result[1];
	BIBLE_CHAPTER 		= result[2];
	BIBLE_LAST_CHAPTER 	= --result[2];
	BIBLE_VERSION		= result[3];
	jQuery('.button_chapters').show();
	getDataBo(BIBLE_VERSION,BIBLE_VERSION+'__'+BIBLE_BOOK_NR+'__'+BIBLE_BOOK);
	getDataCh(BIBLE_VERSION+'__'+BIBLE_BOOK_NR+'__'+BIBLE_BOOK);
	jQuery('.searchbuttons').hide();
	jQuery('#button_top').hide();
	jQuery('.versions').val(BIBLE_VERSION);
	jQuery('#cPanel').show();
	// set the search book ref
	jQuery('.search_book').val(BIBLE_BOOK);
	if(BIBLE_LAST_CHAPTER < 1){
		jQuery('#prev').hide();
	}
	getData(call, false, true);
	if(appMode == 2){
		gotoTop();
	}
}	

	/*******************************************\
	*		Load App Page features Here			*
	\*******************************************/
	
jQuery(document).ready(function() {
	// first page load	
	jQuery('.verse').hover(function() {
			jQuery(this).addClass('hoverStyle');
		}, function() {
			jQuery(this).removeClass('hoverStyle');
		}
	);
	// set tag vars
	var defaultTags = [];	
});

jQuery(document).on('click', ".verse", function(e){
	e.preventDefault();
	setHighLight(this);
});
			
// script to set all app apge features
function appFeatures(when){
	
	// for other ajax pages
	jQuery('.verse').hover(function() {
			jQuery(this).addClass('hoverStyle');
		}, function() {
			jQuery(this).removeClass('hoverStyle');
		}
	);
	
	var highLights = jQuery.jStorage.get('highlights');
	
	if(highLights){
		jQuery.jStorage.deleteKey('highlights');
		jQuery.each( highLights, function( val, mark ) {
			if(jQuery("#" + val).length != 0) {
			  jQuery("#" + val).addClass('highlight_'+mark);
			}
		});
		//save highlights
		jQuery.jStorage.set('highlights',highLights);
	} 
	// ensure that text gets resized
	setTextSize();
	// load notes
	setNotes();
	// load tags
	getTags();
	
}

function makeNote(id){
	var idPieces = id.split('_');
	var bookName = jQuery(".books option:selected").text();
	var editNote = jQuery('#edit__'+id).text();
	if(editNote.length != 0){
		jQuery('#active_note').val(editNote);
	}
	jQuery('.note_verse').html(bookName+' verse '+idPieces[2]);
	jQuery('#note_verse').val(id);
	setTagDiv(id);
}
function submitNote(){
	setNote_db().done(function(notes) {
		if(notes){
			loadNotes(notes);
			var localNotes = jQuery.jStorage.get('notes');
			jQuery.jStorage.deleteKey('notes');
			jQuery.each(notes, function( vers, note) {
				localNotes[vers] = note;
			});
			jQuery.jStorage.set('notes',localNotes);
		}
	});
}
function loadNotes(notes){
	jQuery.each(notes, function( vers, note) {
		if(jQuery("#"+vers).length != 0) {
			if(note.length != 0){
				if(verselineMode == 2){
					html = '&#160;<span class="verse_nr uk-text-muted ltr" onclick="makeNote(\''+vers+ '\');return false;" data-uk-tooltip="{pos:\'right\'}" title="Edit Note & Tags">[&#160;<span  id="edit__'+vers+ '" >'+note+'</span>&#160;]</span>';
				} else {
					html = '<br /><span class="verse_nr uk-text-muted ltr" onclick="makeNote(\''+vers+ '\');return false;" data-uk-tooltip="{pos:\'right\'}" title="Edit Note & Tags">[&#160;<span  id="edit__'+vers+ '" >'+note+'</span>&#160;]</span>';
				}
				jQuery('#note__'+vers).html(html);
				jQuery('#nr__'+vers).attr('title', 'Edit Note & Tags');
			} else {
				jQuery('#note__'+vers).html('');
			}
		}
	});
}
function setNote_db(){
	// set note	on server
	if(user_id > 0 && allowAccount > 0){
		var request = jQuery('#post_note').serialize();
		jQuery('#active_note').val('');
		var getUrl 	= "index.php?option=com_getbible&task=bible.setnote&format=json";
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}
function setNotes(){
	// set note	in browser memory
	if(user_id > 0 && allowAccount > 0){
		var localNotes = jQuery.jStorage.get('notes');
		if(!localNotes){
			getNotes().done(function(localNotes) {
				if(localNotes){
					loadNotes(localNotes);
					jQuery.jStorage.set('notes',localNotes);
				}
			});
		} else {
			loadNotes(localNotes);
		}
	}
	return false;
}
function getNotes(){
	// set highlight	on server
	if(user_id > 0 && allowAccount > 0){
		var request	= '&jsonKey='+jsonKey+'&tu='+openNow;
		var getUrl 	= "index.php?option=com_getbible&task=bible.getnotes&format=json";
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}

function mergeAllHighlights(){
	setHighlights_server(3).done(function(isMerged) {
		if(isMerged){
			loadServerHighlights(true);
		}
	})
}
function saveBrowserHighlights(act){
	setHighlights_server(act);
}
function loadServerHighlights(removeLocal){
	getHighlights().done(function(server) {
		if(removeLocal){
			var highLights = jQuery.jStorage.get('highlights');
			if(highLights){
				jQuery.each(highLights, function(id, color) {
					// remove the class
					jQuery('#'+id).removeClass('highlight_'+color);
				});
				jQuery.jStorage.deleteKey('highlights');
			}
		}
		if(server){
			serverBookMarks = JSON.parse(Base64.decode(server));
			jQuery.each( serverBookMarks, function( id, color ) {
				if(jQuery("#" + id).length != 0) {
				  jQuery("#" + id).addClass('highlight_'+color);
				}
			});
			//save highlights
			jQuery.jStorage.set('highlights',serverBookMarks);
		}
	})	
}
function clearAllHighlights(){
	clearServerHighlights().done(function(isCleared) {
		if(isCleared){
			var highLights = jQuery.jStorage.get('highlights');
			if(highLights){
				jQuery.each(highLights, function(id, color) {
					// remove the class
					jQuery('#'+id).removeClass('highlight_'+color);
				});
				jQuery.jStorage.deleteKey('highlights');
			}
		}
	})
}
function clearServerHighlights(){
	if(user_id > 0 && allowAccount > 0){
		var getUrl 	= "index.php?option=com_getbible&task=bible.clearhighlights&format=json";
		var request = '&jsonKey='+jsonKey+'&tu='+openNow;
		return jQuery.ajax({
			 type: 'GET',
			 url: getUrl,
			 dataType: 'jsonp',
			 data: request,
			 jsonp: 'callback'
		});
	}
	return false;
}

function getHighlights(){
	// get highlights		
	if(user_id > 0 && allowAccount > 0){
		var getUrl 	= "index.php?option=com_getbible&task=bible.gethighlights&format=json";
		var request	= '&jsonKey='+jsonKey+'&tu='+openNow;
		return jQuery.ajax({
			 type: 'GET',
			 url: getUrl,
			 dataType: 'jsonp',
			 data: request,
			 jsonp: 'callback'
		});
	}
	return false;
}

function checkHighLightSync(){
	if(user_id > 0 && allowAccount > 0){
		getHighlights().done(function(server) {
			if(!server){
				server = {not : 'found'};
			} else {
				server = JSON.parse(Base64.decode(server));
			}
			var local = jQuery.jStorage.get('highlights');
			if(!local){
				local 	= {not : 'found'};
			}
			if(!sameObject(local,server)){
				jQuery('.slectionSync').hide();
				jQuery.UIkit.modal("#highlight_checker").show();
				if(local.not){
					// server has highlights but not browser
					jQuery('.server_has').show();
				}else if(server.not){
					// Browser has highlights but not server
					jQuery('.browser_has').show();
				} else {
					// both has highlights
					jQuery('.both_has').show();
				}
			}			
		});	
	}
}
function setTagDiv(id){
	// set tags
	if(user_id > 0 && allowAccount > 0){
		var localVerseTags = jQuery.jStorage.get('taged__'+id, null);
		if(localVerseTags){
			var html = '<ul id="tags__'+id+'">';
				jQuery.each(localVerseTags, function(tag, name) {
					html += '<li>'+name+'</li>';
				});
			html += '</ul>';
		} else {
			var html = '<ul id="tags__'+id+'"></ul>';
		}					
		jQuery('#tagDiv').html(html);
		jQuery('#tags__'+id).tagit({
			// Options
			availableTags: defaultTags,
			autocomplete: {delay: 0, minLength: 2},
			allowSpaces: true,
			showAutocompleteOnFocus: true,
			afterTagAdded: function(evt, ui) {
				if (!ui.duringInitialization) {
					var taged = jQuery('#tags__'+id).tagit('tagLabel', ui.tag);
					// set the taged verse
					setTaged(taged,1,id);
					// if new tag add to tag list
					if(!isInArray(taged,defaultTags)){
						defaultTags.push(taged);
						jQuery.jStorage.set('tags',defaultTags);
					}
				}
			},
			afterTagRemoved: function(evt, ui) {
				var untaged = jQuery('#tags__'+id).tagit('tagLabel', ui.tag);
				// set the taged verse
				setTaged(untaged,0,id);
			}
		});
		jQuery.UIkit.modal("#note_maker").show();
	} else {
		jQuery.UIkit.modal("#note_maker").show();
	}
}
function setTaged(tag,action,verse){
	// set tags
	if(user_id > 0 && allowAccount > 0){
		if(action == 1){
			// add the tag
			setTaged_db(tag,action,verse).done(function(set) {
				if(set){
					var localVerseTags = jQuery.jStorage.get('taged__'+verse, null);
					if(localVerseTags){
						if(!isInArray(tag, localVerseTags)){
							localVerseTags.push(tag);
							jQuery.jStorage.set('taged__'+verse,localVerseTags);
						}
					} else {
						localVerseTags = [];
						localVerseTags.push(tag);
						jQuery.jStorage.set('taged__'+verse,localVerseTags);
					}
				}
			});	
		} else if(action == 0){
			// remove the tag
			setTaged_db(tag,action,verse).done(function(set) {
				if(set){
					var localVerseTags = jQuery.jStorage.get('taged__'+verse, null);
					if(localVerseTags){
						if(isInArray(tag, localVerseTags)){
							// remove from local array
							localVerseTags = jQuery.grep(localVerseTags, function(value) {
								return value != tag;
							});
							jQuery.jStorage.set('taged__'+verse,localVerseTags);
						}
					}
				}
			});
		}
	}
	return false;
}
function setTaged_db(tag,action,verse){
	// set note	on server
	if(user_id > 0 && allowAccount > 0){
		var request = '&jsonKey='+jsonKey+'&tu='+openNow+'&action='+action+'&tag='+tag+'&verse='+verse;
		var getUrl 	= "index.php?option=com_getbible&task=bible.settaged&format=json";
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}
function getTaged(verse){
	// set Taged in browser memory
	if(user_id > 0 && allowAccount > 0){
		getTaged_db(verse).done(function(dbVerseTags) {
			if(dbVerseTags){
				jQuery.each(dbVerseTags, function( vers, tags) {
					var localVerseTag = jQuery.jStorage.get('taged__'+verse+'_'+vers, null);
					if(localVerseTag){
						jQuery.each(tags, function(id,tag) {
							if(!isInArray(tag, localVerseTag)){
								localVerseTag.push(tag);
							}
						});
					} else {
						localVerseTag = [];
						jQuery.each(tags, function(id,tag) {
							if(!isInArray(tag, localVerseTag)){
								localVerseTag.push(tag);
							}
						});
					}
					jQuery.jStorage.set('taged__'+verse+'_'+vers,localVerseTag);
				}); 
			}
		});
	}
	return false;
}
function getTaged_db(verse){
	// set note	on server
	if(user_id > 0 && allowAccount > 0){
		var request = '&jsonKey='+jsonKey+'&tu='+openNow+'&verse='+verse;
		var getUrl 	= "index.php?option=com_getbible&task=bible.gettaged&format=json";
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}
function addTag(tag){
	setTags_db(tag,1).done(function(set) {
		if(set){
			defaultTags.push(tag);
			jQuery.jStorage.set('tags',defaultTags);
		}
	});
}
function removeTag(tag){
	setTags_db(tag,0).done(function(set) {
		if(set){
			defaultTags = jQuery.grep(defaultTags, function(value) {
				return value != tag;
			});
			jQuery.jStorage.set('tags',defaultTags);
		}
	});	
}
function setTags_db(tag,published){
	// set note	on server
	if(user_id > 0 && allowAccount > 0){
		var request = '&jsonKey='+jsonKey+'&tu='+openNow+'&access=1&published='+published+'&note=null&name='+tag;
		var getUrl 	= "index.php?option=com_getbible&task=bible.settags&format=json";
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}
function getTags(){
	// set note	in browser memory
	if(user_id > 0 && allowAccount > 0){
		var localTags = jQuery.jStorage.get('tags');
		if(!localTags){
			getTags_db().done(function(localTags) {
				if(localTags){
					jQuery.jStorage.set('tags',localTags);
					defaultTags = localTags;
				}
			});
		} else {
			defaultTags = localTags;
		}
	}
	return false;
}
function getTags_db(){
	// set highlight	on server
	if(user_id > 0 && allowAccount > 0){
		var request	= '&jsonKey='+jsonKey+'&tu='+openNow;
		var getUrl 	= "index.php?option=com_getbible&task=bible.gettags&format=json";
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}

// check if object is the same
function sameObject(a,b) {
	if (!b || !a || b.length != a.length) {
		return false;
	}
	var same = true;
	jQuery.each(a, function(id, color) {
		if(color !== b[id]){
			same = false;
			return false;
		}
	});
	jQuery.each(b, function(id, color) {
		if(color !== a[id]){
			same = false;
			return false;
		}
	});
	return same;
}

// set the highlight
function setHighLight(verse) {
	// get default current highlight
	var currentMark = jQuery.jStorage.get('currentMark', 'a');
	// get highlights
	var highLightsStore = jQuery.jStorage.get('highlights');
	if(!highLightsStore){
		highLightsStore = {};
	}
	var id 		= jQuery(verse).attr("id");
	var add 	= true;
	var delet 	= false;
	var deletMark;
	jQuery(highlightsArray).each(function(index, mark) {
		// remove the class and unset values
		jQuery('#'+id+' span').removeClass('highlight');
		if(jQuery('#'+id).hasClass('highlight_'+mark)){
			deletMark 	= mark;
			delet 		= true;
			return false;
		}
	});
	if(delet){
		jQuery('#'+id).removeClass('highlight_'+deletMark);
		delete highLightsStore[id];
		if('highlight_'+deletMark == 'highlight_'+currentMark){
			add = false;
			setHighLight_server(0,id+'__'+deletMark);
		}
	}
	if(add){
		setHighLight_server(1,id+'__'+currentMark);
		// add the class and set the values
		highLightsStore[id] = currentMark;
		jQuery('#'+id).addClass('highlight_'+currentMark);
	}
	//save highlights
	jQuery.jStorage.set('highlights',highLightsStore);
}

function setHighlights_server(action){
	
	// set highlight	on server	
	if(user_id > 0 && allowAccount > 0){
		var getUrl		= "index.php?option=com_getbible&task=bible.sethighlights&format=json";
		var highLights 	= jQuery.jStorage.get('highlights');
		if(highLights){
			var checker = JSON.stringify(highLights);
			var request = 'highlight='+Base64.encode(checker)+'&publish=1';
		} else {
			return false;
		}
		/************************************
		* action has the following options	*
		* 1 = first set of highlights		*
		* 2 = replace all server highlights	*
		* 3 = merge with server highlights	*
		************************************/
		switch(action){
			case 2:
			case 3:
			request = request+'&act='+action;
			break;
			default:
			request = request+'&act=1';
		}
		request = request+'&jsonKey='+jsonKey+'&tu='+openNow;
		return jQuery.ajax({
			 type: 'GET',
			 url: getUrl,
			 dataType: 'jsonp',
			 data: request,
			 jsonp: 'callback'
		});
	}
	return false;
}
function setHighLight_server(publish,mark){
	
	// set highlight	on server	
	if(user_id > 0 && allowAccount > 0){
		var getUrl = "index.php?option=com_getbible&task=bible.setHighLight&format=json";
		if(mark.length > 0){
			var request = 'highlight='+mark+'&publish='+publish+'&jsonKey='+jsonKey+'&tu='+openNow;
		} else {
			return false;
		}
		return jQuery.ajax({
			type: 'GET',
			url: getUrl,
			dataType: 'jsonp',
			data: request,
			jsonp: 'callback'
		});
	}
	return false;
}

// get selected text
function getSelectionText() {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }
    return text;
}
// set the highlight color
function setCurrentColor(mark){
	//save current highlight
	jQuery.jStorage.set('currentMark',mark);
}
// set the text size
function setCurrentTextSize(size){
	//get current size
	var was = jQuery.jStorage.get('TextSize', 'medium');
	//save new current size
	jQuery.jStorage.set('TextSize',size);
	// update text
	setTextSize(was,size);
}
function setTextSize(wasSize,newSize){
	if(!newSize){
		var newSize = jQuery.jStorage.get('TextSize', 'medium');
	}
	// set new class size
	jQuery(".verse").addClass('verse_'+newSize);
	jQuery(".verse_nr").addClass('nr_'+newSize);
	if(wasSize){
		if(newSize !== wasSize){
			// remove last class size
			jQuery(".verse").removeClass('verse_'+wasSize);
			jQuery(".verse_nr").removeClass('nr_'+wasSize);
		}
	}
	
}

// load the next book
function loadNextBook(addTo,Found){
	
	//  Call to get scripture
	var bookNow = false;
	jQuery(".books option").each(function(){
		option = this.value;
		var check = option.split('__');
		if(check[2] == BIBLE_BOOK){
			bookNow = option;
			return false;
		}
	});
	if(bookNow){
		var bookNowArray 	= bookNow.split('__');
		var bookNew			= false;
		if(bookNowArray[1] == 66){
			--BIBLE_LAST_CHAPTER;
			--BIBLE_CHAPTER;
			jQuery('#scripture').removeClass('text_loading');
		} else {
			BIBLE_BOOK_NR 		= ++bookNowArray[1];
			jQuery(".books option").each(function(){
				option = this.value;
				var check = option.split('__');
				if(check[1] == BIBLE_BOOK_NR){
					bookNew = option;
					return false;
				}
			});
		}
		if(bookNew){
			// add loading class
			if(!addTo){
				jQuery('#scripture').addClass('text_loading');
			}
			jQuery('#t_loader').show();
			var bookNewArray 	= bookNew.split('__');
			BIBLE_BOOK			= bookNewArray[2];
			BIBLE_VERSION		= bookNewArray[0];
			BIBLE_CHAPTER 		= 1;
			BIBLE_LAST_CHAPTER 	= 0;
			getDataBo(BIBLE_VERSION,BIBLE_VERSION+'__'+BIBLE_BOOK_NR+'__'+BIBLE_BOOK);
			getDataCh(BIBLE_VERSION+'__'+BIBLE_BOOK_NR+'__'+BIBLE_BOOK);
			jQuery('.versions').val(BIBLE_VERSION);
			jQuery(".books").val(bookNew);
			jQuery('.search_book').val(BIBLE_BOOK);
			// set the search book ref
			jQuery('#prev').hide();
			// load next book
			getData('p='+BIBLE_BOOK+BIBLE_CHAPTER+'&v='+BIBLE_VERSION,addTo,Found);
			if(appMode == 2){
				gotoTop();
			}
		}
	}
}

// set the found verse page next chapter load text
FoundTheVerse = false;
// Ajax Call to get Data
function getData(request, addTo, Found) {
	// keep the api key out of the store value
	var requestStore = request;
	// if memory is too full remove some
	if(jQuery.jStorage.storageSize() > 4500000){ 
		var storeIndex = jQuery.jStorage.index();
		// now remove the first once set when full
		jQuery.jStorage.deleteKey(storeIndex[5]);
		jQuery.jStorage.deleteKey(storeIndex[6]);
		jQuery.jStorage.deleteKey(storeIndex[7]);
		jQuery.jStorage.deleteKey(storeIndex[8]);
		jQuery.jStorage.deleteKey(storeIndex[9]);
		
	}
	// add loading class
	if(!addTo){
		jQuery('#scripture').addClass('text_loading');
	}	
	if(Found){
		FoundTheVerse = Found;
	}
	// Check if "requestStore" exists in the local storage
	var jsonStore = jQuery.jStorage.get(requestStore);
	if(!jsonStore || typeof jsonStore.version === 'undefined'){
		if (typeof appKey !== 'undefined') {
			request = request+'&appKey='+appKey;
		}
		jQuery.ajax({
		 url:jsonUrl,
		 dataType: 'jsonp',
		 data: request,
		 jsonp: 'getbible',
		 success:function(json){
			 // and save the result
			 jQuery.jStorage.set(requestStore,json);
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
			 if(searchApp != 1 || FoundTheVerse){
				jQuery('.navigation').show();
			 }
		 },
		 error:function(e){
				jQuery('#b_loader').hide();
				jQuery('#t_loader').hide();
				if((searchApp != 1) || FoundTheVerse){
					jQuery('.navigation').show();
				} else {
					jQuery('#scripture').html('<h2>Not Found! Please try again!</h2> '); // <---- this is the div id we update
					jQuery('#scripture').removeClass('text_loading');
				}
				if(!addTo && appMode == 1){
					jQuery('#scripture').html('<h2>No scripture was returned, please try again!</h2>'); // <---- this is the div id we update
					jQuery('#scripture').removeClass('text_loading');
				} else {
					// check if result else load next book
					loadNextBook(addTo,Found);
				}
			 },
		});
	} else {
		if(Found){
			FoundTheVerse = Found;
		}
		if(!addTo){
			jQuery('#scripture').addClass('text_loading');
		}
		// set text direction
		 if (jsonStore.direction == 'RTL'){
			var direction = 'rtl';
		 } else {
			var direction = 'ltr'; 
		 }
		 // check jsonStore
		 if (jsonStore.type == 'verse'){
			setVerses(jsonStore,direction,addTo);
		 } else if (jsonStore.type == 'chapter'){
			setChapter(jsonStore,direction,addTo);
		 } else if (jsonStore.type == 'book'){
			setBook(jsonStore,direction,addTo);
		 } else if (jsonStore.type == 'search'){
			setSearch(jsonStore,direction);
		 }
		 jQuery('#b_loader').hide();
		 jQuery('#t_loader').hide();
		 if(searchApp != 1 || FoundTheVerse){
			jQuery('.navigation').show();
		 }
	}
}

// Ajax Call to get Defaults
function getDefaults(request, requestStore) {
	if (typeof cPanelUrl !== 'undefined') {
		var getUrl = cPanelUrl+"index.php?option=com_getbible&task=bible.defaults&format=json";     	
	} else {
		var getUrl = "index.php?option=com_getbible&task=bible.defaults&format=json";
	}
	// Check if "requestStore" exists in the local storage
	var jsonStore = jQuery.jStorage.get(requestStore);
	if(!jsonStore){
		 if (typeof appKey !== 'undefined') {
			request = request+'&appKey='+appKey;
		}
		// get the defaults from server
		jQuery.ajax({
		 type: 'GET',
		 url: getUrl,
		 dataType: 'jsonp',
		 data: request,
		 jsonp: 'callback',
		 success:function(json){
			 // and save the result
			 jQuery.jStorage.set(requestStore,json);
			 // set defaults
			 BIBLE_BOOK 		= json.book_ref;
			 BIBLE_CHAPTER 		= json.chapter;
			 BIBLE_LAST_CHAPTER = json.lastchapter;
			 BIBLE_VERSION 		= json.version;
			 defaultVersion 	= json.version;
			 defaultVers		= json.vers;
			 defaultBook 		= json.book_ref;
			 defaultBookNr 		= json.book_nr;
			 defaultChapter 	= json.chapter;
			 setQuery 			= "p="+defaultBook+defaultChapter+"&v="+defaultVersion;
			 if(request && json.search == 1){
				 searchFor 			= json.searchFor;
				 searchCrit 		= json.crit;
				 searchType 		= json.type;
				 searchApp 			= json.search;
				 setQuery 			= "s="+searchFor+"&crit="+searchCrit+"&t="+searchType+"&v="+defaultVersion;
			 }
			 
			if(searchApp != 1){
				getDataBo(defaultVersion,defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
				getDataCh(defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
			} else {
				
				jQuery('#cPanel').hide();
				jQuery('.searchbuttons').show();
			}
			if(BIBLE_LAST_CHAPTER < 1){
				jQuery('#prev').hide();
			}
			// get the data
			getData(setQuery);
			// set the search version
			jQuery('.search_version').val(BIBLE_VERSION);
			// set the search book ref
			jQuery('.search_book').val(BIBLE_BOOK);
			 
		 },
		 error:function(e){

			 },
		});
	} else {
		// set defaults
		 BIBLE_BOOK 		= jsonStore.book_ref;	
		 BIBLE_CHAPTER 		= jsonStore.chapter;
		 BIBLE_LAST_CHAPTER = jsonStore.lastchapter;
		 BIBLE_VERSION 		= jsonStore.version;		
		 defaultVersion 	= jsonStore.version;
		 defaultVers		= jsonStore.vers;

		 defaultBook 		= jsonStore.book_ref;
		 defaultBookNr 		= jsonStore.book_nr;
		 defaultChapter 	= jsonStore.chapter;
		 setQuery 			= "p="+defaultBook+defaultChapter+"&v="+defaultVersion;
		 if(request && jsonStore.search == 1){
			 searchFor 			= jsonStore.searchFor;
			 searchCrit 		= jsonStore.crit;
			 searchType 		= jsonStore.type;
			 searchApp 			= jsonStore.search;
			 setQuery 			= "s="+searchFor+"&crit="+searchCrit+"&t="+searchType+"&v="+defaultVersion;
		 }
		 
		if(searchApp != 1){
			getDataBo(defaultVersion,defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
			getDataCh(defaultVersion+'__'+defaultBookNr+'__'+defaultBook);
		} else {
			jQuery('#cPanel').hide();
			jQuery('.searchbuttons').show();
		}
		if(BIBLE_LAST_CHAPTER < 1){
			jQuery('#prev').hide();
		}
		// get the data
		getData(setQuery);
		// set the search version
		jQuery('.search_version').val(BIBLE_VERSION);
		// set the search book ref
		jQuery('.search_book').val(BIBLE_BOOK);
	}	
}

// Set Verses
function setVerses(json,direction,addTo){
	var output = '';
		jQuery.each(json.book, function(index, value) {
			output += '<p class="uk-text-center uk-text-bold">'+value.book_name+'&#160;'+value.chapter_nr+'</p><p class=\"'+direction+'\">';
			jQuery.each(value.chapter, function(index, value) {
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '</span>&#160;&#160;<span class="verse" id="'+value.book_nr+'_'+value.chapter_nr+'_' +value.verse_nr+ '">';

				output += value.verse;
				if(verselineMode == 2){
					output += '</span>&#160;';
				} else {
					output += '</span><br/>';
				}
			});
			output += '</p>';
		});
		if(addTo){
			jQuery('#scripture').append(output);
		} else {
			jQuery('#scripture').html(output);  // <---- this is the div id we update
		}
		appFeatures(2);
		jQuery('#scripture').removeClass('text_loading');
}

// Set Chapter on App page
function setChapter(json,direction,addTo){
	listVers = getVerses();
	jQuery(".booksMenu").text(json.book_name+' '+json.chapter_nr+' ('+json.version+')');
	jQuery(".books :selected").text(json.book_name+' '+json.chapter_nr);
	var bookNr = null;
	var chapterNr = null;
	var output = '<p class="'+direction+'">';
	if(addTo){	output += '<span class="chapter_nr">'+json.chapter_nr+'</span>'; }
	jQuery.each(json.chapter, function(index, value) {
		if(allowAccount > 0){
			if(isInArray(value.verse_nr, listVers) ){
				output += '&#160;&#160;<span class="verse_nr ltr" id="nr__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '" onclick="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;" data-uk-tooltip="{pos:\'left\'}" title="Add Note & Tags">' +value.verse_nr+ '&#160;</span><span oncontextmenu="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;" class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += '<span class="highlight">' +value.verse+ '</span>';
			} else {
				output += '&#160;&#160;<span class="verse_nr ltr" id="nr__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '" onclick="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;" data-uk-tooltip="{pos:\'left\'}" title="Add Note & Tags">' +value.verse_nr+ '&#160;</span><span oncontextmenu="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;" class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			}
		} else {
			if(isInArray(value.verse_nr, listVers) ){
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '&#160;</span><span class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += '<span class="highlight">' +value.verse+ '</span>';
			} else {
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '&#160;</span><span class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			}
		}
		
		if(allowAccount > 0){
			if(verselineMode == 2){
				output += '</span><span id="note__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '"></span>&#160;';
			} else {
				output += '</span><span id="note__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '"></span><br/>';
			}
		} else {
			if(verselineMode == 2){
				output += '</span>&#160;';
			} else {
				output += '</span><br/>';
			}
		}
		if(!bookNr && !chapterNr){
			bookNr = json.book_nr;
			chapterNr = json.chapter_nr;
		}
	});
	// load verse tags
	getTaged(bookNr+'_'+chapterNr);
	output += '</p>';
	if(addTo){
		jQuery('#scripture').append(output);
	} else {
		jQuery('#scripture').html(output);  // <---- this is the div id we update
	}
	appFeatures(2);
	jQuery('#scripture').removeClass('text_loading');
}

// Set Book
function setBook(json,direction,addTo){
	var output = '';
	jQuery.each(json.book, function(index, value) {
		output += '<p class="uk-text-center uk-text-bold">'+json.book_name+'&#160;'+value.chapter_nr+'</p><p class="'+direction+'">';
		jQuery.each(value.chapter, function(index, value) {
			output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '</span>&#160;&#160;<span class="verse" id="'+json.book_nr+'_'+value.chapter_nr+'_' +value.verse_nr+ '">';
			output += value.verse;
			if(verselineMode == 2){
				output += '</span>&#160;';
			} else {
				output += '</span><br/>';
			}
		});
		output += '</p>';
	});
	if(addTo){
		jQuery('#scripture').append(output);
	} else {
		jQuery('#scripture').html(output);  // <---- this is the div id we update
	}
	appFeatures(2);
	jQuery('#scripture').removeClass('text_loading');
}

// Set Search
function setSearch(json,direction){

	var output = '<small>('+json.counter+')</small><br/>';
	jQuery.each(json.book, function(index, value) {
		var book_ref 	= value.book_ref.replace(/\s+/g, '');
		var setCall 	= 'p='+book_ref+value.chapter_nr+"&v="+BIBLE_VERSION;
		var setGlobal 	= book_ref+'__'+value.book_nr+'__'+value.chapter_nr+'__'+BIBLE_VERSION;
		output += '<p class="uk-text-center uk-text-bold"><a href="javascript:void(0)" onclick="loadFoundChapter(\''+setCall+'\',\''+setGlobal+'\')">'+value.book_name+'&#160;'+value.chapter_nr+'</a></p><p class="'+direction+'">';
		jQuery.each(value.chapter, function(index, chapter) {
			output += '&#160;&#160;<span class="verse_nr ltr">' +chapter.verse_nr+ '</span>&#160;&#160;<span class="verse" id="'+value.book_nr+'_'+value.chapter_nr+'_' +chapter.verse_nr+ '">';
			output += chapter.verse;
			output += '</span><br/>';
		});
		output += '</p>';
	});
	jQuery('#scripture').html(output);  // <---- this is the div id we update
	// add highlighting if auto hightligs is turned on
	if(highlightOption == 1){
		highScripture();							
	}
	appFeatures(2);
	jQuery('#scripture').removeClass('text_loading');
}

// get verses from string
function getVerses(){
	if(isNumeric(defaultVers)){
		var listVers = [];
		if (isInArray(",", defaultVers)){
			var result = defaultVers.split(',');
		} else {
			if (isInArray("-", defaultVers)){
				var numbers = defaultVers.split('-');
				for (var nr = numbers[0]; nr <= numbers[1]; nr++) {
					listVers.push(nr*1);
				}
			} else {
				listVers.push(defaultVers*1);
			}
		}
		defaultVers = 0;
		if(typeof result !== 'undefined'){
			for(var i = 0; i <= result.length; i++){
				if (isInArray("-", result[i])){
					var numbers = result[i].split('-');
					for (var nr = numbers[0]; nr <= numbers[1]; nr++) {
						listVers.push(nr*1);
					}
				} else {
					listVers.push(result[i]*1);
				}
			}
		}
		return listVers;
	}
	return false;
}
// check if value is in array
function isInArray(value, array) {
	if(typeof array !== 'undefined'){
		if(array !== false){
  			return array.indexOf(value) > -1;
		}
	} return false;
}
// check if number is found in string but not 0
function isNumeric(number) {
	if(typeof number !== 'undefined'){
		if(number != 0 && number != null){
			var matches = number.match(/\d+/g);
			if (matches != null) {
				return true;
			}
		}
	}
	return false;
}

	/*******************************************\
	*			  Control Pannel				*
	\*******************************************/

// Ajax Call to get chapter
function getDataCh(call) {
	if (typeof cPanelUrl !== 'undefined') {
		var getUrl = cPanelUrl+"index.php?option=com_getbible&task=bible.chapter&format=json";     	
	} else {
		var getUrl = "index.php?option=com_getbible&task=bible.chapter&format=json";
	}
	jQuery('#scripture').addClass('text_loading');
	var result = call.split('__');
	var Get = 'v='+result[0]+'&nr='+result[1];
	// check if already in local store
	var requestStore = 'chapters_'+Get;
	var jsonStore = jQuery.jStorage.get(requestStore);
	if(!jsonStore){
		if (typeof appKey !== 'undefined') {
			Get = Get+'&appKey='+appKey;
		}
		// get the chapters from server
		jQuery.ajax({
		 type: 'GET',
		 url: getUrl,
		 dataType: 'jsonp',
		 data: Get,
		 jsonp: 'callback',
		 success:function(json){
			 // and save the result
			jQuery.jStorage.set(requestStore,json);
			var output = '<div style="display: inline-block;">';
			jQuery.each(json, function(index, element) {
				var setGlobal = result[2]+'__'+element+'__'+result[0];
				output += '<div style="float: left; margin: 5px;"><button  class="uk-button uk-button-small" type="button" href="javascript:void(0)" onclick="getScripture(\'p='+result[2]+element+'&v='+result[0]+'\', \''+setGlobal+'\')"><span style="display: block; width: 30px;">'+element+'</span></button></div>';
			});
			output += '</div>';
			jQuery('#t_loader').hide();
			jQuery('#chapters').html(output);  // <---- this is the div id we update
		 },
		 error:function(){
				jQuery('#chapters').html('error!');
			 },
		});
	} else {
		var output = '<div style="display: inline-block;">';
		jQuery.each(jsonStore, function(index, element) {
			var setGlobal = result[2]+'__'+element+'__'+result[0];
			output += '<div style="float: left; margin: 5px;"><button  class="uk-button uk-button-small" type="button" href="javascript:void(0)" onclick="getScripture(\'p='+result[2]+element+'&v='+result[0]+'\', \''+setGlobal+'\')"><span style="display: block; width: 30px;">'+element+'</span></button></div>';
		});
		output += '</div>';
		jQuery('#t_loader').hide();
		jQuery('#chapters').html(output);  // <---- this is the div id we update
	}
}

// Ajax Call to get Books
function getDataBo(version, first, versionChange) {
	jQuery('.books').hide();
	jQuery('.f_loader').show();
	if (typeof cPanelUrl !== 'undefined') {
		var getUrl = cPanelUrl+"index.php?option=com_getbible&task=bible.books&format=json";     	
	} else {
		var getUrl = "index.php?option=com_getbible&task=bible.books&format=json";
	}
	jQuery('#scripture').addClass('text_loading');
	
	// check if already in local store
	var requestStore = 'books_'+version;
	// first check if any books have been updated	
	var reloadBooksStore = jQuery.jStorage.get('booksDate');
	if(!reloadBooksStore){
			jQuery.jStorage.set('booksDate',booksDate);
			reloadBooksStore = booksDate;
	}
	if(reloadBooksStore == booksDate){
		var jsonStore = jQuery.jStorage.get(requestStore);
	} else {
		var newBooksDate = booksDate.split('_');
		if(version == newBooksDate[0]){
			var jsonStore = jQuery.jStorage.get('reload');
			jQuery.jStorage.set('booksDate',booksDate);
		} else {
			var jsonStore = jQuery.jStorage.get(requestStore);
		}
	}
	if(!jsonStore){
		var Get = 'v='+version;
		if (typeof appKey !== 'undefined') {
			Get = Get+'&appKey='+appKey;
		}
		// get the books from server
		jQuery.ajax({
		 type: 'GET',
		 url: getUrl,
		 dataType: 'jsonp',
		 data: Get,
		 jsonp: 'callback',
		 success:function(json){
			 // and save the result
			jQuery.jStorage.set(requestStore,json);
			var op = new Option('- Select Book -', '');
			/// jquerify the DOM object 'o' so we can use the html method
			jQuery(op).html('- Select Book -');
			jQuery('.books').append(op);
			jQuery.each(json, function() {
				str = this.ref.replace(/\s+/g, '');
				$value = version+'__'+this.book_nr+'__'+str;
				var op = new Option(this.book_name, $value);
				/// jquerify the DOM object 'o' so we can use the html method
				jQuery(op).html(this.book_name);
				jQuery('.books').append(op);
			});
			if(first){
				if(versionChange == 1){
					var active = first.split('__');
					var option = '';
					var loadthis = '';
					jQuery(".books option").each(function(){
						option = this.value;
						var check = option.split('__');
						if(check[1] == active[1]){
							 jQuery(".books").val(option);
							 loadthis = option;
							 breakOut = true;
							 return false;
						}
					});
					if(loadthis && breakOut){
						jQuery('.books').val(loadthis);
						var builder 	= loadthis.split('__');
						var calling 	= 'p='+builder[2]+BIBLE_CHAPTER+'&v='+builder[0];
						var globalSet 	= builder[2]+'__'+BIBLE_CHAPTER+'__'+builder[0];
						getDataCh(loadthis);
						getScripture(calling,globalSet);
					}
				} else {
					jQuery('.books').val(first);
				}
			}
			jQuery('.f_loader').hide();
			jQuery('.books').show();
		 },
		 error:function(){
				jQuery('.books').append('error');
			 },
		});
	} else {
		var op = new Option('- Select Book -', '');
		// jquerify the DOM object 'o' so we can use the html method
		jQuery(op).html('- Select Book -');
		jQuery('.books').append(op);
		jQuery.each(jsonStore, function() {
			str = this.ref.replace(/\s+/g, '');
			$value = version+'__'+this.book_nr+'__'+str;
			var op = new Option(this.book_name, $value);
			/// jquerify the DOM object 'o' so we can use the html method
			jQuery(op).html(this.book_name);
			jQuery('.books').append(op);
		});
		if(first){
			if(versionChange == 1){
				var active = first.split('__');
				var option = '';
				var loadthis = '';
				jQuery(".books option").each(function(){
					option = this.value;
					var check = option.split('__');
					if(check[1] == active[1]){
						 jQuery(".books").val(option);
						 loadthis = option;
						 breakOut = true;
						 return false;
					}
				});
				if(loadthis && breakOut){
					jQuery('.books').val(loadthis);
					var builder 	= loadthis.split('__');
					var calling 	= 'p='+builder[2]+BIBLE_CHAPTER+'&v='+builder[0];
					var globalSet 	= builder[2]+'__'+BIBLE_CHAPTER+'__'+builder[0];
					getDataCh(loadthis);
					getScripture(calling,globalSet);
				}
			} else {
				jQuery('.books').val(first);
			}
		}
		jQuery('.f_loader').hide();
		jQuery('.books').show();
	}
}
// scroll to top function
function gotoTop(){
	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
}
// show small cpanel
function showSmallCpanel(){
	jQuery('#small_cpanel').slideToggle( "slow" );
	gotoTop();
}
// show chapter selection
function showChapters() {
	// jQuery('.button_chapters').hide();
	jQuery('#chapters').slideToggle( "slow" );
	gotoTop();
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
	jQuery('.button_chapters').show();
	gotoTop();
	if(BIBLE_LAST_CHAPTER < 1){
		jQuery('#prev').hide();
	}
}

// Set window scroll
var didScroll = false;
jQuery(window).scroll(function() {
    didScroll = true;
});
// set delay timer var
var clickTimer = false;
// Get next chapter as you scroll down
function loadTimer1(){
	timerInterval_1 = setInterval(function() {
		if ((autoLoadChapter === 1) && didScroll) {
			if (jQuery(window).scrollTop() >= jQuery(document).height() - jQuery(window).height() - 10) {
				if(appMode == 1){				
					// listen for click
				   if(clickTimer) { 
					  // abort previous request if 800ms have not passed
					  clearTimeout(clickTimer);
				   }
					clickTimer = setTimeout(function() {  nextChapter();  },1000);
				} else {
					nextChapter();
				}
				// set scroll lock
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
		jQuery('#b_loader').show();
	} else if(appMode == 2){
		addTo = false;
		gotoTop();
		jQuery('#t_loader').show();
		if(BIBLE_CHAPTER > 1){
			jQuery('#prev').show();
		}
	}
	jQuery('.navigation').hide();
	getData('p='+BIBLE_BOOK+BIBLE_CHAPTER+'&v='+BIBLE_VERSION,addTo);
}
// get previous chapter with prev button
function prevChapter(){
	addTo = false;
	if(appMode == 2){
		gotoTop();
	}
	if(BIBLE_LAST_CHAPTER < 1){
		// this should not happen... since it should be hidden.
		jQuery('#prev').hide();
	} else {
		jQuery('.navigation').hide();
		jQuery('#t_loader').show();
		getData('p='+BIBLE_BOOK+BIBLE_LAST_CHAPTER+'&v='+BIBLE_VERSION,addTo);
		BIBLE_CHAPTER--;
		BIBLE_LAST_CHAPTER--;
		if(BIBLE_LAST_CHAPTER < 1){
			jQuery('#prev').hide();
		}
	}
}
