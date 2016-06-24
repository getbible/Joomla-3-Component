/**
* 
* 	@version 	1.0.9  June 24, 2016
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
var viewType			= 0;
var viewTags			= 0;
var viewNotes			= 0;

// set highlight array
var alpha 			= "abcdefghijklmnopqrstuvwxyz";
var highlightsArray = alpha.split("");

// set tag name
var TagVerseName 	= null;

// get the data from the API
jQuery(function() {
	// load defaults
	getDefaults(defaultRequest, defaultKey, tagQuery);
	appFeatures(1);
	setTextSize();
	setCpanelTags();
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
function loadChapter(call, setGlobal){
	if(autoLoadChapter === 1){
		loadTimer1();
	}
	//  Call to get scripture
	jQuery('#tagheader').hide();
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
	} else {
		jQuery('#active_note').val('');
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
function setTagDiv(id){
	// set tags
	if(user_id > 0 && allowAccount > 0){
		var localVerseTags = jQuery.jStorage.get('taged__'+id, null);
		if(localVerseTags){
			var html = '<ul id="tag__'+id+'">';
				jQuery.each(localVerseTags, function(tag, name) {
					html += '<li>'+name+'</li>';
				});
			html += '</ul>';
		} else {
			var html = '<ul id="tag__'+id+'"></ul>';
		}					
		jQuery('#tagDiv').html(html);
		jQuery('#tag__'+id).tagit({
			// Options
			availableTags: defaultTags,
			autocomplete: {delay: autocomplete_delay, minLength: autocomplete_min_length},
			showAutocompleteOnFocus: autocomplete_show,
			allowSpaces: allow_spaces,
			placeholderText: placeholder_text,
			caseSensitive: case_sensitive,
			
			afterTagAdded: function(evt, ui) {
				if (!ui.duringInitialization) {
					var taged = jQuery('#tag__'+id).tagit('tagLabel', ui.tag);
					// set the taged verse
					setTaged(taged,1,id);
					// if new tag add to tag list
					if(!in_array(taged,defaultTags)){
						defaultTags.push(taged);
						jQuery.jStorage.set('tags',defaultTags);
					}
					tagVerse(taged,1,id);
					setTimeout(function() { setCpanelTags(); }, 1000);
				}
			},
			afterTagRemoved: function(evt, ui) {
				var untaged = jQuery('#tag__'+id).tagit('tagLabel', ui.tag);
				// unset the taged verse
				setTaged(untaged,0,id);
				tagVerse(untaged,0,id);
				setTimeout(function() { setCpanelTags(); }, 1000);
			},
			onTagClicked: function(event, ui) {
				// do something special
				var tagName = jQuery('#tag__'+id).tagit('tagLabel', ui.tag);
				// get tag verse list
				getTagVerse(tagName);								
			}
		});
		jQuery.UIkit.modal("#note_maker").show();
	} else {
		jQuery.UIkit.modal("#note_maker").show();
	}
}
function tagVerse(tag,action,vers){
	
	if(action == 1){
		var title = '';
		if (jQuery('#tag_'+vers).length > 0){
			title = jQuery('#tag_'+vers).attr('title')+', '+tag;
		} else {
			title = tag;
		}
		if(verselineMode == 2){
			html = '<span id="tag_'+vers+'" class="tag verse_nr uk-text-muted" title="'+title+'" ><i class="uk-icon-tag"></i>&nbsp;</span>';
		} else {
			html = '<span id="tag_'+vers+'" class="tag verse_nr uk-text-muted" title="'+title+'" ><i class="uk-icon-tag"></i>&nbsp;</span>';
		}
		jQuery('#tag_'+vers).remove();
		jQuery('#nr__'+vers+' > .tags').append(html);
	} else {
		var title = '';
		if (jQuery('#tag_'+vers).length > 0){
			title = jQuery('#tag_'+vers).attr('title');
			if (title.indexOf(', ') >= 0){
				var array = title.split(', ');
				// remove the tag
				array = jQuery.grep(array, function(value) {
				  return value != tag;
				});
				title = array.join(", ");
			} else if (title.indexOf(tag) >= 0){
				title = title.replace(tag,'');
			}
			if (title){
				if(verselineMode == 2){
					html = '<span id="tag_'+vers+'" class="tag verse_nr uk-text-muted" title="'+title+'" ><i class="uk-icon-tag"></i>&nbsp;</span>';
				} else {
					html = '<span id="tag_'+vers+'" class="tag verse_nr uk-text-muted" title="'+title+'" ><i class="uk-icon-tag"></i>&nbsp;</span>';
				}
				jQuery('#tag_'+vers).remove();
				jQuery('#nr__'+vers+' > .tags').append(html);
			} else {
				jQuery('#tag_'+vers).remove();
			}
		} 
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
						if(!in_array(tag, localVerseTags)){
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
						if(in_array(tag, localVerseTags)){
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
							if(!in_array(tag, localVerseTag)){
								localVerseTag.push(tag);
							}
						});
					} else {
						localVerseTag = [];
						jQuery.each(tags, function(id,tag) {
							if(!in_array(tag, localVerseTag)){
								localVerseTag.push(tag);
							}
						});
					}
					var title = localVerseTag.join(", ");
					tagVerse(title,1,verse+'_'+vers);
					jQuery.jStorage.set('taged__'+verse+'_'+vers,localVerseTag);
				}); 
			}
		});
	}
	return false;
}
function setCpanelTags(){
	if(user_id > 0 && allowAccount > 0){
		getStatusTags_db().done(function(tags) {
			if(tags){
				var usedTags = '';
				var unusedTags = '';
				var defaultTags = '';
				jQuery.each(tags, function( status, tagStatus) {
					jQuery.each(tagStatus, function( alpha, tagName) {
						if('used' == status && tagName) {
							var id = tagName.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
							usedTags += ' <button id="cp_used_'+id+'" class="uk-button uk-button-primary uk-margin-small-bottom" rel="'+htmlspecialchars(tagName)+'" onclick="getTagVerseButton(\'cp_used_'+id+'\')" type="button">'+htmlspecialchars(tagName)+'</button> ';
						} else if('unused' == status && tagName) {
							var id = tagName.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
							unusedTags += ' <button id="cp_unused_'+id+'" class="uk-button uk-button-danger uk-margin-small-bottom" rel="'+htmlspecialchars(tagName)+'" onclick="removeTag(\'cp_unused_'+id+'\')" type="button"><i class="uk-icon-remove"></i> '+tagName+'</button> ';
						} else if('default' == status && tagName) {
							defaultTags += ' <button class="uk-button uk-margin-small-bottom" type="button" disabled>'+htmlspecialchars(tagName)+'</button> ';
						}
					});
				});
				if(usedTags.length > 0){
					jQuery('#usedTags').html('<p data-uk-margin>'+usedTags+'</p>');
				} else {
					jQuery('#usedTags').html('No Active tags');
				}
				if(unusedTags.length > 0){
					jQuery('#unusedTags').html('<p data-uk-margin>'+unusedTags+'</p>');
				} else {
					jQuery('#unusedTags').html('No Inactive tags');					
				}
				if(defaultTags.length > 0){
					jQuery('#defaultTags').html('<p data-uk-margin>'+defaultTags+'</p>');
				} else {
					jQuery('#defaultTags').html('No Inactive Defaults tags');
				}
			}
		});
	}
}

function getStatusTags_db(){
	// get status of tags from server
	if(user_id > 0 && allowAccount > 0){
		var request = '&jsonKey='+jsonKey+'&tu='+openNow;
		var getUrl 	= "index.php?option=com_getbible&task=bible.getstatustags&format=json";
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
function removeTag(id){
	if(user_id > 0 && allowAccount > 0){
		var tag = jQuery('#'+id).attr('rel');
		jQuery('#confirmDelete').attr('rel', tag);
		jQuery.UIkit.modal("#delete_tag").show();
	}
}
function removeTag_confirmed(){
	if(user_id > 0 && allowAccount > 0){
		jQuery.UIkit.modal("#user_cPanel").show();
		var tag = jQuery('#confirmDelete').attr('rel');
		setTags_db(tag,0).done(function(set) {
			if(set){
				defaultTags = jQuery.grep(defaultTags, function(value) {
					return value != tag;
				});
				jQuery.jStorage.set('tags',defaultTags);
				setCpanelTags();
			}
		});
	}
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
			getTags_db().done(function(allTags) {
				if(allTags){
					if(tags_defaults){
						var newArray	= jQuery.merge(allTags, tags_defaults);
						allTags			= uniqueArray(newArray);
					}
					
					jQuery.jStorage.set('tags',allTags);
					defaultTags = allTags;
				}
			});
		} else {
			if(tags_defaults){
				var newArray	= jQuery.merge(localTags, tags_defaults);
				localTags		= uniqueArray(newArray);
			}
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
function getTagVerse(name){
	if(user_id > 0 && allowAccount > 0){
		getTagVerse_db(name).done(function(tagVerseQuery) {
			if(tagVerseQuery){
				if(typeof timerInterval_1 !== 'undefined'){
					clearInterval(timerInterval_1); // stop the interval
				}
				TagVerseName = name;
				// get the verses
				getData(tagVerseQuery, false, false);
				// hide stuff not needed for tag view
				jQuery('.searchbuttons').hide();
				jQuery('.navigation').hide();
				jQuery('.button_chapters').hide();
			 	jQuery('#cPanel').hide();
				// close the modal if open
				jQuery.UIkit.modal("#note_maker").hide();
				return true;
			}
		});
	}
	return false;
}
function getTagVerseButton(id){
	if(user_id > 0 && allowAccount > 0){
		var name = jQuery('#'+id).attr('rel');
		getTagVerse_db(name).done(function(tagVerseQuery) {
			if(tagVerseQuery){
				if(typeof timerInterval_1 !== 'undefined'){
					clearInterval(timerInterval_1); // stop the interval
				}
				TagVerseName = name;
				// get the verses
				getData(tagVerseQuery, false, false);
				// hide stuff not needed for tag view
				jQuery('.searchbuttons').hide();
				jQuery('.navigation').hide();
				jQuery('.button_chapters').hide();
			 	jQuery('#cPanel').hide();
				// close the modal if open
				jQuery.UIkit.modal("#user_cPanel").hide();
				return true;
			}
		});
	}
	return false;
}
function getTagVerse_db(tag){
	// set highlight	on server
	if(user_id > 0 && allowAccount > 0){
		var request	= '&jsonKey='+jsonKey+'&tu='+openNow+'&version='+BIBLE_VERSION+'&tag='+tag;
		var getUrl 	= "index.php?option=com_getbible&task=bible.gettagverse&format=json";
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
function printTag(){
	
	window.print();
	return false;
}

// email function starts here
function setupEmail(){
	if(user_id > 0 && allowAccount > 0){
		jQuery.UIkit.modal("#setup_email").show();
	}
}
function sendEmail(){
	jQuery.UIkit.modal("#setup_email").hide();
	if(user_id > 0 && allowAccount > 0){
		// get html to send in email
		var html 	= jQuery('#printTagArea').html();
		html		= encodeURIComponent(html);
		html		= Base64.encode(html);
		// get the email address to send the email to
		var email 	= jQuery('#email_address').val();
		email		= Base64.encode(email);
		// and then send the email!!!
		sendEmail_server(html,email).done(function(response) {
			jQuery.UIkit.notify({message: response.message, timeout: 5000, status: response.status, pos: 'top-right'});
		});
	}
	return false;
}

function sendEmail_server(html,email){
	// set highlight	on server
	if(user_id > 0 && allowAccount > 0){
		if(viewType == 1){
			// a tag type
			var title = encodeURIComponent(TagVerseName);
		} else if(viewType == 2){
			// a search type
			var title = encodeURIComponent(searchFor);
		} else {
			var title = encodeURIComponent('not found');
		}
		var request	= '&jsonKey='+jsonKey+'&tu='+openNow+'&version='+BIBLE_VERSION+'&email='+email+'&html='+html+'&title='+title+'&type='+viewType;
		var getUrl 	= "index.php?option=com_getbible&task=bible.sendemail&format=json";
		return jQuery.ajax({
			type: 'POST',
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
// all highlights stuff
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
		var getUrl = "index.php?option=com_getbible&task=bible.sethighlight&format=json";
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
			jQuery("#scripture").removeClass("uk-invisible");
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
				jQuery('#scripture').addClass('uk-invisible');
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
	// always close these when new data is loaded
	jQuery('#small_cpanel').hide();
	jQuery('#chapters').hide();
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
		jQuery('#scripture').addClass('uk-invisible');
	}	
	if(Found){
		FoundTheVerse = true;
	} else {
		FoundTheVerse = false;
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
		 },
		 error:function(e){
				jQuery('#b_loader').hide();
				jQuery('#t_loader').hide();
				if((searchApp != 1) || FoundTheVerse){
					jQuery('.navigation').show();
				} else {
					jQuery('#scripture').html('<h2>Not Found! Please try again!</h2> '); // <---- this is the div id we update
					jQuery("#scripture").removeClass("uk-invisible");
				}
				if(!addTo && appMode == 1){
					jQuery('#scripture').html('<h2>No scripture was returned, please try again!</h2>'); // <---- this is the div id we update
					jQuery("#scripture").removeClass("uk-invisible");
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
			jQuery('#scripture').addClass('uk-invisible');
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
	}
}

// Ajax Call to get Defaults
function getDefaults(request, requestStore, tagview) {
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
			 if(tagview){
				 getTagVerse(tagview);
			 } else {
				 setQuery = "p="+defaultBook+defaultChapter+"&v="+defaultVersion;
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
			 }
			 
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
		 if(tagview){
			 getTagVerse(tagview);
		 } else {
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
}

// Set Verses
function setVerses(json,direction,addTo){
	var output		= '<div id="tagheader" class="uk-button-group"><a class="uk-button" data-uk-modal="" href="#user_cPanel"><i class="uk-icon-cog uk-icon-spin"></i></a><button class="uk-button" type="button" disabled>'+TagVerseName+'&#160;<i class="uk-icon-tag"></i></button><button onclick="printTag()" class="uk-button" type="button"><i class="uk-icon-print"></i></button><button onclick="setupEmail()" class="uk-button" type="button"><i class="uk-icon-envelope-o"></i></button></div><div id="printTagArea">';
	var bookTags	= [];
	var viewing		= setViewing();
	jQuery.each(json.book, function(index, books) {
		var book_ref 	= books.book_ref.replace(/\s+/g, '');
		var setCall 	= 'p='+book_ref+books.chapter_nr+"&v="+BIBLE_VERSION;
		var setGlobal 	= book_ref+'__'+books.book_nr+'__'+books.chapter_nr+'__'+BIBLE_VERSION;
		output += '<p class="uk-text-center uk-text-bold"><a href="javascript:void(0)" onclick="loadChapter(\''+setCall+'\',\''+setGlobal+'\')">'+books.book_name+'&#160;'+books.chapter_nr+'</a></p><p class=\"'+direction+'\">';
		jQuery.each(books.chapter, function(index, value) {
			if(right_click == 1){ var oncontextmenu = 'oncontextmenu="makeNote(\''+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '\');return false;"'; } else {  var oncontextmenu = ''; }
			if(allowAccount > 0){
				output += '&#160;&#160;<span class="verse_nr ltr" id="nr__'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '" onclick="makeNote(\''+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '\');return false;" data-uk-tooltip="{pos:\'left\'}" title="Add Note & Tags">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span '+oncontextmenu+' class="verse" id="'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			} else {
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span class="verse" id="'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			}
			
			if(allowAccount > 0){
				if(verselineMode == 2){
					output += '</span><span id="note__'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '" '+viewing.note+'></span>&#160;';
				} else {
					output += '</span><span id="note__'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '" '+viewing.note+'></span><br/>';
				}
			} else {
				if(verselineMode == 2){
					output += '</span>&#160;';
				} else {
					output += '</span><br/>';
				}
			}
		});
		bookTags.push(books.book_nr+'_'+books.chapter_nr);
		output += '</p>';
	});
	bookTags = uniqueArray(bookTags);
	jQuery(bookTags).each(function(index, bookTag) {
		// load verse tags
		getTaged(bookTag);
	});
	output += '</div>';
	jQuery('#scripture').html(output);  // <---- this is the div id we update
	appFeatures(2);
	viewType = 1;
	jQuery("#scripture").removeClass("uk-invisible");
}

// Set Chapter on App page
function setChapter(json,direction,addTo){
	listVers = getVerses();
	jQuery(".booksMenu").text(json.book_name+' '+json.chapter_nr+' ('+json.version+')');
	jQuery(".books :selected").text(json.book_name+' '+json.chapter_nr);
	var bookNr		= null;
	var chapterNr	= null;
	var viewing		= setViewing();
	var output = '<p class="'+direction+'">';
	if(addTo){	output += '<span class="chapter_nr">'+json.chapter_nr+'</span>'; }
	
	jQuery.each(json.chapter, function(index, value) {
		if(right_click == 1){ var oncontextmenu = 'oncontextmenu="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;"'; } else {  var oncontextmenu = ''; }
		if(allowAccount > 0){
			if(in_array(value.verse_nr, listVers) ){
				output += '&#160;&#160;<span class="verse_nr ltr" id="nr__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+'" onclick="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;" data-uk-tooltip="{pos:\'left\'}" title="Add Note & Tags">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span '+oncontextmenu+' class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += '<span class="highlight">' +value.verse+ '</span>';
			} else {
				output += '&#160;&#160;<span class="verse_nr ltr" id="nr__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '" onclick="makeNote(\''+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '\');return false;" data-uk-tooltip="{pos:\'left\'}" title="Add Note & Tags">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span '+oncontextmenu+' class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			}
		} else {
			if(in_array(value.verse_nr, listVers) ){
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += '<span class="highlight">' +value.verse+ '</span>';
			} else {
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span class="verse" id="'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			}
		}
		
		if(allowAccount > 0){
			if(verselineMode == 2){
				output += '</span><span id="note__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '" '+viewing.note+'></span>&#160;';
			} else {
				output += '</span><span id="note__'+json.book_nr+'_'+json.chapter_nr+'_' +value.verse_nr+ '" '+viewing.note+'></span><br/>';
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
	stopAutoLoad = 0;
	viewType = 0;
	if(searchApp != 1 || FoundTheVerse){
		jQuery('.navigation').show();
	}
	jQuery("#scripture").removeClass("uk-invisible");
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
	stopAutoLoad = 1;
	viewType = 3;
	jQuery("#scripture").removeClass("uk-invisible");
}

// Set Search
function setSearch(json,direction){

	var output		= '<div id="printTagArea"><small>('+json.counter+')</small><br/>';
	var bookTags	= [];
	var viewing		= setViewing();
	jQuery.each(json.book, function(index, books) {
		var book_ref 	= books.book_ref.replace(/\s+/g, '');
		var setCall 	= 'p='+book_ref+books.chapter_nr+"&v="+BIBLE_VERSION;
		var setGlobal 	= book_ref+'__'+books.book_nr+'__'+books.chapter_nr+'__'+BIBLE_VERSION;
		output += '<p class="uk-text-center uk-text-bold"><a href="javascript:void(0)" onclick="loadChapter(\''+setCall+'\',\''+setGlobal+'\')">'+books.book_name+'&#160;'+books.chapter_nr+'</a></p><p class="'+direction+'">';
		jQuery.each(books.chapter, function(index, value) {
			if(right_click == 1){ var oncontextmenu = 'oncontextmenu="makeNote(\''+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '\');return false;"'; } else {  var oncontextmenu = ''; }
			if(allowAccount > 0){
				output += '&#160;&#160;<span class="verse_nr ltr" id="nr__'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '" onclick="makeNote(\''+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '\');return false;" data-uk-tooltip="{pos:\'left\'}" title="Add Note & Tags">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span '+oncontextmenu+' class="verse" id="'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			} else {
				output += '&#160;&#160;<span class="verse_nr ltr">' +value.verse_nr+ '&#160;<span '+viewing.tag+'></span></span><span class="verse" id="'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '">';
				output += value.verse;
			}
			
			if(allowAccount > 0){
				if(verselineMode == 2){
					output += '</span><span id="note__'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '" '+viewing.note+'></span>&#160;';
				} else {
					output += '</span><span id="note__'+books.book_nr+'_'+books.chapter_nr+'_' +value.verse_nr+ '" '+viewing.note+'></span><br/>';
				}
			} else {
				if(verselineMode == 2){
					output += '</span>&#160;';
				} else {
					output += '</span><br/>';
				}
			}
		});
		bookTags.push(books.book_nr+'_'+books.chapter_nr);
		output += '</p>';
	});
	output += '</div>';
	bookTags = uniqueArray(bookTags);
	jQuery(bookTags).each(function(index, bookTag) {
		// load verse tags
		getTaged(bookTag);
	});
	jQuery('#scripture').html(output);  // <---- this is the div id we update
	// add highlighting if auto hightligs is turned on
	if(highlightOption == 1){
		highScripture();							
	}
	appFeatures(2);
	stopAutoLoad = 1;
	viewType = 2;
	jQuery("#scripture").removeClass("uk-invisible");
}

// get verses from string
function getVerses(){
	if(is_numeric(defaultVers)){
		var listVers = [];
		if (in_array(",", defaultVers)){
			var result = defaultVers.split(',');
		} else {
			if (in_array("-", defaultVers)){
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
				if (in_array("-", result[i])){
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
function in_array(needle, haystack, argStrict) {
  //  discuss at: http://phpjs.org/functions/in_array/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: vlado houba
  // improved by: Jonas Sciangula Street (Joni2Back)
  //    input by: Billy
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
  //   returns 1: true
  //   example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
  //   returns 2: false
  //   example 3: in_array(1, ['1', '2', '3']);
  //   example 3: in_array(1, ['1', '2', '3'], false);
  //   returns 3: true
  //   returns 3: true
  //   example 4: in_array(1, ['1', '2', '3'], true);
  //   returns 4: false

  var key = '',
    strict = !! argStrict;

  //we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] == ndl)
  //in just one for, in order to improve the performance 
  //deciding wich type of comparation will do before walk array
  if (strict) {
    for (key in haystack) {
      if (haystack[key] === needle) {
        return true;
      }
    }
  } else {
    for (key in haystack) {
      if (haystack[key] == needle) {
        return true;
      }
    }
  }

  return false;
}

function uniqueArray(list) {
  var result = [];
  jQuery.each(list, function(i, e) {
    if (jQuery.inArray(e, result) == -1) result.push(e);
  });
  return result;
}
// check if number is found in string but not 0
function is_numeric(mixed_var) {
  //  discuss at: http://phpjs.org/functions/is_numeric/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: David
  // improved by: taith
  // bugfixed by: Tim de Koning
  // bugfixed by: WebDevHobo (http://webdevhobo.blogspot.com/)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Denis Chenu (http://shnoulle.net)
  //   example 1: is_numeric(186.31);
  //   returns 1: true
  //   example 2: is_numeric('Kevin van Zonneveld');
  //   returns 2: false
  //   example 3: is_numeric(' +186.31e2');
  //   returns 3: true
  //   example 4: is_numeric('');
  //   returns 4: false
  //   example 5: is_numeric([]);
  //   returns 5: false
  //   example 6: is_numeric('1 ');
  //   returns 6: false

  var whitespace =
    " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
  return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
    1)) && mixed_var !== '' && !isNaN(mixed_var);
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
	jQuery('#scripture').addClass('uk-invisible');
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
	jQuery('#scripture').addClass('uk-invisible');
	
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
			 jQuery('.books').find('option').remove().end();
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
		jQuery('.books').find('option').remove().end();
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
// clear all browser memory
function clearBrowser(){
	jQuery.jStorage.flush();
}
function setViewing(){
	checkViewing();
	var noteAction = 'class="notes"';
	if(viewNotes == 1){
		// hide notes
		noteAction = 'class="notes uk-hidden no-print"';
		var status = jQuery("#toggleNotes").attr('rel');
		if(status == 'hide'){
			// change text to show
			var hideText = jQuery("#toggleNotes").text();
			var showText = jQuery("#toggleNotes").attr('toggle');
			jQuery("#toggleNotes").text(showText);
			jQuery("#toggleNote").text(showText);
			jQuery("#toggleNotes").attr('toggle', hideText);
			jQuery("#toggleNotes").attr('rel', 'show');
		}
	}
	var tagAction = 'class="tags"';
	if(viewTags == 1){
		// hide tags
		tagAction = 'class="tags uk-hidden"';
		var status = jQuery("#toggleTags").attr('rel');
		if(status == 'hide'){
			// change text to show
			var hideText = jQuery("#toggleTags").text();
			var showText = jQuery("#toggleTags").attr('toggle');
			jQuery("#toggleTags").text(showText);
			jQuery("#toggleTag").text(showText);
			jQuery("#toggleTags").attr('toggle', hideText);
			jQuery("#toggleTags").attr('rel', 'show');
		}
	}
	// set action
	var action = {note:noteAction, tag:tagAction};
	return action;
}
function checkViewing(){
	var viewNotesStored = jQuery.jStorage.get('viewNotes');
	if(viewNotesStored){
		viewNotes = viewNotesStored;
	}
	var viewTagsStored = jQuery.jStorage.get('viewTags');
	if(viewTagsStored){
		viewTags = viewTagsStored;
	}
}
// toggel highlights notes & tags
function toggle(type){
	if('tags' == type){
		var status = jQuery("#toggleTags").attr('rel');
		if(status == 'hide'){
			// change text to show
			var hideText = jQuery("#toggleTags").text();
			var showText = jQuery("#toggleTags").attr('toggle');
			jQuery("#toggleTags").text(showText);
			jQuery("#toggleTag").text(showText);
			jQuery("#toggleTags").attr('toggle', hideText);
			jQuery("#toggleTags").attr('rel', 'show');
			// do action
			jQuery('.tags').addClass('uk-hidden');
			viewTags = 1;
		} else if(status == 'show'){
			// change text to hide
			var showText = jQuery("#toggleTags").text();
			var hideText = jQuery("#toggleTags").attr('toggle');
			jQuery("#toggleTags").text(hideText);
			jQuery("#toggleTag").text(hideText);
			jQuery("#toggleTags").attr('toggle', showText);
			jQuery("#toggleTags").attr('rel', 'hide');
			// do action
			jQuery('.tags').removeClass('uk-hidden');
			viewTags = 0;
		}
		jQuery.jStorage.set('viewTags',viewTags)		
	} else if('notes' == type){
		var status = jQuery("#toggleNotes").attr('rel');
		if(status == 'hide'){
			// change text to show
			var hideText = jQuery("#toggleNotes").text();
			var showText = jQuery("#toggleNotes").attr('toggle');
			jQuery("#toggleNotes").text(showText);
			jQuery("#toggleNote").text(showText);
			jQuery("#toggleNotes").attr('toggle', hideText);
			jQuery("#toggleNotes").attr('rel', 'show');
			// do action
			jQuery('.notes').addClass('uk-hidden');
			jQuery('.notes').addClass('no-print');
			viewNotes = 1;
		} else if(status == 'show'){
			// change text to hide
			var showText = jQuery("#toggleNotes").text();
			var hideText = jQuery("#toggleNotes").attr('toggle');
			jQuery("#toggleNotes").text(hideText);
			jQuery("#toggleNote").text(hideText);
			jQuery("#toggleNotes").attr('toggle', showText);
			jQuery("#toggleNotes").attr('rel', 'hide');
			// do action
			jQuery('.notes').removeClass('uk-hidden');
			jQuery('.notes').removeClass('no-print');
			viewNotes = 0;
		}
		jQuery.jStorage.set('viewNotes',viewNotes)
	} else if('highlights' == type){
		var status = jQuery("#toggleHighlights").attr('rel');
		if(status == 'hide'){
			// chang text to show
			var hideText = jQuery("#toggleHighlights").text();
			var showText = jQuery("#toggleHighlights").attr('toggle');
			jQuery("#toggleHighlights").text(showText);
			jQuery("#toggleHighlights").attr('toggle', hideText);
			jQuery("#toggleHighlights").attr('rel', 'show');
			// do action
			
		} else if(status == 'show'){
			// chang text to show
			var showText = jQuery("#toggleHighlights").text();
			var hideText = jQuery("#toggleHighlights").attr('toggle');
			jQuery("#toggleHighlights").text(hideText);
			jQuery("#toggleHighlights").attr('toggle', showText);
			jQuery("#toggleHighlights").attr('rel', 'hide');
			// do action
						
		}
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
	if(appMode == 1 && stopAutoLoad == 0){
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

function htmlspecialchars(string, quote_style, charset, double_encode) {
  //       discuss at: http://phpjs.org/functions/htmlspecialchars/
  //      original by: Mirek Slugen
  //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //      bugfixed by: Nathan
  //      bugfixed by: Arno
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //      bugfixed by: Brett Zamir (http://brett-zamir.me)
  //       revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //         input by: Ratheous
  //         input by: Mailfaker (http://www.weedem.fr/)
  //         input by: felix
  // reimplemented by: Brett Zamir (http://brett-zamir.me)
  //             note: charset argument not supported
  //        example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
  //        returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
  //        example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
  //        returns 2: 'ab"c&#039;d'
  //        example 3: htmlspecialchars('my "&entity;" is still here', null, null, false);
  //        returns 3: 'my &quot;&entity;&quot; is still here'

  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined' || quote_style === null) {
    quote_style = 2;
  }
  string = string.toString();
  if (double_encode !== false) {
    // Put this first to avoid double-encoding
    string = string.replace(/&/g, '&amp;');
  }
  string = string.replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') {
    // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/'/g, '&#039;');
  }
  if (!noquotes) {
    string = string.replace(/"/g, '&quot;');
  }

  return string;
}
