<?php

class toba_referencia extends toba_modelo_proyecto
{
	function instalar()
	{
		$this->manejador_interface->mensaje('Instalando el Proyecto de REFERENCIA.', false);
		
		$id_base = 'toba_referencia';
		//--- Chequea si existe la entrada de la base de negocios en el archivo de bases
		if (! $this->get_instalacion()->existe_base_datos_definida($id_base)) {
			//Por defecto crea la base en el mismo motor que la instancia
			$parametros = $this->get_instancia()->get_parametros_db();
			$parametros['base'] = $id_base; 
			$this->get_instalacion()->agregar_db($id_base, $parametros);
		}
		
		//--- Chequea si existe fisicamente la base creada
		if (! $this->get_instalacion()->existe_base_datos($id_base)) {
			$this->get_instalacion()->crear_base_datos($id_base);
		}
		
		//--- Instala el modelo de datos del proyecto
		$db = $this->get_instalacion()->conectar_base($id_base);
		try {
			$rs = $db->consultar("SELECT 1 FROM ref_persona_juegos");
			$existe = true;
		} catch (toba_error $e) {
			$existe = false;
		}
		$reemplazar = false;
		if ($existe) {
			$reemplazar = $this->manejador_interface->dialogo_simple("\nYa existe el modelo de datos ".
							"del proyecto REFERENCIA desea reemplazarlo", 's');
		}
		if ($reemplazar) {
			$db->ejecutar_archivo($this->get_dir().'/sql/borrado.sql');
		}
		if (! $existe || $reemplazar) {
			$db->ejecutar_archivo($this->get_dir().'/sql/creacion.sql');
			$this->manejador_interface->mensaje("OK");
		}
	}
}

?>