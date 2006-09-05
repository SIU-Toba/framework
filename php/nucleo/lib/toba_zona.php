<?php

/*
* 
* Una zona representa un menu alrededor de un concepto (o editable) central
* Por ejemplo mostrar un menú de opciones relacionado con un cliente particular.
* Cada una de estas opciones es un item relacionado con la zona
*
* La zona tiene estas funciones: 
* 	- Proveer barras que permitan acceder a las distintas opciones propagando el editable
* 		(proporcionando acceso a los items vecinos)
* 	- Proveer informacion sobre el editable a cualquier consumidor
* 		Esto es necesario cuando: las caracteristicas del elemento cambian la interface,
* 									Se desee proveer acceso a elementos asociados, etc.
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
		$this->items_vecinos = toba_proyecto::get_items_zona($id, toba::hilo()->get_usuario());
		//Se propago algo por el canal utilizado por la zona?
		$this->editable_id = toba::hilo()->get_parametro(apex_hilo_qs_zona);
		if ( isset($this->editable_id) ) {
			$this->cargar(toba::vinculador()->url_a_variable($this->editable_id));
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

	function cargada()
	{
		return isset($this->editable_id);
	}
	
	function cargar($id)
	{
		//--- Cambio ??
		if (!isset($this->editable_id) || $id !== $this->editable_id) {
			$this->editable_id = $id;
			$this->cargar_info();
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

	function get_editable()
	{
		return $this->editable_id;
	}
	
	function get_info()
	{
		return $this->editable_info;
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
		echo "<div class='zona-barra-id'>";
		echo $this->get_editable_id();
		echo "</div>";
	}
	
	function generar_html_barra_nombre()
	{
		echo "<div class='zona-barra-desc'>";
		echo $this->get_editable_nombre();
		echo "</div>";
	}

	/**
	 * Genera el html de la seccion de ITEMs VECINOS en la barra
	 */
	function generar_html_barra_vinculos()
	{
	
		foreach($this->items_vecinos as $item){
			$vinculo = toba::vinculador()->crear_vinculo($item['item_proyecto'], $item['item'], 
														array(), array('zona' =>true, 'validar'=>false));
			if (isset($vinculo)) {
	 			echo "<a href='$vinculo'>";
				if((isset($item['imagen_origen']))&&(isset($item['imagen']))){
					if($item['imagen_origen']=="apex"){
						echo toba_recurso::imagen_apl($item['imagen'],true,null,null,$item['nombre']);
					}elseif($item['imagen_origen']=="proyecto"){
						echo toba_recurso::imagen_pro($item['imagen'],true,null,null,$item['nombre']);
					}else{
						echo toba_recurso::imagen_apl("check_cascada_off.gif",true,null,null,$item['nombre']);
					}
				}else{
					echo toba_recurso::imagen_apl("check_cascada_off.gif",true,null,null,$item['nombre']);
				}
				echo "</a>";
			}
		}
	}
	
	function generar_html_barra_especifico()
	{	
	}
	
	function generar_html_barra_inferior()
	{
	}

}
?>