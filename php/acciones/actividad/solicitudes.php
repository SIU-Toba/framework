<?
	function cronometro($fila, $posicion)
	{
        if($fila[6]>0){
    		global $solicitud;
	    	//Le tengo que pasar como parametro al editor la dimension en juego y el ID del perfil de datos
            $contexto = $solicitud->contexto["elemento"];
		    $destino = $solicitud->vinculador->generar_url('/admin/items/cronometro',array(apex_hilo_qs_edi_solicitud=>$fila[2], apex_hilo_qs_edi=>$contexto ));
    		return "<a href='$destino'>".recurso::imagen_apl("cronometro.gif",true,null,null,"Ver cronometro")."</a>";
        }
	}

	function tiempo($fila, $posicion)
	{
		return number_format($fila[$posicion],2,',','.') . "&nbsp;seg.";
	}
//---------------------------------------------------------------------

	if($this->contexto['cargado_ok'])
    {    
        $cuadro_param["titulo"]="ACCESOS al ITEM";
    	$cuadro_param["descripcion"]="";
    	$cuadro_param["col_titulos"]="#,Usuario,Momento,Tiempo,IP,CRON.";
//     	$cuadro_param["col_titulos"]="#,Momento,Tiempo,CRON.";
	   	$cuadro_param["col_formato"]=array(4=>"tiempo",6=>"cronometro");
    	$cuadro_param["col_ver"]=array(2=>"n",1=>"t",3=>"t",4=>"n",5=>"t",6=>"e");
//    	$cuadro_param["col_ver"]=array(0=>"n",1=>"t",2=>"n",3=>"t",4=>"e");
    	$cuadro_param["ancho"]="500";
    	$cuadro_param["ordenar"]=false;
        $cuadro_param["mensaje_error"]="No hay solicitudes!";    
    	$fuente_datos = "apl";
    	$sql = "    
            SELECT  se.sesion_browser,
                    se.usuario, 
                    s.solicitud,
                    s.momento,
                    s.tiempo_respuesta,
                    sob.ip,
                    COUNT(sc.solicitud)
            FROM    apex_solicitud_browser sob,
                    apex_sesion_browser se,
                    apex_solicitud s
                    LEFT OUTER JOIN apex_solicitud_cronometro sc 
                    ON sc.solicitud = s.solicitud
            WHERE   s.solicitud = sob.solicitud_browser
            AND     sob.sesion_browser = se.sesion_browser
            AND     s.item = '" . $this->contexto["elemento"] . "'
            GROUP BY 1,2,3,4,5,6
            ORDER BY 1,2;";
/*
    	$sql = "    
            SELECT  s.solicitud,
                    s.momento,
                    s.tiempo_repuesta,
                    COUNT(sc.solicitud)
            FROM    apex_solicitud s
                    LEFT OUTER JOIN apex_solicitud_cronometro sc 
                    ON sc.solicitud = s.solicitud
            WHERE   s.solicitud = sob.solicitud_browser
            AND     sob.sesion_browser = se.sesion_browser
            AND     s.item = '" . $this->contexto["elemento"] . "'
            GROUP BY 1,2,3,4,5,6
            ORDER BY 1,2;";
*/
         //dump_SQL($sql);
    	include_once("nucleo/browser/interface/cuadro.php");
    	$cuadro =& new cuadro_db($cuadro_param,$fuente_datos,$sql);
        echo "<br>";
        $cuadro->generar_html();
        echo "<br>";
    }else{
        echo ei_mensaje("No se especifico el ITEM");
    }
?>