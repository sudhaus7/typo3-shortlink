<?php


namespace SUDHAUS7\Shortcutlink\Service;

use Psr\Http\Message\ServerRequestInterface;
use SUDHAUS7\Shortcutlink\Exception\NoSuchShortlinkException;
use SUDHAUS7\Shortcutlink\Exception\ShortlinkPermissionDeniedException;
use Tuupola\Base62;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ShortlinkService
{
    /**
     * @var string
     */
    protected static $TABLENAME = 'tx_shortcutlink_domain_model_shortlink';

    /**
     * @var string
     */
    private $url = '';
    
    /**
     * @var int FrontendUser ID
     */
    private $feuser = 0;
    
    /**
     * @var array
     */
    private $data = [];

    public function __construct()
    {
    }
    
    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }
    
    /**
     * @param int $feuser
     */
    public function setFeuser(int $feuser)
    {
        $this->feuser = $feuser;
    }
    
    /**
     * @return string
     * @throws \Exception
     */
    public function encode(): string
    {
        if ($this->url === 'http://' || $this->url === 'https://') {
            return $this->url;
        }

        /** @var Connection $db */
        $db = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::$TABLENAME);

        $checksum = hash('sha256', $this->url.'-'.$this->feuser);

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
                    'tstamp'=>time(),
                    'feuser'=>$this->feuser
                ])->execute();

            $uid = $query->getConnection()->lastInsertId();
            $base62 = new Base62();
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
     * @param $shortlink
     */
    public function updateShorlink($shortlink): void
    {
        /** @var Connection $db */
        $db = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::$TABLENAME);
        $checksum = hash('sha256', $this->url.'-'.$this->feuser);
        $query = $db->createQueryBuilder();
        $row = $query->select('*')
            ->from(self::$TABLENAME)
            ->where(
                $query->expr()->eq('checksum', $query->createNamedParameter($checksum))
            )->execute()->fetch();

        if (empty($row)) {
            $query->update(self::$TABLENAME)
            ->set('redirectto', $this->url)
            ->set('checksum', $checksum)
            ->set('feuser', (int)$this->feuser)
            ->where(
                $query->expr()->eq('shortlink', $query->createNamedParameter($shortlink))
            )->execute();
        }
    }
    
    /**
     * @param $shortlink
     */
    public function deleteShortlink($shortlink): void
    {
        /** @var Connection $db */
        $db = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::$TABLENAME);

        $query = $db->createQueryBuilder();
        $query->delete(self::$TABLENAME)

            ->where(
                $query->expr()->eq('shortlink', $query->createNamedParameter($shortlink))
            )->execute();
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

        $query->update(self::$TABLENAME)
            ->set('tstamp', time())
            ->where(
                $query->expr()->eq('shortlink', $query->createNamedParameter($shortlink))
            )->execute();

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
        $confArr = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('shortcutlink');

        return $confArr['base'].$this->encode();
    }
}
