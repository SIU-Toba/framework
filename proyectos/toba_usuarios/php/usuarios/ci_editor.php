<?php

class ci_editor extends toba_ci
{
	const clave_falsa = 'xS34Io9gF2JD';					//La clave no se envia al cliente
	
	protected $s__proyecto;
	protected $s__usuario;
	protected $s__usuario_arai;
				
	function ini()
	{
		toba::solicitud()->set_autocomplete(false);	//Evita que el browser quiera guardar la clave de usuario
	}
	
	function datos($tabla)
	{
		return $this->controlador->dep('datos')->tabla($tabla);
	}
	
	function limpiar_datos()
	{
		unset($this->s__proyecto);
	}
	
	function conf()
	{
		if ($this->controlador()->dep('datos')->esta_cargada()) {
			$usuario = $this->datos('basica')->get();
			$this->s__usuario = $usuario['usuario'];
			$desc = 'Usuario: <strong>' . texto_plano($usuario['nombre']) . '</strong>';
			$this->pantalla()->set_descripcion($desc);
			$this->dep('basica')->ef('usuario')->set_solo_lectura(true);
			$this->dep('basica')->ef('usuario_arai')->set_solo_lectura(true);
			$this->dep('basica')->ef('cuenta')->set_solo_lectura(true);
		} else {
			$this->controlador->pantalla()->eliminar_evento('eliminar');
		}
		
		// Elimina la dependencia form_pregunta_secreta cuando esta vinculado a arai-usuarios
		if (toba::instalacion()->vincula_arai_usuarios() && $this->pantalla('usuario')->existe_dependencia('form_pregunta_secreta')) {
			
			$this->pantalla('usuario')->eliminar_dep('form_pregunta_secreta');
		}
	}
	
	function conf__proyecto()
	{
		if (!isset($this->s__proyecto)) {
			$this->pantalla('proyecto')->eliminar_dep('form_proyectos');	
		}
	}

	//---- Info BASICA -------------------------------------------------------

	function evt__basica__modificacion($datos)
	{
		$largo_clave =  toba_parametros::get_largo_pwd(null);

		// seteo los datos de arai-usuarios
		$datos = gestion_arai_usuarios::set_datos($datos, $largo_clave);
		if (isset($datos['usuario_arai'])) {
			$this->s__usuario_arai = $datos['usuario_arai'];
		}

		if ($datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		} else {						//Chequeamos que la composicion de la clave sea valida			
			toba_usuario::verificar_composicion_clave($datos['clave'], $largo_clave);
		}

		if (! isset($datos['autentificacion'])) {
			$datos['autentificacion']  = apex_pa_algoritmo_hash;
		}

		$this->datos('basica')->set($datos);
	}

	function conf__basica($form)
	{
		$datos = $this->datos('basica')->get();
		if (isset($datos)) {
			$datos['clave'] = self::clave_falsa;
		}

		$largo_clave = toba_parametros::get_largo_pwd(null);							//Como aun no se sobre que proyecto trabajo.. el largo es el por defecto, osea 8.
		$form->ef('clave')->set_expreg(toba_usuario::get_exp_reg_pwd($largo_clave));
		$form->ef('clave')->set_descripcion("La clave debe tener al menos $largo_clave caracteres, entre letras mayúsculas, minúsculas, números y símbolos, no pudiendo repetir caracteres adyacentes");
		
		// obtengo los datos de arai-usuarios
		$datos = gestion_arai_usuarios::get_datos($datos);
		
		// quito los campos que no se utilizan cuando esta vinculado con arai-usuarios
		if (toba::instalacion()->vincula_arai_usuarios()) {
			$form->desactivar_efs(array('usuario', 'nombre', 'email', 'clave', 'forzar_cambio_pwd', 'vencimiento'));
		} else {
			$form->desactivar_efs(array('usuario_arai', 'cuenta'));
		}
		
		return $datos;
	}

	//---- Asociacion a PROYECTOS -------------------------------------------------

	function evt__proyecto__salida()
	{
		$this->datos('proyecto')->resetear_cursor();		
	}

	function evt__cuadro_proyectos__seleccion($seleccion)
	{
		$this->s__proyecto = $seleccion['proyecto'];
	}
	
	function conf__cuadro_proyectos($componente)
	{	
		$proyectos = consultas_instancia::get_lista_proyectos();
		foreach ($proyectos as $id => $proyecto) {
			$grupos_acceso = $this->datos('proyecto')->get_filas(array('proyecto' => $proyecto['proyecto']));
			$grupos = array();
			//-- Perfil funcional -------------------------
			foreach ($grupos_acceso as $ga) {
				$grupos[] = $ga['grupo_acceso'];
			}
			$proyectos[$id]['grupos_acceso'] = empty($grupos) ? '<span style="color:gray">-- Sin Acceso --</span>' : implode(', ', $grupos);
			//-- Perfil datos -----------------------------
			$perfil_datos = $this->datos('proyecto_pd')->get_filas(array('proyecto' => $proyecto['proyecto']));
			if ($perfil_datos) {
				$proyectos[$id]['perfil_datos'] = $perfil_datos[0]['perfil_datos_nombre'];
			} else {
				$proyectos[$id]['perfil_datos'] = '&nbsp;';
			}
		}
		$componente->set_datos($proyectos);
	}

	function evt__form_proyectos__modificacion($datos)
	{		
		//-- Perfil funcional -------------------------
		$id = $this->datos('proyecto')->get_id_fila_condicion(array('proyecto'=>$this->s__proyecto));
		foreach ($id as $clave) {
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		$fila = array();
		$fila['proyecto'] = $this->s__proyecto;
		$fila['usuario'] = $this->s__usuario;
		foreach ($datos['usuario_grupo_acc'] as $id=>$grupo_acceso) {
			$fila['usuario_grupo_acc'] = $grupo_acceso;
			$this->datos('proyecto')->nueva_fila($fila);
		}
		//-- Perfil datos -----------------------------
		$id = $this->datos('proyecto_pd')->get_id_fila_condicion(array('proyecto'=>$this->s__proyecto));
		if ( isset($datos['usuario_perfil_datos']) ) {
			$fila = array();
			$fila['proyecto'] = $this->s__proyecto;
			$fila['usuario'] = $this->s__usuario;
			$fila['usuario_perfil_datos'] = $datos['usuario_perfil_datos'];
			if (empty($id)) {
				$this->datos('proyecto_pd')->nueva_fila($fila);
			} else {
				$this->datos('proyecto_pd')->modificar_fila($id[0], $fila);
			}
		} else if (! empty($id)) {
			//-- Si por pantalla no viene nada pero esta en la tabla hay que borrarlo
			$this->datos('proyecto_pd')->eliminar_fila($id[0]);
		}
		$this->limpiar_datos();
	}

	function evt__form_proyectos__baja()
	{
		//-- Perfil funcional -------------------------
		$id = $this->datos('proyecto')->get_id_fila_condicion(array('proyecto' => $this->s__proyecto));
		foreach ($id as $clave) {
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		//-- Perfil datos -----------------------------
		$id = $this->datos('proyecto_pd')->get_id_fila_condicion(array('proyecto'=>$this->s__proyecto));
		if (!empty($id)) {
			$this->datos('proyecto_pd')->eliminar_fila($id[0]);
		}
		$this->limpiar_datos();
	}
	
	function evt__form_proyectos__cancelar()
	{
		unset($this->s__proyecto);
	}
	
	function conf__form_proyectos($componente)
	{
		if (isset($this->s__proyecto)) {
			$datos = array();
			$datos['proyecto'] = $this->s__proyecto;
				//-- Perfil funcional -------------------------
			$grupo_acc = $this->datos('proyecto')->get_filas(array('usuario'=> $this->s__usuario, 'proyecto'=>$this->s__proyecto));
			if (empty($grupo_acc)) {
				$componente->eliminar_evento('baja');
			}
			
			$ga_seleccionados = array();
			foreach ($grupo_acc as $i=>$ga) {
				$ga_seleccionados[] = $ga['usuario_grupo_acc'];
			}
			$datos['usuario_grupo_acc'] = $ga_seleccionados;
			//-- Perfil datos -----------------------------
			$perfil_datos = $this->datos('proyecto_pd')->get_filas(array('usuario'=> $this->s__usuario, 'proyecto'=>$this->s__proyecto));
			if ($perfil_datos) {
				$datos['usuario_perfil_datos'] = $perfil_datos[0]['usuario_perfil_datos'];
			}
			$componente->set_datos($datos);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- form_pregunta_secreta --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_pregunta_secreta(toba_ei_formulario_ml $form_ml)
	{
		$datos = $this->datos('pregunta_secreta')->get_filas();
		foreach ($datos as $klave => $fila) {
			$datos[$klave]['activa'] = ($datos[$klave]['activa'] == '1') ? 'SI' : 'NO';
			$datos[$klave]['pregunta'] = $this->desencriptar_datos($datos[$klave]['pregunta']);
			$datos[$klave]['respuesta'] = $this->desencriptar_datos($datos[$klave]['respuesta']);
		}
		$form_ml->set_datos($datos);
	}

	function evt__form_pregunta_secreta__modificacion($datos)
	{
		$hay_alta = false;
		//Solo una de las preguntas tiene que estar activa.
		foreach ($datos as $klave => $fila) {
			if ($fila['apex_ei_analisis_fila'] == 'A') {
				$datos[$klave]['activa'] = '1';
				$hay_alta = true;
			} elseif (isset($datos[$klave]['activa'])) {
				unset($datos[$klave]['activa']);
			}
			$datos[$klave]['pregunta'] = $this->encriptar_datos($datos[$klave]['pregunta']);
			$datos[$klave]['respuesta'] = $this->encriptar_datos($datos[$klave]['respuesta']);
		}
		if ($hay_alta) {		//Si hay una fila nueva deshabilito las anteriores
			$this->datos('pregunta_secreta')->set_columna_valor('activa', '0');
		}
		$this->datos('pregunta_secreta')->procesar_filas($datos);
	}
		
	//---- Consultas ---------------------------------------------------
	
	function get_lista_grupos_acceso_proyecto()
	{
		$proyecto = quote($this->s__proyecto);
		$sql = "SELECT 	usuario_grupo_acc,
						nombre,
						descripcion
				FROM 	apex_usuario_grupo_acc
				WHERE 	proyecto = $proyecto;";
		return toba::db()->consultar($sql);
	}
	
	private function encriptar_datos($dato_original)
	{	
		$clave = toba::instalacion()->get_claves_encriptacion();		
		return base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $clave['get'], $dato_original, MCRYPT_MODE_CBC, substr($clave['db'], 0, 8)));		
	}
	
	private function desencriptar_datos($dato_encriptado)
	{
		$clave = toba::instalacion()->get_claves_encriptacion();		
		return mcrypt_decrypt(MCRYPT_BLOWFISH, $clave['get'], base64_decode($dato_encriptado), MCRYPT_MODE_CBC, substr($clave['db'], 0, 8));		
	}
	
	public function get_usuario_arai() {
		return $this->s__usuario_arai;
	}
}
?>