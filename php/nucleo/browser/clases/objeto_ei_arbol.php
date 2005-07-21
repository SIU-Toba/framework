<?php
require_once("objeto_ei.php");

class objeto_ei_arbol extends objeto_ei
{
	protected $nodo_inicial;
	protected $item_propiedades = array();
	protected $mostrar_raiz = true;

    function __construct($id)
    {
        parent::__construct($id);
		$this->submit = "ei_arbol" . $this->id[1];
		$this->objeto_js = "objeto_ei_arbol_{$id[1]}";
	}
	
	function destruir()
	{
		$this->memoria["eventos"] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria["eventos"][$id] = true;
			}
		}	
		parent::destruir();
	}


	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}
	
	function set_item_propiedades($id_item)
	{
		$this->item_propiedades = $id_item;
	}
	
	function set_mostrar_raiz($mostrar)
	{
		$this->mostrar_raiz = $mostrar;
	}
	
    function cargar_datos($nodo=null, $memorizar=true)
    {
		$this->nodo_inicial = $nodo;
	}
	
	function recuperar_interaccion()
	{
	}

	function get_lista_eventos()
	{
		$eventos = array();
		$eventos += eventos::ver_propiedades();
		return $eventos;
	}
	
	function disparar_eventos()
	{
		$this->recuperar_interaccion();
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if(isset($this->memoria['eventos'][$evento]) ) {
				//Se selecciono algo??
				$parametros = null;
				if (isset($_POST[$this->submit."__seleccion"])) {
					$parametros = $_POST[$this->submit."__seleccion"];
				}
				$this->reportar_evento( $evento, $parametros );
			}
		}
	}
	
	function obtener_html()
	{
		$salida = "";
		$salida .= form::hidden($this->submit, '');
		$salida .= form::hidden($this->submit."__seleccion", '');
		if ($this->nodo_inicial != null) {
	//		$salida .= "<span style='float:right'>Niveles:<select><option>1</option><option>2</option></select></span>";
			$salida .= "<ul class='ei_arbol-raiz' style=''>";
			$salida .= $this->recorrer_recursivo($this->nodo_inicial, true);		
			$salida .= "</ul>";
		}
		echo $salida;
	}
	
	function recorrer_recursivo(recorrible_como_arbol $nodo, $es_raiz = false)
	{
		$salida = "<li class='ei_arbol-nodo'>";
		if (!$es_raiz || $this->mostrar_raiz) {
			//Barra de utileria
			$salida .= "<span style='float: right'>";
			foreach ($nodo->utilerias() as $utileria) {
				$img = recurso::imagen($utileria['imagen'], null, null, $utileria['ayuda']);
				if (isset($utileria['vinculo'])) {
					$salida .= "<a href='".$utileria['vinculo']."'>$img</a>\n";
				} else {
					$salida .= $img;
				}
			}
			$salida .= "</span>";
			//Expandir / Contraer
			if (! $nodo->es_hoja()) {
				$salida .= "<img src='".recurso::imagen_apl('arbol/contraer.gif', false)."' 
						onclick='{$this->objeto_js}.cambiar_expansion(this);' style='cursor:hand'> ";
			} else {
				$salida .= gif_nulo(16,1);
			}
				
			//Barra de Iconos
			foreach ($nodo->iconos() as $icono) {
				$img = recurso::imagen($icono['imagen'], null, null, $icono['ayuda']);
				if (isset($icono['vinculo'])) {
					$salida .= "<a href='".$icono['vinculo']."'>$img</a>\n";
				} else {
					$salida .= $img."\n";
				}
			}
			//Nombre
			$corto = $this->acortar_nombre($nodo->nombre_corto());
			$title= "title='Nombre: ".$nodo->nombre_largo()."\nId:  ".$nodo->id()."'";
			$nombre= "<span class='ei_arbol-nombre' $title>$corto</span>";
			if ($nodo->tiene_propiedades()) {
				$salida .= "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"".$nodo->id()."\");' 
						class='ei_arbol-ver-prop' $title>$nombre</a>";			
			} else {
				$salida .= $nombre;
			}
		}
		//Recursividad
		if (! $nodo->es_hoja()) {
			$salida .= "<ul class='ei_arbol-rama'>";
			foreach ($nodo->hijos() as $nodo_hijo) {
				$salida .= $this->recorrer_recursivo($nodo_hijo);
			}
			$salida .= "</ul>";
		}
		$salida .= "</li>\n";
		return $salida;
	}
	

	function acortar_nombre($nombre) 
	{
		$limite = 30;
		if (strlen($nombre) <= $limite)
			return $nombre;
		else
			return substr($nombre, 0, $limite)."...";
		return $nombre;
	}
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		$item = js::arreglo($this->item_propiedades, false);
		echo $identado."var {$this->objeto_js} = new objeto_ei_arbol('{$this->objeto_js}',
												 '{$this->submit}', $item);\n";

	}

	//-------------------------------------------------------------------------------

	public function consumo_javascript_global()
	{
		$consumo = parent::consumo_javascript_global();
		$consumo[] = 'clases/objeto_ei_arbol';
		return $consumo;
	}	

}

?>