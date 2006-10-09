<?php
require_once('info_componente.php');

class info_datos_relacion extends info_componente
{
	/**
	*	Retorna la metaclase correspondiente al AP del datos relacion
	*/
	function get_metaclase_subcomponente($subcomponente)
	{
		require_once('info_ap_relacion_db.php');
		return new info_ap_relacion_db($this->datos['info_estructura']);
	}	
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function get_utilerias()
	{
		//ei_arbol($this->datos);
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear una nueva tabla asociada a la relación",
			'vinculo' => toba::vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'datos_relacion', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		if (isset($this->datos['info_estructura']['ap_clase'])) {
			// Hay PHP asociado
			if ( admin_util::existe_archivo_subclase($this->datos['info_estructura']['ap_archivo']) ) {
				$iconos[] = info_componente::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'php_ap.gif' );
				$iconos[] = info_componente::get_utileria_editor_abrir_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'reflexion/abrir_ap.gif' );
			} else {
				$iconos[] = info_componente::get_utileria_editor_ver_php( array(	'proyecto'=>$this->proyecto,
																					'componente' =>$this->id ),
																			'ap',
																			'php_ap_inexistente.gif',
																			false );
			}
		}		
		return array_merge($iconos, parent::get_utilerias());	
	}
	
	
	/**
	 * La clonacion del DR puede implicar clonar su AP
	 */
	protected function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		parent::clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		if (isset($this->datos['info_estructura']['ap_archivo'])) {
			$archivo = $this->datos['info_estructura']['ap_archivo'];
			$nuevo_archivo = $dir_subclases."/".basename($archivo);
			$path_origen = toba_instancia::get_path_proyecto(contexto_info::get_proyecto())."/php/";
			if (isset($proyecto_dest)) {
				$path_destino = toba_instancia::get_path_proyecto($proyecto_dest)."/php/";
			} else {
				$path_destino = $path_origen;	
			}
			$dr->tabla('prop_basicas')->set_fila_columna_valor(0, 'ap_archivo', $nuevo_archivo);
			//--- Si el dir. destino no existe, se lo crea
			if (!file_exists($path_destino.$dir_subclases)) {
				manejador_archivos::crear_arbol_directorios($path_destino.$dir_subclases);
			}
			copy($path_origen.$archivo, $path_destino.$nuevo_archivo);
		}
	}	
	
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
}
?>