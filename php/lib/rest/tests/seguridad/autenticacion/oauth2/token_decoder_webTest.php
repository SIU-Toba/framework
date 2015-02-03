<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 1/19/15
 * Time: 5:14 PM
 */

namespace rest\tests\seguridad\autenticacion\oauth2;


use rest\seguridad\autenticacion\oauth2\oauth_token_decoder_web;

class token_decoder_webTest extends \PHPUnit_Framework_TestCase
{
    function testDecode()
    {
//        /**
//         * @var \PHPUnit_Framework_MockObject_MockObject
//         */
//        $guzzle = $this->getMockBuilder('GuzzleHttp\Client')
//            ->getMock();
//        $guzzle->method('get')->willReturn();
        include '../../3ros/guzzle/autoload.php';
        include "vendor/autoload.php";
        $cliente = new \GuzzleHttp\Client(array('base_url' =>"http://localhost:8000/oauth/tokeninfo"));
        $decoder = new oauth_token_decoder_web($cliente);
        $decoder->set_cache_manager(new \Doctrine\Common\Cache\ApcCache());
        $decoder->decode('fb430584b384ea78b201b77946b545149f57f02b2');
    }
}
 