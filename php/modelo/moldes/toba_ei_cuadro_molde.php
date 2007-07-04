<?php
/*
*	
*/
class toba_ei_cuadro_molde extends toba_molde_elemento_componente_ei
{
	protected $clase = 'toba_ei_cuadro';
	protected $mapeo_cols;
	protected $proxima_col = 0;
	
	function ini()
	{
		parent::ini();
		$this->datos->tabla('prop_basicas')->nueva_fila(array());
		$this->datos->tabla('prop_basicas')->set_cursor(0);
	}
	
	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	
	
	function set_clave($clave)
	{
		if(is_array($clave)){
			$clave = implode(',',$clave);
		}
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'columnas_clave',$clave);
	}

	function agregar_columna($identificador, $etiqueta=null, $estilo=null, $orden=null)
	{
		if(!isset($estilo)) $estilo = 4;
		if(!isset($etiqueta)) $etiqueta = $identificador;
		if(!isset($orden)) $orden = $this->proxima_col; $this->proxima_col++;
		$datos = array(	'estilo'				=> $estilo,
						'orden' 				=> $orden,
						'clave'					=> $identificador,
						'titulo'				=> $etiqueta
					);
		$this->mapeo_cols[$identificador] = $this->datos->tabla('columnas')->nueva_fila($datos);		
	}
}
?>