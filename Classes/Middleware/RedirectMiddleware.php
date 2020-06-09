<?php


namespace SUDHAUS7\Shortcutlink\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SUDHAUS7\Shortcutlink\Exception\NoSuchShortlinkException;
use SUDHAUS7\Shortcutlink\Exception\ShortlinkPermissionDeniedException;
use SUDHAUS7\Shortcutlink\Service\ShortlinkService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class RedirectMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $confArr = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('shortcutlink');
        if (strpos($request->getUri()->getPath(), $confArr['base'])===0) {
            $pathArr = GeneralUtility::trimExplode('/', trim($request->getUri()->getPath(), '/'));
            if ('/'.$pathArr[0].'/'===$confArr['base'] && !empty($pathArr[1])) {
    
                /** @var ShortlinkService $shortLink */
                $shortLink = GeneralUtility::makeInstance(ShortlinkService::class);
    
    
                // we have to do a prelimenary authentication here..
                /** @var FrontendUserAuthentication $frontendUser */
                $frontendUser = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
                $frontendUser->start();
                
                if(($frontendUser->user[$frontendUser->userid_column ?? 'uid'] ?? 0) > 0) {
                    // is logged in, set user
                    $shortLink->setFeuser($frontendUser->user[$frontendUser->userid_column ?? 'uid']);
                }
                
                /** @var Response $response */
                $response = GeneralUtility::makeInstance(Response::class);
                try {
                    $url = $shortLink->decode($pathArr[1]);
                } catch (NoSuchShortlinkException $e) {
                    return $response->withStatus(404, $e->getMessage());
                } catch (ShortlinkPermissionDeniedException $e) {
                    return $response->withStatus(403, $e->getMessage());
                }
                return $response->withHeader('Location', $url)->withStatus(302, 'TYPO3 Shortlink redirect');
            }
        }
        return $handler->handle($request);
    }
}
