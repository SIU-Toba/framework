<?php 

class pant_visualizacion extends toba_ei_pantalla
{
	function generar_layout()
	{
		$solicitud = $this->controlador->get_solicitud();
		if (isset($solicitud)) {
			$this->mostrar_solicitud($this->controlador->get_solicitud());		
		} else {
			echo ei_mensaje('No hay solicitudes cronometradas', 'info');
		}
	}

	function mostrar_solicitud($id_solicitud, $ancho="100%")
	{
		$schema_logs = toba::instancia()->get_db()->get_schema(). '_logs';
		$id_solicitud = addslashes($id_solicitud);
		$sql = "SELECT marca, nivel_ejecucion as nivel, texto, tiempo FROM $schema_logs.apex_solicitud_cronometro
					WHERE solicitud = ".quote($id_solicitud).' ORDER BY marca';
		$rs = toba::instancia()->get_db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error("No se encuentra la solicitud $id_solicitud");
		}
		$this->mostrar_cronometro($id_solicitud, $rs, $ancho);
	}	

	
	function mostrar_cronometro($id_solicitud, $datos, $ancho='100%')
	{
		$inicial = $datos[0]['tiempo'];
		$final = $datos[count($datos) - 1]['tiempo'];

		$tiempo_total = $final - $inicial;
		$anterior = $inicial;
		for ($a = 0; $a < count($datos); $a++) {
			$lapso = $datos[$a]['tiempo'] - $anterior;
			$datos[$a]['lapso'] = $lapso;
			$datos[$a]['porcentaje'] = (($lapso * 100) / $tiempo_total);
			$anterior = $datos[$a]['tiempo'];
		}
		//Genero HTML
		$ancho_grafico = 200;
		$porcentaje_total = 0;
		$barra_mayor = 30;
		$alto_barra = 10;
		$margen = 10;    
		$escapador = toba::escaper();
		echo '<div>';
		echo "<span class='logger-proyecto'>";
		echo ucfirst($this->controlador->get_proyecto());
		echo "<span class='logger-selec'>Solicitud ". $escapador->escapeHtml($id_solicitud)."</span>";
		//--- Botones anterior/siguiente
		//$this->generar_boton('anterior');			
		//$this->generar_boton('siguiente');
		echo '</span>';
		echo "<span class='cronometro-total'>";
		echo toba_recurso::imagen_toba('reloj.png', true).' '.$this->formato_numero($tiempo_total).' segundos';		
		echo '</span>';
		echo "</div><hr style='clear:both' />";	
		echo "<table width='". $escapador->escapeHtmlAttr($ancho)."' align='center'>";
		echo"<tr>
			<td>
			<TABLE width='100%' class='tabla-0'>\n";
		echo"   <TR>\n";
		echo"     <td  class='ei-cuadro-col-tit'>#</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'  width='90%'>Lapso</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'>Tiempo</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'>%</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'>&nbsp;</td>\n";
		echo"  </TR>\n";
		for ($a = 1; $a < count($datos); $a++) {
			$porcentaje = number_format($datos[$a]['porcentaje'], 2, ',', '.');
			if (!(($datos[$a]['texto'] == 'basura')&&($porcentaje < 1))) {
				if ($datos[$a]['texto'] == 'basura') {
					$texto = 'NO ETIQUETADO';
				} else {
					$texto = $datos[$a - 1]['texto'] . ' - ' . $datos[$a]['texto'];
				}
				echo"   <TR>\n";
				if (!($datos[$a]['porcentaje'] > $barra_mayor)) {
					echo"     <td  class='lista-e'>". $escapador->escapeHtml($datos[$a]['marca'])."</td>\n";
					echo"     <td  class='lista-t' width='90%'>". $escapador->escapeHtml($texto)."</td>\n";
					echo"     <td  class='lista-n'>".$this->formato_numero($datos[$a]['lapso'])."&nbsp;s</td>\n";
					echo"     <td  class='lista-n'>". $escapador->escapeHtml($porcentaje)."&nbsp;%</td>\n";
				} else {
					echo"     <td  class='lista-e2'>". $escapador->escapeHtml($datos[$a]['marca'])."</b></td>\n";
					echo"     <td  class='lista-t' width='90%'><b>". $escapador->escapeHtml($texto)."</b></td>\n";
					echo"     <td  class='lista-n'><b>".$this->formato_numero($datos[$a]['lapso'])."&nbsp;s</b></td>\n";
					echo"     <td  class='lista-n'><b>". $escapador->escapeHtml($porcentaje)."&nbsp;%</b></td>\n";
					$barra_mayor = $porcentaje;
				}
				$ancho_barra = ($porcentaje / 100 ) * $ancho_grafico;
				echo "     <td  class='cron-base'>\n";
				if ($porcentaje >= 1.00) {
					echo"  <TABLE class='tabla-0'>\n";
					echo"  <TR>\n";
					if ($datos[$a]['texto'] == 'basura') {
						echo "  <td  class='cron-basura'>";
					} else {
						echo "  <td  class='". $escapador->escapeHtmlAttr('cron-'.$datos[$a]['nivel'])."'>";
					}
					echo gif_nulo($ancho_barra, $alto_barra, 'NIVEL: ' .$datos[$a]['nivel']);
					echo "</td>\n";
					echo "  </TR>\n";
					echo "  </TABLE>\n";
				}
				echo "     </td>\n";
				echo "  </TR>\n";
			}
			$porcentaje_total += $datos[$a]['porcentaje'];
		}
		echo "   <TR>\n";
		echo "     <td  class='lista-col-titulo'></td>\n";
		echo "     <td  class='lista-col-titulo'>TOTAL</td>\n";
		echo "     <td  class='lista-e'>". $this->formato_numero($tiempo_total) ."&nbsp;s</td>\n";
		echo "     <td  class='lista-e'>". $this->formato_numero($porcentaje_total) ."&nbsp;%</td>\n";
		echo "     <td  class='lista-col-titulo'>\n";
		echo gif_nulo(((($barra_mayor / 100 ) * $ancho_grafico) + $margen), 10);
		echo "     </td>\n";
		echo "  </TR>\n";
		echo "  </TABLE>\n";
		echo "  </td>\n";
		echo "  </TR>\n";
		echo "  </TABLE>\n";
	}
    
	function formato_numero($numero)
	{
		return number_format($numero, 3, ',', '.');
	}

}

?>