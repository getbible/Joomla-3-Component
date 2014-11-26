<?php
/**
* 
* 	@version 	1.0.3  November 25, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$versions = $this->versions;

?>
<script type="text/javascript">

    Joomla.submitbutton = function(pressbutton) {
        switch(pressbutton) {
            case 'setupCpanel':

                window.location = '<?php echo JRoute::_( 'index.php?option=com_getbible&task=cpanel.save', false );?>';

            break;
        }

    }
	
</script>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('The Get Bible for Joomla 3', true)); ?>
                <div class="row-fluid">
                    <div class="span10">
                        <!-- <div class="well well-small"> -->
                        <div>
                            <a target="_blank" style="float: right;"href="http://getbible.net/" title="Get Bible"><img src="../media/com_getbible/images/Bible.jpg" height="233"/></a>
                            <div>
                                <p>The purpose with this application is to take the Word of God to every person in their own language for free! We would also like it to be fast, stable, and easy to use.</p>
                                
                                
                                <p>Get Bible is a member of <a href="http://www.wefeargod.com/" title="The Fear of God" target="_blank">we fear God</a> and also supports the <a href="http://www.whybible.com/" title="Why Bible" target="_blank">Why? Bible</a> initiative. We therefore strongly affirm that we choose to believe the Bible because it is a reliable collection of historical documents written by eyewitnesses during the lifetime of other eyewitnesses. They report supernatural events that took place in fulfillment of specific prophesies and claim that their writings are divine rather than human in origin. We believe the Bible is the verbally inspired Word of God and is the sole, infallible rule of faith and practice.</p>
                                
                                <h2 class="nav-header">This is a <a href="http://www.mountainofsuccess.com/" title="Success" target="_blank">Mountain of Success</a> project.</h2>
                                <div>
                                  <h3>What is Success? Well God's word says:</h3>
                                  <blockquote>This book of the law shall not depart out of thy mouth; but   thou shalt meditate therein day and night, that thou mayest observe to   do according to all that is written therein: for then thou shalt make   thy way prosperous, and then thou shalt have good success. ~ Joshua 1:8</blockquote>
                                  <p>Therefore a mountain of success can only be found in obedience to God. Looking back at the cross of Jesus planted on Golgotha; we see a mountain of success. Therefore we read in the scriptures the following</p>
                                  <p> </p>
                                  <blockquote>...and the stone that smote the image became a great mountain, and filled the whole earth. ~  Daniel 2:35</blockquote>
                                  <p>Mountain of Success is an online Christian mission initiative where this victory of our Lord is proclaimed.</p>
                                  <p>So it is all about God's Success!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             <?php echo JHtml::_('bootstrap.endTab'); ?>
             
             <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'api-info', JText::_('API', true)); ?>
                <div class="row-fluid">
                    <div class="span10">
                        <div class="well well-small">
                            <div>
                            
                                <h2 class="nav-header">How does the API Work?</h2>
        
                                <p>The API render the scripture in JSON, in many different translation/languages.</p>
                                <p>Getting a JSON response from the set url "http://getbible.net/json?" with a simple query string "p=Jn3:16" forms the base of the API.</p>
                                
                                <h2 class="nav-header">Parameters</h2>
                                
                                <p>There are just two parameters available, both are self-explanatory, <strong>passage</strong> and <strong>version</strong>.
                                
                                <p>Yet you can also use <strong>v</strong>, <strong>ver</strong>, <strong>lang</strong> and <strong>translation</strong> in place of <i>version</i> and <strong>p</strong>, <strong>text</strong>, <strong>scrip</strong> and <strong>scripture</strong> in place of <i>passage</i>.</p>
                                <p> You can call a book, chapter or a single verse, or even a string of verses. When the <strong>Version</strong> is omitted the KJV is provided by default. </p>
                                <p>The following are all valid:</p>
                                <ul>
                                  <li><a target="_blank" href="http://getbible.net/json?passage=1Jn3:16">http://getbible.net/json?passage=Jn3:16</a></li>
                                  <li><a target="_blank" href="http://getbible.net/json?p=James">http://getbible.net/json?p=James</a></li>
                                  <li><a target="_blank" href="http://getbible.net/json?text=ps119">http://getbible.net/json?text=ps119</a></li>
                                  <li><a target="_blank" href="http://getbible.net/json?scrip=Acts%203:17-4;2:1">http://getbible.net/json?scrip=Acts 3:17-4;2:1</a></li>
                                  <li><a target="_blank" href="http://getbible.net/json?scripture=Psa%20119:4-16;23:1-6&v=amp">http://getbible.net/json?scripture=Psa 119:4-16;23:1-6&v=amp</a></li>
                                  <li><a target="_blank" href="http://getbible.net/json?passage=Acts%2015:1-5,%2010,%2015&version=aov">http://getbible.net/json?passage=Acts 15:1-5, 10, 15&version=aov</a></li>
                                </ul>
                                <p>Here is a jQuery script to make an API call from your own application</p>
<pre>
<code>
jQuery.ajax({
    url:'http://getbible.net/json',
    dataType: 'jsonp',
    data: 'p=John1&v=kjv',
    jsonp: 'getbible',
    success:function(json){
        // set text direction
        if (json.direction == 'RTL'){
            var direction = 'rtl';
        } else {
            var direction = 'ltr'; 
        }
        // check response type
        if (json.type == 'verse'){
            var output = '';
                jQuery.each(json.book, function(index, value) {
                    output += '&lt;center&gt;&lt;b&gt;'+value.book_name+'&#160;'+value.chapter_nr+'&lt;/b&gt;&lt;/center&gt;&lt;br/&gt;&lt;p class="'+direction+'"&gt;';
                    jQuery.each(value.chapter, function(index, value) {
                        output += '&#160;&#160;&lt;small class="ltr"&gt;' +value.verse_nr+ '&lt;/small&gt;&#160;&#160;';
                        output += value.verse;
                        output += '&lt;br/&gt;';
                    });
                    output += '&lt;/p&gt;';
                });
            jQuery('#scripture').html(output);  // <---- this is the div id we update
        } else if (json.type == 'chapter'){
            var output = '&lt;center&gt;&lt;b&gt;'+json.book_name+'&#160;'+json.chapter_nr+'&lt;/b&gt;&lt;/center&gt;&lt;br/&gt;&lt;p class="'+direction+'"&gt;';
            jQuery.each(json.chapter, function(index, value) {
                output += '&#160;&#160;&lt;small class="ltr"&gt;' +value.verse_nr+ '&lt;/small&gt;&#160;&#160;';
                output += value.verse;
                output += '&lt;br/&gt;';
            });
            output += '&lt;/p&gt;';
            jQuery('#scripture').html(output);  // <---- this is the div id we update
        } else if (json.type == 'book'){
            var output = '';
            jQuery.each(json.book, function(index, value) {
                output += '&lt;center&gt;&lt;b&gt;'+json.book_name+'&#160;'+value.chapter_nr+'&lt;/b&gt;&lt;/center&gt;&lt;br/&gt;&lt;p class="'+direction+'"&gt;';
                jQuery.each(value.chapter, function(index, value) {
                    output += '&#160;&#160;&lt;small class="ltr"&gt;' +value.verse_nr+ '&lt;/small&gt;&#160;&#160;';
                    output += value.verse;
                    output += '&lt;br/&gt;';
                });
            output += '&lt;/p&gt;';
        });
        if(addTo){
            jQuery('#scripture').html(output);  // <---- this is the div id we update
        }
    },
    error:function(){
        jQuery('#scripture').html('&lt;h2&gt;No scripture was returned, please try again!&lt;/h2&gt;'); // <---- this is the div id we update
     },
});  
</code>
</pre>
                                <p>If you are a Brother in the Lord and an advanced programmer we can do with some help, please contact me at <a href="mailto:<?php echo $this->params->get('emailGlobal'); ?>" title="<?php echo $this->params->get('nameGlobal'); ?>"><?php echo $this->params->get('emailGlobal'); ?></a></p>
                                
                                <h2>Restrictions</h2>
                                <ul>
                                <li>During the beta stage you are not restricted in how many lookups you can request. Once the testing period is over, it is likely that a limit of 7,000 requests per user per day will be put in place.</li>
                                <li>All of the texts currently available are in the public domain, so there are no restrictions on how the results can be stored or used.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
             <?php echo JHtml::_('bootstrap.endTab'); ?>
            
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'versions', JText::_('Available Versions', true)); ?>
            	<div class="row-fluid">
                <div class="span10">
                    <!-- <div class="well well-small"> -->
                    <div>
                            
                            <p>Most of the translations are provided by The Unbound Bible. The code that should be passed as the version parameter is shown in brackets.</p>
                            <p>If you want a translation that is not currently listed below, contact me at <a href="mailto:<?php echo $this->params->get('emailGlobal'); ?>" title="<?php echo $this->params->get('nameGlobal'); ?>"><?php echo $this->params->get('emailGlobal'); ?></a>. <br/>Please note that version that are currently in copyright (e.g. NIV, NKJV, etc.) cannot be added unless you are able to secure copyright permission. </p>
                            <p><a href="http://www.4-14.org.uk/xml-bible-web-service-api" target="_blank">Permission</a> has been granted for the NASB and Amplified Bibles.</p>
                            <ul>
                            <?php if($versions): ?>
                            <?php foreach($versions as $version): ?>
								<?php if($version['not']): ?>
                                <li>
									<?php echo $version['versionLang'] ?> <?php echo $version['versionName'] ?>  (<?php echo $version['versionCode'] ?>)
                                </li>
                                <?php else: ?>
                                <li>
                                	<a target="_blank" href="index.php?option=com_getbible&view=versions">
										<?php echo $version['versionLang'] ?> <?php echo $version['versionName'] ?>  (<?php echo $version['versionCode'] ?>)
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php else: ?>
                            	<p>Get Bible is offline, so we can't check what versions is available at this time.</p>
                            <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
           <?php echo JHtml::_('bootstrap.endTab'); ?>
            
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
</div>