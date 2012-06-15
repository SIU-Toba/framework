<?php

class toba_item_molde extends toba_molde_elemento
{
	protected $clase = 'toba_item';
	protected $ci = null;						// CI base
	protected $cn = null;						// CN base

	function ini()
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'padre_proyecto',$this->proyecto);
		$this->set_tipo_solicitud('web');
		$this->set_tipo_pagina('titulo');
		$tipo_pagina = toba_info_editores::get_tipo_pagina_defecto($this->proyecto);
		$this->set_tipo_pagina($tipo_pagina['pagina_tipo'], $tipo_pagina['proyecto']);
		$this->set_acceso_menu();
	}
	
	
	function cargar($id)
	{
		$this->datos->cargar(array('proyecto' => $this->proyecto, 'item' => $id));
	}
	
	//----------------------------------------------------
	//-- API CONSTRUCCION
	//----------------------------------------------------

	function ci()
	{
		if(!isset($this->ci)) $this->ci = new toba_ci_molde($this->asistente);
		return $this->ci;
	}
	
	function cn()
	{
		if(!isset($this->cn)) $this->cn = new toba_cn_molde($this->asistente);
		return $this->cn;
	}
	
	function set_ci($ci)
	{
		$this->ci = $ci;
	}

	function set_carpeta_item($id)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'padre',$id);
	}
	
	function set_tipo_solicitud($id)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'solicitud_tipo',$id);
	}

	function set_tipo_pagina($id, $proyecto=null)
	{
		if(!isset($proyecto)) $proyecto = 'toba';
		$this->datos->tabla('base')->set_fila_columna_valor(0,'pagina_tipo_proyecto',$proyecto);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'pagina_tipo',$id);
	}

	function set_acceso_menu($estado=true, $orden=0)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'menu',$estado);
		$this->datos->tabla('base')->set_fila_columna_valor(0,'orden',$orden);
	}

	function cargar_grupos_acceso_activos()
	{
		foreach(toba_editor::get_perfiles_funcionales_previsualizacion() as $grupo) {
			$this->datos->tabla('permisos')->nueva_fila(array('usuario_grupo_acc'=>$grupo));
		}
	}
	
	function set_accion($archivo)
	{
		$this->archivo = $archivo;
	}

	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------

	function generar()
	{
		//Abrir transaccion
		if(isset($this->ci)) {
			$this->ci->generar();	
			$clave = $this->ci->get_clave_componente_generado();
			$this->asociar_objeto($clave['clave']);
		}
		if(isset($this->cn)) {
			$this->cn->generar();	
			$clave = $this->ci->get_clave_componente_generado();
			$this->asociar_objeto($clave['clave']);
		}
		parent::generar();
	}
	
	function asociar_objeto($clave)
	{
		static $a = 0;
		$this->datos->tabla('objetos')->nueva_fila(array('proyecto' => $this->proyecto, 'objeto' => $clave, 'orden' => $a));
		$a++;
	}

	protected function get_codigo_php()
	{
		return '';
	}
	
	protected function asociar_archivo()
	{
		
	}

	function get_clave_componente_generado()
	{
		$datos = $this->datos->tabla('base')->get_clave_valor(0);
		return array(	'clave' => $datos['item'],
						'proyecto' => $datos['proyecto']);
	}
}
?>