<?php

class toba_gtk_admin 
{
	protected static $comp_req = array(
			'vbox', 'arbol_comandos', 'label_comando',
			'caja_opciones', 'label_info',
			'frame', 'frame_contenido', 'frame_label'
	);
	
	protected $toba_instalador;
	protected $progreso;
	protected $seleccion;
	protected $opciones = array();
	protected $comp;
	protected $inicial = true;
	protected $tooltips;
	
	function __construct($toba_instalador, $progreso)
	{
		$this->toba_instalador = $toba_instalador;
		$this->progreso = $progreso;
		$this->tooltips = new GtkTooltips();
	}

	function construir_dialogo($id_proyecto=null)
	{
		$archivo = dirname(__FILE__).'/toba.glade';
    	$glade = new GladeXML($archivo, 'vbox');
		foreach (self::$comp_req as $comp) {
			$this->comp[$comp] = $glade->get_widget($comp);
		}		
		$glade->signal_autoconnect_instance($this);
				
		//--- Arbol
		$columna = new GtkTreeViewColumn('Comandos');
		$renderer = new GtkCellRendererPixbuf();
		$columna->pack_start($renderer, false);
		$columna->set_attributes($renderer, 'pixbuf', 1);
		
		$renderer = new GtkCellRendererText();
		$columna->pack_start($renderer, true);
		$columna->set_attributes($renderer, 'text', 0);
		$this->comp['arbol_comandos']->append_column($columna);

		$seleccionado = $this->cargar_comandos($id_proyecto);						
		$selection = $this->comp['arbol_comandos']->get_selection();
		$selection->set_mode(Gtk::SELECTION_SINGLE);	
		$selection->connect('changed', array($this, 'evt__seleccionar_comando'));
		if (isset($seleccionado)) {
			$selection->select_iter($seleccionado);
		}
		//$this->connect('button-release-event', array($this, 'evt__popup'));
		
		return $this->comp['vbox'];		
	}
	
	
	/**
	 * Retorna un modelo de comandos administrativos disponibles
	 */
	function cargar_comandos($seleccionar_proyecto=null)
	{
		$instalacion = $this->toba_instalador->get_instalacion();
		
		//---El formato es Nombre,Imagen,Clave, Label
		$modelo = new GtkTreeStore(Gtk::TYPE_STRING, Gtk::TYPE_OBJECT, Gtk::TYPE_STRING, Gtk::TYPE_STRING);
		$path = toba_nucleo::toba_dir().'/www/img/instalacion.png';
		$img = GdkPixbuf::new_from_file($path);
		$raiz = null;
		$path_inst = $instalacion->get_dir();
		$nodo_instal = $modelo->append($raiz, array('Instalación', $img,'instalacion',
											"Instalación"));

		$seleccion = null;
		if ($instalacion->existe_info_basica() ) {
			//---Agrega las instancias
			foreach ($instalacion->get_lista_instancias() as $id_instancia) {
				$instancia = $instalacion->get_instancia($id_instancia);
				$path = toba_nucleo::toba_dir().'/www/img/instancia.gif';
				$img = GdkPixbuf::new_from_file($path);	
				$nodo_inst = $modelo->append($nodo_instal, 
								array($id_instancia, $img, 'instalacion/'.$id_instancia,
										"Instancia $id_instancia"));
				//---Agrega los proyectos
				$path = toba_nucleo::toba_dir().'/www/img/nucleo/proyecto.gif';
				$img = GdkPixbuf::new_from_file($path);	
				foreach ($instancia->get_lista_proyectos_vinculados() as $id_proyecto) {
					$nodo_pro = $modelo->append($nodo_inst, 
									array($id_proyecto, $img, 'instalacion/'.$id_instancia.'/'.$id_proyecto,
											"Proyecto $id_proyecto")
								);
					if ($id_proyecto == $seleccionar_proyecto) {
						$seleccion = $nodo_pro;
					}								
				}
			}
	
			//---BASES
			$path = toba_nucleo::toba_dir().'/www/img/fuente.png';
			$img = GdkPixbuf::new_from_file($path);
			$nodo_bases = $modelo->append($raiz, array('Bases', $img, 'base', 'Bases de Toba'));
			foreach( $instalacion->get_lista_bases() as $db ) {
				$modelo->append($nodo_bases, array($db, $img, 'base_'.$db, "Base '$db'"));
			}		
		}

		
		//--- Comandos extra		
		$path = toba_nucleo::toba_dir().'/www/img/cpu.png';
		$img = GdkPixbuf::new_from_file($path);
		$modelo->append($raiz, array('Núcleo', $img, 'nucleo', 'Núcleo de Toba'));

		$path = toba_nucleo::toba_dir().'/www/img/ayuda.png';
		$img = GdkPixbuf::new_from_file($path);		
		$modelo->append($raiz, array('Documentación', $img, 'doc', 'Documentación'));

		$path = toba_nucleo::toba_dir().'/www/img/objetos/item.gif';
		$img = GdkPixbuf::new_from_file($path);				
		$modelo->append($raiz, array('Items', $img, 'item', 'Items'));

		$path = toba_nucleo::toba_dir().'/www/img/testing.gif';
		$img = GdkPixbuf::new_from_file($path);				
		$modelo->append($raiz, array('Testing', $img, 'test', 'Testing'));

		//--- Expansion
		$this->comp['arbol_comandos']->set_model($modelo);
		$this->comp['arbol_comandos']->expand_row(0, true);
		if (isset($seleccion)) {
			return $seleccion;
		}
	}
	
	protected function determinar_comando($comando)
	{
		$instancia = null;
		$proyecto = null;
		$base = null;
		$partes = explode('/', $comando);
		if (count($partes) == 2) {
			$instancia = trim($partes[1]);
			$comando = 'instancia';
		} elseif (count($partes) == 3) {
			$instancia = trim($partes[1]);
			$proyecto = trim($partes[2]);
			$comando = 'proyecto';			
		} else {
			//-- Busca si es 'base'
			if (substr($comando, 0, 5) == 'base_') {
				$base = substr($comando, 5);				
				$comando = 'base';
			}
		}
		return array($comando, $instancia, $proyecto, $base);	
	}
	
	protected function get_objeto_comando($seleccion)
	{
		list($comando, $instancia, $proyecto, $base) = $this->determinar_comando($seleccion);
		$nombre_com = 'comando_'.$comando;
		require_once("consola/comandos/$nombre_com.php");
		$objeto = new $nombre_com(isset($this->progreso) ? $this->progreso : null);
		$argumentos = array();
		if (isset($instancia)) {
			$argumentos[] = "-i$instancia";
		}
		if (isset($proyecto)) {
			$argumentos[] = "-p$proyecto";
		}	
		if (isset($base)) {
			$argumentos[] = "-d$base";
		}		
		$objeto->set_argumentos($argumentos);
		return $objeto;
	}
	
	
	//--------------------------------
	//---------- EVENTOS
	//--------------------------------

	function evt__refrescar()
	{
		/*$instalacion = $this->toba_instalador->get_instalacion();
		$instalacion->cargar_ini(true);
		foreach ($instalacion->get_lista_instancias() as $id_instancia) {
			toba_
		}*/
		toba_modelo_catalogo::instanciacion(true);
		$this->cargar_comandos();
	}
	
	function evt__mostrar_opciones()
	{
		$objeto_cmd = $this->get_objeto_comando($this->seleccion);
		$this->opciones = $objeto_cmd->inspeccionar_opciones();
		$this->comp['label_info']->set_markup($objeto_cmd->get_info_extra());
				
		//------ Cambia algunas cosas de lugar
		if ($objeto_cmd instanceof comando_base) {
			$opciones_a_sacar = array('listar', 'registrar');
			$hay_que_sacar = $objeto_cmd->tiene_definido_base();
			foreach (array_keys($this->opciones) as $id) {
				$existe = in_array($id, $opciones_a_sacar);
				if ($existe && $hay_que_sacar) {
					unset($this->opciones[$id]);
				}
				if (!$existe && !$hay_que_sacar) {
					unset($this->opciones[$id]);
				}
			}
		}
		//-----------------------------------
		
		$i=0;
		foreach ($this->opciones as $opcion => $atributos) {
			if (!isset($atributos['tags']['gtk_no_mostrar'])) {
				$nombre = ucwords(str_replace('_', ' ', $opcion));
				$boton = new GtkToolButton();		
				$boton->set_label($nombre);
				if (isset($atributos['tags']['gtk_icono'])) {
					$archivo = $atributos['tags']['gtk_icono'];
					$img = GtkImage::new_from_file(toba_dir().'/www/img/'.$archivo);
					$boton->set_property('icon-widget', $img);	
				}
				$boton->set_tooltip($this->tooltips, $atributos['ayuda']);			
				$boton->connect('clicked', array($this, 'evt__ejecutar'), $opcion, $atributos);
				$this->comp['caja_opciones']->insert($boton, $i);
				
				//--- Desactivar algunos 
				if ($objeto_cmd instanceof comando_instalacion 
						&& ($opcion == 'instalar' || $opcion == 'crear')
						&&	toba_modelo_instalacion::existe_info_basica()) {
					$boton->set_sensitive(false);			
				}
				
				//----
				$i++;
			}
			if (isset($atributos['tags']['gtk_separador'])) {
				$boton = new GtkSeparatorToolItem();
				$this->comp['caja_opciones']->insert($boton, $i);
				$i++;
			}			
		}
		$this->comp['caja_opciones']->show_all();
	}

	protected function limpiar_botones()
	{
	    $this->comp['label_info']->set_text('');		
    	foreach ($this->comp['caja_opciones']->get_children() as $hijo) {
    		$this->comp['caja_opciones']->remove($hijo);
    	}
	}
	
	function evt__seleccionar_comando($seleccion)
	{
		if ($this->inicial) {
			//$seleccion->unselect_all();
			$this->inicial = false;
		}
		$this->limpiar_botones();
	    list($model, $iter) = $seleccion->get_selected();
	    if (isset($iter)) {
	    	$this->seleccion = $model->get_value($iter, 2);
	    	$this->evt__mostrar_opciones();
	    	$this->comp['label_comando']->set_markup('<b>'.$model->get_value($iter, 3)
	    											.'</b>');
	    } else {
	    	$this->comp['label_comando']->set_text('');
	    	unset($this->seleccion);
	    }
	}
	
	function evt__ejecutar($item, $opcion, $atributos)
	{
		if (isset($this->progreso)) {
			$this->progreso->cerrar();
		}
		try {
			
			$param_extra = null;
			//---- Ejecución de dialogos particulares			
			if (isset($atributos['tags']['gtk_param_extra'])) {
				$clase = 'toba_gtk_'.$atributos['tags']['gtk_param_extra'];			
				require_once("instalacion/$clase.php");
				$parametros_comando = $this->determinar_comando($this->seleccion);
				$dialogo = new $clase($this->toba_instalador, $parametros_comando);
				$param_extra = $dialogo->show();
				if (!isset($param_extra)) {
					return;			
				}
			}
			//--------------------------------------
			$this->progreso = new inst_dlg_progreso($this, false);
			$this->comp['frame']->set_visible(true);
			$this->comp['frame_contenido']->add($this->progreso->get_widget());
			$this->progreso->set_cant_pasos_general(1);
			$this->progreso->set_cant_pasos_internos(100);		

			$objeto_com = $this->get_objeto_comando($this->seleccion);
			$info = $objeto_com->get_nombre()." $opcion ".$objeto_com->get_argumentos_string();
			$this->comp['frame_label']->set_markup("<b>Comando</b>: ".$info);
			
			$objeto_com->procesar('opcion__'.$opcion, $param_extra);
			if (isset($this->progreso)) {
				$this->progreso->finalizar();
			}
		} catch (Exception  $e) {
			inst_fact::logger()->error($e->__toString());
			inst_fact::gtk()->mostrar_excepcion($e);
			$this->evt__progreso__cancelar();
		}

	}
	
	function evt__progreso__cancelar()
	{
		$this->evt__progreso__cerrar();
	}
	
	function evt__progreso__cerrar()
	{
		$this->progreso->cerrar();
		unset($this->progreso);
		$this->comp['frame']->set_visible(false);
	}	
	
	
	//----------------------------------
	//---------- DIALOGOS ESPECIALES
	//---------------------------------
	
	function dialogo_registrar_base()
	{
		
	}
	
}
?>