<?php


namespace SUDHAUS7\Shortcutlink\ViewHelpers\Link;

use SUDHAUS7\Shortcutlink\Service\ShortlinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExternalViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ExternalViewHelper
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
        $uri = $this->arguments['uri'];
        $defaultScheme = $this->arguments['defaultScheme'];

        $scheme = parse_url($uri, PHP_URL_SCHEME);
        if ($scheme === null && $defaultScheme !== '') {
            $uri = $defaultScheme.'://'.$uri;
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
