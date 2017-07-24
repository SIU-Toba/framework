# SIU-Toba

## CHANGELOG

[CURRENT](https://github.com/SIU-Toba/framework/compare/master...develop)

[3.0.6](https://github.com/SIU-Toba/framework/tree/3.0.6)
- Se actualiza arai-cli a la 2.1 que tiene ventana de preconfiguración

[3.0.5](https://github.com/SIU-Toba/framework/tree/3.0.5) (2017-07-07):
- Se agrega control a toba_nucleo por si la app no se inicializa en el acceso_rest
- Se fixea error de invocación en la función toba_perfil_datos::get_restricciones_dimension
- Se agrega el comando toba_docker al bin-dir de composer
- Agregado del comando instalar al proyecto toba_usuarios para registrar la bd
- Exportación de secuencias a archivo para permitir trabajo en branches simultáneos
- Quitados hooks de gitflow y comando set_id_branch
- Quitada la restauración del schema de auditoría
- Agregados hooks para gitflow (AVH Edition) y post-checkout para trabajo con branches
- Agregado el comando set_id_branch para permitir trabajar metadatos con branches simultaneos

[3.0.4](https://github.com/SIU-Toba/framework/tree/3.0.4) (2017-06-27):
- Bugfix a rutas en el lanzador de comandos

[3.0.3](https://github.com/SIU-Toba/framework/tree/3.0.3) (2017-06-16):
- Se cambia el item de inicio en el proyecto toba_usuarios
- Se fuerza la eliminación de triggers al actualizar la auditoría
- Se agrega la libreria vlucas/dotenvphp para cargar variables de entorno a partir de un archivo .env
- Se modifica el lanzador de comandos de toba (ya no es necesario cargar antes el entorno)
- Se agregan nuevos parametros al comando servicios_web

[3.0.2](https://github.com/SIU-Toba/framework/tree/3.0.2) (2017-05-17):
- Se quitan las closures en toba_rest
- Se actualiza Numbers_Words a la version correcta
- Actualizada siutoba/rest: 2.0.1
- Se fixea bug en el script de instalación

[3.0.1](https://github.com/SIU-Toba/framework/tree/3.0.1) (2017-05-15):
- Actualizada siutoba/rest: 2.0.0
- Fix en alta de perfiles funcionales en toba_usuarios
- Fix typo en toba_db

[3.0.0](https://github.com/SIU-Toba/framework/tree/v3.0.0) (2017-05-04):
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
