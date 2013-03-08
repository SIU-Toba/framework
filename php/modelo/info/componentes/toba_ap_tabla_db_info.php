<?php

class toba_ap_tabla_db_info implements toba_meta_clase
{
	protected $datos;
	
	function __construct($datos)
	{
		$this->datos = $datos;
	}
	
	
	function get_nombre_instancia_abreviado()
	{
		return "dt_ap";	
	}	
	
	function set_subclase($nombre, $archivo, $pm)
	{
		$db = toba_contexto_info::get_db();
		$nombre = $db->quote($nombre);
		$archivo = $db->quote($archivo);
		$pm = $db->quote($pm);
		$sql = "
			UPDATE apex_objeto_db_registros
			SET 
				ap = 0,
				ap_clase = $nombre,
				ap_archivo = $archivo,
				punto_montaje = $pm
			WHERE
					objeto_proyecto = '{$this->datos['proyecto']}'
				AND	objeto = '{$this->datos['objeto']}'
		";
		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}	
	
	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		
		//-- Ini
		$doc = 'Se ejecuta al inicio de todos los request en donde participa el componente';
		$metodo = new toba_codigo_metodo_php('ini', array(), array($doc));
		$metodo->set_doc($doc);
		$molde->agregar($metodo);
		
		//-- Pre Sinc
	 	$doc = "Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a sincronizar";
	 	$comentarios = array(
	 		$doc,
			"La transacción con la bd ya fue iniciada (si es que hay)"	 		
	 	);
		$metodo = new toba_codigo_metodo_php('evt__pre_sincronizacion', array(), $comentarios);
		$metodo->set_doc($doc);
		$molde->agregar($metodo);
		
		//-- Post Sinc
	 	$doc = "Ventana para incluír validaciones (disparar una excepcion) o disparar procesos posteriores a la sincronización";
	 	$comentarios = array(
	 		$doc,
			"La transacción con la bd aún no fue terminada (si es que hay)"	 		
	 	);		
		$metodo = new toba_codigo_metodo_php('evt__post_sincronizacion', array(), $comentarios);
		$metodo->set_doc($doc);
		$molde->agregar($metodo);
		
		
		//-------------------------------------
		$doc = "Ventana de extensión previo a la inserción de un registro durante una sincronización con la base"; 
		$comentarios = array($doc, '@param mixed $id_registro Clave interna en la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__pre_insert',array('$id_registro'),$comentarios);
		$metodo->set_doc($doc);
		$molde->agregar( $metodo );
		
		//-------------------------------------
		$doc = "Ventana de extensión posterior a la inserción de un registro durante una sincronización con la base";
		$comentarios = array($doc, '@param mixed $id_registro Clave interna en la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__post_insert',array('$id_registro'),$comentarios);
		$metodo->set_doc($doc);
		$molde->agregar( $metodo );
		
		//-------------------------------------
		$doc = "Ventana de extensión previo a la actualización de un registro durante una sincronización con la base";
		$comentarios = array($doc, '@param mixed $id_registro Clave interna en la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__pre_update',array('$id_registro'),$comentarios);
		$metodo->set_doc($doc);
		$molde->agregar( $metodo );
		
		//-------------------------------------
		$doc = "Ventana de extensión posterior a la actualización de un registro durante una sincronización con la base";
		$comentarios = array($doc, '@param mixed $id_registro Clave interna en la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__post_update',array('$id_registro'),$comentarios);
		$metodo->set_doc($doc);
		$molde->agregar( $metodo );
		
		//-------------------------------------
		$doc = "Ventana de extensión previa al borrado de un registro durante una sincronización con la base";
		$comentarios = array($doc, '@param mixed $id_registro Clave interna en la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__pre_delete',array('$id_registro'),$comentarios);
		$metodo->set_doc($doc);
		$molde->agregar( $metodo );
		
		//-------------------------------------
		$doc = "Ventana de extensión posterior al borrado de un registro durante una sincronización con la base";
		$comentarios = array($doc, '@param mixed $id_registro Clave interna en la estructura $this->datos');
		$metodo = new toba_codigo_metodo_php('evt__post_delete',array('$id_registro'),$comentarios);
		$metodo->set_doc($doc);
		$molde->agregar( $metodo );
		return $molde;
		
	}
	
	function get_clase_nombre()
	{
		return 'toba_ap_tabla_db_s';
	}

	function get_clase_archivo()
	{
		return 'nucleo/componentes/persistencia/toba_ap_tabla_db_s.php';
	}
	
	function get_punto_montaje()
	{
		return $this->datos['punto_montaje'];
	}

	function get_subclase_nombre()
	{
		return $this->datos['ap_sub_clase'];
	}

	function get_subclase_archivo()
	{
		return $this->datos['ap_sub_clase_archivo'];	
	}
	
	//---------------------------------------------------------------------
	
	function get_descripcion_subcomponente()
	{
		return 'Administrador de Persistencia';
	}
}
?>