<?php
/**
* 
* 	@version 	1.0.5  December 08, 2014
* 	@package 	Get Bible API
* 	@author  	Llewellyn van der Merwe <llewellyn@vdm.io>
* 	@copyright	Copyright (C) 2013 Vast Development Method <http://www.vdm.io>
* 	@license	GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
*
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('bootstrap.tooltip');
?>
<script type="text/javascript">
	function submitbutton()
	{

		var form = document.getElementById('adminForm');

		// do field validation
		if (form.translation.value == ""){
			alert("<?php echo JText::_('COM_GETBIBLE_MSG_INSTALL_PLEASE_SELECT_A_VERSION', true); ?>");
		}
		else
		{
			jQuery('#loading').css('display', 'block');
			form.submit();
		}
		
	};
	
	
	// Add spindle-wheel for installations:
	jQuery(document).ready(function($) {
		var outerDiv = $('#import_bible');

		$('<div id="loading"></div>')
			.css("background", "rgba(255, 255, 255, .8) url('../media/jui/img/ajax-loader.gif') 50% 15% no-repeat")
			.css("top", outerDiv.position().top - $(window).scrollTop())
			.css("left", outerDiv.position().left - $(window).scrollLeft())
			.css("width", outerDiv.width())
			.css("height", outerDiv.height())
			.css("position", "fixed")
			.css("opacity", "0.90")
			.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
			.css("filter", "alpha(opacity = 90)")
			.css("display", "none")
			.appendTo(outerDiv);
	});
</script>
<div id="import_bible" class="clearfix">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <?php if ($this->versions): ?>
    <div id="j-main-container" class="span10">
        <div class="form-horizontal">
            <div class="row-fluid">
                <div class="span5">
                    <div class="well">
                        <h2 class="nav-header"><?php echo JText::_('COM_GETBIBLE_SELECT_VERSION_YOU_WANT_INSTALLED'); ?></h2>
                        <form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_getbible&view=import');?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">               
                            <fieldset>
                                <div class="control-group">
                                    <label class="uk-form-label" ><?php echo JText::_('COM_GETBIBLE_AVALIABLE_VERSIONS'); ?></label>
                                    <select  name="translation"  id="translation">
                                        <?php $i=0;foreach($this->versions as $version): ?>
                                        <?php if(!$i):?>
                                            <option  selected="selected" value="<?php echo $version->value; ?>"><?php echo $version->text; ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo $version->value; ?>"><?php echo $version->text; ?></option>
                                        <?php endif; ?>
                                        <?php $i++; ?>
                                        <?php endforeach; ?>
                                     </select>
                                 </div>
                                 <div class="control-group">
                                    <input class="btn btn-primary" type="button" value="<?php echo JText::_('COM_GETBIBLE_INSTALL_BIBLE'); ?>" onclick="submitbutton()" />
                                </div>
                                <div class="alert alert-info">
                                  <i class="icon-support"></i> <?php echo JText::_('COM_GETBIBLE_INSTALL_WILL_TAKE_LONG'); ?>
                                </div>
                                <div class="alert alert-info">
                                  <i class="icon-home"></i> <?php echo JText::_('COM_GETBIBLE_NOTE_BIBLES_HOSTED_WITH_GETBIBLE'); ?>
                                </div>
                                <div class="alert alert-info">
                                  <i class="icon-support"></i> <?php echo JText::_('COM_GETBIBLE_NOTE_INSTALL_ERROR_WITH_GETBIBLE'); ?>
                                </div>
                                
                            </fieldset>
                            <?php echo JHtml::_('form.token'); ?>
                        </form>
                    </div>
                </div>
                <div class="span5">
                    <div class="well">
                	<h2 class="nav-header"><?php echo JText::_('COM_GETBIBLE_VERSION_REQUEST'); ?></h2>
                        <div class="alert alert-info">
                            <p><i class="icon-book"></i> All of the versions/translation currently available are in the public domain, so there are no restrictions.</p>
                        </div>
                        <div class="alert alert-info">
                            <p>Please note that versions that are currently in copyright (e.g. NIV, NKJV, etc.) cannot be added unless you are able to secure copyright permission. If you want a versions/translation that is not currently available via GetBible, please contact me at <a href="mailto:<?php echo $this->params->get('emailGlobal'); ?>" title="<?php echo $this->params->get('nameGlobal'); ?>"><?php echo $this->params->get('emailGlobal'); ?></a></p>
                        </div>
                        <div class="alert alert-info">
                            <p><a href="http://www.4-14.org.uk/xml-bible-web-service-api" target="_blank">Permission</a> has been granted for the NASB and Amplified Bibles.</p>
                        </div>
                	</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div id="j-main-container" class="span10">
    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span5">
            	<div class="well well-small">
                    <h2 class="nav-header"><?php echo JText::_('COM_GETBIBLE_SELECT_VERSION_YOU_WANT_INSTALLED'); ?></h2>
                    
                    <form enctype="multipart/form-data" action="" method="post" name="adminForm" id="adminForm" class="form-horizontal">               
                            <fieldset>
                                <div class="control-group">
                                    <label class="uk-form-label" ><?php echo JText::_('COM_GETBIBLE_AVALIABLE_VERSIONS'); ?></label>
                                    <input class="form-control" id="disabledInput" type="text" placeholder="- none -" disabled>

                                 </div>
                                 <div class="control-group">
                                    <input class="btn btn-primary" type="button" value="<?php echo JText::_('COM_GETBIBLE_INSTALL_BIBLE'); ?>" onclick="submitbutton()"  disabled="disabled"/>
                                </div>
                                <div class="alert alert-warning">
                                    <p>Please try again later!</p>
                                    <p>Please contact me at <a href="mailto:<?php echo $this->params->get('emailGlobal'); ?>" title="<?php echo $this->params->get('nameGlobal'); ?>"><?php echo $this->params->get('emailGlobal'); ?></a> if you have any further questions.</p>
                                </div>
                            </fieldset>
                        </form>
                </div>
            </div>
            <div class="span5">
            	<div class="well">
                <h2 class="nav-header"><?php echo JText::_('COM_GETBIBLE_VERSION_REQUEST'); ?></h2>
                    <div class="alert alert-info">
                        <p><i class="icon-book"></i> All of the versions/translation currently available are in the public domain, so there are no restrictions.</p>
                    </div>
                    <div class="alert alert-info">
                        <p>Please note that versions that are currently in copyright (e.g. NIV, NKJV, etc.) cannot be added unless you are able to secure copyright permission. If you want a versions/translation that is not currently available via GetBible, please contact me at <a href="mailto:<?php echo $this->params->get('emailGlobal'); ?>" title="<?php echo $this->params->get('nameGlobal'); ?>"><?php echo $this->params->get('emailGlobal'); ?></a></p>
                    </div>
                    <div class="alert alert-info">
                        <p><a href="http://www.4-14.org.uk/xml-bible-web-service-api" target="_blank">Permission</a> has been granted for the NASB and Amplified Bibles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>