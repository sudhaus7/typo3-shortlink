<?php


namespace SUDHAUS7\Shortcutlink\Backend;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\ViewHelpers\Form\TypoScriptConstantsViewHelper;

class ExtTemplateKeyGenerator
{
    /**
     * Tag builder instance
     *
     * @var \TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder
     */
    protected $tag = null;
    
    /**
     * constructor of this class
     */
    public function __construct()
    {
        //$this->tag = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TagBuilder');
    }
    
    /**
     * render textarea for extConf
     *
     * @param array $parameter
     * @param TypoScriptConstantsViewHelper $parentObject
     * @return string
     */
    public function renderx(array $parameter = array(), TypoScriptConstantsViewHelper $parentObject)
    {
        $this->tag->setTagName('input');
        $this->tag->forceClosingTag(true);
        $this->tag->addAttribute('type', 'hidden');
        //$this->tag->addAttribute('rows', 15);
        //$this->tag->addAttribute('style', 'width:100%;');
        $this->tag->addAttribute('name', $parameter['fieldName']);
        //$this->tag->addAttribute('id', 'em-' . $parameter['fieldName']);
        if ($parameter['fieldValue'] !== null) {
            $this->tag->setContent(trim($parameter['fieldValue']));
        }
        return $this->tag->render().'xxxx';
    }
    
    public function render(array $parameter = array(), TypoScriptConstantsViewHelper $parentObject)
    {
        $field = sprintf('<input type="hidden" name="%s" value="%s"/>',$parameter['fieldName'],trim($parameter['fieldValue']));
        
        $info = 'API DISABLED';
        
        if (!empty($parameter['fieldValue'])) {
            $info = sprintf('Hashed API Key: %s',$parameter['fieldValue']);
        }
        
        
        
        return $field.'<p>'.$info.'</p>';
    }
}
