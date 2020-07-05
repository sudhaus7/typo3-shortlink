<?php


namespace SUDHAUS7\Shortcutlink\Middleware;

use MongoDB\Driver\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SUDHAUS7\Shortcutlink\Exception\NoSuchShortlinkException;
use SUDHAUS7\Shortcutlink\Exception\ShortlinkPermissionDeniedException;
use SUDHAUS7\Shortcutlink\Service\ShortlinkService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class RedirectMiddleware implements MiddlewareInterface
{
    
    /**
     * @var array Extension Configuration
     */
    protected $confArr;
    
    public function __construct()
    {
        $this->confArr = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('shortcutlink');
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // check if we are responsible
        if ( strpos($request->getUri()->getPath(), $this->confArr['base']) === 0 ) {
            
            // we are responsible, expand the uri
            $pathArr = GeneralUtility::trimExplode('/', trim($request->getUri()->getPath(), '/'));
            if ( $request->getMethod() === 'GET' ) {
                $response = $this->handleGet($pathArr);
                if ( $response !== null ) {
                    return $response;
                }
            }
            if ( $request->getMethod() === 'POST' ) {
                return $this->handlePost($request, $pathArr);
            }
            if ( $request->getMethod() === 'PUT' ) {
                return $this->handlePut($request, $pathArr);
            }
            if ( $request->getMethod() === 'DELETE' ) {
                //TODO: Handle delete
            }
        }
        return $handler->handle($request);
    }
    
    /**
     * @param array $pathArr
     * @return ResponseInterface|null
     */
    private function handleGet(array $pathArr): ?ResponseInterface
    {
        if ( '/' . $pathArr[0] . '/' === $this->confArr && !empty($pathArr[1]) ) {
            
            /** @var ShortlinkService $shortLink */
            $shortLink = GeneralUtility::makeInstance(ShortlinkService::class);
            
            
            // we have to do a prelimenary authentication here..
            /** @var FrontendUserAuthentication $frontendUser */
            $frontendUser = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
            $frontendUser->start();
            
            if ( ($frontendUser->user[$frontendUser->userid_column ?? 'uid'] ?? 0) > 0 ) {
                // is logged in, set user
                $shortLink->setFeuser($frontendUser->user[$frontendUser->userid_column ?? 'uid']);
            }
            
            /** @var Response $response */
            $response = GeneralUtility::makeInstance(Response::class);
            try {
                $url = $shortLink->decode($pathArr[1]);
            } catch ( NoSuchShortlinkException $e ) {
                return $response->withStatus(404, $e->getMessage());
            } catch ( ShortlinkPermissionDeniedException $e ) {
                return $response->withStatus(403, $e->getMessage());
            }
            return $response->withHeader('Location', $url)->withStatus(302, 'TYPO3 Shortlink redirect');
        }
        return null;
    }
    
    private function handlePut(ServerRequestInterface $request, array $pathArr)
    {
    
    }
    
    private function handlePost(ServerRequestInterface $request, array $pathArr): ResponseInterface
    {
        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);
        if ( !$this->checkAuthorisation($request) ) {
            $response->getBody()->write('Forbidden');
            return $response->withStatus(304, 'Forbidden');
        }
        $payload = $this->getPayload($request);
        if (!$this->validatePayload($payload)) {
            $response->getBody()->write('Invalid Payload');
            return $response->withStatus(500, 'Invalid Payload');
        }
        
        /** @var ShortlinkService $shortlink */
        $shortlink = GeneralUtility::makeInstance(ShortlinkService::class);
        $shortlink->setUrl($payload['url']);
        if (isset($payload['feuser'])) {
            $shortlink->setFeuser((int)$payload['feuser']);
        }
        $response->getBody()->write(\json_encode(['encoded'=>$shortlink->getShorturlWithDomain($request)]));
        return $response->withHeader('Content-type', 'application/json');
    }
    
    
    protected function validatePayload(array $payload): bool
    {
        if (!isset($payload['url']) || empty($payload['url']) || !filter_var($payload['url'], FILTER_VALIDATE_URL)) {
            return false;
        }
        
        return true;
        
    }
    
    protected function getPayload(ServerRequestInterface $request) : array
    {
        if (in_array('application/json',$request->getHeader('content-type'))) {
            $payload = \json_decode((string)$request->getBody(),true);
        } else {
            $payload = $request->getParsedBody();
        }
        return $payload;
    }
    
    protected function checkAuthorisation(ServerRequestInterface $request) : bool
    {
        if($request->hasHeader('Authorization')) {
            $authheader = $request->getHeader('Authorization');
            // We expect ApiKey as Method and the api key as payload
            if (!empty($authheader)) {
                [$method, $apikey] = GeneralUtility::trimExplode(' ', $authheader[0],true);
                if (strtolower($method)==='apikey' && !empty($apikey)) {
                    /** @var PasswordHashFactory $hashService */
                    $hashService = GeneralUtility::makeInstance(PasswordHashFactory::class);
                    $hashInstance = $hashService->getDefaultHashInstance('BE');
                    return $hashInstance->checkPassword($apikey, $this->confArr['apikey']);
                }
            }
        }
        return false;
    }
    
}
