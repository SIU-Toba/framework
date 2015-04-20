<?php

class RDITipos 
{
	const RECURSO = 'Recurso';
	const RECURSOPERSONAL = 'RecursoPersonal';
	const FOTO = 'RecursoPersonalFoto';
	const RECIBOSUELDO = 'RecursoPersonalReciboSueldo';
	
	static function getAncestroTipos()
	{
		return self::RECURSO;
	}
	
	static function getTiposBasicos()
	{		
		return array(	self::RECURSOPERSONAL,
						self::FOTO,
						self::RECIBOSUELDO
                );
	}	
}
?>