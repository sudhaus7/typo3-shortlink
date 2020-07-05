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
    xmlns:scl="http://typo3.org/ns/SUDHAUS7/Shortcutlink/ViewHelpers"
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

## Locking a shortend URL to a frontend user

It is possible to lock a frontend user ID to the shortened URL, which means that the short link can only be opened with an existing frontend user Session with the same user id

For this all ViewHelpers have an additional attribute `chainToUserid` which accepts the user id of the frontend user it has to be chained to.

For example:
```html
<scl:link.page pageId="15" chainToUserid="42">The link</scl:link.page>
```

The resulting short link can only be opened if the user with the ID 42 is logged in.

The PHP equivalent to this would be:

```php
$url = 'https://google.com/';
$shortener = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\SUDHAUS7\Shortcutlink\Service\ShortlinkService::class);
    
$shortener->setUrl($url);
$shortener->setFeuser(42);
$shortlink = $shortener->getShorturlWithDomain();
``` 

## REST Interface

The entrypoint you define in the extension-setup (default: /goto/) can be used as a Restful Interface API for creating, updating and deleting shortened URLs.

To enable this feature you need to go to the TYPO3 Backend and into the Settings Module and open the 'Configure Extensions' Dialog.

Here you open the configuration options of the 'shortcutlink' extension.

You will find a button 'generate a new API key'. Klick that Button and a new key will be generated. Make sure to copy it once it is generated, it can not be restored. Save the configuration after that.

This key can now be used for POST,PUT and DELETE operations by adding it as a header named 'ApiKey' to the HTTP Request header.

To create a new shortened url you will use the POST method without a key after /goto/

Here is an example with curl:

```bash
curl -X POST \
-d "url=https://google.com" \
-H "ApiKey: GTeZNuFWHfMxSTBf2krrasTCu9INcmHa5KwQ12uggDM" \
http://mysite/goto/
```   
The response will be the shortlink URL as text/plain. For example:
```
http://mysite/goto/4J5tqQUkVnc
```

Alternatively you can use a json-encoded string as payload, if you set the Content-type to application/json. The answer will still be text/plain though:

```bash
curl -X POST \
-d '{"url":"https://google.com"}' \
-H "Content-type: application/json" \
-H "ApiKey: GTeZNuFWHfMxSTBf2krrasTCu9INcmHa5KwQ12uggDM" \
http://mysite/goto/
``` 

Possible fields in the payload are:
* url = the url to shorten (required)
* feuser = a frontenduser id this shortcode is locked to (optional)

Possible return codes are:
* 200 - Success
* 304 Forbidden - when the ApiKey is wrong or missing
* 500 Invalid Payload - when the payload is not valid

To update a shortened url you would use the PUT method on the shortened URL. PUT supports like POST a json encoded and a query encoded payload. 

for example:

```bash
curl -X PUT \
-d '{"url":"https://bing.com"}' \
-H "Content-type: application/json" \
-H "ApiKey: GTeZNuFWHfMxSTBf2krrasTCu9INcmHa5KwQ12uggDM" \
http://mysite/goto/4J5tqQUkVnc
```

The body will simply contain `OK` on an successfull update. If the shortened URL is locked to a user, the userid needs to be provided as well. Only the URL can be updated, not the user.

 Possible return codes are:
 * 200 - Success
 * 304 Forbidden - when the ApiKey is wrong or missing
 * 500 Invalid Payload - when the payload is not valid
 * 404 - if the shortened URL is not valid
 * 403 - if a feuser has been provided but doesn't match the stored user
 
 Respectively the method DELETE will delete a shortened url. DELETE does not require a payload.
 
 ```bash
 curl -X DELETE \
 -H "ApiKey: GTeZNuFWHfMxSTBf2krrasTCu9INcmHa5KwQ12uggDM" \
 http://mysite/goto/4J5tqQUkVnc
 ```
 
The body will simply contain `OK` on an successfull delete. 

 Possible return codes are:
 * 200 - Success
 * 304 Forbidden - when the ApiKey is wrong or missing
 * 404 - if the shortened URL is not valid

TODO: Documentation
