<?php
require_once('info_componente.php');

class info_datos_tabla extends info_componente
{
	/**
	*	Retorna la metaclase correspondiente al AP del datos tabla
	*/
	function get_metaclase_subcomponente($subcomponente)
	{
		require_once('info_ap_tabla_db.php');
		return new info_ap_tabla_db($this->datos['info_estructura']);
	}

	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------
	
	function get_utilerias()
	{
		//--- Mejora para el caso de que la query sea una unica
		if (isset($this->datos['info']['ap_clase'])) {
			$this->datos['info_estructura']['ap_clase'] = $this->datos['info']['ap_clase'];
		}
		if (isset($this->datos['info']['ap_archivo'])) {
			$this->datos['info_estructura']['ap_sub_clase_archivo'] = $this->datos['info']['ap_archivo'];
		}		
		
		$iconos = array();
		if (isset($this->datos['info_estructura']['ap_sub_clase_archivo'])) {
			if ( admin_util::existe_archivo_subclase($this->datos['info_estructura']['ap_sub_clase_archivo']) ) {
				$iconos[] = info_componente::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'nucleo/php_ap.gif' );
				$iconos[] = info_componente::get_utileria_editor_abrir_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'reflexion/abrir_ap.gif' );
			} else {
				$iconos[] = info_componente::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'nucleo/php_ap_inexistente.gif',
																			false );
			}
		}
		return array_merge($iconos, parent::get_utilerias());	
	}	

	/**
	 * La clonacion del DT puede implicar clonar su AP
	 */
	protected function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		parent::clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		if (isset($this->datos['info_estructura']['ap_sub_clase_archivo'])) {
			$archivo = $this->datos['info_estructura']['ap_sub_clase_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			$path_origen = toba::instancia()->get_path_proyecto(contexto_info::get_proyecto())."/php/";
			if (isset($proyecto_dest)) {
				$path_destino = toba::instancia()->get_path_proyecto($proyecto_dest)."/php/";
			} else {
				$path_destino = $path_origen;	
			}
			$dr->tabla('prop_basicas')->set_fila_columna_valor(0, 'ap_archivo', $nuevo_archivo);
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				toba_manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			copy($path_origen.$archivo, $path_destino.$nuevo_archivo);
		}
	}		
	
	//---------------------------------------------------------------------	
	//-- Generacion de METADATOS para otros componentes
	//---------------------------------------------------------------------

	/**
	*	Exporta la definicion de una manera entendible para el datos_tabla de la tabla 
	*		donde se guardan los EFs del ei_formulario
	*/
	function exportar_datos_efs($incluir_pk=false)
	{
		$a=0;
		foreach($this->datos['info_columnas'] as $columna){
			if( (!$columna['pk']) || $incluir_pk){
				$datos[$a]['identificador'] = $columna['columna'];
				$datos[$a]['columnas'] = $columna['columna'];
				$datos[$a]['etiqueta'] = ucfirst(  str_replace("_"," ",$columna['columna']) );
				if(isset($columna['secuencia'])){
					$datos[$a]['elemento_formulario'] = 'ef_fijo';
				}else{
					if($columna['no_nulo_db']) $datos[$a]['obligatorio'] = 1;
					switch($columna['tipo']){
						case 'E':
							$datos[$a]['elemento_formulario'] = 'ef_editable_numero';
							break;
						case 'N':
							$datos[$a]['elemento_formulario'] = 'ef_editable_numero';
							break;
						case 'F':
							$datos[$a]['elemento_formulario'] = 'ef_editable_fecha';
							break;
						default:
							$datos[$a]['elemento_formulario'] = 'ef_editable';
					}
				}
				$datos[$a]['orden'] = $a;
				$a++;			
			}
		}
		return $datos;
	}

	/**
	*	Exporta la definicion de una manera entendible para el datos_tabla de la tabla 
	*		donde se guardan las columnas del ei_cuadro
	*/
	function exportar_datos_columnas($incluir_pk=false)
	{
		$datos = array();
		$a=0;
		foreach($this->datos['info_columnas'] as $columna){
			if( ((!$columna['pk']) || $incluir_pk) && $columna['secuencia'] == '' ){
				$datos[$a]['clave'] = $columna['columna'];
				$datos[$a]['titulo'] = ucfirst(  str_replace("_"," ",$columna['columna']) );
				switch($columna['tipo']){
					case 'E':
						$datos[$a]['estilo'] = '0';//numero-1
						break;
					case 'N':
						$datos[$a]['estilo'] = '0';
						break;
					default:
						$datos[$a]['estilo'] = '4';	//texto-1
				}
				$datos[$a]['orden'] = $a;
				$a++;			
			}
		}
		return $datos;
	}

	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar( new toba_molde_metodo_php('evt__validar_fila', array('fila') ) );
		$molde->agregar( new toba_molde_metodo_php('validar_fila', array('fila','id=null') ) );
		return $molde;
	}
}
?>