<?php
/*
*	
*/
class toba_ei_formulario_molde extends toba_molde_elemento_componente_ei
{
	protected $clase = 'toba_ei_formulario';
	protected $mapeo_efs;
	protected $proximo_ef = 0;
	
	function ini()
	{
		parent::ini();
		$this->datos->tabla('prop_basicas')->nueva_fila(array());
		$this->datos->tabla('prop_basicas')->set_cursor(0);
	}
	
	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	

	function agregar_ef($tipo, $identificador, $etiqueta = null, $orden=null, $columnas=null)
	{
		if(!isset($etiqueta)) $etiqueta = $identificador;
		if(!isset($orden)) $orden = $this->proximo_ef; $this->proximo_ef++;
		if(!isset($columnas)) {
			$columnas = $identificador;	
		}else{
			if(!is_array($columnas)){
				throw new error_toba('Las columnas deben definirse mediante un array');	
			}else{
				$columnas = implode(', ',$columnas);
			}
		}
		$datos = array(	'elemento_formulario'	=> $tipo,
						'orden' 				=> $orden,
						'identificador'			=> $identificador,
						'etiqueta'				=> $etiqueta,
						'columnas'				=> $columnas 
					);
		$this->mapeo_efs[$identificador] = $this->datos->tabla('efs')->nueva_fila($datos);
	}
}
?>