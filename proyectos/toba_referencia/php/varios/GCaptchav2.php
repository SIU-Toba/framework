<?php

class GCaptchav2 implements toba_captcha_interface
{
    private $secret = null;
    private $sitekey = null;
    
    public function __construct(string $sitekey, string $secret)
    {
        $this->secret = $secret;
        $this->sitekey = $sitekey;
    }
    
    public function check(string $recaptchaResponse): bool
    {
         $url = 'https://www.google.com/recaptcha/api/siteverify?secret='.
                    $this->secret.
                    '&response='. $recaptchaResponse.
                    '&remoteip='. $_SERVER['REMOTE_ADDR'];

        $client = new GuzzleHttp\Client([]);
        $response = $client->get($url, []);
        
        $respuesta  = json_decode($response->getBody()->getContents(), true);
        return (200 === $response->getStatusCode() && false !== $respuesta['success']);
    }
    
    public function get_script(array $params): string
    {        
        return "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }
    
    public function get_widget(): string
    {
        return $this->get_script([]) . 
        '<div class="recaptcha-wrap">
            <div class="g-recaptcha" data-sitekey="'. $this->sitekey . '" data-tabindex="6"></div>
        </div>';
    }

}