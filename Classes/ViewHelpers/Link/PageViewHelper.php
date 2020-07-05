<?php


namespace SUDHAUS7\Shortcutlink\ViewHelpers\Link;

use SUDHAUS7\Shortcutlink\Service\ShortlinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\PageViewHelper
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
     * @return string Rendered page URI
     */
    public function render()
    {
        $pageUid = isset($this->arguments['pageUid']) ? (int)$this->arguments['pageUid'] : null;
        $pageType = isset($this->arguments['pageType']) ? (int)$this->arguments['pageType'] : 0;
        $noCache = isset($this->arguments['noCache']) ? (bool)$this->arguments['noCache'] : false;
        $noCacheHash = isset($this->arguments['noCacheHash']) ? (bool)$this->arguments['noCacheHash'] : false;
        $section = isset($this->arguments['section']) ? (string)$this->arguments['section'] : '';
        $linkAccessRestrictedPages = isset($this->arguments['linkAccessRestrictedPages']) ? (bool)$this->arguments['linkAccessRestrictedPages'] : false;
        $additionalParams = isset($this->arguments['additionalParams']) ? (array)$this->arguments['additionalParams'] : [];
        $absolute = isset($this->arguments['absolute']) ? (bool)$this->arguments['absolute'] : false;
        $addQueryString = isset($this->arguments['addQueryString']) ? (bool)$this->arguments['addQueryString'] : false;
        $argumentsToBeExcludedFromQueryString = isset($this->arguments['argumentsToBeExcludedFromQueryString']) ? (array)$this->arguments['argumentsToBeExcludedFromQueryString'] : [];
        $addQueryStringMethod = $this->arguments['addQueryStringMethod'] ?? null;
        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setUseCacheHash(!$noCacheHash)
            ->setSection($section)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->setAddQueryStringMethod($addQueryStringMethod)
            ->build();
        if ((string)$uri !== '') {

            /** @var ShortlinkService $shortener */
            $shortener = GeneralUtility::makeInstance(ShortlinkService::class);

            $shortener->setUrl($uri);
            $shortener->setFeuser($this->arguments['chainToUserid']);

            $this->tag->addAttribute('href', $shortener->getShorturlWithDomain());

            $this->tag->setContent($this->renderChildren());
            $this->tag->forceClosingTag(true);
            $result = $this->tag->render();
        } else {
            $result = $this->renderChildren();
        }
        return $result;
    }
}
