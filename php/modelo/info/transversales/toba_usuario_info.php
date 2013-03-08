<?php
class toba_usuario_info extends toba_elemento_transversal_info
{

	function ini()
	{
		$proyecto = quote($this->_id['proyecto']);
		$sql = "SELECT
					pm_usuario,
					usuario_subclase,
					usuario_subclase_archivo
				FROM apex_proyecto
				WHERE proyecto = $proyecto;";

		$this->_datos['_info'] = toba::db()->consultar_fila($sql);
		toba::logger()->debug($sql);
	}

	function set_subclase($nombre, $archivo, $pm)
	{
		$db = toba_contexto_info::get_db();
		$nombre = $db->quote($nombre);
		$archivo = $db->quote($archivo);
		$pm = $db->quote($pm);
		$id = $db->quote($this->_id['proyecto']);
		$sql = "
			UPDATE apex_proyecto
			SET
				usuario_subclase = $nombre,
				usuario_subclase_archivo = $archivo,
				pm_usuario = $pm
			WHERE
					proyecto = $id;";
		toba::logger()->debug($sql);
		$db->ejecutar($sql);
	}

	//-----------------------------------------------------------------------------------
	function get_clase_nombre()
	{
		return 'toba_usuario';
	}

	function get_clase_archivo()
	{
		return 'nucleo/lib/toba_usuario.php';
	}

	function get_punto_montaje()
	{
		return $this->_datos['_info']['pm_usuario'];
	}

	function get_subclase_nombre()
	{
		return $this->_datos['_info']['usuario_subclase'];
	}

	function get_subclase_archivo()
	{
		return $this->_datos['_info']['usuario_subclase_archivo'];
	}

	function get_molde_vacio()
	{
		$molde = new toba_codigo_clase( $this->get_subclase_nombre(), $this->get_clase_nombre() );
		return $molde;
	}

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		$molde->agregar_bloque($this->get_bloque_auth());
		$molde->agregar_bloque($this->get_bloque_info());
		$molde->agregar_bloque($this->get_bloque_acciones());
		$molde->agregar_bloque($this->get_bloque_perfiles());
		return $molde;
	}

	function get_bloque_auth()
	{
		$bloque = array();

		$doc = array('Autentica a un usuario en el sistema.');
		$metodo = new toba_codigo_metodo_php('autenticar', array('$id_usuario', '$clave' , '$datos_iniciales = null'), $doc);
		$metodo->set_tipo_funcion('static');
		$metodo->set_contenido(	'return false;');
		$bloque[] = $metodo;

		$doc = array('Permite fijar una clave para el usuario actual.');
		$metodo = new toba_codigo_metodo_php('set_clave', array('$clave_plana'), $doc);		
		$metodo->set_contenido(	'parent::set_clave($clave_plana);');
		$bloque[] = $metodo;

		$doc = array('Genera una clave aleatoria para un largo de caracteres dado');
		$metodo = new toba_codigo_metodo_php('generar_clave_aleatoria', array('$long'), $doc);
		$metodo->set_contenido(	'return parent::generar_clave_aleatoria($long);');
		$bloque[] = $metodo;

		return $bloque;
	}

	function get_bloque_info()
	{
		$bloque = array();
		$doc = array('Devuelve el nombre del usuario para mostrar en el sistema');
		$metodo = new toba_codigo_metodo_php('get_nombre', array(), $doc);
		$metodo->set_contenido(	'return null;');
		$bloque[] = $metodo;

		$doc = array('Devuelve el id interno del usuario en el sistema');
		$metodo = new toba_codigo_metodo_php('get_id', array(), $doc);
		$metodo->set_contenido(	'return null;');
		$bloque[] = $metodo;

		$doc = array('Decide si el usuario esta bloqueado o puede loguearse.');
		$metodo = new toba_codigo_metodo_php('es_usuario_bloqueado', array('$usuario'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;

		$doc = array('Decide si la IP entregada esta bloqueada o se permite el log in');
		$metodo = new toba_codigo_metodo_php('es_ip_rechazada', array('$ip'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;

		$doc = array('Devuelve la cantidad de intentos de log in para una IP en una ventana de tiempo determinada');
		$metodo = new toba_codigo_metodo_php('get_cantidad_intentos_en_ventana_temporal', array('$ip', '$ventana_temporal = null'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;

		$doc = array('Devuelve la cantidad de intentos de log in para un usuario en una ventana de tiempo determinada');
		$metodo = new toba_codigo_metodo_php('get_cantidad_intentos_usuario_en_ventana_temporal', array('$usuario', '$ventana_temporal = null'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;
		
		return $bloque;
	}

	function get_bloque_perfiles()
	{
		$bloque = array();
		
		$doc = array();
		$metodo = new toba_codigo_metodo_php('get_perfiles_funcionales', array(), $doc);
		$metodo->set_contenido(	'return array();');
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('get_restricciones_funcionales', array('$perfiles = null'), $doc);
		$metodo->set_contenido(	'return array();');
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('get_perfil_datos', array(), $doc);
		$bloque[] = $metodo;

		return $bloque;
	}

	function get_bloque_acciones()
	{
		$bloque = array();
		
		$doc = array();
		$metodo = new toba_codigo_metodo_php('registrar_error_login', array('$usuario', '$ip', '$texto'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('bloquear_ip', array('$ip'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;

		$doc = array();
		$metodo = new toba_codigo_metodo_php('bloquear_usuario', array('$usuario'), $doc);
		$metodo->set_tipo_funcion('static');
		$bloque[] = $metodo;
		return $bloque;
	}

}
?>
