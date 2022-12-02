<?php


namespace SUDHAUS7\Shortcutlink\ViewHelpers\Link;

use SUDHAUS7\Shortcutlink\Service\ShortlinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class ActionViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper
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
     * @return string Rendered link
     */
    public function render()
    {
        $action = $this->arguments['action'];
        $controller = $this->arguments['controller'];
        $extensionName = $this->arguments['extensionName'];
        $pluginName = $this->arguments['pluginName'];
        $pageUid = (int)$this->arguments['pageUid'] ?: null;
        $pageType = (int)$this->arguments['pageType'];
        $noCache = (bool)$this->arguments['noCache'];
        $section = (string)$this->arguments['section'];
        $format = (string)$this->arguments['format'];
        $linkAccessRestrictedPages = (bool)$this->arguments['linkAccessRestrictedPages'];
        $additionalParams = (array)$this->arguments['additionalParams'];
        $absolute = (bool)$this->arguments['absolute'];
        $addQueryString = (bool)$this->arguments['addQueryString'];
        $argumentsToBeExcludedFromQueryString = (array)$this->arguments['argumentsToBeExcludedFromQueryString'];
        $parameters = $this->arguments['arguments'];
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setSection($section)
            ->setFormat($format)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->uriFor($action, $parameters, $controller, $extensionName, $pluginName);
        if ($uri === '') {
            return $this->renderChildren();
        }
        /** @var ShortlinkService $shortener */
        $shortener = GeneralUtility::makeInstance(ShortlinkService::class);

        $shortener->setUrl($uri);
        $shortener->setFeuser($this->arguments['chainToUserid']);

        $this->tag->addAttribute('href', $shortener->getShorturlWithDomain());
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);
        return $this->tag->render();
    }
}
