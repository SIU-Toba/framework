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
class zona
{
	protected $id;						//ID de la zona
	protected $items_vecinos; 			//Array de ITEMs que viven en la ZONA
	protected $editable_id;				//ID del editable cargado
	protected $editable_info;			//Propiedades del EDITABLE
	
	function __construct($id)
	{
		$this->id = $id;
		//Creo la lista de los VECINOS de la ZONA
		$this->items_vecinos = info_proyecto::get_items_zona($id, toba::get_hilo()->obtener_usuario());
		//Se propago algo por el canal utilizado por la zona?
		$this->editable_id = toba::get_hilo()->obtener_parametro(apex_hilo_qs_zona);
		if ( isset($this->editable_id) ){
			$this->cargar(explode(apex_qs_separador,$this->editable_id));
		}
	}

	protected function cargar_descripcion()
	{
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
		$this->editable_id = $id;
		$this->cargar_descripcion();
	}

	function get_editable()
	{
		return $this->editable_id;
	}
	
	function info(){
		$dump["id"]=$this->id;
		$dump["items_vecinos"]= $this->items_vecinos;
		$dump["editable_id"]= $this->editable_id;
		$dump["editable_info"]= $this->editable_info;
		ei_arbol($dump,"ZONA");
	}
	
	//-------------------------------------------------------------------------------
	//--------------------------   INTERFACE GRAFICA   ------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html_barra_superior()
	{
		echo "<table class='zona-barra-sup'><tr>";
		$this->obtener_html_barra_info();
		$this->obtener_html_barra_vinculos();
		$this->obtener_html_barra_especifico();
		echo "<td width='15'>&nbsp;</td>";
		echo "</tr></table>\n";
	}

	/**
	 * Muestra la seccion INFORMATIVA (izquierda) de la barra
	 */
	function obtener_html_barra_info()
	{
		echo "	<td width='250' class='zona-barra-id'>";
		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='zona-barra-desc'>&nbsp;".$this->editable_info['nombre']."</td>";
	}

	/**
	 * Genera el html de la seccion de ITEMs VECINOS en la barra
	 */
	function obtener_html_barra_vinculos()
	{
		$js_cambiar_color = " onmouseover=\"this.className='barra-item-link2';\" ".
    	                    "  onmouseout=\"this.className='barra-item-link';\"";
		foreach($this->items_vecinos as $item){
			echo "<td  class='barra-item-link' $js_cambiar_color width='1'>";
 			echo "<a href='" . toba::get_vinculador()->generar_solicitud($item['item_proyecto'],
																				$item['item'],
																				null,
																				true) ."'>";
			if((isset($item['imagen_origen']))&&(isset($item['imagen']))){
				if($item['imagen_origen']=="apex"){
					echo recurso::imagen_apl($item['imagen'],true,null,null,$item['descripcion']);
				}elseif($item['imagen_origen']=="proyecto"){
					echo recurso::imagen_pro($item['imagen'],true,null,null,$item['descripcion']);
				}else{
					echo recurso::imagen_apl("check_cascada_off.gif",true,null,null,$item['descripcion']);
				}
			}else{
				echo recurso::imagen_apl("check_cascada_off.gif",true,null,null,$item['descripcion']);
			}
			echo "</a>";
			echo "</td>";
		}
	}
	
	function obtener_html_barra_especifico()
	{	
	}

	
	function obtener_html_barra_inferior()
	{
		return null;
	}

}
?>