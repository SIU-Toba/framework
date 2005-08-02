<?
//Generacion: 23-07-2005 05:04:17
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_ci_pantalla extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_ci_pantalla'
{
	function __construct($fuente=null, $min_registros=0, $max_registros=0 )
	{
		$def['tabla']='apex_objeto_ci_pantalla';
		$def['columna'][0]['nombre']='objeto_ci_proyecto';
		$def['columna'][0]['pk']='1';
		//$def['columna'][0]['no_nulo']='1';
		$def['columna'][1]['nombre']='objeto_ci';
		$def['columna'][1]['pk']='1';
		//$def['columna'][1]['no_nulo']='1';
		$def['columna'][2]['nombre']='pantalla';
		$def['columna'][2]['pk']='1';
		$def['columna'][2]['secuencia']='apex_obj_ci_pantalla_seq';
		$def['columna'][3]['nombre']='identificador';
		//$def['columna'][3]['no_nulo']='1';
		$def['columna'][4]['nombre']='orden';
		$def['columna'][5]['nombre']='etiqueta';
		$def['columna'][6]['nombre']='descripcion';
		$def['columna'][7]['nombre']='tip';
		$def['columna'][8]['nombre']='imagen_recurso_origen';
		$def['columna'][9]['nombre']='imagen';
		$def['columna'][10]['nombre']='objetos';
		$def['columna'][11]['nombre']='eventos';
		parent::__construct( $def, $fuente, $min_registros, $max_registros);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_ci_proyecto = '{$id['proyecto']}'";
		$where[] = "objeto_ci = '{$id['objeto']}'";
		$this->cargar_datos($where);
	}

	//-----------------------------------------------------
	//--- Manejo de la relacion con las DEPENDENCIAS
	//-----------------------------------------------------

	function set_dependencias_pantalla($pantalla, $dependencias)
	//Setea las dependencias asociadas a una pantalla		
	{
		$deps = implode(",", $dependencias);
		$this->set_registro_valor($pantalla, 'objetos', $deps);
	}
	
	function get_dependencias_pantalla($pantalla)
	//Devuelve las dependencias asociadas a una pantalla
	{
		$out = null;
		$deps = $this->get_registro_valor($pantalla, 'objetos');
		if(trim($deps)!=""){
			$out = array_map("trim", explode(",", $deps ) );		
		}
		return $out;
	}

	function eliminar_dependencia($dependencia)
	//Elimino una dependencia de todas las pantallas donde este
	{
		$ids = $this->get_id_registro_condicion();
		//Recorro las pantallas
		foreach($ids as $id){
			$deps = $this->get_dependencias_pantalla($id);
			if(is_array($deps)){
				$deps = array_flip( $deps );
				if(isset($deps[$dependencia])){
					unset($deps[$dependencia]);
					$this->set_dependencias_pantalla( $id, array_flip($deps) );
				}
			}
		}
	}

	//-----------------------------------------------------
	//--- Manejo de la relacion con los EVENTOS
	//-----------------------------------------------------

	function set_eventos_pantalla($pantalla, $eventos)
	//Setea las eventos asociadas a una pantalla		
	{
		$deps = implode(",", $eventos);
		$this->set_registro_valor($pantalla, 'eventos', $deps);
	}
	
	function get_eventos_pantalla($pantalla)
	//Devuelve las eventos asociadas a una pantalla
	{
		$out = null;
		$deps = $this->get_registro_valor($pantalla, 'eventos');
		if(trim($deps)!=""){
			$out = array_map("trim", explode(",", $deps ) );		
		}
		return $out;
	}

	function eliminar_evento($evento)
	//Elimino una evento de todas las pantallas donde este
	{
		$ids = $this->get_id_registro_condicion();
		//Recorro las pantallas
		foreach($ids as $id){
			$evts = $this->get_eventos_pantalla($id);
			if(is_array($evts)){
				$evts = array_flip( $evts );
				if(isset($evts[$evento])){
					unset($evts[$evento]);
					$this->set_eventos_pantalla( $id, array_flip($evts) );
				}
			}
		}
	}
	//-----------------------------------------------------
}
?>