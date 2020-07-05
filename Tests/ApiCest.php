<?php
class ApiCest 
{
    
    
    public function tryApi(ApiTester $I)
    {
        $I->haveHttpHeader('ApiKey', 'GTeZNuFWHfMxSTBf2krrasTCu9INcmHa5KwQ12uggDM');
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPOST('/', [
            'url' => 'https://google.com/'
        ]);
    
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('http://127.0.0.1');
        $url = $I->grabResponse();
    
        $urlparts = explode('/',$url);
        $shortlink = array_pop($urlparts);
        
        $I->stopFollowingRedirects();
        $I->sendGET('/'.$shortlink);
        $I->seeResponseCodeIs(302);
        $I->seeHttpHeader('Location','https://google.com/');
    
    
        $I->sendPUT('/'.$shortlink,[
            'url'=>'https://bing.com',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('OK');
    
        //$I->startFollowingRedirects();
        $I->sendGET('/'.$shortlink);
        $I->seeResponseCodeIs(302);
        $I->seeHttpHeader('Location','https://bing.com');
        
        $I->sendDELETE('/'.$shortlink);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('OK');
        
    }
}
