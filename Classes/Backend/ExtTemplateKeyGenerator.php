<?php


namespace SUDHAUS7\Shortcutlink\Backend;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\ViewHelpers\Form\TypoScriptConstantsViewHelper;

class ExtTemplateKeyGenerator
{
    /**
     * @var string The button id
     */
    static $BTNID = 'shortcutlinknewkeybtn1593882265';
    /**
     * @var string The info block id
     */
    static $INFOID = 'shortcutlinkinfo1593882265';
    /**
     * Tag builder instance
     *
     * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
     */
    protected $tag = null;
    
    /**
     * @param array $parameter
     * @param TypoScriptConstantsViewHelper $parentObject
     * @return string
     */
    public function render(array $parameter = array(), TypoScriptConstantsViewHelper $parentObject)
    {
        $field = sprintf('<input type="hidden" name="%s" value="%s"/>',$parameter['fieldName'],trim($parameter['fieldValue']));
        
        $info = 'API DISABLED';
        
        if (!empty($parameter['fieldValue'])) {
            $info = sprintf('Hashed API Key: %s',$parameter['fieldValue']);
        }
        
        $button = sprintf('<button id="%s">generate a new api key</button>',self::$BTNID);
        
        $script = "<script type=\"text/javascript\">
document.getElementById('".self::$BTNID."').addEventListener('click',function(event) {
event.preventDefault();
event.stopPropagation();
$.ajax({
    url: TYPO3.settings.ajaxUrls['shortcutlinknewapikey'],
    method: 'GET',
    dataType: 'json',
    success: function(response) {
        $('input[name=\"".$parameter['fieldName']."\"]').val(response.hashedapikey);
        $('#".self::$INFOID."').html('Hashed API Key: '+response.hashedapikey+'<br/>Your new API Key: <pre>'+response.newapikey+'</pre><br/>This is the only time your API KEY will be visible here. Please copy it now. It will be activated on saving this configuration.');
    }
});
});
</script>";
        
        
        return $field.'<p id="'.self::$INFOID.'">'.$info.'</p><p>'.$button.'</p>'.$script;
    }
}
