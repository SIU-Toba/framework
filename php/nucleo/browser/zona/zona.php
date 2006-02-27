<?php
/*
* 
* - BARRAS de la ZONAS
* - Tengo que armar la funcion de refresco de la pagina vecina (refrescar_listado_editable_apex)
* 
* Una ZONA es un lugar donde trabajar con un EDITABLE.
* Un EDITABLE es un elemento del sistema que se configura a travez de varios ITEMS
* Los ITEMS que permiten acceder a las distintas facetas del editable se consideran VECINOS
* La zona tiene dos funciones: 
* 	- Proveer barras que permitan acceder a los distintos aspectos de editable 
* 		(proporcionando acceso a los items vecinos)
* 	- Proveer informacion sobre el editable a cualquier consumidor
* 		Esto es necesario cuando: las caracteristicas del elemento cambian la interface,
* 									Se desee proveer acceso a elementos asociados, etc.
*/
class zona
{
	var $id;					//ID de la zona
	var $proyecto;				//Proyecto de la zona
	var $solicitud;				//Solicitud en la que esta cargada
	var $items_vecinos; 		//Array de ITEMs que viven en la ZONA
	var $editable_id;			//ID del editable cargado
	var $editable_info;			//Propiedades del EDITABLE
	var $editable_cargado;		//Hay un editable cargado?
	var $editable_propagado;	//ID recibido por el canal de propagacion de la ZONA
	var $listado;
	
	function zona($id,$proyecto,&$solicitud)
	{
		$this->id = $id;
		$this->proyecto = $proyecto;
		$this->solicitud =& $solicitud;

		//Creo la lista de los VECINOS de la ZONA
		//Por el mismo motivo que la solicitud, el control de acceso a ITEMs no lo puedo
		//Hacer respecto del proyecto actual, sino desde el usuario (los items pueden ser
		//de otro proyecto)
		$sql = "SELECT	i.proyecto as 					item_proyecto,
						i.item as						item,
						i.zona_orden as					orden,
						i.imagen as						imagen,
						i.imagen_recurso_origen as		imagen_origen,
						i.nombre as						nombre,
						i.descripcion as				descripcion
				FROM	apex_item i,
						apex_usuario_grupo_acc_item ui,
						apex_usuario_proyecto up
				WHERE	i.zona = '$id'
				AND		i.zona_proyecto = '$proyecto'
				AND 	ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		ui.usuario_grupo_acc = up.usuario_grupo_acc
                AND     ui.proyecto = up.proyecto
                AND     up.usuario = '".toba::get_hilo()->obtener_usuario()."'
				AND		i.zona_listar = 1
				ORDER BY 3";;
		$this->items_vecinos = toba::get_db('instancia')->consultar($sql);
		$this->editable_cargado = false;
		//Se propago algo por el canal utilizado por la zona?
		$this->editable_propagado = toba::get_hilo()->obtener_parametro(apex_hilo_qs_zona);
		if ( isset($this->editable_propagado) ){
			$this->editable_propagado = explode(apex_qs_separador,$this->editable_propagado);
			$this->cargar_editable();
		}
	}

	function cargar_editable($editable=null)
	//Esta funcion debe ser reescrita por los hijos
	//La finalidad es cargar el ARRAY 'editable_info'
	{
	}

	/**
	 * Descarga el editable que contiene actualmente la zona
	 */
	function resetear()
	{
		unset($this->editable_id);
		unset($this->editable_info);
		$this->editable_cargado = false;
		unset($this->editable_propagado);
	}
	
	function info(){
		$dump["id"]=$this->id;
		$dump["id_proyecto"]=$this->proyecto;
		$dump["items_vecinos"]= $this->items_vecinos;
		$dump["editable_id"]= $this->editable_id;
		$dump["editable_info"]= $this->editable_info;
		$dump["editable_cargado"]= $this->editable_cargado;
		$dump["editable_propagado"]= $this->editable_propagado;
		ei_arbol($dump,"ZONA");
	}
	
	function controlar_carga()
	//Se cargo un EDITABLE en la ZONA? Me fijo si hay un ID disponible
	{
		return $this->editable_cargado;
	}

	function obtener_editable_propagado()
	//Estoy en una secuencia de propagacion de zona
	{
		return $this->editable_propagado;
	}

	function obtener_editable_cargado()
	//Estoy en una secuencia de propagacion de zona
	{
		return $this->editable_id;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------   INTERFACE GRAFICA   ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html_barra_superior()
	//Genera el HTML de la BARRA
	{
		//global $cronometro;
		//$cronometro->marcar('basura',apex_nivel_nucleo);
		echo "<table width='100%' class='tabla-0'><tr>";
		//INTERFACE que solicta CRONOMETRAR la PAGINA
		if(toba::get_vinculador()->consultar_vinculo("toba","/basicos/cronometro",true))
		{
			echo "<td  class='barra-item-id' width='1'>";
			echo "<a href='".toba::get_vinculador()->generar_solicitud(null,null,null,true,true)
					."'>".recurso::imagen_apl("cronometro.gif",true,null,null,"Cronometrar la ejecución del ITEM")."</a>";
			echo "</td>";
		}
		$this->obtener_html_barra_info();
		$this->obtener_html_barra_vinculos();
		$this->obtener_html_barra_especifico();
		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		echo "</tr></table>\n";
		//$cronometro->marcar('ZONA: Barra SUPERIOR',apex_nivel_nucleo);
	}
//-----------------------------------------------------

	function obtener_html_barra_info()
	//Muestra la seccion INFORMATIVA (izquierda) de la barra
	{
		echo "	<td width='250' class='barra-item-id'>";
//		echo "&nbsp;".$this->editable_id[0]."&nbsp;";
		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
//		echo "&nbsp;".$this->editable_id[1]."@".$this->editable_id[0]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['nombre']."</td>";
	}
//-----------------------------------------------------

	function obtener_html_barra_vinculos()
	//Genera el html de la seccion de ITEMs VECINOS en la barra
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
//-----------------------------------------------------
	
	function obtener_html_barra_especifico()
	//Esto es especifico de cada EDITABLE
	{	
	}
//-----------------------------------------------------
	
	function obtener_html_barra_inferior()
	//Esto es especifico de cada EDITABLE
	{
		return null;
	}
//-----------------------------------------------------

	function refrescar_listado_editable_apex()
	//Esta funcion refresca el LISTADO de la izquierda cuando se modifico
	//el estado de existencia de un EDITABLE y esta tiene que impactar allado
	{
		echo "<script language'javascript'>";
		echo "if(parent.".apex_frame_lista.".editor == '".$this->listado."') parent.".apex_frame_lista.".location.reload()";
		echo "</script>";
	}
//-----------------------------------------------------
}
?>