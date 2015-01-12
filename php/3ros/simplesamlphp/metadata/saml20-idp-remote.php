<?php

if (isset($_SERVER['TOBA_INSTALACION_DIR'])) {			//Determinacion de la ruta de instalacion a traves del alias en toba.conf
	$base = $_SERVER['TOBA_INSTALACION_DIR'];
} else {
	$base = dirname(__FILE__).'/../../../../instalacion';
}
$ini = parse_ini_file($base . '/saml.ini',true);

foreach($ini as $key => $array)
{
	if (substr($key, 0, 4) !== 'idp:') {
		continue;
	}  
	$key = trim(substr($key, 4));	
	$metadata[$key] = array(
          'name' => array(
                  'en' => $array['name']
          ),
          'SingleSignOnService'  => $array['SingleSignOnService'],
          'certFingerprint'      => $array['certFingerprint']
	);
  
	if( trim($array['SingleLogoutService']) != '' )
	{
	  $metadata[$key]['SingleLogoutService'] = $array['SingleLogoutService'];
	}

}
