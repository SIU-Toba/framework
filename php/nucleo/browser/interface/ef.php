<?
require_once("form.php");// Elementos STANDART de formulario
define("apex_ef_no_seteado","nopar");// Valor que debe ser considerado como NO ACTIVADO, si se cambia cambiar en las clases JS
define("apex_ef_separador","||");// Valor que debe ser considerado como NO ACTIVADO
define("apex_ef_valor_oculto", "#oculto#"); // Valor que debe ser considerado como SOLO DISPONIBLE EN SERVER
define("apex_ef_dependenca","%");//Mascara para 

/*######################################################################################################
########################################################################################################
#################################      ELEMENTOS de FORMULARIO      ####################################
########################################################################################################
########################################################################################################
*/
require_once("ef_combo.php");		//EF de tipo COMBO
require_once("ef_editable.php");	//EF de tipo Editable (INPUT)
require_once("ef_oculto.php");		//EF de tipo OCULTO
require_once("ef_popup.php");		//Ef de tipo POPUP
require_once("ef_varios.php");		//Varios
require_once("ef_multi_seleccion.php");
require_once("ef_sin_estado.php");	//EF de tipo COMBO
//require_once("ef_sin_estado.php");		//EF de que no poseen estado

class ef //Clase abstracta, padre de todos los EF
{
	var $padre;		    		// PADRE del ELEMENTO (ID del objeto en el que este esta incluido)
	var $nombre_formulario;		// Nombre del formulario donde el ELEMENTO esta metido
	var $id;			    	// ID del ELEMENTO
	var $etiqueta;	   			// Etiqueta del ELEMENTO
	var $descripcion;   		// Descripcion del ELEMENTO
	var $id_form_orig;			// ID original a utilizar en el FORM
	var $id_form;	    		// ID a utilizar en el FORM
	var $dato;          		// NOMBRE del DATO que esta manejando el ELEMENTO (si es un DATO compuesto, es un array)
	var $estado;	    		// Estado ACTUAL del ELEMETO (Si el DATO es compuesto, es un array)
	var $obligatorio;			// Flag que indica SI se el ELEMENTO representa un valor obligatorio
	var $validacion=true;		// Flag que indica el estado de la validacion realizada sobre el estado
	var $solo_lectura;      	// Flag que indica si el objeto se debe deshabilitar cuando se muestra
	var $ocultable = false;		// Indica si el EF provee una interface para ocultarlo y mostrarlo
	var $javascript="";			// Javascript del elemento de formulario
	var $input_extra = "";		// Parametros adicionales
	var $expandido = true;
	//--- DEPENDENCIAS ---
	var $dependencias;			// Array de DEPENDENCIAS (Ids de EFs MAESTROS)
	var $maestros;				// Array de id_form de Maestros
	var $dependencias_datos;	// Datos devueltos por las dependencias
	var $dependientes;			// Array de ESCLAVOS
	var $dep_master = false;	//Soy master?
	var $dep_slave = false;		//Soy slave?
	var $agregado_form;			//Número de linea en un form multilinea
	
	function ef($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->padre = $padre;
		$this->nombre_formulario = $nombre_formulario;
		$this->id = $id;
		$this->id_form_orig = $this->nombre_formulario . $this->padre[1] . $this->id;
		$this->establecer_id_form();
		$this->etiqueta = $etiqueta;
		$this->descripcion = $descripcion;
     	$this->dato = $dato;
     	$this->obligatorio = $obligatorio;
		
		//---- Declaracion de dependencias
		if(isset($parametros["dependencias"])){
			if($parametros["dependencias"]!=""){
				$this->dependencias = explode(",",$parametros["dependencias"]);
				$this->dependencias = array_map("trim",$this->dependencias);
				$this->dep_slave = true;
			}else{
				$this->dependencias = null;
			}
		}else{
			$this->dependencias = null;
		}		
		//---- Tipo de manejo de dependencias
		if(isset($parametros["dependencias_tipo"])){
			if($parametros["dependencias_tipo"]!=""){
				$this->dependencias_tipo = $parametros["dependencias"];
			}else{
				$this->dependencias_tipo = "";
			}
		}else{
			$this->dependencias_tipo = "";
		}
		//---- Javascript		
		if(isset($parametros["javascript"])){
			$temp = explode(",",$parametros["javascript"]);
			$evento = $temp[0];
			$metodo = $temp[1];
			$this->javascript = " " . $evento .  "=\"return " . $metodo . ";\"";
		}
		if(isset($parametros['ocultable'])){
			$this->ocultable = true;
		}
	}

//-----------------------------------------------------
//----------------- DEPENDENCIAS ----------------------
//-----------------------------------------------------

	//*************************
	//***      MASTER       ***
	//*************************

	function registrar_ef_dependiente($ef, $id_form)
	//Registra dependencias
	{
		//echo "SOY ". $this->id . " me registran " . $dependiente;
		$this->dependientes[$ef] = $id_form;
		$this->dep_master = true;
	}
	
	function javascript_master_evento()
	//Dispara el evento de modificacion del padre
	{
		return " onchange='modificacion_maestro_{$this->id_form}();' ";
	}

	function javascript_master_notificar()
	{
		//Le aviso a los dependientes que me modifique
		$js = "function modificacion_maestro_{$this->id_form}()\n{\n";
		foreach($this->dependientes as $dependiente){
			$js .= " escuchar_master_{$dependiente}{$this->agregado_form}();\n";
		}
		$js .= " atender_proxima_consulta();\n";
		$js .= "\n}\n";
		return $js;
	}
	
	function javascript_master_get_estado()
	{
		return "
		function master_get_estado_{$this->id_form}()
		{
			alert('OBTENCION ESTADO ef {$this->id}. Redefinir');
		}
		";		
	}
	
	function javascript_master_cargado()
	{
		return "
		function master_cargado_{$this->id_form}()
		{
			alert('PREGUNTA si esta cargado el ef {$this->id}. Redefinir');
		}
		";		
	}
	//*************************
	//***      SLAVE        ***
	//*************************
	
	//Tiene que generar codigo javascript (una callback)
	//que escuche los eventos del maestro

	function registrar_ef_maestro($ef, $id_form)
	{
		$this->maestros[$ef] = $id_form;
	}

	function obtener_dependencias()
	{
		return $this->dependencias;
	}
	//-----------------------------------------------------	
	function javascript_slave_escuchar()
	{
		$lista_maestros = dump_array_javascript($this->maestros, 'maestros');
		$js = "
		function escuchar_master_{$this->id_form}()
		{
			$lista_maestros
			//-- ¿Estan los maestros cargados?
			var cargados = true;
			for (var i in maestros) {
  				if (! eval('master_cargado_' + maestros[i] + '{$this->agregado_form}()'))
  					cargados = false;
			}
			//Me reseteo (por si nunca se vuelve a la callback)
			//Esto resetea al mismo tiempo a los EFs que dependen de MI
			//Esto se tiene que hacer aunque los masters no esten cargados tambien
			//por el caso De un master que se paso al estado 'no_seteado'
			reset_{$this->id_form}();

			if(cargados)//SI Se cargaron todos los MAESTROS...
			{
				//-- Obtengo el valor de mis maestros
				var dependencias = '';
				for (var i in maestros) {
					valor = eval('master_get_estado_' + maestros[i] + '{$this->agregado_form}()');
					if (valor != '')
						dependencias = dependencias + '|' + i + ';' + valor;
					else //Caso particular para los ocultos
						dependencias = dependencias + '|' + i;					
				}
				//Empaqueto toda la informacion que tengo que mandar.
				parametros = '{$this->padre[0]};{$this->padre[1]};{$this->id}' + dependencias;
				//Se encola la recarga de informacion
				//alert('parametros:' + parametros);
				var prefijo_vinculo = '".toba::get_hilo()->prefijo_vinculo()."';
				encolar_consulta('toba','/basicos/ef/respuesta',parametros,'recargar_slave_{$this->id_form}', prefijo_vinculo);
			}
		}
		";
		return $js;
	}
	//-----------------------------------------------------	
	
	function javascript_slave_recargar_datos()
	//Funcion encargada de cargar el EF con los datos que llegaron del SERVER
	//(Esta es la callback de javascript que procesa la respuesta)
	//Esta funcion hay que redeclararla en los HIJOS
	{
		return "
		function recargar_slave_{$this->id_form}(datos)
		{
			alert('Soy la callback de recarga... ME LLAMARON ('+datos+')!!\\n Hay que redefinir esta funcion en los hijos');	
		}
		";	
	}
	//-----------------------------------------------------		

	function javascript_slave_reset()
	{		
		return "
		function reset_{$this->id_form}(datos)
		{
			alert('RESET del EF: {$this->id}. Hay que recargarla en los hijos');
		}
		";	
	}
	//-----------------------------------------------------		
	
	function cargar_datos_dependencias($datos)
	//El form indica despues de su carga el valor que tomaron las
	//dependencias (los maestros) de este EF. Si estan todos completos, puede
	//proceder a cargar sus datos
	{
		//asercion::es_array($datos);
		//ei_arbol($datos);
		if(count($datos)>0)
		{
			$this->dependencias_datos = $datos;
			//ei_arbol($this->dependencias_datos);
			//Controlo que todas las dependencias esten cargadas
			$control_dep = true;
			foreach($this->dependencias as $dep){
				if(!array_key_exists($dep, $datos) || trim($datos[$dep])==""){
					$control_dep = false;
					break;
				}
			}
			if($control_dep){
				$this->cargar_datos_master_ok();
			}
		}
	}
	//-----------------------------------------------------
	
	function estado_dependencias()
	//Indica el estado del EF respecto de sus despendencias
	{
		if(true){
			return true;
		}
	}

//-----------------------------------------------------
//-------------------- JAVASCRIPT ---------------------
//-----------------------------------------------------
	function crear_objeto_js()
	//Retorna la sentencia de creación del objeto javascript que representa al EF
	{
		return "new ef({$this->parametros_js()})";
	}
	
	function objeto_js()
	//Retorna el nombre de la instancia del objeto en javascript
	//Ej: alert({$ef->objeto_js()}.valor())
	{
		return $this->id_form_orig;
	}
	
	function parametros_js()
	{
		$obligatorio = ( $this->obligatorio ) ? "true" : "false";
		$colapsable = ( $this->expandido ) ? "false" : "true";
		return "'{$this->id_form_orig}', '{$this->etiqueta}', $obligatorio, $colapsable";
	}

	function obtener_javascript_input()
	//Javascript acoplable al INPUT,
	//ATENCION: Hay que unificar el consumo de la cascada con el uso adhoc
	{
		if($this->dep_master){
			return $this->javascript_master_evento();
		}else{
			return $this->javascript;
		}
	}
	//-----------------------------------------------------

	function obtener_javascript_general()
	{
		$js = "";
		if($this->dep_master){
			$js .= $this->javascript_master_notificar();
			$js .= $this->javascript_master_get_estado();
			$js .= $this->javascript_master_cargado();			
		}
		if($this->dep_slave){
			$js .= $this->javascript_slave_escuchar();
			$js .= $this->javascript_slave_recargar_datos();
			$js .= $this->javascript_slave_reset();
		}
		if($js!=""){
			return "\n<SCRIPT language='javascript'>\n" . $js . "\n</SCRIPT>\n";	
		}
	}
	//-----------------------------------------------------

	function obtener_consumo_javascript()
	//Esta funcion permite que un EF declare la necesidad de incluir
	//codigo javascript necesario para su correcto funcionamiento (generalmente javascript:
	//expresiones regulares comunes a varios EF, includes de manejo de fechas, etc...
	{
		return null;
	}
	//-----------------------------------------------------
	
    function obtener_javascript()
    //Devuelve el javascript del elemento que se incorpora en la
    //funcion validadora del FORM
    {
        return "";
    }

//-----------------------------------------------------
//-------------- ACCESO A PROPIEDADES -----------------
//-----------------------------------------------------

	function establecer_id_form($agregado="")
	{
		$this->agregado_form = $agregado;
		$this->id_form = $this->id_form_orig . $agregado;
	}

	function establecer_etiqueta($etiqueta)
	{
		$this->etiqueta = $etiqueta;
	}

	function establecer_solo_lectura()
	{
        $this->solo_lectura = true;
    }

	function establecer_lectura()
	{
        $this->solo_lectura = false;
    }
	
	function set_expandido($expandido)
	{
		$this->expandido = $expandido;
	}
	
	function esta_expandido()
	{
		return $this->expandido;
	}

	function obtener_id()
	//Devuelve el ID de un elemento de interface
	{
		return $this->id;
	}

	function obtener_etiqueta()
	//Devuelve el nombre de un elemento de interface
	{
		return $this->etiqueta;
	}

	function obtener_dato()
	//Devuelve el nombre de un elemento de interface
	{
		return $this->dato;
	}

	function obtener_id_form_orig()
	//Devuelve el ID del elemento en el formulario
	{
		return $this->id_form_orig;
	}	

	function obtener_id_form()
	//Devuelve el ID del elemento en el formulario
	{
		return $this->id_form;
	}	

	function obtener_info()
	//INFORMACION por DEFECTO: Valor simple
	{
		if($this->activado()){
			return "{$this->etiqueta}: {$this->estado}";
		}
	}

//-----------------------------------------------------
//-------------- ACCESO al ESTADO ---------------------
//-----------------------------------------------------

	function cargar_estado($estado=null)
	//Carga el estado interno
	{
   		if(isset($estado)){								
    		$this->estado=$estado;
			return true;
	    }elseif(isset($_POST[$this->id_form])){
				if(!is_array($_POST[$this->id_form])){
					if(get_magic_quotes_gpc()){
						$this->estado = stripslashes($_POST[$this->id_form]);
					}else{
	   				$this->estado = $_POST[$this->id_form];
					}
				}else{
	   				$this->estado = $_POST[$this->id_form];
				}
			return true;
    	}
		return false;
	}

	function obtener_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			return $this->estado;
		}else{
			return 'NULL';
		}
	}

	function activado()
	{
		//Devuelve TRUE si el elemento esta seteado y FALSE en el caso contrario
		return isset($this->estado) && ($this->estado !== apex_ef_no_seteado);
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		if($this->activado()){
			unset($this->estado);
		}
	}

    function validar_estado()
    //Validacion interna del EF
    {
		$this->validacion = true;
        return array(true,"");
    }
	
//-----------------------------------------------------
//-------------------- INTERFACE ----------------------
//-----------------------------------------------------
    
	function obtener_input()
	{
		//Esta funcion se define en cada hijo,
		//Devuelve el HTML que constituye interface
	}

	function envoltura_std($elemento_formulario, $item_editor_padre=null,$canal_editor_detalle_ef=0)
	//Envoltura normal
	{
		if($this->validacion){
	        if($this->obligatorio){
    	        $estilo = "ef-etiqueta-obligatorio";
				$marca = "(*)";
        	}else{
	            $estilo = "ef-etiqueta";
				$marca ="";
    	    }
		}else{
            $estilo = "ef-etiqueta-error";
			$marca ="";
		}
		global $solicitud;
		$clase = ($this->esta_expandido()) ? "" : "style='display:none' class='abm-fila-oculta'";
		echo "<div id='nodo_{$this->id_form}' $clase>";
		echo "<table border='0' width='150' cellpadding='0' cellspacing='0'>\n";
		echo "<tr><td >".gif_nulo(150,0)."</td>";
		echo "<td>".gif_nulo(1,1)."</td></tr>\n";
		echo "<tr><td>".gif_nulo(1,1)."</td></tr>\n";
		echo "<tr><td class='$estilo'>";
		if(trim($this->descripcion)!=""){
			echo recurso::imagen_apl("descripcion.gif",true,null,null,$this->descripcion);
		}
		echo "&nbsp;{$this->etiqueta} $marca</td>\n";
		//Acceso directo al EDITOR del ABM 
		//(con el editor de columnas cargado en ESTA!)
		if(apex_pa_acceso_directo_editor){
			echo "<td class='$estilo' >";
			if( ($this->padre[0]) == $solicitud->hilo->obtener_proyecto() &&
			(isset($item_editor_padre)) )
			{
				$clave_abm_registro_padre = implode(apex_qs_separador,$this->padre);
				$clave_abm_registro_propio = $clave_abm_registro_padre . apex_qs_separador .$this->id;
				echo $solicitud->vinculador->obtener_vinculo_a_item(
							"toba",$item_editor_padre,
							array( apex_hilo_qs_zona => $clave_abm_registro_padre,
									apex_hilo_qs_canal_obj.$canal_editor_detalle_ef => $clave_abm_registro_propio ),
							true);

			}
			echo "</td>\n";
		}
		echo "<td class='ef-zonainput' id='cont_{$this->id_form}'>$elemento_formulario</td></tr>\n";
		echo "</table>\n";
		echo "</div>";
	}
	
	function envoltura_ei_ml()
	{
		if($this->validacion){
	        if($this->obligatorio){
    	        $estilo = "ef-etiqueta-obligatorio";
				$marca = "(*)";
        	}else{
	            $estilo = "ef-etiqueta";
				$marca ="";
    	    }
		}else{
            $estilo = "ef-etiqueta-error";
			$marca ="";
		}	
		$html = "<div class='$estilo'>".$this->obtener_etiqueta()." $marca";
		if(trim($this->descripcion)!=""){
			$html .= "<br>".recurso::imagen_apl("descripcion.gif",true,null,null,$this->descripcion);
		}
		$html .= "</div>";
		return $html;
	}

	function obtener_interface()
	{
		$this->envoltura_std($this->obtener_input(),
								"/admin/objetos/editores/abms",
								141);
	}

	function obtener_interface_ut()
	{
		$this->envoltura_std($this->obtener_input(),
								"/admin/objetos/editores/ut_formulario",
								411);
	}	
	
	function obtener_interface_ei()
	{
		$this->envoltura_std($this->obtener_input(),
								"/admin/objetos/editores/formulario",
								574);
	}	

	function obtener_interface_ei_filtro()
	{
		$this->envoltura_std($this->obtener_input(),
								"/admin/objetos/editores/ei_filtro",
								1088);
	}
		
	function obtener_html()
	{
		$this->envoltura_filtro( $this->obtener_input() );
	}

	function envoltura_filtro($elemento_formulario)
	//Crea la envoltura HTML del elemento para utilizarse como filtro
	//(Cambia de color si esta activado)
	{
		$a="";
		if($this->activado())$a="-activado";
		echo "<table border='0' cellpadding='0' cellspacing='0'>\n";
		echo "<tr><td ><img src='img/nulo.gif' width='180' height='1'></td><td><img src='img/nulo.gif' width='1' height='1'></td></tr>\n";
		echo "<tr><td class='parametro-item{$a}'>&nbsp;{$this->etiqueta}</td>\n";
		echo "<td class='parametro-item{$a}'>$elemento_formulario</td></tr>\n";
		echo "</table>\n";
	}
	
}
//########################################################################################################
//########################################################################################################
?>
