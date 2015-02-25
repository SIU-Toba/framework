<?php
 
/**
 *  SAML Metadata view
 */

require_once dirname(dirname(__FILE__)).'/_toolkit_loader.php';

require_once 'settings.php' ;

try {
    $auth = new OneLogin_Saml2_Auth($settingsInfo);
    $settings = $auth->getSettings();
    $metadata = $settings->getSPMetadata();
    $errors = $settings->validateMetadata($metadata);
    if (empty($errors)) {
        header('Content-Type: text/xml');
        echo $metadata;
    } else {
        throw new OneLogin_Saml2_Error(
            'Invalid SP metadata: '.implode(', ', $errors),
            OneLogin_Saml2_Error::METADATA_SP_INVALID
        );
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
