<?php


namespace SUDHAUS7\Shortcutlink\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Base62;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ApikeyController
{

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashExceptionDD
     */
    public function GetNewApikeyAction(ServerRequestInterface $request): ResponseInterface
    {
        $base62 = new Base62();
        $newApiKey = $base62->encode(\random_bytes(32));
        /** @var PasswordHashFactory $hashService */
        $hashService = GeneralUtility::makeInstance(PasswordHashFactory::class);
        $hashInstance = $hashService->getDefaultHashInstance('BE');
        $hashedApiKey = $hashInstance->getHashedPassword($newApiKey);
        $payload = [
            'newapikey'=>$newApiKey,
            'hashedapikey'=>$hashedApiKey
        ];
        $response = GeneralUtility::makeInstance(Response::class);
        $response->getBody()->write(\json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
