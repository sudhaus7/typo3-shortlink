<?php


namespace SUDHAUS7\Shortcutlink\ViewHelpers\Uri;


use SUDHAUS7\Shortcutlink\Service\ShortlinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class ExternalViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Uri\ExternalViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('chainToUserid', 'int', 'Only this user is allowed to use this shortlink', false, 0);
        
    }
    
    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $uri = parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
        /** @var ShortlinkService $shortener */
        $shortener = GeneralUtility::makeInstance(ShortlinkService::class);
        
        $shortener->setUrl($uri);
        $shortener->setFeuser($arguments['chainToUserid']);
        
        return $shortener->getShorturlWithDomain();
    }
}
