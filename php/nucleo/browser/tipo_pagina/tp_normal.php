<?php
require_once("tp_basico.php");

class tp_normal extends tp_basico
{

	/**
	 * @todo Sacar este hack cuando el administrador sea un proyecto
	 */
	protected function comienzo_cuerpo()
	{
		parent::comienzo_cuerpo();
		$item = toba::get_hilo()->obtener_item_solicitado();
		
		//ATENCION: Hack para que no se muestre menu en el admin
		$es_admin = defined('apex_pa_item_inicial') && (apex_pa_item_inicial=="toba||/admin/acceso");
		if (!$es_admin) {
			$this->menu();					
		}
		if (!$es_admin && ! toba::get_hilo()->obtener_proyecto_datos('con_frames')) {
			$this->cabecera_aplicacion();			
		}		
	}
	
	protected function cabecera_aplicacion()
	{
		?>
		<table width='100%' height="<?=$this->alto_cabecera?>"class='tabla-0'><tr>
		<td class='menu-0'><?=$this->mostrar_logo()?></td>
		<td class='menu-0'  width='100%'>&nbsp;</td>
		<td class='menu-1'>
			<table width='50' cellpadding='3' cellspacing='0' border='0'>
			<tr>
			<td class='menu-0'>
		<?
				if(apex_pa_proyecto=="multi")
				{
					echo "<td>";
					echo recurso::imagen_apl("proyecto.gif",true);
					echo "</td>";
					include_once("nucleo/browser/interface/ef.php");
					//Si estoy en modo MULTIPROYECTO muestro un combo para cambiar a otro proyecto,
					//sino muestro el nombre del proyecto ACTUAL
					echo form::abrir("multiproyecto",toba::get_hilo()->cambiar_proyecto(),"target = '_top'");
					echo "<td>";
					$parametros["sql"] = "SELECT 	p.proyecto, 
			                						p.descripcion_corta
			                				FROM 	apex_proyecto p,
			                						apex_usuario_proyecto up
			                				WHERE 	p.proyecto = up.proyecto
											AND  	listar_multiproyecto = 1 
											AND		up.usuario = '".toba::get_hilo()->obtener_usuario()."'
											ORDER BY orden;";
					$proy =& new ef_combo_db(null,"",apex_sesion_post_proyecto,apex_sesion_post_proyecto,
			                                "Seleccione el proyecto en el que desea ingresar.","","",$parametros);
					$proy->cargar_estado(toba::get_hilo()->obtener_proyecto());//Que el elemento seteado
					echo $proy->obtener_input(" onchange='multiproyecto.submit();'");
					echo "</td>";
					echo "<td>";
			        echo form::image('cambiar',recurso::imagen_apl('cambiar_proyecto.gif'));
			        echo "</td>";
					echo form::cerrar();
				}
		?>
			</td>
			<td class='menu-0'><?=$this->info_usuario()?></td>
			<td class='menu-0'><a href="#" onclick='javascript:salir()'><img src='<? echo recurso::imagen_apl('finalizar_sesion.gif') ?>' border='0'></a></td>
			</tr>
			</table>
		</td>
		</tr></table>	
		<?php
	}

	protected function mostrar_logo()
	{
		echo recurso::imagen_pro('logo.gif', true);
	}
	
	protected function info_usuario()
	{
		$usuario = new usuario_toba(toba::get_hilo()->obtener_usuario());
		echo "<div style='white-space: nowrap'>";
		echo "<strong>".$usuario->nombre()."</strong></div>";
		echo $usuario->id();
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
		$info = toba::get_solicitud()->get_datos_item();			
	
		echo "<td width='99%' class='barra-0-tit'>".$this->titulo_pagina()."&nbsp;&nbsp;</td>";

		if(toba::get_solicitud()->cronometrar){
			$parametros = array("solicitud"=> toba::get_solicitud()->id() );
			echo "<td  class='barra-0-tit' width='1'>&nbsp;";
			echo toba::get_vinculador()->obtener_vinculo_a_item("toba","/basicos/cronometro",$parametros,true);
			echo "&nbsp;</td>";
		}
		
		if (trim($info['item_descripcion']) != '') {
			echo "<td  class='barra-0-tit' width='1'>";
			echo recurso::imagen_apl("ayuda_grande.gif", true, 22, 22, trim($info['item_descripcion']));
			echo "</td>";
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
		if (apex_pa_acceso_directo_editor) {
			//Etitor Item
			$parametros = array(apex_hilo_qs_zona=> $info["item_proyecto"] . apex_qs_separador . $info["item"]);
			$vinculos[] = $vinculador->obtener_vinculo_a_item_cp("toba","/admin/items/editor_items",$parametros,true);
	
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
		}
		return $vinculos;
	}
}


?>
