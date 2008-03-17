<?php 
require_once('lib/consultas_instancia.php');

class ci_perfil_acceso extends toba_ci
{
	protected $s__filtro = null;
	protected $s__arbol_cargado = false;
	protected $s__proyecto;
	protected $s__perfil_funcional = '';
	
	function conf__seleccion_perfil()
	{
		if (!isset($this->s__filtro)) {
			$this->pantalla('seleccion_perfil')->eliminar_evento('agregar');
		}else{
			$this->s__proyecto = $this->s__filtro['proyecto'];
		}
	}
	
	function evt__guardar($datos)
	{
		$raices = $this->dep('arbol_perfiles')->get_datos();
		toba::db()->abrir_transaccion();
		/*
			$this->dep('datos')->sincronizar();
		
			Alta: se lepide el grupo de acceso al DT principal y se le pasa a los nodos
				$raiz->set_grupo_acceso( X );	
		*/
		
		foreach($raices as $raiz) {
			$raiz->sincronizar();	
		}
		unset($this->s__arbol_cargado);
		toba::db()->cerrar_transaccion();
	}
	
	function evt__volver()
	{
		$this->set_pantalla('seleccion_perfil');
	}
	
	function evt__eliminar()
	{
		
	}
	
	function evt__agregar()
	{
		$this->set_pantalla('edicion_perfil');
	}
	
	function conf__cuadro_grupos_acceso($componente)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas_instancia::get_lista_grupos_acceso_proyecto($this->s__filtro['proyecto']);
			$componente->set_datos($datos);
		}
	}
	
	function evt__cuadro_grupos_acceso__seleccion($seleccion)
	{
		$this->s__proyecto = $seleccion['proyecto'];
		$this->s__perfil_funcional = $seleccion['usuario_grupo_acc'];
		$this->set_pantalla('edicion_perfil');
	}
	
	function evt__filtro_proyectos__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	
	function evt__filtro_proyectos__cancelar()
	{
		unset($this->s__filtro);
	}
	
	function conf__filtro_proyectos($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}		
	}

}

?>