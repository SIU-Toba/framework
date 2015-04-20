<?php

class RDIServicioRecursoPersonalFoto extends RDIServicioRecursoPersonal
{
	function getTipo()
	{
		return RDITipos::FOTO;
	}
	
	function getParametrosOpcionales()
	{
		$parametros = parent::getParametrosOpcionales();		
		$parametros['rdirpf:ancho'] = 'ancho';
		$parametros['rdirpf:alto'] = 'alto';
		$parametros['rdirpf:unidad'] = 'unidad';
		$parametros['rdirpf:color'] = 'color';
		$parametros['rdirpf:resolucion'] = 'resolucion';
		return $parametros;
	}
	
	function getNombre($parametros)
	{
		return 'foto-' . $parametros['tipoIdentificacion'] . $parametros['numeroIdentificacion'];
	}
}