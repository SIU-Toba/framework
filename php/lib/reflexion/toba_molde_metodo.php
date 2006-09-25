<?php

class toba_molde_metodo_php implements elemento_molde
{
	protected $nombre;
	protected $parametros;
	protected $comentarios;
	protected $contenido;
	
	function __construct($nombre, $parametros=array(), $comentarios=array())
	{
		$this->nombre = $nombre;
		if ( !is_array($parametros) || !is_array($comentarios) ) {
			throw new toba_error('molde metodo: los parametros y los comentarios tienen que ser un array');
		}
	}
	
	function agregar_contenido($contenido)
	{
		if ( !is_array($contenido) ) {
			throw new toba_error('molde metodo: El contenido del metodo tiene que ser un array de filas');
		}
	}
	
	function generar_php()
	{
	
	}
}

?>