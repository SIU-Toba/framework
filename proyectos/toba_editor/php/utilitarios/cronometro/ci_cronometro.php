<?php 


//----------------------------------------------------------------------------------	

class ci_cronometro extends toba_ci
{
	protected $s__solicitud;
	protected $s__proyecto;
	
	function ini()
	{

		$solicitud = toba::memoria()->get_parametro("solicitud");
		$proyecto = toba::memoria()->get_parametro("solicitud_proy");
		if ($solicitud) {
			$this->s__solicitud = $solicitud;	
		}
		if ($proyecto) {
			$this->s__proyecto = $proyecto;
		} else {
			$this->s__proyecto = toba_editor::get_proyecto_cargado();			
		}
	}
	
	function get_solicitud()
	{
		return $this->s__solicitud;
	}
	
	function get_proyecto()
	{
		return $this->s__proyecto;	
	}
	
	function evt__borrar()
	{
		$sql = "DELETE FROM apex_solicitud_cronometro";
		toba::instancia()->get_db()->ejecutar($sql);
		$this->s__solicitud = null;
	}
}

//---------------------------------------------------
//---------------------------------------------------
//---------------------------------------------------

class pant_visualizacion extends toba_ei_pantalla
{
	function generar_layout()
	{
		$solicitud = $this->controlador->get_solicitud();
		if (isset($solicitud)) {
			$this->mostrar_solicitud($this->controlador->get_solicitud());		
		} else {
			echo ei_mensaje("No hay solicitudes cronometradas", "info");
		}
	}
	
	function mostrar_solicitud($id_solicitud,$ancho="100%")
	{
		$id_solicitud = addslashes($id_solicitud);
        $sql = "SELECT marca, nivel_ejecucion as nivel, texto, tiempo FROM apex_solicitud_cronometro
				        WHERE solicitud = '$id_solicitud' ORDER BY marca";
        $rs = toba::instancia()->get_db()->consultar($sql);
	    if (empty($rs)) {
	    	throw new toba_error("No se encuentra la solicitud $id_solicitud");
	    }
   	    $this->mostrar_cronometro($id_solicitud, $rs, $ancho);
	}	

    function mostrar_cronometro($id_solicitud, $datos, $ancho="100%")
    {
        //Calculo los porcentuales
        $tiempo_total = 0;
        for($a=0;$a<count($datos);$a++){
            $tiempo_total += $datos[$a]["tiempo"];
        }
        for($a=0;$a<count($datos);$a++){
            $datos[$a]["porcentaje"] =(($datos[$a]["tiempo"] * 100) / $tiempo_total);
        }
        //Genero HTML
        $ancho_grafico = 200;
        $porcentaje_total = 0;
        $barra_mayor = 30;
        $alto_barra = 10;
        $margen = 10;    
		echo "<div>";
		echo "<span class='logger-proyecto'>";
		echo ucfirst($this->controlador->get_proyecto());
		echo "<span class='logger-selec'>Solicitud $id_solicitud</span>";
				//--- Botones anterior/siguiente
		//$this->generar_boton('anterior');			
		//$this->generar_boton('siguiente');
		echo "</span>";
		echo "<span class='cronometro-total'>";
		echo toba_recurso::imagen_toba("reloj.png",true)." $tiempo_total segundos";		
		echo "</span>";
		echo "</div><hr style='clear:both'>";	
   		echo "<table width='$ancho' align='center'>";
		echo"<tr>
			    <td>
				<TABLE width='100%' class='tabla-0'>\n";
		echo"   <TR>\n";
		echo"     <td  class='ei-cuadro-col-tit'>#</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'  width='90%'>Observación</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'>Tiempo</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'>%</td>\n";
		echo"     <td  class='ei-cuadro-col-tit'>&nbsp;</td>\n";
  		echo"  </TR>\n";
        for($a=0;$a<count($datos);$a++){
            $porcentaje = number_format($datos[$a]['porcentaje'],2,',','.');
            if(!(($datos[$a]['texto']=="basura")&&($porcentaje < 1)))
            {
	    		if($datos[$a]['texto']=="basura"){
               		$texto = "NO ETIQUETADO";
                }else{
                	$texto = $datos[$a]['texto'];
                }
           		echo"   <TR>\n";
                if(!($datos[$a]["porcentaje"] > $barra_mayor)){
            		echo"     <td  class='lista-e'>{$datos[$a]['marca']}</td>\n";
        	        echo"     <td  class='lista-t' width='90%'>$texto</td>\n";
    	        	echo"     <td  class='lista-n'>{$datos[$a]['tiempo']}&nbsp;s</td>\n";
    	        	echo"     <td  class='lista-n'>$porcentaje&nbsp;%</td>\n";
                }else{
            		echo"     <td  class='lista-e2'>{$datos[$a]['marca']}</b></td>\n";
	        	    echo"     <td  class='lista-t' width='90%'><b>$texto</b></td>\n";
    	        	echo"     <td  class='lista-n'><b>{$datos[$a]['tiempo']}&nbsp;s</b></td>\n";
    	        	echo"     <td  class='lista-n'><b>$porcentaje&nbsp;%</b></td>\n";
                    $barra_mayor = $porcentaje;
                }
                $ancho_barra = ($porcentaje /100 )* $ancho_grafico;
            	echo"     <td  class='cron-base'>\n";
                if ($porcentaje >= 1.00){
    				echo"  <TABLE class='tabla-0'>\n";
        	    	echo"  <TR>\n";
                    if($datos[$a]['texto']=="basura"){
                		echo"  <td  class='cron-basura'>";
                    }else{
                		echo"  <td  class='cron-{$datos[$a]['nivel']}'>";
                    }
                    echo gif_nulo($ancho_barra,$alto_barra,"NIVEL: " .$datos[$a]['nivel']);
                    echo "</td>\n";
          	    	echo"  </TR>\n";
	            	echo"  </TABLE>\n";
                }
                echo"     </td>\n";
          		echo"  </TR>\n";
            }
            $porcentaje_total += $datos[$a]['porcentaje'];
        }
		echo"   <TR>\n";
		echo"     <td  class='lista-col-titulo'></td>\n";
		echo"     <td  class='lista-col-titulo'>TOTAL</td>\n";
		echo"     <td  class='lista-e'>". number_format($tiempo_total,2,',','.') ."&nbsp;s</td>\n";
		echo"     <td  class='lista-e'>". number_format($porcentaje_total,2,',','.') ."&nbsp;%</td>\n";
      	echo"     <td  class='lista-col-titulo'>\n";
        echo gif_nulo(((($barra_mayor /100 )* $ancho_grafico) + $margen),10);
        echo"     </td>\n";
  		echo"  </TR>\n";
		echo"  </TABLE>\n";
        echo"  </td>\n";
  		echo"  </TR>\n";
		echo"  </TABLE>\n";
    }
	
}

?>