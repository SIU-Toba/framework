<?
require_once("interfaces.php");

class info_item implements recorrible_como_arbol
{
	protected $subelementos = array();
	protected $proyecto;
	protected $id;
	protected $datos;
	
	function __construct( $datos )
	{
		$this->datos = $datos;	
		$this->id = $this->datos['info']['item'];
		$this->proyecto = $this->datos['info']['item_proyecto'];
		$this->cargar_dependencias();
	}

	function cargar_dependencias()
	{
		//Si hay objetos asociados...
		if (count($this->datos['info_objetos'])>0)	{
			for ($a=0; $a<count($this->datos['info_objetos']); $a++) {
				$clave['proyecto'] = $this->datos['info_objetos'][$a]['objeto_proyecto'];
				$clave['componente'] = $this->datos['info_objetos'][$a]['objeto'];
				$this->subelementos[$a] = constructor_toba::get_info( $clave );
			}
		}
	}

	//---------------------------------------------------------------------	
	// Preguntas basicas
	//---------------------------------------------------------------------

	function es_carpeta() { return $this->datos['info']['carpeta']; }
	
	function es_de_menu() {	return $this->datos['info']['menu']; }
	
	function es_publico() { return $this->datos['info']['publico']; } 

	function crono() 
	{ 
		if (isset($this->datos['crono']))
			return $this->datos['crono'] == 1; 
	}

	function tipo_solicitud() { return $this->datos['info']['solicitud_tipo']; }
	
	function vinculo_editor()
	{
		if ($this->es_carpeta())
			$item_editor = "/admin/items/carpeta_propiedades";
		else
			$item_editor = "/admin/items/editor_items";		
		return toba::get_vinculador()->generar_solicitud("toba", $item_editor,
						array( apex_hilo_qs_zona => $this->proyecto .apex_qs_separador. $this->id),
						false, false, null, true, "central");
	}
	
	function contiene_objeto($id)
	{
		foreach ($this->subelementos as $elem) {
			if ($elem->contiene_objeto($id)) {
				return true;
			}
		}
	}
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------
	
	function get_id()
	{
		return $this->id;	
	}
	
	function get_hijos()
	{
		return $this->subelementos;
	}
	
	function get_padre()
	{
		return null;	
	}
	
	function es_hoja()
	{
		return (count($this->subelementos) == 0);
	}

	function tiene_propiedades()
	{
		return false;
	}	
	
	function get_nombre_corto()
	{
		return $this->datos['info']['item_nombre'];
	}
	
	function get_nombre_largo()
	{
		return $this->get_nombre_corto();
	}
	
	function get_iconos()
	{
		$iconos = array();
		if ($this->es_carpeta()) {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta.gif", false),
				'ayuda' => "Editar propiedades de la carpeta",
				'vinculo' => $this->vinculo_editor()
				);

		} else {
			$iconos[] = array(
				'imagen' => recurso::imagen_apl("items/item.gif", false),
				'ayuda' => "Editar propiedades del ITEM",
				'vinculo' => $this->vinculo_editor()
				);
				
			if ($this->tipo_solicitud() == "consola") {
				$iconos[] = array(
								'imagen' => recurso::imagen_apl("solic_consola.gif",false),
								'ayuda' => 'Solicitud de Consola'
							);
			} elseif($this->tipo_solicitud()=="wddx") {
				$iconos[] = array(
								'imagen' => recurso::imagen_apl("solic_wddx.gif",false),
								'ayuda' => 'Solicitud WDDX'
							);
			} else {
				$iconos[] = array(
								'imagen' => recurso::imagen_apl("items/instanciar.gif",false),
								'ayuda' => 'Ejecutar el ITEM',
								'vinculo' => toba::get_vinculador()->generar_solicitud($this->proyecto, $this->id, 
												null,false,false,null,true, "central")
							);
			}
		}
		return $iconos;
	}
	
	function get_utilerias()
	{
		$utilerias = array();
		if ($this->es_carpeta()) {	
			if($this->es_de_menu()) {
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("items/menu.gif",false),
					'ayuda' => "La CARPETA esta incluido en el MENU del PROYECTO",
					'vinculo' => null
				);
			}
			// Ordenamiento, Nueva carpeta, nuevo item
/*			
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta_ordenar.gif", false),
				'ayuda'=> "Ordena alfabéticamente los items incluídos en esta CARPETA",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/carpeta_ordenar", 
								array("padre_p"=>$this->proyecto, "padre_i"=>$this->id) )
			);
*/
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/carpeta_nuevo.gif", false),
				'ayuda'=> "Crear SUBCARPETA en esta rama del CATALOGO",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/carpeta_propiedades", 
								array("padre_p"=>$this->proyecto, "padre_i"=>$this->id),false,false,null,true, "central" )
			);
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("items/item_nuevo.gif", false),
				'ayuda'=> "Crear ITEM hijo en esta rama del CATALOGO",
				'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/items/editor_items", 
								array("padre_p"=>$this->proyecto, "padre_i"=>$this->id),false,false,null,true, "central" )
			);			

		} else { //Es un item común
			if($this->crono()){		
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("cronometro.gif", false),
					'ayuda'=> "El ITEM se cronometra"
				);			
			}
			if($this->es_publico()){
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("usuarios/usuario.gif", false),
					'ayuda'=> "ITEM público"
				);				
			}
			if ($this->es_de_menu()) {
				$utilerias[] = array(
					'imagen' => recurso::imagen_apl("items/menu.gif", false),
					'ayuda'=> "El ITEM esta incluido en el MENU del PROYECTO"
				);	
			}
/*			//ID del objeto
			$utilerias[] = array(
				'imagen' => recurso::imagen_apl("nota.gif", false),
				'ayuda' => $this->id
			);*/
		}
		$utilerias[] = array(
			'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al item",
			'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos_toba/crear",
								array('destino_tipo' =>'item', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id ),
										false, false, null, true, "central")
		);
		$utilerias[] = array(
			'imagen' => recurso::imagen_apl("objetos/editar.gif", false),
			'ayuda' => "Editar propiedades del ITEM",
			'vinculo' => $this->vinculo_editor()
			);		
		return $utilerias;
	}
	
	function get_info_extra()
	{
		return "";	
	}

	function tiene_hijos_cargados()
	{
		return true;	
	}
}
?>