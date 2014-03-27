<?php 
require_once('lib/consultas_instancia.php');
require_once(toba_dir().'/php/3ros/Graph/Graph.php');	//Necesario para el calculo de orden topologico de las tablas

class ci_navegacion_perfiles extends toba_ci
{
	protected $s__filtro;
	protected $s__ver_esquema;
	
	function ini__operacion()
	{
		$this->s__ver_esquema = false;
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}		
	}
	
	function datos($tabla)
	{
		return $this->dep('datos')->tabla($tabla);
	}
	
	function conf__seleccion_perfil()
	{
		if (!isset($this->s__filtro)) {
			$this->pantalla('seleccion_perfil')->eliminar_evento('agregar');
		} 
		if (! $this->s__ver_esquema) {
			$this->evento('ver_grafico')->set_etiqueta('Ver Esquema');
			$this->pantalla()->eliminar_dep('esquema');
		} else {
			$this->evento('ver_grafico')->set_etiqueta('Ocultar Esquema');			
		}
		if (!isset($this->s__filtro)) {
			$this->pantalla('seleccion_perfil')->eliminar_evento('ver_grafico');
		} 		
	}
	
	function conf__edicion_perfil()
	{
		//-- Si es una instalación de producción avisar que los cambios se aplicaran solo a esta instalacion y no al proyecto/personalizacion
		admin_instancia::chequear_usar_perfiles_propios($this->dep('editor_perfiles')->get_proyecto(), $this->pantalla());
	}
	
	function actualizar_info_ini()
	{
		admin_instancia::set_usar_perfiles_propios($this->dep('editor_perfiles')->get_proyecto());
	}
	
	function actualizar_script_roles($eliminados=array())
	{
		$usa_permisos_por_tabla = false;
		foreach (toba_info_editores::get_fuentes_datos($this->dep('editor_perfiles')->get_proyecto()) as $fuente) {	//Miro si al menos una fuente usa permisos por tablas.
			$usa_permisos_por_tabla = $usa_permisos_por_tabla ||  ($fuente['permisos_por_tabla'] == '1');
		}
		
		if ($usa_permisos_por_tabla) {
			$modelo = toba_modelo_catalogo::instanciacion();	
			$modelo->set_db(toba::db());	
			$proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), $this->s__filtro['proyecto']);
			$dir = $proyecto->get_dir(). '/';
			try {			
				$proyecto->crear_script_generacion_roles_db($dir, $eliminados);
				toba::notificacion()->agregar('Se han generado los scripts de actualización de roles de base de datos.\n Los mismos se encuentran en el directorio raiz del proyecto, recuerde ejecutarlos', 'info');				
			} catch (toba_error $e) {
				toba::logger()->debug('Falló la generacion del script' . $e->getMessage());
				toba::notificacion()->agregar('No se ha podido generar los scripts de actualización de roles de base de datos, por favor utilice el comando toba proyecto roles_script', 'error');				
			}		
		}
	}
	
	function evt__guardar()
	{
		$this->dep('datos')->persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		//- Sincronizar la relacion
		if ($this->dep('datos')->esta_cargada()) {
			$alta = false;
		} else {
			$alta = true;
		}
		$this->dep('datos')->sincronizar();

		//- Sincroniza el arbol de items		
		$this->dep('editor_perfiles')->guardar_arbol_items($alta);
		$this->dep('datos')->resetear();
		toba::db()->cerrar_transaccion();
		
		//-- Si estamos en produccion guardamos un flag indicando que cambiaron los perfiles y ahora se encarga el proyecto de manejarlos
		$this->actualizar_info_ini();
		$this->actualizar_script_roles();
						
		$this->dep('editor_perfiles')->cortar_arbol();
		$this->set_pantalla('seleccion_perfil');
	}
	

	
	function evt__volver()
	{
		$this->dep('datos')->resetear();
		$this->dep('editor_perfiles')->cortar_arbol();
		$this->set_pantalla('seleccion_perfil');
	}
	
	function evt__ver_grafico()
	{
		$this->s__ver_esquema = ! $this->s__ver_esquema;
	}
		
	
	function evt__eliminar()
	{		
		$datos = $this->datos('accesos')->get();
		if (toba::fuente()->usa_permisos_por_tabla()) {		
			$this->actualizar_script_roles(array($datos['usuario_grupo_acc']));	
		}
		
		$this->dep('datos')->persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		$this->dep('datos')->eliminar();
		$this->dep('datos')->resetear();
		//- Elimino el acceso a los items
		$sql = "DELETE FROM 
					apex_usuario_grupo_acc_item 
				WHERE 
						usuario_grupo_acc = '{$datos['usuario_grupo_acc']}'
					AND proyecto = '{$datos['proyecto']}';";
		toba::db()->ejecutar($sql);
		toba::db()->cerrar_transaccion();
		
		//-- Si estamos en produccion guardamos un flag indicando que cambiaron los perfiles y ahora se encarga el proyecto de manejarlos
		$this->actualizar_info_ini();
		if (toba::fuente()->usa_permisos_por_tabla()) {
			$this->actualizar_script_roles();
		}
		
		$this->dep('editor_perfiles')->cortar_arbol();
		$this->set_pantalla('seleccion_perfil');
	}
	
	function evt__agregar()
	{
		$this->dep('editor_perfiles')->set_proyecto($this->s__filtro['proyecto']);
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
		$this->dep('datos')->cargar($seleccion);
		$this->dep('editor_perfiles')->set_proyecto($seleccion['proyecto']);
		$this->dep('editor_perfiles')->set_perfil_funcional($seleccion['usuario_grupo_acc']);
		$this->set_pantalla('edicion_perfil');
	}
	
	function conf__form_datos_perfil(toba_ei_formulario $componente)
	{
		if (toba::instalacion()->es_produccion()) {
			$componente->desactivar_efs(array('permite_edicion'));
		}
		
		$datos = array();
		if ($this->datos('accesos')->hay_cursor()) {
			$datos = $this->datos('accesos')->get();
			$componente->set_solo_lectura(array('usuario_grupo_acc'));
			
			if (toba::instalacion()->es_produccion() && isset($datos['permite_edicion']) && !$datos['permite_edicion']) {
				$this->pantalla()->eliminar_evento('guardar');
				$this->pantalla()->eliminar_evento('eliminar');
			}
		} else {
			$componente->ef('usuario_grupo_acc')->set_expreg('/^[a-z0-9_]+$/');
			$datos['proyecto'] = $this->s__filtro['proyecto'];
		}	
		$componente->set_datos($datos);
	}
	
	function evt__form_datos_perfil__modificacion($datos)
	{
		$this->datos('accesos')->set($datos);
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
	
	function conf__esquema(toba_ei_esquema $esquema) 
	{
		$grafo = $this->get_grafo();
		$diagrama = 'digraph G {
						rankdir=LR;
						fontsize=10;
						node [fontsize=10, fillcolor=white,shape=box, style=rounded,style=filled, color=gray];
						';
		foreach ($grafo->getNodes() as $nodo) {
			$data = $nodo->getData();
			$label = $data['usuario_grupo_acc'];
			$nombre = $data['nombre'];
			
			$diagrama .= "$label [label=\"$nombre\"];\n";
			foreach ($nodo->getNeighbours() as $nodo_vecino) {			
				//Incluyo la relación
				$vecino = $nodo_vecino->getData();
				$diagrama .= $label . ' -> ' . $vecino['usuario_grupo_acc']. 
							" [label=\"miembro de\",fontsize=10,color=gray];\n";
			}
			
		}
		$diagrama .= '}';		
		$esquema->set_datos($diagrama);
	}

	
	function get_grafo()
	{
		$grafo = new Structures_Graph(true);
		$perfiles = toba_info_permisos::get_perfiles_funcionales($this->s__filtro['proyecto']);
		//Nodos
		$miembros = array();
		foreach ($perfiles as $perfil) {
			$nodo = new Structures_Graph_Node();
			$nodo->setData($perfil);
			$nodos[$perfil['usuario_grupo_acc']] =& $nodo;
			$grafo->addNode($nodo);
			unset($nodo);									//Anulo el nodo, de otra manera sobre escribe todos con los valores del ultimo setData (weird)
		}
		
		//Relaciones
		foreach ($perfiles as $perfil) {
			//Necesita pasarle la conexion porque aun no termino la transacción
			$miembros = toba_info_permisos::get_perfiles_funcionales_miembros($perfil['proyecto'], $perfil['usuario_grupo_acc'], toba::db());
			foreach ($miembros as $miembro) {
				$nodos[$perfil['usuario_grupo_acc']]->connectTo($nodos[$miembro['usuario_grupo_acc_pertenece']]);
			}
		}		
		return $grafo;		
	}
	
	function validar_ciclos()
	{
		$tester = new Structures_Graph_Manipulator_AcyclicTest();
		$grafo = $this->get_grafo();
		$aciclico = $tester->isAcyclic($grafo);
		if (! $aciclico) {
			$ciclo = array();
			foreach ($tester->getCycle($grafo) as $nodo) {
				$data = $nodo->getData();
				$ciclo[] = $data['nombre'];
			}
			$perfiles = implode(', ', $ciclo);
			throw new toba_error_usuario("Existe un ciclo en la asignación de las membresías entre los perfiles: <b>$perfiles</b>.<br><br>Por favor quite alguna membresía.");
		}
	}
	
	function get_menues_existentes()
	{
		return consultas_instancia::get_menus_existentes($this->s__filtro['proyecto']);
	}
}

?>
