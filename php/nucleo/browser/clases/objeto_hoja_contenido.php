<?php
require_once("nucleo/browser/interface/formateo.php");
//Estos son los tipos de columna existentes
define("corte_id",1);
define("corte_des",2);
define("refcruz_id",3);
define("refcruz_des",4);
define("dato_id",5);
define("dato_des",6);
define("dato_valor",7);

class objeto_hoja_contenido {
/*
* Esta clase recibe un Array de datos y una clasificacion de las columnas del mismo
* para procesar el array para obtener diferentes visualizaciones del mismo, principalmente:
* 
*  	- Cortes de control (CdC)
* 	- Referencias Cruzadas (RC)
*/
	var $hoja;						//A lo HOJA que lo contiene
	//Directivas que explicitan como procesar los datos
	var $dir_col;					//Array de Directivas sobre como interpretar cada columna
	var $dir_col_tipo;				//Total que ocurrencias por tipo de campo
	var $dir_col_posicion;			//Indice de la primera ocurrencia de cada tipo
	var $dir_total_x;				//Flag que indica si hay que incluir totales por fila
	var $dir_total_x_formato;		//Formato que hay que aplicar a la columna de totales por fila.
	var $dir_total_y;				//Flag que indica si hay que incluir totales por columna
	var $ancho;
	//Manejo del factor dinamico
	var $php_dinamico;				//Array de sentencias PHP generadas dinamicamente segun las directivas
	//estructuras de control
	var $datos_db;					//Array con los datos que se quieren visualizar
	var $arbol_nucleo;				//Estructura central donde se ubican los datos ya tabulados
	var $arbol_RC;					//Estructura de Referencias Cruzadas
	var $arbol_titulos_CdC;			//titulos de los Cortes de Control
	var $arbol_titulos_RC;			//titulos de las Referencias Cruzadas
	var $arbol_cabecera;			//Estructura de la cabecera
	var $indice_RC;					//Contador usado para generar el indice de Cortes de Control
	var $columnas_cabecera;			//Total de columnas en la cabecera
	var $columnas_total;			//Total general de columnas
	var $fila_vacia;				//Fila en blanco sobre la que luego se posicionan datos
	var $formato_columnas;			//Arbol que posee las definiciones de como formatear cada columna
	//SALIDA
	var $html_cabecera;				//HTML de la cabecera
	var $ordenable;
	//Graficos
	var $graf_columnas;
	var $graf_filas;
	var $graf_gen_tipo;
	var $graf_gen_invertir;
	var $graf_gen_invertible;
	var $graf_gen_ancho;
	var $graf_gen_alto;
	var $graf_categ_x;
	var $graf_datos_fc;				//Array donde se guarda la informacion serializada, para utilizar en graficos F-C
	var $graf_datos_fc_indice; 		//indice de Array js con datos de las tablas para analisis FC
	//Navegacion
	var $navegar;
	var $xls;

	function objeto_hoja_contenido($datos_db, $definicion, $directivas, $navegar, &$hoja)
	{
		$this->hoja =& $hoja;
		$this->datos_db = $datos_db;				
		$this->dir_col = $directivas;				
		$this->dir_total_y = $definicion["total_y"];
		$this->dir_total_x = $definicion["total_x"];				
		$this->dir_total_x_formato = $definicion["total_x_formato"];
		$this->ordenable = $definicion["ordenable"];
		$this->ancho = $definicion["ancho"];
		//-- Seteo los GRAFICOS
		$this->graf_columnas = $definicion["graf_columnas"];
		$this->graf_filas = $definicion["graf_filas"];
		$this->graf_gen_tipo = trim($definicion["grafico"]);
		$this->graf_gen_invertir = $definicion["graf_gen_invertir"];
		$this->graf_gen_invertible = $definicion["graf_gen_invertible"];
		$this->graf_gen_ancho = $definicion["graf_gen_ancho"];
		$this->graf_gen_alto = $definicion["graf_gen_alto"];
		$this->graf_datos_fc_indice = 0;
		//Navegacion
		$this->navegar = $navegar;
		$this->xls = false;
		//Disparo el procesamiento comun a todos los consumidores
		$this->analizar_directivas();				//Obtengo informacion basica sobre las directivas
		$this->planificar_estructuras_control();	//Genero PHP dinamico
		$this->crear_estructuras_control();			//Lo uso para crear las estructuras de control
		//global $cronometro;															//-------------------> *BENCHMARK
		//$cronometro->setMarker('CONTENIDO (constr): generar estructuras de control');//-------------------> *BENCHMARK
	}

	//################################################################################################
	//#################################  Generacion del Contenido ####################################
	//################################################################################################
		
	function analizar_directivas()
	//Obtengo informacion sobre las directivas recibidas
	{
		//Averiguo la cantidad de directivas por tipo
		$this->dir_col_tipo[corte_id]=0;
		$this->dir_col_tipo[corte_des]=0;
		$this->dir_col_tipo[refcruz_id]=0;
		$this->dir_col_tipo[refcruz_des]=0;
		$this->dir_col_tipo[dato_id]=0;
		$this->dir_col_tipo[dato_des]=0;
		$this->dir_col_tipo[dato_valor]=0;
		foreach ($this->dir_col as $tipo){
			$this->dir_col_tipo[$tipo["tipo"]]+=1;
		}
		//Averiguo posicion de la primera ocurrencia de cada tipo de directiva
		if($this->dir_col_tipo[refcruz_id]>0)$this->dir_col_posicion[refcruz_id] = $this->dir_col_tipo[corte_id]*2;
		$this->dir_col_posicion[dato_id] = ($this->dir_col_tipo[corte_id]*2)+($this->dir_col_tipo[refcruz_id]*2);
		$this->dir_col_posicion[dato_des] = $this->dir_col_posicion[dato_id]+1;
		$this->dir_col_posicion[dato_valor] = $this->dir_col_posicion[dato_des]+1;
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function planificar_estructuras_control()
/*
* Genera codigo PHP que crea las estructuras de control utilizadas para crear los CdC y las RC.
* Las sentencias dinamicas se ejecutan luego dentro de iteraciones aplicadas sobre los datos de la base.
* Para crear las estructuras se necesitan dos iteraciones, por esto las sentencias se agrupan en 
* en los array php_dinamico[1] y php_dinamico[2]
*/
	{
		//-------------------------> Planificacion de Referencias Cruzadas <--------------------------
		//Factor dinamico: pueden haber 0 o mas referencias cruzadas
		$php_RC = "";
		for ($a=0;$a<$this->dir_col_tipo[refcruz_id];$a++)
		{
			$posicion = $this->dir_col_posicion[refcruz_id]+($a*2);
			$php_RC .="[\$fila[". $posicion ."]]";//Para arbol_RC
			$this->php_dinamico[1][] = "\$this->arbol_titulos_RC[$a][\$fila[".$posicion."]]=\$fila[".($posicion+1)."];";//Titulos RC
		}	
		// Sentencias para la Iteracion 1.
		$this->php_dinamico[1][] = "\$this->arbol_RC" .$php_RC."=0;";
		// Sentencias para la Iteracion 2. 
		$this->php_dinamico[2][] = "\$columna = \$this->arbol_RC". $php_RC .";";
		
		//-----------------------> Planificacion de CdC / estructura central <-------------------------
		//Factores dinamicos: 0,1 o 2 Cortes de control; Totales x,y opcionales...
		//Sentencias para la iteracion 1. Creo la estructuta y los titulos
		$php_CdC = ""; //Para empezar no hay cortes de control.
 		for ($a=0;$a<($this->dir_col_tipo[corte_id]);$a++)
		{
			$php_CdC .="[\$fila[".($a*2)."]]";
			$this->php_dinamico[1][] = "\$this->arbol_titulos_CdC[$a][\$fila[".($a*2)."]]=\$fila[".(($a*2)+1)."];"; //Titulos CdC
		}	
		$apuntar_dato = "\$this->arbol_nucleo" . $php_CdC ."['datos'][\$fila[".$this->dir_col_posicion[dato_id]."]]";
		$apuntar_total_x = "\$this->arbol_nucleo" . $php_CdC ."['total_x'][\$fila[".$this->dir_col_posicion[dato_id]."]]";
		$apuntar_total_y = "\$this->arbol_nucleo" . $php_CdC ."['total_y']";
		$this->php_dinamico[1][] = $apuntar_dato ."=0;"; //Estructura en blanco
		if ($this->dir_total_x) $this->php_dinamico[1][] = $apuntar_total_x . "=0;";
		//Sentencias para la iteracion 2. Lleno la estructura
		for ($a=0;$a<($this->dir_col_tipo[dato_valor]);$a++)
		{
			$this->php_dinamico[2][] = $apuntar_dato . "[\$columna+$a]=\$fila[".($this->dir_col_posicion[dato_valor]+$a)."];";
			if ($this->dir_total_x) $this->php_dinamico[2][] = $apuntar_total_x . "+=\$fila[".($this->dir_col_posicion[dato_valor]+$a)."];";
			if ($this->dir_total_y)	$this->php_dinamico[2][] = $apuntar_total_y . "[\$columna+$a]+=\$fila[".($this->dir_col_posicion[dato_valor]+$a)."];";	//Esto salta como ERROR NOTICE.
		}					
		$this->php_dinamico[2][] = $apuntar_dato ."[0]=\$fila[".$this->dir_col_posicion[dato_des]."];";
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
	function crear_estructuras_control()
	{
		//Ejecuto la primera serie de PHP dinamico.
		foreach($this->datos_db as $fila){
			for($x=0;$x<count($this->php_dinamico[1]);$x++){
				eval($this->php_dinamico[1][$x]);
			}
		}
		if(is_array($this->arbol_RC)){
			$this->indice_RC = $this->dir_col_tipo[dato_des];	//Primero las descripciones
			$this->arbol_RC = $this->generar_indices_RC($this->arbol_RC);	//Genero el indice de las refenrencias cruzadas
			$this->columnas_total = $this->indice_RC;	//Calculo la cantidad total de columnas
		}else{
			//Si no hay referencias cruzadas...
			$this->arbol_RC = 1; //Para que empieza a colocar datos desde la fila posterior a la descripcion
			$this->columnas_total = $this->dir_col_tipo[dato_valor] + 1;
		}
		for($i=0;$i<$this->columnas_total;$i++)	$this->fila_vacia[$i]=0;//Creo las filas en blanco
		$this->arbol_nucleo = $this->generar_tabla_vacia($this->arbol_nucleo); //Creo las filas de todas las tablas con valores en 0.
		//Lleno las filas con datos
		foreach($this->datos_db as $fila){
			for($x=0;$x<count($this->php_dinamico[2]);$x++){
				//echo "eval : " . $this->php_dinamico[2][$x];
				eval($this->php_dinamico[2][$x]);
			}
		}		
		//Crea la estructura de la cual se deduce el html de la cabecera
		if(is_array($this->arbol_RC)){
			$this->columnas_cabecera = $this->planificar_cabecera($this->arbol_RC);
		}else{
			$this->columnas_cabecera = $this->dir_col_tipo[dato_valor];
		}
		if ($this->dir_total_x){
			$this->columnas_total++; //Hay una columna mas.
		}
		if ($this->graf_filas){
			$this->columnas_total++; //Hay una columna mas.
		}
		if ($this->dir_total_y){ 
		//tengo que corregir al array de totales en Y
		//(Como lo contrui en base a CdC y no a RC puede tener huecos...)
			$this->arbol_nucleo = $this->corregir_array_total_y($this->arbol_nucleo);
		}
		//Creo la estructura que especifica el formato de cada columna
		$this->generar_definicion_formato_columnas();
	}

	//-----------------------------------------------------------------------------------------------
	//--------------  funciones que dan soporte para crear la estructuras de control  ---------------
	//-----------------------------------------------------------------------------------------------
		
	function generar_indices_RC($nivel)
	//Crea el indice de referencias de la estructura $this->RC
	{
		foreach( $nivel as $valor => $contenido )
		{
			if (is_array($contenido))
			{
				$nivel[$valor] = $this->generar_indices_RC($contenido);
			} else {
				$nivel[$valor] = $this->indice_RC;
				$this->indice_RC += $this->dir_col_tipo[dato_valor];
			}
		}			
		return $nivel;
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_tabla_vacia($nivel)
	//Genera el esqueleto de las filas de la estructura $this->CC
	{
		foreach( $nivel as $valor => $contenido )
		{
			if (is_array($contenido))
			{
				if (($valor!='total_x') && ($valor!='total_y'))
					$nivel[$valor] = $this->generar_tabla_vacia($contenido);
			} else {
				$nivel[$valor] = $this->fila_vacia;
			}
		}			
		return $nivel;
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function corregir_array_total_y($nivel)
	//Corrige posibles huecos en la creacion de totales en Y
	{
		foreach( $nivel as $valor => $contenido )
		{
			if (is_array($contenido))
			{
				if ($valor=='total_y'){
					for($a=1;$a<=$this->columnas_cabecera;$a++)
						if(!isset($contenido[$a]))
							$nivel[$valor][$a]=0;
				}else{
					$nivel[$valor] = $this->corregir_array_total_y($contenido);				
				}
			}
		}			
		return $nivel;
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function planificar_cabecera($arbol)
	//genera la estructura de control utilizada para crear la cabecera
	{
		static $nivel = 1;
		$nietos = 0;
		foreach( $arbol as $clave => $contenido )
		{
			if (is_array($contenido)){
				$nivel++;
				$hijos = $this->planificar_cabecera($contenido);
				$nivel--;
			} else {
				$hijos = $this->dir_col_tipo[dato_valor];
			}
			$nietos += $hijos;
			$this->arbol_cabecera[$nivel][] = array('ID'=>$clave,'hijos'=>$hijos);
		}
		return $nietos;
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
		
	function generar_definicion_formato_columnas()
	//Genera la estructura donde se especifica el formato de cada columna
	{
		//Obtengo el formato de la columna con descripciones
		$this->formato_columnas[0]["estilo"]="desc";
		$this->formato_columnas[0]["formato"]=$this->dir_col[$this->dir_col_posicion[dato_des]]["formato"];
		//Obtengo el formato de las filas de  valores
		for ($a=0;$a<($this->dir_col_tipo[dato_valor]);$a++)
		{
			$temp[$a]["estilo"]=$this->dir_col[($this->dir_col_posicion[dato_valor]+$a)]["estilo"];
			$temp[$a]["formato"]=$this->dir_col[($this->dir_col_posicion[dato_valor]+$a)]["formato"];
		}
		//Cantidad de veces que se repiten los datos_valor en la cabecera (>=1).
		$repeticiones = ($this->columnas_cabecera / $this->dir_col_tipo[dato_valor]);
		//Lleno el array de definicion de los datos_valor
		for ($b=0;$b<$repeticiones;$b++){
			for($c=0;$c<count($temp);$c++){
				$this->formato_columnas[]=$temp[$c];
			}
		}
	}

	//################################################################################################
	//################################################################################################
	//#########################################  SALIDA  #############################################
	//################################################################################################
	//################################################################################################

	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%  HTML  %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function obtener_html()
	//Dispara la generacion de HTML
	{
		//$this->dump();
		$this->generar_html_cabecera(); //Genera el HTML de la cabecera de todas las tablas
		$funcion_segun_CdC = "generar_html_nivel_". $this->dir_col_tipo[corte_id];
		//Nivel 2 = bloques;
			//Nivel 1 = grupos;
				//Nivel 0 = tablas;
		$this->$funcion_segun_CdC($this->arbol_nucleo);
 	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_html_cabecera()
	//Genera el HTML de la cabecera
	{
		$html="";
		//Escribo el HTML de las referencias cruzadas (Si hay)
		for($a=0;$a<count($this->arbol_cabecera);$a++)
		{
			$columna_actual = 0;
			$nivel = $a+1;
			$categoria = $this->dir_col[($this->dir_col_posicion[refcruz_id]+($a*2)+1)]['nombre'];
			$html .="<TR>\n";
			//Extrigo el la descripcion de la RC
			$html .="<td class='hoja-serie-nombre-b'>
					<TABLE width='100%'  class='hoja-tabla'>\n
						<TR>\n
							<TD width='98%' class='hoja-serie-nombre'>". $categoria ."</TD>\n
							<TD width='2%'><img src='".recurso::imagen_apl("categ_x.gif")."' border='0'></TD>\n
						</TR>\n
					</TABLE>\n			
					</td>\n";
			$formateador = "formato_" . $this->dir_col[($this->dir_col_posicion[refcruz_id]+($a*2)+1)]["formato"];
			foreach($this->arbol_cabecera[$nivel] as $columna)
			{
				$nombre = $this->arbol_titulos_RC[$a][$columna['ID']];
				$colspan = $columna['hijos'];
				$html .="<TD colspan='$colspan' class='hoja-dato-desc2'>" . $formateador($nombre) . "</TD>\n";
				for($x=0;$x<$colspan;$x++)
				{
					if (isset($this->graf_categ_x[$columna_actual])) {
						$this->graf_categ_x[$columna_actual] .= "$categoria: $nombre \n";    
					}else{
						$this->graf_categ_x[$columna_actual] = "$categoria: $nombre \n";    
					}
					$columna_actual++;
				}
			}
			//Total en X.
			if ($this->dir_total_x) $html .="<TD class='hoja-titulo' >&nbsp;</TD>\n";
			//Popup grafico FILAS
			if ($this->graf_filas) $html .="<TD>&nbsp;</TD>\n";
			$html.="</TR>\n";
		}
		//Escribo el HTML de la descripcion de cada columna
		$html .="<TR>\n";	
		$html .= $this->generar_categoria_x($this->dir_col[$this->dir_col_posicion[dato_des]]['nombre'],0,0);
		//Escribo el titulo de cada columna de datos
		//Busco los titulos
		for($a=0;$a<$this->dir_col_tipo[dato_valor];$a++){
			$titulo_dato[] = $this->dir_col[($this->dir_col_posicion[dato_valor]+$a)]['nombre'];
		}
		if(is_array($this->arbol_RC)){
		//Si hay referencias cruzadas
			$columna_actual = 0;
			foreach($this->arbol_cabecera[$nivel] as  $columna)
			{
				foreach($titulo_dato as $nombre)
				{
					$html .= $this->generar_categoria_x($nombre,$columna_actual+1,2);
					$this->graf_categ_x[$columna_actual] .= "(Dato: $nombre)";
					$columna_actual++;
				}
			}
		} else {
		//SI no hay referencias cruzadas
			foreach($titulo_dato as $colx => $nombre)
			{
				$html .= $this->generar_categoria_x($nombre,$colx+1,1);
				if (is_array($this->graf_categ_x)) {
					if (array_key_exists($colx,$this->graf_categ_x)) {
						$this->graf_categ_x[$colx] .= "(Dato: $nombre)";
					}
				}	
			}
		}
		//Total en X.
		if ($this->dir_total_x) $html .="<TD class='hoja-total-desc2'>Total</TD>\n";
		//Popup grafico FILAS
		if ($this->graf_filas) $html .="<TD>&nbsp;</TD>\n";
		$html.="</TR>\n";
		$this->html_cabecera = $html;
 	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_html_nivel_0($tabla,$id_grupo="null",$id_tabla="null",$titulo="")
	//Genera las tablas.
	{
		if(!($ancho=$this->ancho)){
			$ancho = $this->columnas_total * 100;
			if ($ancho>700) $ancho = '95%';
			if ($ancho<300) $ancho = 300;
		}
		echo "<div align='center'>\n";
		echo "<table width='$ancho' class='objeto-base'>\n";
		echo "<tr><td>";
		$this->hoja->barra_superior($titulo);
		echo "</td></tr>\n";
		echo "<tr><td>";

		echo "<TABLE width='100%' cellpadding='2' cellspacing='0' class='hoja-tabla'>\n";		
		//---> Fila superior (Titulo, exportacion)
		echo  $this->html_cabecera;//Le agrego la cabecera
		//---> FILAS DE DATOS
		$fila_actual = 0;
		foreach ($tabla['datos'] as $clave => $fila)
		{
			echo  "<tr>\n";
			foreach ($fila as $id => $columna)
			{
				if($id=="0")
				//Columna de descripcion
				{
					$estilo = $this->formato_columnas[$id]["estilo"];
					$formateador = "formato_" . $this->formato_columnas[$id]["formato"];
					if($this->navegar){
						echo  "<td class='hoja-dato-". $estilo ."'>\n
								<TABLE class='hoja-tabla'>\n
									<TR>\n
										<td ><a href=\"javascript:drillDown('$clave');\" class='hoja-drill'>" . $formateador($columna) . "</a></TD>\n
										<TD width='2%'><a href=\"javascript:drillDown('$clave');\">".recurso::imagen_apl("objetos/drill_down.gif",true,null,null,"Drill Down")."</a></TD>\n
									</TR>\n
								</TABLE>\n
								</td>\n";
					}else{
						echo  "<td class='hoja-dato-$estilo'>" . $formateador($columna) . "</td>\n";
					}
				}else
				//Columna de datos
				{
					$estilo = $this->formato_columnas[$id]["estilo"];
					$formateador = "formato_" . $this->formato_columnas[$id]["formato"];
					echo  "<td class='$estilo'>" . $formateador($columna) . "</td>\n";
				}
			}
			//Total X.
			if ($this->dir_total_x){
				$formateador = "formato_" . $this->dir_total_x_formato;
				echo "<TD class='hoja-total-0'>".$formateador($tabla['total_x'][$clave])."</TD>\n";
			}
			//Popup grafico FILAS
			if ($this->graf_filas) echo "<td class='columna-titulo-2'><a href=\"javascript:popup_grafico('0','{$this->graf_datos_fc_indice}','$fila_actual');\"><img width='18' heigth='18' src='".recurso::imagen_apl("chart.png")."' border='0' alt='Desplegar los datos de la\n fila en un gr�ico'><a></td>\n";
			echo  "</tr>\n";
			$fila_actual++;
		}
		//Total Y.
		if ($this->dir_total_y){
			$suma = 0;
			echo "<tr>\n";
			echo "<TD class='hoja-total-desc'>Total</TD>\n";
			for($a=1;$a<=count($tabla['total_y']);$a++)
			{
				$suma += $tabla['total_y'][$a];
				$formateador = "formato_" . $this->formato_columnas[$a]["formato"];
				echo  "<td class='hoja-total-0'>" . $formateador($tabla['total_y'][$a]) . "</td>\n";
			}
			if ($this->dir_total_x){
				$formateador = "formato_" . $this->dir_total_x_formato;
				echo  "<td class='hoja-total-1'>".$formateador($suma)."</td>\n";
			}
			echo  "</tr>\n";		
		}
		//Link a grafico emergente para la COLUMNA
		if ($this->graf_columnas){
			echo  "<tr>\n";
			echo  "<td>&nbsp;</td>\n";
			for($a=0;$a<$this->columnas_cabecera;$a++)
			{
				echo  "<td class='columna-titulo-2'><a href=\"javascript:popup_grafico('1','{$this->graf_datos_fc_indice}','$a');\"><img width='18' heigth='18' src='". recurso::imagen_apl("chart.png") ."' border='0' alt='Desplegar los datos de la\n columna en un gr�ico'><a></td>\n";
			}
			if ($this->dir_total_x){
				echo  "<td>&nbsp;</td>\n";
			}
			//Popup grafico FILAS
			if($this->graf_filas) echo "<TD>&nbsp;</TD>\n";
			echo "</tr>\n";		
		}
		echo "</table>\n";
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</div>";

//--------------------------------------------------------------------------------------------------------
//-------------------------------------> Manejo de GRAFICOS !!!! -----------------------------------------
//--------------------------------------------------------------------------------------------------------
/*
		if(($this->graf_gen_tipo!="NO ACTIVADO")||($this->graf_columnas)||($this->graf_filas))
		{
			//--------------> SERIES y CATEGORIAS con sentido normal
			foreach ($tabla['datos'] as $dato)
			{
				//Separo la SERIE de valores de su definicion (CATEG_1)
				$categ_1[] = array_shift($dato);
				$series[] = $dato;
			}
			$categ_2 = $this->graf_categ_x;
			if(false)//Cual es la condicion exacta??
			{
				unset($categ_2);
				for($c=0;$c<count($this->graf_categ_x);$c++)
					$categ_2[$c]= "Columna " . ($c+1);
			}
			//Creo el grafico!!!
			$grafico["tipo"]= $this->graf_gen_tipo;
			$grafico["series"]= $series; 
			$grafico["categ_1"]= $categ_1; 
			$grafico["categ_2"]= $categ_2;
			$grafico["titulo"]= $titulo;
			$grafico["subtitulo"]= $subtitulo;
			$grafico["nom_variable"]= $nom_variable;
			$grafico["nom_categ_1"]= $nom_categ_1;
			$grafico["nom_categ_2"]= $nom_categ_2;
			$grafico["alto"]= $this->graf_gen_alto;
			$grafico["ancho"]= $this->graf_gen_ancho;
			//Cargo los datos serializados para los graficos FC
			$this->graf_datos_fc[0][$this->graf_datos_fc_indice]=$this->hoja->solicitud->vinculador->generar_url("siu_servicios_grafico_popup",$grafico);

			//Creo la serie INVERTIDA.
			foreach ($series as $sfila)
			{
				$indice = 0;
				foreach($sfila as $scolumna)
				{
					$temp[$indice][]=$scolumna;
					$indice++;
				}
			}			
			$series = $temp;
			$categ_2_temp = $categ_2;
			$categ_2 = $categ_1;
			$categ_1 = $categ_2_temp;
			//------------------------------------
			$grafico_i["tipo"]= $this->graf_gen_tipo;
			$grafico_i["series"]= $series; 
			$grafico_i["categ_1"]= $categ_1; 
			$grafico_i["categ_2"]= $categ_2;
			$grafico_i["titulo"]= $titulo;
			$grafico_i["subtitulo"]= $subtitulo;
			$grafico_i["nom_variable"]= $nom_variable;
			$grafico_i["nom_categ_1"]= $nom_categ_1;
			$grafico_i["nom_categ_2"]= $nom_categ_2;
			$grafico_i["alto"]= $this->graf_gen_alto;
			$grafico_i["ancho"]= $this->graf_gen_ancho;
			//Cargo los datos serializados para los graficos FC
			$this->graf_datos_fc[1][$this->graf_datos_fc_indice]=$this->hoja->solicitud->vinculador->generar_url("siu_servicios_grafico_popup",$grafico_i);
*/
//---------------------- Salida GRAFICO -----------------
/*
			$html.="<br>\n";
			if(($this->graf_gen_tipo!="NO ACTIVADO")||($this->graf_gen_tipo=="")){
				if(!$this->graf_gen_invertir){
					$html.= servicio::grafico($grafico,false);
				}else{
					$html.= servicio::grafico($grafico_i,false);
				}
				$html.="<br>\n";
				$id_grafico = $id_grupo ."-". $id_tabla;
				if($this->graf_gen_invertible) $html.="<a href=\"javascript:invertir_grafico('$id_grafico','{$this->graf_datos_fc_indice}');\">Invertir Categorias</a>";
				//$html.="<a href='apl_graficador.php?$qs&debug=1' target='_blank'>debug NORMAL</a> - <a href='apl_graficador.php?$qs_i&debug=1' target='_blank'>debug INVERTIDA</a><br>";
				$this->graf_datos_fc_indice++;
 			}
		}
*/
 	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_html_nivel_1($grupo,$id_grupo="null")
	{
		//Averiguo la posicion de los titulos y la descripcion segun la cantidad de niveles
		$posicion_descripcion = ($this->dir_col_tipo[corte_id]*2-1);
		$posicion_titulo = 	($this->dir_col_tipo[corte_id]-1);
		$descripcion = $this->dir_col[$posicion_descripcion]["nombre"]; 							//1:1; 2:3; 3:5; 4:7;
		foreach($grupo as $id_tabla => $tabla)
		{
			$titulo = $descripcion .": ". $this->arbol_titulos_CdC[$posicion_titulo][$id_tabla];	//1:0; 2:1; 3:2; 4:3;
			$this->generar_html_nivel_0($tabla,$id_grupo,$id_tabla,$titulo);
			echo "<br>\n";
		}
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_html_nivel_2($bloque)
	{
		//Averiguo la posicion de los titulos y la descripcion segun la cantidad de niveles
		$posicion_descripcion = 1;
		$posicion_titulo = 	0;
		$descripcion = $this->dir_col[$posicion_descripcion]["nombre"];								//2:1; 3:3; 4:5;
		foreach($bloque as $id_grupo => $grupo)
		{
			echo "<h4>".$descripcion . ": " . $this->arbol_titulos_CdC[$posicion_titulo][$id_grupo]."<h1>\n";
			$this->generar_html_nivel_1($grupo,$id_grupo);
		}
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_categoria_x($etiqueta, $columna, $estilo)
	{
		if($this->ordenable){
			return "<td class='columna-titulo-$estilo'>\n
					<TABLE width='100%' cellpadding='0' cellspacing='0'>\n
						<TR>\n
							<TD class='columna-titulo-$estilo-b' width='98%'>$etiqueta</TD>\n
							<TD width='2%'>
							<TABLE width='100%' cellpadding='0' cellspacing='0'>\n
								<TR>\n
									<TD class='columna-titulo-$estilo'><a href=\"javascript:ordenar('asc','$columna');\"><img src='".recurso::imagen_apl("orden_asc.gif")."' border='0' alt='Orden ascendente'></a></TD>\n
								</TR>\n															
								<TR>\n
									<TD class='columna-titulo-$estilo'><a href=\"javascript:ordenar('des','$columna');\"><img src='".recurso::imagen_apl("orden_des.gif")."' border='0' alt='Orden descendente'></a></TD>\n
								</TR>\n
							</TABLE>															
							</TD>\n
						</TR>\n
					</TABLE>\n
					</td>\n";
		} else {
			if($columna==0){
				return "<td class='hoja-serie-nombre-b'>
						<TABLE width='100%'  class='hoja-tabla'>\n
							<TR>\n
								<TD width='2%'><img src='".recurso::imagen_apl("categ_y.gif")."' border='0'></TD>\n
								<TD width='98%' class='hoja-serie-nombre'>". $etiqueta ."</TD>\n
							</TR>\n
						</TABLE>\n			
						</td>\n";
			}else{
				return "<TD class='hoja-titulo-col'>$etiqueta</TD>\n";
			}
		}
	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_array_js_graf_fc()
	{
		$js = "	graf_fc = new Array(3);\n";
		$js .= "	graf_fc[0] = new Array();\n";//Datos normales
		$js .= "	graf_fc[1] = new Array();\n";//Datos invertidos
		foreach($this->graf_datos_fc as $sentido => $contenido)
		{
			foreach($contenido as $clave => $datos)
			{
				$js .= "	graf_fc[$sentido][$clave]=\"$datos\";\n";
			}	
		}
		return 	$js;
	}
	
/*
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%   XLS   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function generar_xls_cabecera()
	{
		$html="";
		//Escribo el HTML de las referencias cruzadas (Si hay)
		for($a=0;$a<count($this->arbol_cabecera);$a++)
		{
			$nivel = $a+1;
			$html .="<TR>\n";
			//Extrigo el la descripcion de la RC
			$html .="<td align='center'>". $this->dir_col[($this->dir_col_posicion[refcruz_id]+($a*2)+1)]['nombre'] ."</td>\n";
			foreach($this->arbol_cabecera[$nivel] as $columna)
			{
				$nombre = $this->arbol_titulos_RC[$a][$columna['ID']];
				$colspan = $columna['hijos'];
				$html .="<TD colspan='$colspan' align='center'>$nombre</TD>\n";
			}
			//Total en X.
			if ($this->dir_total_x) $html .="<TD align='center'>&nbsp;</TD>\n";
			$html.="</TR>\n";
		}
		//Escribo el HTML de la descripcion de cada columna
		$html .="<TR>\n";		
		$html .="<td align='center'>".$this->dir_col[$this->dir_col_posicion[dato_des]]['nombre']."</td>\n";
		//Escribo el titulo de cada columna de datos
		//Busco los titulos
		for($a=0;$a<$this->dir_col_tipo[dato_valor];$a++){
			$titulo_dato[] = $this->dir_col[($this->dir_col_posicion[dato_valor]+$a)]['nombre'];
		}
		if(is_array($this->arbol_RC)){
		//Si hay referencias cruzadas
			foreach($this->arbol_cabecera[$nivel] as $columna)
			{
				foreach($titulo_dato as $nombre)
				{
					$html .="<TD align='center'>$nombre</TD>\n";			
				}
			}
		} else {
		//SI no hay referencias cruzadas
			foreach($titulo_dato as $nombre)
			{
				$html .="<TD align='center'>$nombre</TD>\n";			
			}
		}
		//Total en X.
		if ($this->dir_total_x) $html .="<TD align='center'>Total</TD>\n";
		$html.="</TR>\n";		
		return $html;
 	}
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	function generar_xls($id_grupo, $id_tabla)
	{
		//Ubico la tabla que necesito mostrar
		$ref_g = "";
		$ref_t = "";
		if($id_grupo<>"null") $ref_g = "[$id_grupo]";
		if($id_tabla<>"null") $ref_t = "[$id_tabla]";			
		$ref_tabla = "\$tabla = \$this->arbol_nucleo" . $ref_g . $ref_t .";";
		eval($ref_tabla);
		//Tengo que ubicar el titulo que voy a mostrar
		$titulo = "FALTA generar TITULO";
		//Comienzo a crear la tabla
		$html.= "<TABLE border='2'>\n";		
		$html.="<TR><TD colspan='". $this->columnas_total ."' align='center'>$titulo</TD></TR>\n";
		$html.= $this->generar_xls_cabecera();
		//------------------------------------------------------- Genero las filas ------------------------------
		foreach ($tabla['datos'] as $clave => $fila)
		{
			$html.= "<tr>\n";
			foreach ($fila as $id => $columna)
			{
				$formateador = "formato_" . $this->formato_columnas[$id]["formato"];
				$html.= "<td align='center'>" . $this->$formateador($columna) . "</td>\n";
			}
			//Total X.
			if ($this->dir_total_x){
				$formateador = "formato_" . $this->dir_total_x_formato;
				$html .="<TD align='center'>".$this->$formateador($tabla['total_x'][$clave])."</TD>\n";
			}
			$html.= "</tr>\n";
		}
		//Total Y.
		if ($this->dir_total_y){
			$suma = 0;
			$html.= "<tr>\n";
			$html .="<TD align='center'>Total</TD>\n";
			for($a=1;$a<=count($tabla['total_y']);$a++)
			{
				$suma += $tabla['total_y'][$a];
				$formateador = "formato_" . $this->formato_columnas[$a]["formato"];
				$html.= "<td align='center'>" . $this->$formateador($tabla['total_y'][$a]) . "</td>\n";
			}
			if ($this->dir_total_x){
				$formateador = "formato_" . $this->dir_total_x_formato;
				$html.= "<td align='center'>".$this->$formateador($suma)."</td>\n";
			}
			$html.= "</tr>\n";		
		}
		$html.="</table>\n";
		return $html;
	}	
*/	
	//################################################################################################
	//########################################  DEBUGGING  ###########################################
	//################################################################################################

	function dump()
	//Muestra en pantalla el estado interno
	{
//		echo "\n\n\n\n\n\n\n";
//		dump_tabla($this->datos_db,"Array DB {\$this->datos_db}");
//		dump_arbol_t($this->dir_col,"Directivas {\$this->dir_col}");
//		dump_arbol_t($this->dir_col_tipo,"Directivas por tipo {\$this->dir_col_tipo}");
//		dump_arbol_t($this->dir_col_posicion,"Posicion directivas {\$this->dir_col_posicion}");
//		dump_arbol_t($this->php_dinamico,"PHP dinamico {\$this->php_dinamico}");
//		dump_arbol_t($this->arbol_titulos_CdC,"TITULOS cortes de control {\$this->arbol_titulos_CC}");
//		dump_arbol_t($this->arbol_nucleo,"Arbol NUCLEO {\$this->arbol_nucleo}");
//		dump_arbol_t($this->arbol_titulos_RC,"TITULOS referencias cruzadas {\$this->arbol_titulos_RC}");
//		dump_arbol_t($this->arbol_RC,"Arbol Referencias cruzadas {\$this->arbol_RC}");
//		dump_arbol_t($this->arbol_cabecera,"Estructura de la cabecera {\$this->arbol_cabecera}");
//		dump_arbol_t($this->graf_categ_x,"Categoria X para graficos {\$this->graf_categ_x}");
//		dump_arbol_t($this->graf_datos_fc,"DATOS para graficl FC {\$this->graf_datos_fc}");
//		dump_arbol_t($this->formato_columnas,"Definicion del formato de cada columna{\$this->formato_columnas}");
//		add_var($this->columnas_total,"total general de columnas");
//		add_var($this->columnas_cabecera,"total de columnas cabecera");
//		dump_vars();
	}
}
?>
