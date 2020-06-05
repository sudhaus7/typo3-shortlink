<?php


namespace SUDHAUS7\Shortlink\Service;

use Psr\Http\Message\ServerRequestInterface;
use SUDHAUS7\Shortlink\Exception\NoSuchShortlinkException;
use SUDHAUS7\Shortlink\Exception\ShortlinkPermissionDeniedException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ShortlinkService
{
    /**
     * @var string
     */
    protected static $TABLENAME = 'tx_shortlink_domain_model_shortlink';

    /**
     * @var string
     */
    private $url = '';

    private $feuser = 0;

    private $data = [];

    public function __construct()
    {
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function setFeuser(int $feuser)
    {
        $this->feuser = $feuser;
    }

    public function encode(): string
    {
        /** @var Connection $db */
        $db = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::$TABLENAME);

        $checksum = hash('sha256', $this->url);

        $query = $db->createQueryBuilder();
        $row = $query->select('*')
            ->from(self::$TABLENAME)
            ->where(
                $query->expr()->eq('checksum', $query->createNamedParameter($checksum))
            )->execute()->fetch();

        if (!empty($row)) {
            $shortlink = $row['shortlink'];
        } else {
            $query->insert(self::$TABLENAME)
                ->values([
                    'pid'=>0,
                    'checksum'=>$checksum,
                    'redirectto'=>$this->url,
                    'feuser'=>$this->feuser
                ])->execute();

            $uid = $query->getConnection()->lastInsertId();
            $base62 = new \Tuupola\Base62();
            $shortlink = $base62->encode($uid.'-'.random_bytes(4).'-'.$this->feuser);

            $query->update(self::$TABLENAME)
                ->set('shortlink', $shortlink)
                ->where(
                    $query->expr()->eq('uid', $query->createNamedParameter($uid))
                )->execute();
        }
        return (string)$shortlink;
    }

    /**
     * @param string $shortlink
     * @return string
     * @throws NoSuchShortlinkException
     * @throws ShortlinkPermissionDeniedException
     */
    public function decode(string $shortlink): string
    {
        /** @var Connection $db */
        $db = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::$TABLENAME);
        $query = $db->createQueryBuilder();
        $row = $query->select('*')
            ->from(self::$TABLENAME)
            ->where(
                $query->expr()->eq('shortlink', $query->createNamedParameter($shortlink))
            )->execute()->fetch();
        
        // check if we got something, otherwise throw exception
        if (empty($row)) {
            throw new NoSuchShortlinkException('Shortlink Not Found '.$shortlink, 1591368921);
        }

        // Check if the record was stored with a user, and if
        // it did check with the userid in the object. If it
        // doesn't match, throw exception
        if ($row['feuser'] > 0 && $this->feuser!==$row['feuser']) {
            throw new ShortlinkPermissionDeniedException('Shortlink user missmatch', 1591382868);
        }

        return $row['redirectto'];
    }

    /**
     * @param ServerRequestInterface|null $request
     * @return string
     */
    public function getShorturlWithDomain(ServerRequestInterface $request = null): string
    {
        $shortlink = $this->getShorturl();
        if ($request === null) {
            /** @var Site $site */
            $site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
            
            $shortlink = rtrim((string) $site->getBase(), '/').$shortlink;
        } else {
            $site = $request->getAttribute('site');
            $shortlink = rtrim((string)$site->getBase(), '/').$shortlink;
        }
        return $shortlink;
    }

    /**
     * @return string
     */
    public function getShorturl()
    {
        $confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['shortlink'], ['allowed_classes'=>[]]);

        return $confArr['base'].$this->encode();
    }
}
