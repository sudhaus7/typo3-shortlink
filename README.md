# Shortcutlink (Shortlink) 

A urlshortener for TYPO3

## What does it do:

This extension will create a short URL similar to bit.ly or similar services in an easy way. For the Integrator this means that the usual Viewhelpers for creating links have been extended and the shortener can be used transparently for any link created by a FLUID Viewhelper.

Additionaly a Service Class is available to create shortened URLs on the fly for example in a Plugin or in an Extbase Controller.

## Available FLUID Viewhelpers

Import the namespace like this:


```html
<html
    xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
    xmlns:scl="http://typo3.org/ns/SUDHAUS7/Shortlink/ViewHelpers"
    data-namespace-typo3-fluid="true"
>
``` 

or like this:

```
{namespace scl=SUDHAUS7\Shortcutlink\ViewHelpers}
```

The following ViewHelpers are available:

- uri.action
- uri.external
- uri.page
- uri.typolink
- link.action
- link.external
- link.page

these ViewHelpers have all attributes available and behave the same way as their normal counterparts, except they will produce a shortened url

That means you can now replace any 
```html
<f:link.page pageId="15">The link</f:link.page>
```
simply with
```html
<scl:link.page pageId="15">The link</scl:link.page>
```



## Usage in PHP

a simple example to shorten a URL:

```php
$url = 'https://google.com/';
$shortener = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SUDHAUS7\Shortcutlink\Service\ShortlinkService::class);
    
$shortener->setUrl($url);
$shortlink = $shortener->getShorturlWithDomain();
``` 

## Locking a Shortend URL to a Frontenduser

It is possible to lock a Frontendenduser ID to the shortened URL, which means that the shortlink can only be opened with an existing Frontenduser Session with the same userid

For this all ViewHelpers have an additional attribute `chainToUserid` which accepts the Userid of the Frontenduser it has to be chained to.

For example:
```html
<scl:link.page pageId="15" chainToUserid="42">The link</scl:link.page>
```

The resulting Shortlink can only be opened if the User with the ID 42 is logged in.

The PHP equivialent to this would be:

```php
$url = 'https://google.com/';
$shortener = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SUDHAUS7\Shortcutlink\Service\ShortlinkService::class);
    
$shortener->setUrl($url);
$shortener->setFeuser(42);
$shortlink = $shortener->getShorturlWithDomain();
``` 

