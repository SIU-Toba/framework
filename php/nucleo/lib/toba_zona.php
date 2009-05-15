<?php
define('apex_marca_zona_activa','1');
/**
 *
 * Una zona representa un menu alrededor de un concepto central, llamado EDITABLE
 * Por ejemplo mostrar un menú de opciones relacionado con un cliente particular.
 * Cada una de estas opciones es un item relacionado con una misma zona, la 'zona de clientes'
 *
 * La zona tiene estas funciones:
 * 	- Proveer barras que permitan acceder a las distintas opciones propagando el editable
 * 		(proporcionando acceso a los items vecinos)
 * 	- Proveer informacion sobre el editable a cualquier consumidor
 * 		Esto es necesario cuando: las caracteristicas del elemento cambian la interface,
 * 									Se desee proveer acceso a elementos asociados, etc.
 *
 * Consumir usando toba::zona()->
 * 
 * @package SalidaGrafica
 * @wiki Referencia/Zona
 */
class toba_zona
{
	protected $id;						//ID de la zona
	protected $items_vecinos; 			//Array de ITEMs que viven en la ZONA
	protected $editable_id;				//ID del editable cargado
	protected $editable_info;			//Propiedades del EDITABLE
	protected $metodo_cons;
	
	function __construct($id, $metodo_cons)
	{
		$this->id = $id;
		$this->metodo_cons = $metodo_cons;
		
		//Creo la lista de los VECINOS de la ZONA
		$this->items_vecinos = toba::proyecto()->get_items_zona($id);
		//Se propago algo por el canal utilizado por la zona?
		$propagado_url = toba::memoria()->get_parametro(apex_hilo_qs_zona);
		if (! is_null($propagado_url)) {		//Se esta propagando un id
			$this->editable_id = $this->get_id_propagado($propagado_url);
		}else{	//El nuevo item no tiene zona o no se propaga id
			self::cancelar_propagacion();
		}

		if ($this->cargada()) {
			$this->cargar($this->editable_id);
		}
	}

	/**
	 * Descarga el editable que contiene actualmente la zona
	 */
	function resetear()
	{
		unset($this->editable_id);
		unset($this->editable_info);
	}

	/**
	 * La zona posee un editable cargado?
	 * @return boolean
	 */
	function cargada()
	{
		return (isset($this->editable_id) && ($this->editable_id != ''));
	}
	
	/**
	 * Informa a la zona la presencia de un nuevo editable.
	 * Este proceso dispara la carga de su información asociada
	 *
	 * @param mixd $id Id. del nuevo editable
	 */
	function cargar($id)
	{
		//--- Cambio ??
		if (!isset($this->editable_info) || !isset($this->editable_id) || $id !== $this->editable_id) {
			toba::logger()->debug("Cargando la zona '{$this->id}' con el editable '".var_export($id, true)."'");
			$this->editable_id = $id;
			$this->cargar_info();
		}
	}
	
	/**
	 * Vuelve a ejecutar el método de carga de información o descripción del editable
	 */
	function recargar()
	{
		if(isset($this->editable_id)){
			$this->cargar_info();
		} else {
			throw new toba_error('La zona no se encuentra cargada');	
		}
	}
	
	protected function cargar_info()
	{
		if (isset($this->metodo_cons['archivo'])) {
			require_once($this->metodo_cons['archivo']);
		}
		if (isset($this->metodo_cons['clase']) && isset($this->metodo_cons['metodo'])) {
			$llamada = array($this->metodo_cons['clase'], $this->metodo_cons['metodo']);
			$this->editable_info = call_user_func($llamada, $this->editable_id);
		}
	}	

	/**
	 * Retorna el id del editable actualmente cargado
	 * @return mixed
	 */
	function get_editable()
	{
		return $this->editable_id;
	}

	/**
	 *  Retorna la información relacionada con el editable actualmente cargado
	 *
	 * @param mixed $clave Si la información es un arreglo permite retornar una componente del mismo
	 * @return mixed
	 */
	function get_info($clave=null)
	{
		if (! isset($clave)) {
			return $this->editable_info;
		} else {
			return $this->editable_info[$clave];
		}
	}
	
	protected function get_editable_nombre()
	{
		if (is_scalar($this->editable_info)) {
			return $this->editable_info;
		}
		$candidatos = array('nombre', 'descripcion_corta', 'descripcion');
		foreach ($candidatos as $candidato) {
			if (isset($this->editable_info[$candidato])) {
				return $this->editable_info[$candidato];	
			}			
		}
		return '';	
	}
	
	protected function get_editable_id()
	{
		if (is_array($this->editable_id)) {
			return implode(' - ', $this->editable_id);
		} else {
			return $this->editable_id;	
		}
	}

	/**
	 * Determina el modo de propagacion usado por la zona
	 * True => El id del editable se propaga por url (por compatibilidad)
	 * False => El id del editable no se propaga por url y se mantiene en el servidor
	 * @param boolean $modo
	 */
	static function set_modo_url($modo)
	{
		toba::memoria()->set_dato('apex_modo_propagacion_zona', $modo);
	}

	/**
	 * Devuelve si la zona propaga el editable por la URL o si lo mantiene en el servidor
	 * @return boolean
	 */
	function propaga_x_url()
	{
		$modo = toba::memoria()->get_dato('apex_modo_propagacion_zona');
		//Retorna falso siempre que sea distinto de true (por si no se seteo manualmente)
		if ($modo !== true){
			$modo = false;
		}
		return $modo;
	}

	/**
	 * Hace que se guarde el id del editable en sesion
	 */
	function propagar_id()
	{		
		toba::memoria()->set_dato('apex_editable_zona', $this->get_editable());
	}

	/**
	 * Quita el id del editable de sesion.
	 */
	static function cancelar_propagacion()
	{
		toba::memoria()->eliminar_dato('apex_editable_zona');
	}

	/**
	 * Determina cual es el id correcto a utilizar, si el propagado por URL o por sesion.
	 * @param mixed $valor_por_url
	 * @return mixed
	 */
	protected function get_id_propagado($valor_por_url)
	{
		$id_editable = null;
		//Busco el id propagado x sesion
		$propagado_sesion = toba::memoria()->get_dato('apex_editable_zona');

		//Si en la url vino algo distinto a la marca de zona activa.
		if ($valor_por_url != apex_marca_zona_activa) {
			$id_editable = toba::vinculador()->url_a_variable($valor_por_url);
		} elseif (! is_null($propagado_sesion)) {
			$id_editable = $propagado_sesion;
		}
		return $id_editable;
	}

	//-------------------------------------------------------------------------------
	//--------------------------   INTERFACE GRAFICA   ------------------------------
	//-------------------------------------------------------------------------------

	function generar_html_barra_superior()
	{
		echo "<div class='zona-barra-sup' id='zona_{$this->id}'>";
		echo "<div class='zona-items'>";
		$this->generar_html_barra_vinculos();
		echo "</div>";		
		$this->generar_html_barra_nombre();
		$this->generar_html_barra_id();		
		$this->generar_html_barra_especifico();
		echo "</div>\n";
	}
	
	/**
	 * Muestra la seccion INFORMATIVA (izquierda) de la barra
	 */
	function generar_html_barra_id()
	{
		if ($this->propaga_x_url()){
			echo "<div class='zona-barra-id'>";
			echo $this->get_editable_id();
			echo "</div>";
		}
	}
	
	function generar_html_barra_nombre()
	{
		echo "<div class='zona-barra-desc'>";
		echo $this->get_editable_nombre();
		echo "</div>";
	}

	/**
	 * Genera el html de la seccion de OPERACIONES pertenecientes a la barra
	 * Extender en caso de querer cambiar radicamente la forma de mostrar iconos,
	 * probar antes si con estilos no es posible encontrar el layout buscado
	 * @ventana
	 */
	function generar_html_barra_vinculos()
	{
		foreach($this->items_vecinos as $item){
			$vinculo = toba::vinculador()->get_url($item['item_proyecto'], $item['item']);
			//Pide el vinculo por si no tiene permisos
			if (isset($vinculo)) {
				$js = "onclick=\"toba.ir_a_operacion('{$item['item_proyecto']}', '{$item['item']}', false, true)\"";
	 			echo "<a href='#' $js>";
				if((isset($item['imagen_origen']))&&(isset($item['imagen']))){
					if($item['imagen_origen']=="apex"){
						echo toba_recurso::imagen_toba($item['imagen'],true,null,null,$item['nombre']);
					}elseif($item['imagen_origen']=="proyecto"){
						echo toba_recurso::imagen_proyecto($item['imagen'],true,null,null,$item['nombre']);
					}else{
						echo toba_recurso::imagen_toba("descripcion.gif",true,null,null,$item['nombre']);
					}
				}else{
					echo toba_recurso::imagen_toba("descripcion.gif",true,null,null,$item['nombre']);
				}
				echo "</a>";
			}
		}
	}
	
	/**
	 * Ventana de extensión para incluir más opciones a la barra superior
	 * @ventana
	 */
	function generar_html_barra_especifico()
	{	
	}
	
	/**
	 * Ventana de extensión para incluir más opciones en una barra inferior
	 * @ventana
	 */	
	function generar_html_barra_inferior()
	{
	}

}
?>