<?php
/**
 * Clase que implementa el comando sobre la bd.
 *
 * Class comando_base.
 * @package consola
 */
require_once('comando_toba.php');
class comando_base extends comando_toba
{
        /**
         * Retorna informaci�n acerca del comando
         *
         * @return string
         */
        static function get_info()
        {
                return 'Administracion de BASES de DATOS';
        }

        /**
         * Muestra un help de uso
         *
         */
        function mostrar_observaciones()
        {
                $this->consola->mensaje("INVOCACION: toba base OPCION [-d id_base]");
                $this->consola->enter();
        }

        /**
         * Determina si existe el parametro que indica sobre que base operar
         *
         * @return boolean
         */
        function tiene_definido_base()
        {
                $param = $this->get_parametros();
                if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
                        return true;
                } else {
                        return false;
                }
        }

        /**
         * Devuelve un string con los parametros de la base
         *
         * @return string
         */
        function get_info_extra()
        {
                if ($this->tiene_definido_base()) {
                        $db = $this->get_id_base_actual();
                        $param = $this->get_instalacion()->get_parametros_base( $db );
                        $salida = "";
                        foreach ($param as $id => $valor) {
                                $salida .= "$id: $valor\n";
                        }
                        return $salida;
                }
        }

        /**
         * Muestra un listado de las bases disponibles
         *
         * @gtk_icono info_chico.gif
         */
        function opcion__listar()
        {
                $this->mostrar_bases_definidas();
        }


        /**
         * Agrega la definici�n de una base al archivo bases.ini.
         *
         * @param array $parametros
         * @consola_parametros Opcionales: [-h 'ubicacion bd'] [-p 'puerto'] [-u 'usuario bd'] [-b nombre bd] [-c 'archivo clave bd'] [--schema-toba 'schemaname'] [-o base_origen] toma los datos de otra definicion
         * @gtk_icono nucleo/agregar.gif
         * @gtk_param_extra registrar_base
         */
        function opcion__registrar($parametros=null)
        {
                if (isset($parametros)) {
                        list($def, $origen, $datos) = $parametros;
                }
                //--- Nombre del registro
                if (!isset($def)) {
                        $def = $this->get_id_base_actual();
                }
                if ( $this->get_instalacion()->existe_base_datos_definida( $def ) ) {
                        throw new toba_error( "Ya existe una base definida con el ID '$def'");
                }

                //--- Base de origen
                $param = $this->get_parametros();
                if (!isset($origen) && isset($param['-o']) &&  (trim($param['-o']) != '') ) {
                        $origen =  $param['-o'];
                }
                if (isset($origen)) {
                        if (! $this->get_instalacion()->existe_base_datos_definida($origen)) {
                                throw new toba_error( "No existe la base origen '$origen'");
                        }
                        $datos = $this->get_instalacion()->get_parametros_base($origen);
                }

                if (! isset($datos)) {
                   $datos = $this->definir_parametros_base();
                }

                //--- Registraci�n
                $this->get_instalacion()->agregar_db( $def, $datos );
                if (! isset($datos['encoding']) || trim($datos['encoding']) == '') {
                        $this->get_instalacion()->determinar_encoding( $def );
                }
        }


        /**
         * Elimina la definici�n de la base en bases.ini
         *
         * @gtk_icono borrar.gif
         * @gtk_separador 1
         */
        function opcion__desregistrar()
        {
                $i = $this->get_instalacion();
                $def = $this->get_id_base_actual();
                $param = $this->get_parametros();
                if ( $i->existe_base_datos_definida( $def ) ) {
                        $this->consola->enter();
                        $this->consola->subtitulo("DEFINICION: $def");
                        $this->consola->lista_asociativa( $i->get_parametros_base( $def ), array('Parametro','Valor') );
                        $this->consola->enter();
                        if (isset($param['--no-interactivo'])) {
                            $i->eliminar_db( $def );
                        } elseif ($this->consola->dialogo_simple("Desea eliminar la definicion?")) {
                            $i->eliminar_db( $def );
                        }
                } else {
                        throw new toba_error( "NO EXISTE una base definida con el ID '$def'");
                }
        }


        /**
        * Crea f�sicamente la base de datos
         *
        * @gtk_icono nucleo/agregar.gif
        */
        function opcion__crear()
        {
                $def = $this->get_id_base_actual();
                if( $this->get_instalacion()->existe_base_datos( $def ) !== true ) {
                        $this->get_instalacion()->crear_base_datos( $def );
                } else {
                        throw new toba_error( "La base '$def' ya est� creada en el MOTOR");
                }
        }


        /**
        * Elimina f�sicamente la base de datos
         *
        * @gtk_icono borrar.png
        */
        function opcion__eliminar()
        {
                $def = $this->get_id_base_actual();
                if ( $this->get_instalacion()->existe_base_datos( $def ) ) {
                        $this->consola->enter();
                        $this->consola->subtitulo("BASE de DATOS: $def");
                        $this->consola->lista_asociativa( $this->get_instalacion()->get_parametros_base( $def ), array('Parametro','Valor') );
                        $this->consola->enter();
                        if ( $this->consola->dialogo_simple("Desea eliminar la BASE de DATOS?") ) {
                                $this->get_instalacion()->borrar_base_datos( $def );
                        }
                } else {
                        throw new toba_error( "NO EXISTE una base '$def' en el MOTOR");
                }
        }

        /**
        * Ejecuta un archivo sql
        *
        * @param string $archivo
        * @consola_parametros [-a archivo]
        * @gtk_icono sql.gif
        * @gtk_param_extra ejecutar_sql
        */
        function opcion__ejecutar_sql($archivo=null)
        {
                if (! isset($archivo)) {
                        $param = $this->get_parametros();
                        if ( isset($param['-a']) &&  (trim($param['-a']) != '') ) {
                                $archivo = $param['-a'];
                        } else {
                                throw new toba_error("Es necesario indicar el archivo a ejecutar. Utilice el modificador '-a'");
                        }
                }
                $db = $this->get_instalacion()->conectar_base($this->get_id_base_actual());
                $db->ejecutar_archivo($archivo);
        }

        /**
        * Chequea la conexi�n con la base
        *
        * @gtk_icono fuente.png
        */
        function opcion__test_conexion()
        {
                $def = $this->get_id_base_actual();
                $existe = $this->get_instalacion()->existe_base_datos( $def, array(), true );
                if ($existe === true) {
                        $this->consola->mensaje('Conexion OK!');
                } else {
                        $this->consola->error("No es posible conectarse a '$def': $existe");
                }
        }

        /**
         * Actualiza las secuencias de la base, solo funciona con PostgreSQL
         *
         */
        function opcion__actualizar_secuencias()
        {
                $this->consola->mensaje("Actualizando secuencias", false);
                $db = $this->get_instalacion()->conectar_base($this->get_id_base_actual());
                $secuencias = $db->get_lista_secuencias();
                $db->abrir_transaccion();
                foreach ($secuencias as $datos) {
                        $sql_nuevo = "SELECT
                                                                max(CASE {$datos['campo']}::varchar ~ '^[0-9]+$' WHEN true THEN {$datos['campo']}::bigint ELSE 0 END) as nuevo
                                                  FROM {$datos['tabla']}
                        ";
                        $res = $db->consultar($sql_nuevo, null, true);
                        $nuevo = $res[0]['nuevo'];
                        //Si no hay un maximo, es el primero del grupo
                        if ($nuevo == NULL) {
                                $nuevo = 1;
                        }

                        $sql = "SELECT setval('{$datos['nombre']}', $nuevo)";
                        $db->consultar( $sql );
                        $this->consola->progreso_avanzar();
                }
                $db->cerrar_transaccion();
                $this->consola->progreso_fin();
        }


        /**
        * Determina sobre que base definida en 'info_bases' se va a trabajar
        *
        */
        private function get_id_base_actual()
        {
                $param = $this->get_parametros();
                if ( isset($param['-d']) &&  (trim($param['-d']) != '') ) {
                        return $param['-d'];
                } else {
                        throw new toba_error("Es necesario indicar el ID de la definici�n de la base. Utilice el modificador '-d'");
                }
        }

        private function definir_parametros_base()
        {
             $param = $this->get_parametros();
             $datos = array();
                 //---- Datos
             $form = $this->consola->get_formulario("Definir una nueva BASE de DATOS");
             $datos['motor'] = $this->definir_motor($param, $form);
             $datos['profile'] = $this->definir_profile_motor($param, $form);
             $datos['usuario'] = $this->definir_usuario_motor($param, $form);
             $datos['clave'] = $this->definir_clave_motor($param, $form);
             $datos['base'] = $this->definir_base_motor($param, $form);
             $datos['puerto'] = $this->definir_puerto_motor($param, $form);
             $datos['schema'] = $this->definir_schema_toba($param, $this->id_instancia_actual,  $form);

             $encoding_fijado = $this->definir_encoding($param);
             if (! is_null($encoding_fijado)) {
                 $datos['encoding'] = $encoding_fijado;
             }

             if (isset($param['--no-interactivo'])) {
                 $form->desactivar_confirmacion_datos();
             }

             if (empty($datos) || $form->tiene_campos()) {
                 $datos = array_merge($datos, $form->procesar());
             }

             return $datos;
        }

        protected function definir_motor($param, $form=null)
        {
             $nombre_parametro = array( '-m', '--motor', 'toba-base-motor');
             $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
             if ($result['invalido']) {
                     if (! is_null($form)) {
                         $form->agregar_campo( array( 'id' => 'motor',  'nombre' => 'MOTOR (ej. postgres7)' ));
                     } else {
                         return 'postgres7';
                     }
             }
             return $result['resultado'];
        }

        protected function definir_schema_toba($param, $id_instancia, $form=null)
        {
             $nombre_parametro = array('--schema-toba');
             $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
             if ($result['invalido']) {
                     if (! is_null($form)) {
                         $form->agregar_campo( array( 'id' => 'schema', 'nombre' => 'SCHEMA (ej. public)' , 'obligatorio' => false));
                     } else {
                         return $id_instancia;
                     }
             }
             return $result['resultado'];
        }

        protected function definir_profile_motor($param, $form=null)
        {
            $nombre_parametro = array('-h', '--base-profile', 'toba-base-profile');
            $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
            if ($result['invalido']) {
                if (! is_null($form)) {
                    $form->agregar_campo( array( 'id' => 'profile',	'nombre' => 'HOST/PROFILE (ej. localhost)' ));
                } else {
                    return '127.0.0.1';
                }
            }
            return $result['resultado'];
        }

        protected function definir_usuario_motor($param, $form=null)
        {
            $nombre_parametro = array('-u', '--base-usuario', 'toba-base-usuario');
            $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
            if ($result['invalido']) {
                if (! is_null($form)) {
                  $form->agregar_campo( array( 'id' => 'usuario', 'nombre' => 'USUARIO (ej. postgres)' ));
                } else {
                   return 'postgres';
                }
            }
            return $result['resultado'];
        }

        protected function definir_base_motor($param, $form=null)
        {
            $nombre_toba = 'toba_'.toba_modelo_instalacion::get_version_actual()->get_release('_');
            $nombre_parametro = array('-b', '--base-nombre', 'toba-base-nombre');
            $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
            if ($result['invalido']) {
                if (! is_null($form)) {
                  $form->agregar_campo( array( 'id' => 'base', 	'nombre' => 'BASE' ));
                } else {
                  return $nombre_toba;
                }
            }
            return $result['resultado'];
        }

        protected function definir_puerto_motor($param, $form=null)
        {
            $nombre_parametro = array('-p', '--base-puerto', 'toba-base-puerto');
            $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
            if ($result['invalido']) {
                return '5432';
            }
            return $result['resultado'];
        }

        protected function definir_clave_motor($param, $form=null)
        {
            $nombre_parametro = array('-c', '--archivo-clave-bd', 'toba-archivo-clave-bd');
            $clave = null;
            do {
                 $ind = current($nombre_parametro);
                 $es_invalido = (! isset($param[$ind]));
                 if (! $es_invalido) {
                         $clave = $this->recuperar_contenido_archivo($param[$ind]);
                 }
            } while($es_invalido && next($nombre_parametro) !== false);

            if ($es_invalido) {
                if (! is_null($form)) {
                         $form->agregar_campo( array( 'id' => 'clave', 'nombre' => 'CLAVE', 'obligatorio' => false ));
                 } else {
                     $clave = 'postgres';
                }
            }
            return $clave;
        }

        protected function definir_encoding($param, $form=null)
        {
            $nombre_parametro = array('-e', '--encoding-bd', 'toba-encoding-bd');
            $result = $this->recuperar_dato_y_validez($param, $nombre_parametro);
            return (! $result['invalido']) ? $result['resultado']: null;
        }

        protected function recuperar_dato_y_validez($param, $nombre_parametro)
        {
                $resultado = null;
                do {
                        $ind = current($nombre_parametro);
                        $es_invalido = (! isset($param[$ind]));
                        if (! $es_invalido) {
                                $resultado = $param[$ind];
                        }
                } while ($es_invalido && next($nombre_parametro) !== false);

                return array('invalido' => $es_invalido, 'resultado' => $resultado);
        }

        function recuperar_contenido_archivo($nombre)
        {
                $resultado = '';
                if (file_exists($nombre)) {
                        $resultado = file_get_contents($nombre);
                }
                return $resultado;
        }
}
?>