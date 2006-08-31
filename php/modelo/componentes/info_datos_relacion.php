<?php
require_once('info_componente.php');

class info_datos_relacion extends info_componente
{
	function get_metadatos_subcomponente($subcomponente)
	{
		//ei_arbol($this->datos);
		$sub['clase'] = $this->datos['info_estructura']['ap_clase'];
		$sub['archivo'] = $this->datos['info_estructura']['ap_archivo'];
		//HARCODEO!
		$sub['padre_clase'] = 'ap_tabla_db';
		$sub['padre_archivo'] = 'nucleo/componentes/persistencia/ap_tabla_db.php';
		require_once('info_ap_tabla_db.php');
		$mt = new info_ap_tabla_db();
		$sub['meta_clase'] = $mt;
		return $sub;
	}	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function get_utilerias()
	{
		//ei_arbol($this->datos);
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear una nueva tabla asociada a la relación",
			'vinculo' => toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/objetos_toba/crear",
								array(	'destino_tipo' => 'datos_relacion', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id),
										false, false, null, true, "central"),
			'plegado' => true										
		);
		$param_editores = array(apex_hilo_qs_zona=>$this->proyecto.apex_qs_separador.$this->id,
								'subcomponente'=>'ap');
		if (isset($this->datos['info_estructura']["ap_clase"])) {
			$iconos[] = array(
				'imagen' => toba_recurso::imagen_apl("php_ap.gif", false),
				'ayuda' => "Ver detalles de la extensión del Adm.Persistencia",
				'vinculo' => toba::get_vinculador()->generar_solicitud(editor::get_id(),"/admin/objetos/php", $param_editores,
																		false, false, null, true, "central"),
				'plegado' => true																		
			);
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
			$path_origen = info_instancia::get_path_proyecto(contexto_info::get_proyecto())."/php/";
			if (isset($proyecto_dest)) {
				$path_destino = info_instancia::get_path_proyecto($proyecto_dest)."/php/";
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
}
?>