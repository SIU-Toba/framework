<?php
require_once("nucleo/lib/usuario_toba.php");

class marco_aplicacion
{
	protected $alto_cabecera = 34;
	protected $con_borde = true;
	
	function frameset()
	{
		$vinculo_contenido = explode(apex_qs_separador,apex_pa_item_inicial_contenido);
		$con_borde = $this->con_borde ? "YES" : "NO";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?=$this->titulo() ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<frameset rows="<?=$this->alto_cabecera ?>,*,0" cols="*" framespacing="0" border="0" frameborder="<?=$con_borde ?>">
  <frame src="<? echo toba::get_vinculador()->generar_solicitud("toba","/basicos/cabecera")?>" name="cabecera" noresize scrolling="no">
  <frame src="<? echo toba::get_vinculador()->generar_solicitud($vinculo_contenido[0],$vinculo_contenido[1]) ?>" name="contenido"  scrolling="auto">
  <frame src="<? echo toba::get_vinculador()->generar_solicitud("toba","/basicos/com_js")?>" name="<? echo  apex_frame_com ?>" scrolling="NO">
</frameset>
<noframes><body>
</body></noframes>
</html>
<?php
	}
	
	protected function titulo()
	{
		return toba::get_hilo()->obtener_proyecto_descripcion();		
	}
	
	function cabecera()
	{
		if (defined("apex_pa_menu_archivo")) {
			require_once(apex_pa_menu_archivo);
			$clase = basename(apex_pa_menu_archivo, ".php");;
			$menu = new $clase();
			$menu->mostrar_frame_sup();
		} else {
			throw new excepcion_toba("Se requiere que se especifique un tipo de menu en las propiedades basicas");
		}
		
		?>
		<table width='100%' class='tabla-0'><tr>
		<td class='menu-0'><?=$this->mostrar_logo()?></td>
		<td class='menu-0'  width='100%'>&nbsp;</td>
		<td class='menu-1'>
			<table width='50' cellpadding='3' cellspacing='0' border='0'>
			<tr>
			<td class='menu-0'>
		<?
				if(apex_pa_proyecto=="multi")
				{
					echo "<td class='listado-barra-superior-tabi'>";
					echo recurso::imagen_apl("proyecto.gif",true);
					echo "</td>";
					include_once("nucleo/browser/interface/ef.php");
					//Si estoy en modo MULTIPROYECTO muestro un combo para cambiar a otro proyecto,
					//sino muestro el nombre del proyecto ACTUAL
					echo form::abrir("multiproyecto",toba::get_hilo()->cambiar_proyecto(),"target = '_top'");
					echo "<td class='listado-barra-superior-tabi2'>";
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
					echo "<td class='listado-barra-superior-tabi'>";
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
		<script language='javascript'>
		load_ok=1;
		</script>
		<?php		
	}

	function mostrar_logo()
	{
		echo recurso::imagen_pro('logo.gif', true);
	}
	
	function info_usuario()
	{
		$usuario = new usuario_toba(toba::get_hilo()->obtener_usuario());
		echo "<div style='white-space: nowrap'>";
		echo "<strong>".$usuario->nombre()."</strong></div>";
		echo $usuario->id();
	}
}

?>