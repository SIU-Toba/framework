<?php
class toba_ei_cuadro_salida_impresion_html extends toba_ei_cuadro_salida_html
{

	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	//																	METODOS GENERALES
	//--------------------------------------------------------------------------------------------------------------------------------------------------//

	/**
	* @ignore
	*/
	function impresion_html_inicio()
	{
		$info = $this->_cuadro->get_informacion_basica_cuadro();
		$cant_columnas = $this->_cuadro->get_cantidad_columnas_total();


		$ancho = isset($info["ancho"]) ? $info["ancho"] : "";
		echo "<TABLE width='$ancho' class='ei-base ei-cuadro-base'>";
		// Cabecera
		echo"<tr><td class='ei-cuadro-cabecera' colspan='$cant_columnas'>";
		$this->impresion_html_cabecera();
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td class='ei-cuadro-cc-fondo' colspan='$cant_columnas'>\n";
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if( $this->_cuadro->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
	}

	/**
     * @ignore
     */
	protected function impresion_html_cabecera()
	{
		$info = $this->_cuadro->get_informacion_basica_cuadro();
		if(trim($info["subtitulo"])<>""){
			echo $info["subtitulo"];
		}
	}

	/**
	 * @ignore
	 */
	function impresion_html_fin()
	{
		$acumulador = $this->_cuadro->get_acumulador_general();
		if (isset($acumulador)) {
			$this->html_cuadro_totales_columnas($acumulador);
		}
		$this->html_acumulador_usuario();
		$this->html_cuadro_fin();
		echo "</td></tr>\n";
		//--- FIN CONTENIDO  ---------
		// Pie
		echo"<tr><td class='ei-cuadro-pie'>";
		$this->html_pie();
		echo "</td></tr>\n";
		echo "</TABLE>\n";
	}

	/**
	 * @ignore
	 */
	function impresion_html_cuadro(&$filas, &$totales)
	{
		$this->html_cuadro( $filas );
	}

    /**
     * @ignore
     */
	function impresion_html_mensaje_cuadro_vacio($texto)
	{
		$this->html_mensaje_cuadro_vacio($texto);
	}

	protected function html_cuadro_cabecera_columna_evento($rowspan, $pre_columnas)		
	{
		//Redefine para anular comportamiento
	}

	function generar_layout_fila($columnas, $datos, $id_fila,  $clave_fila, $evt_multiples, $objeto_js, $estilo_fila, $formateo)
	{
			$esta_seleccionada = $this->_cuadro->es_clave_fila_seleccionada($clave_fila);
			$estilo_seleccion = ($esta_seleccionada) ? "ei-cuadro-fila-sel" : "ei-cuadro-fila";

			 //---> Creo las CELDAS de una FILA <----
            echo "<tr class='$estilo_fila' >\n";
 			foreach (array_keys($columnas) as $a) {
                //*** 1) Recupero el VALOR
				$valor = "";
                if(isset($columnas[$a]["clave"])) {
					if(isset($datos[$id_fila][$columnas[$a]["clave"]])) {
						$valor_real = $datos[$id_fila][$columnas[$a]["clave"]];
						//-- Hace el saneamiento para evitar inyección XSS
						if (!isset($columnas[$a]['permitir_html']) || $columnas[$a]['permitir_html'] == 0) {
							  $valor_real = texto_plano($valor_real);
						}
					}else{
						$valor_real = null;
						//ATENCION!! hay una columna que no esta disponible!
					}
	                //Hay que formatear?
	                if(isset($columnas[$a]["formateo"])) {
	                    $funcion = "formato_" . $columnas[$a]["formateo"];
	                    //Formateo el valor
	                    $valor = $formateo->$funcion($valor_real);
	                } else {
	                	$valor = $valor_real;
	                }
	            }

                //*** 2) Genero el HTML
				$ancho = "";
            	if(isset($columnas[$a]["ancho"])) {
	                $ancho = " width='". $columnas[$a]["ancho"] . "'";
	            }

	          //Emito el valor de la celda
                echo "<td class='$estilo_seleccion ".$columnas[$a]["estilo"]."' $ancho>\n";
                if (trim($valor) !== '') {
                	echo $valor;
                } else {
                	echo '&nbsp;';
                }
                echo "</td>\n";
                //Termino la CELDA
            }
            echo "</tr>\n";
	}

	//-------------------------------------------------------------------------------
	//-- Generacion de los CORTES de CONTROL
	//-------------------------------------------------------------------------------
	 /**
     * @ignore
     */
	function impresion_html_cabecera_corte_control(&$nodo )
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'html_cabecera_cc_contenido';
		$metodo_redeclarado = $metodo . '__' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}
		$nivel_css = $this->get_nivel_css($nodo['profundidad']);
		$class = "ei-cuadro-cc-tit-nivel-$nivel_css";
		if($this->_cuadro->get_cortes_modo() == apex_cuadro_cc_tabular) {
				$total_columnas = $this->_cuadro->get_cantidad_columnas_total();
				if ($this->_cuadro->debe_colapsar_cortes()) {
					echo "<table width='100%' class='tabla-0' border='0'><tr><td width='100%' class='$class ei-cuadro-cc-colapsable'>";
				} else {
					echo "<tr><td  colspan='$total_columnas' class='$class'>\n";
				}
				$this->$metodo($nodo);
				if ($this->_cuadro->debe_colapsar_cortes()) {
					echo "</td><td class='$class ei-cuadro-cc-colapsable impresion-ocultable'>&nbsp;</td></tr></table>";
				}else {
					echo "</td></tr>\n";
				}
		}else{
				echo "<li class='$class'>\n";
				$this->$metodo($nodo);
		}
	}

    /**
     * @ignore
     */
	function impresion_html_pie_corte_control( &$nodo , $es_ultimo)
	{
		$this->html_pie_corte_control($nodo, $es_ultimo);
	}

	function impresion_html_cc_inicio_nivel()
	{
	}

    function impresion_html_cc_fin_nivel()
	{
	}

	function impresion_html_inicio_zona_colapsable($id_unico, $estilo)
	{
			echo "<table class='tabla-0' id='$id_unico' width='100%' border='1' ><tr><td>\n";
	}

	function impresion_html_fin_zona_colapsable()
	{
			echo "</td></tr></table>\n";
	}
	
}
?>