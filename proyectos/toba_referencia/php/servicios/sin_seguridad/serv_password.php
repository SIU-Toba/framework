<?php
class serv_password extends toba_servicio_web
{
	
	function get_opciones()
	{
		$policy = new WSPolicy(array(
								"security" => array(
									"useUsernameToken" => true,
									"includeTimestamp" => true
									)	
								)
							);
		$security = new WSSecurityToken(array(
											"user" => "toba",
											"password" => "toba",
											"ttl" => 300
											)
										);		

		return array(
			"reliable"			=> true,		
            "policy" 			=> $policy,
            "securityToken"		=> $security,
             'actions' => array(
					"http://siu.edu.ar/toba_referencia/serv_pruebas/aplanar_array"			=> "aplanar_array",
					"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_alta"			=> "persona_alta",
					"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_deportes"	=> "persona_set_deportes",		
					"http://siu.edu.ar/toba_referencia/serv_pruebas/persona_set_juegos"		=> "persona_set_juegos",		
				),
		);
	}
	

}

?>