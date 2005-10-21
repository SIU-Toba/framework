<?php
require_once("tp_basico.php");

class tp_normal extends tp_basico
{
	function encabezado()
	{
		$this->cabecera_html();
		$this->comienzo_cuerpo();
		$this->barra_superior();
	}
	
	function pie()
	{
		echo "</BODY>\n";
		echo "</HTML>\n";
	}	
	
	protected function cabecera_html()
	{
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		echo "<HTML>\n";
		echo "<HEAD>\n";
		echo "<title>".$this->titulo_pagina()."</title>\n";
		$this->plantillas_css();
		$this->estilos_css();
		$this->js_basico();
		echo "</HEAD>\n";
	}
	
	protected function titulo_pagina()
	{
		$item = toba::get_solicitud()->get_datos_item();
		return $item['item_nombre'];
	}

	protected function plantillas_css()
	{
		echo recurso::link_css(apex_proyecto_estilo, "screen");
		echo recurso::link_css(apex_proyecto_estilo."_impr", "print");		
	}
	
	protected function estilos_css()
	{
		global $color_serie;		
		?>
		<style type="text/css">
			#dhtmltooltip{
				position: absolute;
				width: 130px;
				border: 1px solid <? echo $color_serie["p"][1] ?>;
				padding: 2px;
				background-color: <? echo $color_serie["s"][6] ?>;
				visibility: hidden;
				z-index: 1;
				font-size: 10px;
				color: <? echo $color_serie["p"][1] ?>;
			}
		</style>
		<?php
	}
	
	protected function js_basico()
	{
		//Incluyo el javascript STANDART	
		$consumos = array();
		$consumos[] = 'basico';
		$consumos[] = 'cola_mensajes';
		$consumos[] = 'clases/toba';
		$consumos[] = 'utilidades/datadumper';
		$consumos[] = 'comunicacion_server';
		js::cargar_consumos_globales($consumos);
	}


	protected function comienzo_cuerpo()
	{
		echo "<body onLoad='firstFocus()'>\n";
		js::cargar_consumos_globales(array('tooltips'));		
		$this->menu();
	}
	
	protected function menu()
	{
		if (defined("apex_pa_menu_archivo")) {
			require_once(apex_pa_menu_archivo);
			$clase = basename(apex_pa_menu_archivo, ".php");;
			$menu = new $clase();
			$menu->mostrar();
		} elseif(defined('apex_pa_menu') && apex_pa_menu == "milonic") {
			//--- Migracion 0.8.3 ----
			//Cargo el menu milonic, si el punto de acceso lo solicita
			toba::get_logger()->obsoleto("", "", "0.8.3", "El menú debe ser una propiedad del proyecto");
			require_once("nucleo/browser/includes/menu_inferior.php");
			//--------------------------------
		}
	}
		
	
	protected function barra_superior()
	{
		echo "<table width='100%' class='tabla-0'><tr>";
		foreach ($this->vinculos_izquierda() as $vinculo) {
			if ($vinculo != '') {
				echo "<td  class='barra-0-edit' width='1'>$vinculo</td>";
			}
		}
		echo "\n\n";
		echo "<td width='1' class='barra-0'>". gif_nulo(8,22) . "</td>";
		echo "<td width='99%' class='barra-0-tit'>".$this->titulo_pagina()."&nbsp;&nbsp;</td>";

		$info = toba::get_solicitud()->get_datos_item();			
		if(toba::get_solicitud()->cronometrar){
			$parametros = array("solicitud"=> toba::get_solicitud()->id() );
			echo "<td  class='barra-0-tit' width='1'>&nbsp;";
			echo toba::get_vinculador()->obtener_vinculo_a_item("toba","/basicos/cronometro",$parametros,true);
			echo "&nbsp;</td>";
		}
		
		if(toba::get_solicitud()->existe_ayuda()){
			$parametros = array("item"=>$info["item"],
								"proyecto"=>$info["item_proyecto"]);
			echo "<td  class='barra-0-tit' width='1'>&nbsp;";
			echo toba::get_vinculador()->obtener_vinculo_a_item("toba","/basicos/ayuda",$parametros,true);
			echo "&nbsp;</td>";
		}
		echo "</tr></table>\n\n";
	}
	
	protected function vinculos_izquierda()
	{
		$vinculador = toba::get_vinculador();
		$info = toba::get_solicitud()->get_datos_item();
		$vinculos = array();
		
		//Etitor Item
		$parametros = array(apex_hilo_qs_zona=> $info["item_proyecto"] . apex_qs_separador . $info["item"]);
		$vinculos[] = $vinculador->obtener_vinculo_a_item_cp("toba","/admin/items/propiedades",$parametros,true);

		//Catalogo Unificado
		$parametros = array("proyecto"=>$info["item_proyecto"],"item"=>$info["item"]);
		$vinculos[] = $vinculador->obtener_vinculo_a_item_cp("toba","/admin/items/catalogo_unificado",$parametros,true, false, false, "", null, null, 'lateral');
		
		//Ayuda del item
		$parametros = array(apex_hilo_qs_zona=> $info["item_proyecto"] . apex_qs_separador . $info["item"]);
		$vinculos[] = $vinculador->obtener_vinculo_a_item_cp("toba","/admin/items/info",$parametros,true);

		//Editor de estilos CSS
		if ($vinculador->consultar_vinculo("toba",'/admin/objetos/editores/editor_estilos', true)) {
			$parametros = array('plantilla' => recurso::css(apex_pa_estilo));
			$vinculos[] = $vinculador->obtener_vinculo_a_item_cp("toba",'/admin/objetos/editores/editor_estilos',$parametros,true);
		}		
		
		//Consola JS
		if ($vinculador->consultar_vinculo("toba",'/admin/objetos/consola_js', true)) {		
			//-- Link a la consola JS
			$parametros = array();
			$vinculos[] = $vinculador->obtener_vinculo_a_item_cp("toba",'/admin/objetos/consola_js',$parametros,true);
		}			
		
		//Boton que dispara la cronometracion
		$zona = toba::get_solicitud()->zona();
		if( !isset($zona)){
		//SI existe una zona que todavia no se cargo, el vinculo no va a propagar al EDITABLE
		//En ese caso, el cronometrador tiene que posicionarse sobre la barra de la ZONA
			if($vinculador->consultar_vinculo("toba","/basicos/cronometro",true))
			{
				$vinculos[] = "<a href='".$vinculador->generar_solicitud(null,null,null,true,true)."'>".
								recurso::imagen_apl("cronometro.gif",true,null,null,"Cronometrar la ejecución del ITEM").
								"</a>";
			}
		}	
		return $vinculos;
	}
}


?>
