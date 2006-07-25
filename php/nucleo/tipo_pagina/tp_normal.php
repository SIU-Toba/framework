<?php
require_once("tp_basico_titulo.php");
require_once("nucleo/lib/interface/form.php");

class tp_normal extends tp_basico_titulo
{
	protected $menu;
	protected $alto_cabecera = "34px";

	function __construct()
	{
		$archivo_menu = info_proyecto::instancia()->get_parametro('menu_archivo');
		require_once($archivo_menu);
		$clase = basename($archivo_menu, ".php");
		$this->menu = new $clase();
	}
	
	protected function comienzo_cuerpo()
	{
		parent::comienzo_cuerpo();
		$this->menu();					
		$this->cabecera_aplicacion();			
	}

	protected function menu()
	{
		if (isset($this->menu)) {
			$this->menu->mostrar();
		}		
	}

	protected function plantillas_css()
	{
		if (isset($this->menu)) {
			$estilo = $this->menu->plantilla_css();
			if ($estilo != '') {
				echo recurso::link_css($estilo, "screen");
			}
		}
		parent::plantillas_css();
	}
	
	protected function cabecera_aplicacion()
	{
		if (apex_pa_proyecto=="multi") {
			echo form::abrir("multiproyecto",toba::get_hilo()->cambiar_proyecto(),"target = '_top'");
		}	
		?>
		<table width='100%' style='height: <?=$this->alto_cabecera?>' class='menu-barra'><tr>
		<td ><?=$this->mostrar_logo()?></td>
		<td  width='100%'>&nbsp;</td>
		<td>
			<table width='50' cellpadding='3' cellspacing='0' border='0'>
			<tr>
			<td>
		<?
				if(apex_pa_proyecto=="multi") {		
					echo "<td>";
					echo recurso::imagen_apl("proyecto.gif",true);
					echo "</td>";
					//Si estoy en modo MULTIPROYECTO muestro un combo para cambiar a otro proyecto,
					//sino muestro el nombre del proyecto ACTUAL
					echo "<td>";
					$datos = info_instancia::get_lista_proyectos_instancia(toba::get_hilo()->obtener_usuario());
					$datos = rs_convertir_asociativo($datos, array(0), 1);
					echo form::select(apex_sesion_post_proyecto, toba::get_hilo()->obtener_proyecto(), $datos,
								'ef-combo', "onchange='multiproyecto.submit();'");
					echo "</td>";
					echo "<td>";
					echo form::image('cambiar',recurso::imagen_apl('cambiar_proyecto.gif'));
					echo "</td>";
				}
		?>
			</td>
			<td class='menu-0'><?=$this->info_usuario()?></td>
		<? if ( editor::modo_prueba() ) { ?>
			<td><a href="#" onclick='javascript:window.close()'><img src='<? echo recurso::imagen_apl('finalizar_sesion.gif') ?>' border='0'></a></td>
		<? } else { ?>
			<td><a href="#" onclick='javascript:salir()'><img src='<? echo recurso::imagen_apl('finalizar_sesion.gif') ?>' border='0'></a></td>
		<? } ?>
			</tr>
			</table>
		</td>
		</tr></table>	
		<?php
		if(apex_pa_proyecto=="multi") {		
			echo form::cerrar();
		}		
	}

	protected function mostrar_logo()
	{
		echo recurso::imagen_pro('logo.gif', true);
	}
	
	protected function info_usuario()
	{
		echo "<div style='white-space: nowrap'>";
		echo "<strong>".toba::get_usuario()->get_nombre()."</strong></div>";
		echo toba::get_usuario()->get_id();
	}		
	
	function pie()
	{
		parent::pie();	
	}
}
?>