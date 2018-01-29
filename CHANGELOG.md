# SIU-Toba

## CHANGELOG

[CURRENT](https://github.com/SIU-Toba/framework/compare/master...develop)


[3.0.15](https://github.com/SIU-Toba/framework/releases/tag/v3.0.15) (2018-01-29):
- Se agrega carpeta para skin simple

[3.0.14](https://github.com/SIU-Toba/framework/releases/tag/v3.0.14)  (2017-11-06):
- Fix toba_editor, se quita excepción al clonar un componente individualmente
- Fix toba_editor, se quita caracter por fuera de delimitador
- Fix carga de perfiles funcionales con membresias en produccion (credits FJarque)
- Fix lectura de parametros seteados en instancia.ini

[3.0.13](https://github.com/SIU-Toba/framework/releases/tag/v3.0.13)  (2017-10-23):
- Fix al metodo toba_manejador_archivos::chmod_recursivo, faltaba referencia en la llamada recursiva
- Fix al metodo toba_manejador_archivos::eliminar_directorio, fallaba cuando leia un directorio con nombre '0'

[3.0.12](https://github.com/SIU-Toba/framework/releases/tag/v3.0.12)  (2017-10-05):
- Fix al método toba_perfil_datos::get_restricciones_dimension, devolvía una estructura incorrecta (tnx F.Miñola)
- Fix al nombre del parametro en el comando servicios_web para que coincida con la documentación
- Se agrega "/" en el template del alias para el proyecto

[3.0.11](https://github.com/SIU-Toba/framework/releases/tag/v3.0.11)  (2017-10-02):
- Se modifica el template del punto de acceso, solo define constante de metadatos compilados en produccion
- Se actualiza la libreria Guzzle a v6.3
- Se cambia la recuperacion de headers en ejemplo rest de toba_referencia
- Se agrega chequeo por null a la funcion toba_varios::rest_decode
- Se reemplazan llamadas a mantener_estado_sesion() en toba_editor, toba_referencia y toba_testing
- Se reemplazan funciones deprecadas en toba_vinculador y toba_memoria
- Fix a typo en toba_encriptador::cifrar_para_web
- Se quitan referencias a funciones deprecadas en toba_editor
- Se marca toba_usuario::get_perfil_datos como deprecado conforme las subclases
- Se eliminan utilerias del arbol de toba_editor que no se usaban hace tiempo
- Se reemplazan llamadas toba_manejador_archivos::ejecutar --> toba_manejador_procesos::ejecutar
- Fix en documentación para funciones deprecadas
- Fix en actualización de secuencias al procesar el JSON
- Se invierte el orden de los resultados en toba_modelo_instalacion::get_claves_encriptacion
- Se reemplazan llamadas encriptar_con_sal --> toba_hash
- Se pasa a usar hash_equals en las comparaciones de toba_hash y toba_usuario_basico
- Se agrega paquete ioncube/php-openssl-cryptor
- Se deprecan los metodos toba_varios::encriptar_con_sal y toba_varios::get_salt(a remover v3.1.0) [Ver aquí](https://github.com/SIU-Toba/framework/wiki/Funciones-Deprecadas)
- Se deprecan los metodos toba_encriptador::cifrar y toba_encriptador::descifrar(a remover v3.1.0) [Ver aquí](https://github.com/SIU-Toba/framework/wiki/Funciones-Deprecadas)
- La clase toba_encriptador requiere openssl
- Métodos agregados
    * toba_encriptador::encriptar
    * toba_encriptador::desencriptar
    * toba_encriptador::cifrar_para_web
    * toba_encriptador::descifrar_para_web

[3.0.10](https://github.com/SIU-Toba/framework/releases/tag/v3.0.10)  (2017-09-19):
- Se fixea ruta al directorio del proyecto calculada desde toba
- Se fixea el vinculador para que arme correctamente el primer parametro de la URL
- Se modifica constructor en la clase Console_Table
- Se elimina version vieja de js_app_launcher que había quedado en el arbol de directorios

[3.0.9](https://github.com/SIU-Toba/framework/releases/tag/v3.0.9)  (2017-08-28):
- Se fixea la ruta al paquete rest, estaba armando mal el classpath
- Solo se recuperan WS-REST en la operación de servicios consumidos en toba_usuarios

[3.0.8](https://github.com/SIU-Toba/framework/releases/tag/v3.0.8)  (2017-08-15):
- Elimino metodo constructor con formato PHP4.x de PHP_Highlight por incompatibilidad en PHP 5.6.x
- Actualizo Services_JSON: 1.0.3.3 por misma razón

[3.0.7](https://github.com/SIU-Toba/framework/releases/tag/v3.0.7)  (2017-08-14):
- Agregado de método constructor a PHP_Highlight
- Se definen como estáticos varios métodos de toba_fecha
- Actualización de lib Services_JSON: 1.0.3.2
- Se pasa Jasper a paquete sugerido (necesita require por parte del proyecto)

[3.0.6](https://github.com/SIU-Toba/framework/releases/tag/v3.0.6)  (2017-07-24):
- Se actualiza arai-cli a la 2.1 que tiene ventana de preconfiguración

[3.0.5](https://github.com/SIU-Toba/framework/releases/tag/v3.0.5) (2017-07-07):
- Se agrega control a toba_nucleo por si la app no se inicializa en el acceso_rest
- Se fixea error de invocación en la función toba_perfil_datos::get_restricciones_dimension
- Se agrega el comando toba_docker al bin-dir de composer
- Agregado del comando instalar al proyecto toba_usuarios para registrar la bd
- Exportación de secuencias a archivo para permitir trabajo en branches simultáneos
- Quitados hooks de gitflow y comando set_id_branch
- Quitada la restauración del schema de auditoría
- Agregados hooks para gitflow (AVH Edition) y post-checkout para trabajo con branches
- Agregado el comando set_id_branch para permitir trabajar metadatos con branches simultaneos

[3.0.4](https://github.com/SIU-Toba/framework/releases/tag/v3.0.4) (2017-06-27):
- Bugfix a rutas en el lanzador de comandos

[3.0.3](https://github.com/SIU-Toba/framework/releases/tag/v3.0.3) (2017-06-16):
- Se cambia el item de inicio en el proyecto toba_usuarios
- Se fuerza la eliminación de triggers al actualizar la auditoría
- Se agrega la libreria vlucas/dotenvphp para cargar variables de entorno a partir de un archivo .env
- Se modifica el lanzador de comandos de toba (ya no es necesario cargar antes el entorno)
- Se agregan nuevos parametros al comando servicios_web

[3.0.2](https://github.com/SIU-Toba/framework/releases/tag/v3.0.2) (2017-05-17):
- Se quitan las closures en toba_rest
- Se actualiza Numbers_Words a la version correcta
- Actualizada siutoba/rest: 2.0.1
- Se fixea bug en el script de instalación

[3.0.1](https://github.com/SIU-Toba/framework/releases/tag/v3.0.1) (2017-05-15):
- Actualizada siutoba/rest: 2.0.0
- Fix en alta de perfiles funcionales en toba_usuarios
- Fix typo en toba_db

[3.0.0](https://github.com/SIU-Toba/framework/releases/tag/vv3.0.0) (2017-05-04):
- Removida activeCalendar
- Numbers_Words cambia implementacion, reemplazar las llamadas segun formato buscado
  - Constructor: new Numbers_Words_es_Ar ---> new Numbers_Words_Locale_es_AR
  - Formato: 
      * toWords($importe) ---> toAccountable($importe, 'es_AR')
      * toWords($importe,0,false,false) ---> toAccountableWords( 'ARS', $importe, false, false, true)
      * toCurrencyWords('ARS', $importe) ---> toAccountable($importe, 'es_AR')
- phpExcel
  - La constante FORMAT_CURRENCY_USD_CUSTOM se paso a la clase toba_vista_excel
  - La constante FORMAT_DATE_DATETIMEFULL se paso a la clase toba_vista_excel
- securimage (via toba_imagen_captcha)
  - Ya no se persiste en memoria el indice 'texto-captcha'
  - Ya no se persiste en memoria el indice 'tamanio-texto-captcha'
  - El constructor espera un arreglo de opciones, no un string con el codigo
  - Se elimina metodo set_path_fuentes
  - El metodo set_codigo no persiste el valor en session, por tanto el check automatico falla
  - Agregado el metodo get_codigo para permitir check manual
- Ezpdf
  - Se agrega utf8_encode a los datos que debe mostrar el PDF (requerido por la libreria)
- Removida Apache Shindig (si la necesita debe proveerla el proyecto)
- Removida WSF-PHP (queda clase toba_solicitud_servicio_web)
- Removida simpleSamlPHP
  - Se deben copiar los archivos de configuración en php/3ros/simplesamlphp/ a la carpeta correspondiente en vendor
- Removida jscomp por falta de uso
- Removida librería interna de impresión por falta de uso
- Removida phpDocumentor  
- Movida RDILib de php/contrib/lib a composer
- Agregado de modo mantenimiento para WS Rest
- Compatibilidad con PHP 7.0
- Incorporación de Json Web Tokens para autenticación WS Rest
- Agregado de Jasper via composer
- La autenticacion via saml_onelogin puede manejar varios SP
- Se agrega mecanismo para dumpear datos de configuración en las clases de autenticación
- En el comando de exportación de usuarios para SIU-Araí se puede especificar el formato de entrada de los datos
- El proyecto toba_usuarios ahora loguea correctamente el usuario conectado cuando la instancia posee auditoría
- La validación de estructura de datos en toba_ei_cuadro toma en cuenta la existencia de valores nulos
- En la clase toba_parametros se exponen los siguientes métodos:
    *  get_redefinicion_parametro
    *  get_redefinicion_parametro_runtime
- Actualización de librerías varias (mas info composer.lock)
    *  Guzzle: 6.2.3
    *  Securimage: 3.6.5
    *  Onelogin: 2.10.5
    *  SimpleSamlPHP: 1.14.13
    *  PHPMailer: 5.2.23
    *  PHPExcel: 1.8.1
    *  PDF-PHP: 0.12.32 (anteriormente EasyPDF)
    *  Simpletest: 1.1.7
    *  Jwt-util: 1.0.2
    *  Rest: 1.1.8
    *  Arai-cli: 2.0.0
    *  Arai-json-migrator: 1.0.1
    *  Arai-json-parser: 1.0.0
    *  RDI: 0.9.7
    *  Zend-Escaper: 2.2.10
