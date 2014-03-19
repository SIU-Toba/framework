<?php

$ini = parse_ini_file(dirname(__FILE__).'/../../../../instalacion/saml.ini',true);
foreach($ini as $key => $array)
{
	if (!substr($key, 0, 4) == 'idp:') {
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
