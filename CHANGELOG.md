# SIU-Toba

## CHANGELOG

[CURRENT](https://github.com/SIU-Toba/framework/compare/master...develop)

[3.3.8](https://github.com/SIU-Toba/framework/releases/tag/v3.3.8) (2020-06-24)
- Se agregan parametros para version de Api a toba_rest (Faltante de v3.6.6)
- Se agrega un recurso de información basico a toba_referencia a modo de ejemplo

[3.3.7](https://github.com/SIU-Toba/framework/releases/tag/v3.3.7) (2020-06-23)
- Se eliminan los tags html remanentes en la construccion del mensaje del log.

[3.3.6](https://github.com/SIU-Toba/framework/releases/tag/v3.3.6) (2020-06-03)
- Se actualizan los paquetes
     * siu-toba/rest: v2.0.4
- Se actualiza el paquete
     * nikic/php-parser: v4.5.0
- Se eliminan atributos innecesarios que llegan desde IDP SAML
- Security update al paquete
     * phpmailer/phpmailer: v6.1.6
- Se corrige bug en toba_logger introducido al redirigir la salida
- Se actualiza cliente para mostrar documentacion de la api rest: 
     * swagger-ui: v3.25.3
- Se agregan parametros a proyecto.ini para especificar la version interna de la API que ya no va pegada a la del proyecto
- Se agrega posibilidad de servir varias versiones de una api rest en simultaneo
- Se vuelve obligatorio especificar una version para el sistema en proyecto.ini
- Se incrementa el limite de caracteres en la ER para validacion de emails
- Se agrega posibilidad de redirigir la salida del logger a stdout o stderr ademas de la tipica a archivo
- Se agrega posibilidad de especificar autenticacion para REST via variables de entorno [Ver aqui](https://github.com/SIU-Toba/framework/wiki/Envvar-Config#caso-especial-para-web-services)

[3.3.5](https://github.com/SIU-Toba/framework/releases/tag/v3.3.5) (2020-04-30)
- Actualizacion de paquetes JS:
   * jquery: v3.5.0
   * jquery-migrate: v3.2.0
   * siu-js-app-launcher: v1.0.5
- Agrega modificador al comando *instancia regenerar* para evitar pregunta sobre exportar metadatos
- Mejora en el parseo de variables de entorno en toba_config (credits andres-blanco)
- Bugfix en toba_perfil_datos, se pisaba el alias cuando la tabla estaba mas de una vez involucrada como gatillo 
- Renombra el paquete CKEditor --> CKEditor4 y actualiza a v4.13

[3.3.4](https://github.com/SIU-Toba/framework/releases/tag/v3.3.4) (2020-03-30)
- Se corrige bug al cargar el punto de montaje para los items con acciones (PHP Plano)
- Se cambia el proveedor y version de los siguientes paquetes:
   * facebook/webdriver --> PHP-Webdriver/webdriver
   * zendframework/zend-escaper --> laminas/laminas-escaper
- Se actualiza el paquete siu/security-multitool @v1.1.0 por cambio de proveedor en dependencias
- Se corrigen typos en las clases que permiten testing via Selenium
- Se corrige bug en cambios al autoload realizado @v3.3.2
- Se corrige bug en manejador_sesiones que prevenia la actualizacion del perfil de datos al cambiar la cuenta.
- Se agrega operacion a toba_usuarios para asignar 2FA por perfil funcional.
- Los msg a usuario se transforman en comentarios en archivo env.

[3.3.3](https://github.com/SIU-Toba/framework/releases/tag/v3.3.3) (2020-03-17)
- Hotfix el Hook para Arai no instanciaba correctamente el CryptoService lo que generaba un error por libreria faltante

[3.3.2](https://github.com/SIU-Toba/framework/releases/tag/v3.3.2) (2020-02-20)
- Se actualiza la version del paquete siu/security-multitool para evitar conflicto de providers (#75)
- Se mejora la generacion de autoload via el paquete nikic/php-parser
- Fix a warning al generar el autoload de los proyectos
- Se agrega posibilidad de instalar el framework aun sin proyectos internos

[3.3.1](https://github.com/SIU-Toba/framework/releases/tag/v3.3.1) (2020-02-06)
- Actualiza version de CKEditor (implica cambio en URL del asset)
- Mejora chequeo en condicional del metodo get_nombre_clase_extendida
- Bugfix a la lectura de variables de entorno para configurar el manejo de sesiones (credits svier)
- Bugfix en toba_ei_cuadro_salida_html, se recupera inicializacion de variable $css y agrega parametro actual faltante en invocacion (credits esassone)

[3.3.0](https://github.com/SIU-Toba/framework/releases/tag/v3.3.0) (2019-12-27)
- Se fija como version minima de PHP 7.3.0
- Se actualiza el item de login en toba_usuarios y toba_referencia, tambien se eliminan objetos huerfanos
- Se agrega FK a la tabla apex_grupo_acc_restriccion_funcional
- Se compatibiliza el mecanismo de actualizacion de secuencias y recuperacion de definicion de tablas con Postgresql 12
- Se actualizan los siquientes paquetes:
    * symfony/yaml: v4.4
    * guzzlehttp/guzzle: v6.5
    * michelf/php-markdown: v1.9
    * phpmailer/phpmailer: v6.1
    * phpoffice/phpspreadsheet: v1.10
    * vlucas/dotenv: v3.6
    * psr/log: v1.1
    * siu/manejador-salida-toba: v1.1
    * simpletest/simpletest: v1.2
- Se brinda la posibilidad de definir si la salida de una vista se enviara inline o como adjunto
- Se actualiza la interface del tipo de pagina popup para compatibilizar con los cambios en el manejador de salida
- Se agrega la posibilidad de cambiar el pwd de un usuario determinado via comando de consola
- Se agrega la posibilidad de marcar la instalacion para solicitar un segundo factor luego de loguear con un IDP
- Se eliminan los assets correspondientes a Yahoo/YUI por cuestiones de seguridad y el menu basado en las mismas
- Las membresias de perfiles funcionales se exportan en un archivo separado en una instalacion de produccion
- Se actualiza el paquete siu/interfaces-manejador-salida-toba @v1.2 por bugfixes (rompe BC)
- La exportacion via toba_vista_excel ahora tendra el formato xlsx
- Se simplifica tratamiento de ef_fieldset en el formulario
- Se pasa el paquete siu/rdi a suggested

[3.2.15](https://github.com/SIU-Toba/framework/releases/tag/v3.2.15) (2020-04-30)
- Actualizacion de seguridad de JQuery, actualiza tambien siu/js-app-launcher (merge desde develop)
- Agrega modificador al comando ''instancia regenerar'' para evitar exportar los datos (merge desde develop)
- Mejora en el parseo de variables de entorno en toba_config (merge desde develop)
- Bugfix en toba_perfil_datos, se pisaba el alias cuando la tabla estaba mas de una vez involucrada como gatillo (merge desde develop)

[3.2.14](https://github.com/SIU-Toba/framework/releases/tag/v3.2.14) (2020-03-27)
- Fuerza la recarga del perfil de datos al cambiar de cuenta (merge desde develop)

[3.2.13](https://github.com/SIU-Toba/framework/releases/tag/v3.2.13) (2020-03-20)
- Actualiza autoload para reincorporar clase usada por el editor (merge desde develop)

[3.2.12](https://github.com/SIU-Toba/framework/releases/tag/v3.2.12) (2020-02-19)
- Actualiza el paquete siu/security-multitool (merge desde develop)

[3.2.11](https://github.com/SIU-Toba/framework/releases/tag/v3.2.11) (2020-02-06)
- Bugfix a la lectura de variables de entorno para configurar el manejo de sesiones (merge desde develop)
- Se compatibiliza el mecanismo de actualizacion de secuencias y recuperacion de definicion de tablas con Postgresql 12

[3.2.10](https://github.com/SIU-Toba/framework/releases/tag/v3.2.10) (2019-11-25)
- Fix a la desactivacion de eventos (con manejador-bootstrap)
- Se actualizan los paquetes:
  * robrichards/xmlseclibs @v3.0.4 (PR#67)
  * phpoffice/phpspreadsheet @1.8.0 (PR#68)
- Se permite fijar el save_handler para las sessiones via variable de entorno (PR#66)
- Se activa el menu via appLauncher al invocar la sincronizacion con SIU-Arai (PR#63)
- Se incorpora el parametro 'usa_proxy_vars' en saml_onelogin.ini para marcar cuando nos encontramos detras de un proxy (PR#62)
- Se setea el header content-disposition para la descarga en la vista JasperReports(PR#60)

[3.2.9](https://github.com/SIU-Toba/framework/releases/tag/v3.2.9) (2019-10-18)
- Actualiza la version del paquete siu-app-launcher@1.0.4
- Implementa modo no interactivo para la creacion del usuario admin de la instalacion

[3.2.8](https://github.com/SIU-Toba/framework/releases/tag/v3.2.8) (2019-10-09)
- Se chequea la existencia de la carpeta node_modules en el script que instala assets
- Se agregan opciones nuevas al archivo smtp.ini
- Se agrega comando para recompilar unicamente perfiles funcionales
- Se agrega un comando especifico para verificar existencia de la instancia Toba

[3.2.7](https://github.com/SIU-Toba/framework/releases/tag/v3.2.7) (2019-09-19)
- Se agrega posibilidad de especificar el encoding cliente al momento de registrar la base de datos
- Se corrige tamaño de imagenes en el selector de toba_editor (credits lcassettai)
- Se permite utilizar un URI que no termine en rest para la documentacion de la API

[3.2.6](https://github.com/SIU-Toba/framework/releases/tag/v3.2.6) (2019-08-16)
- Se adelanta la carga de jquery y derivados
- Se incorpora la version del proyecto como atributo "version" de las provision del mismo
- Se agrega toba_modelo_instalacion al esquema de configuraciones via toba_config

[3.2.5](https://github.com/SIU-Toba/framework/releases/tag/v3.2.5) (2019-08-06)
- Bugfix en la lectura del archivo de configuracion para ldap

[3.2.4](https://github.com/SIU-Toba/framework/releases/tag/v3.2.4) (2019-06-28)
- Bugfix en el editor de columnas externas del DT en el proyecto toba_editor
- Bugfix en toba_sincronizador_archivos para evitar warnings durante la exportacion de metadatos

[3.2.3](https://github.com/SIU-Toba/framework/releases/tag/v3.2.3) (2019-06-04)
- Bugfix en toba_varios::array_a_latin1 y toba_varios::array_a_utf8
- Bugfix en regeneracion al eliminar los perfiles funcionales editables
- Fix en toba_rest cuando se setea la versión de la api a partir de la del proyecto
- Nueva opción `--usuario-email-admin` que permite especificar el email para la cuenta administrativa al momento de instalar el framework
- Update de libreria:
   * jquery: v3.4
- Reincorpora paquete "siu/arai-json-migrator" eliminado por error
- Bugfix en toba_usuarios al generar la clave aleatoria, retornaba bytes en lugar de un string
- Agrega metodo a toba_usuario_basico para recuperar atributos del token saml
- Agrega excepcion al enviar caracteres invalidos a la generación via PHPSpreadsheet

[3.2.2](https://github.com/SIU-Toba/framework/releases/tag/v3.2.2) (2019-03-13)
- Agrega metodo a toba_usuario_basico para obtener el identificador en arai-usuario para la cuenta actualmente logueada
- Nueva opción `--excluir-bloqueads` para el comando `exportar_usuarios_arai`, evita exportar las cuentas bloqueadas

[3.2.1](https://github.com/SIU-Toba/framework/releases/tag/v3.2.1) (2019-02-28)
- Undo de braindead merge by gitflow
- Recompilado de metadatos de toba_editor

[3.2.0](https://github.com/SIU-Toba/framework/releases/tag/v3.2.0) (2019-02-22)
- Se agrega ejemplo de uso de Araí-Reportes a toba_referencia
- Bugfix en el metodo toba_parametros::get_proyecto() pasaba mal el id de la instancia
- Fix en ef_multiseleccion para que se muestre correctamente la imagen de error al ser usados en ei_filtro
- Se mejora la autoconfiguración de clientes rest via SIU-Araí
- El hook de contacto con Arai pospone la carga del archivo con las claves al momento estrictamente necesario
- Bugfix en exportacion pdf y excel de columnas compuestas de toba_ei_filtro
- Actualizacion librerias
  * vlucas/phpdotevn: v3.1.0
  * ircmaxell/random-lib cambia de proveedor a paragonie/random-lib @2.0
- Se modifican estilos en la pantalla de login generica para que al cambiar el manejador de salida no se vea desalineado
- En la API JS se agregan parametros al metodo toba::set_menu_popup para poder reusarlo en los menu de manejadores de salida
- Fix en toba_basic_logger para evitar leak de memoria cuando en la traza se devuelven objetos
- Fix en toba_autenticacion_saml_onelogin cuando se permite usar la autenticacion basica de Toba
- Se incorpora Workaround para salvaguardar bug en Yarn
- Se incorpora toba_ei_calendario al esquema de manejadores de salida
- Se agrega un ejemplo de manejador-salida parcial que deriva del paquete siu/manejador-salida-boostrap
- El SP Onelogin va a correr en modo estricto (requiere certificados) en una instancia de produccion a menos que se especifique lo contrario 
- Actualizacion librerias
  * onelogin/php-saml: v3.1.0
- El comando servicios_web configurar-cli agrega parametro para indicar el tipo de autenticacion
- Fix en toba_factory_service para permitir manejadores de salida parciales
- El ef_fecha muestra un rango de años fijo en el cliente (no relacionado con la validacion)
- El log de WS extiende desde Psr\Log 
- Se modifica tipclick.js y se agrega una clase css basica al archivo toba.css del template del proyecto
- Se agregan clases css a las tablas que contienen los efs de seleccion multiple
- Se agrega la imagen del calendario al proceso de migración ya que cambia su ruta
- Se agrega la accion predefinida Arai-Reportes para los eventos
- Se crea la clase toba_vista_arai_reportes
- Se agrega un cliente rest para JasperReports
- Se modifica la componente JS del ef_fecha para proveer un datepicker compatible con bootstrap
- Se modifican los componenetes toba_ei_esquema y toba_ei_grafico para incluir los namespaces de JpGraph
- Se actualiza JpGraph v4.2.0 y se le agrega namespaces de manera propia.
- Se actualiza operacion de armado de menues en toba_usuarios
- Se eliminan viejas versiones de JQuery, JQuery-ui dentro de la carpeta JS de Toba (requiere re-escritura en caso de URL hardcoded)
- Actualizacion librerias JS e inclusion como recurso basico:
  * JQuery: v3.3.1
  * JQuery-ui: v1.12.1 (en directorio /js/utilidades/jquery-ui)
  * Agrega JQuery-migrate: v.3.0
- Se modifica ef_editable_fecha y ef_editable_fecha_hora para compatibilizar el funcionamiento
- Se reemplaza el calendario anterior en JS por jquery-ui::datepicker (ojo con las redefiniciones JS!!)
- Se elimina el calendario del DOM al seleccionar una fecha (mejora problema visual en manejador-salida-bootstrap)
- Se deja de usar toba_notificacion para informar un error en un servicio ajax, se hace echo del msg.
- Se desactiva el uso del manejador-salida-bootstrap en toba_usuarios para no forzar a su instalación por defecto
- Bugfix a la ejecucion de test desde consola
- Se agrega siu/manejador-salida-bootstrap como paquete sugerido
- Se traslada parte de la generación del marcado HTML al paquete siu/manejador-salida-toba

[3.1.14](https://github.com/SIU-Toba/framework/releases/tag/v3.1.14) (2019-10-15):
- Se limita la version de la lib php-saml a la rama 2.16.x
- Se implementa modo no interactivo al comando crear_usuario (backport from develop)
- Se agregan opciones nuevas al archivo smtp.ini (backport from develop)
- Se agrega comando para recompilar unicamente perfiles funcionales (backport from develop)
- Se agrega un comando especifico para verificar existencia de la instancia Toba (backport from develop)

[3.1.13](https://github.com/SIU-Toba/framework/releases/tag/v3.1.13) (2019-09-19):
- Se incorpora la posibilidad de especificar el encoding al comando base::registrar (merge desde develop)

[3.1.12](https://github.com/SIU-Toba/framework/releases/tag/v3.1.12) (2019-08-16):
- Se incorpora la version del proyecto como atributo de las provisions del mismo (merge desde develop)
- Suma toba_modelo_instalacion al esquema via toba_config (merge desde develop)
- Fix en toba_rest cuando se setea la versión de la api a partir de la del proyecto (merge PR#44)
- Bugfix en la lectura del archivo de configuracion para ldap (merge desde develop)

[3.1.11](https://github.com/SIU-Toba/framework/releases/tag/v3.1.11) (2019-06-03):
- Fix en toba_varios::array_a_utf8 y toba_varios::array_a_latin1, ahora usan las funciones utf8_e_seguro y utf8_d_seguro (merge develop)
- Se ajustan los nombres de paquetes al formato composer 2.0 (lowercase)

[3.1.10](https://github.com/SIU-Toba/framework/releases/tag/v3.1.10) (2019-04-30):
- Actualizacion librerias:
  * onelogin/php-saml: v2.15.0
  * phpoffice/phpspreadsheet: v1.6.0
  * rospdf/pdf-php: v0.12.51
  * vlucas/phpdotenv: v2.6.1
  * phpmailer/phpmailer: v6.0.7
  * guzzlehttp/psr7: v1.5.2 
- Reintegra el paquete siu/arai-json-migrator quitado por error

[3.1.9](https://github.com/SIU-Toba/framework/releases/tag/v3.1.9) (2019-04-09):
- Bugfix en el metodo toba_parametros::get_proyecto() pasaba mal el id de la instancia (merge desde 3.2.0)

[3.1.8](https://github.com/SIU-Toba/framework/releases/tag/v3.1.8) (2019-03-13):
- Agrega metodo para obtener el identificador de usuario en Arai-Usuarios para la cuenta actualmente logueada (merge desde develop)

[3.1.7](https://github.com/SIU-Toba/framework/releases/tag/v3.1.7) (2019-02-13)
- Se mejora la autoconfiguración de apis rest via arai-cli (merge @develop)
- Se pospone la carga del archivo de claves de arai (merge @develop)

[3.1.6](https://github.com/SIU-Toba/framework/releases/tag/v3.1.6) (2019-01-15)
- Modifica el hook de Toba para Registry, remueve codigo no necesario
- Modifica toba_version quitando la opcion pre-alpha y agregando la opcion dev

[3.1.5](https://github.com/SIU-Toba/framework/releases/tag/v3.1.5) (2018-12-07)
- Fix en verificacion de versiones compatibles de Arai (merge desde develop)
- Acualizacion de seguridad de librerias:
  * phpmailer/phpmailer: v6.0.6
  * phpoffice/phpspreadsheet: v1.5.1

[3.1.4](https://github.com/SIU-Toba/framework/releases/tag/v3.1.4) (2018-11-16)
- Quita el paquete simplesamlphp/simplesamlphp del requiere en composer

[3.1.3](https://github.com/SIU-Toba/framework/releases/tag/v3.1.3) (2018-10-29)
- Bugfix en toba_analizador_logger, se invocaba mal una variable estatica

[3.1.2](https://github.com/SIU-Toba/framework/releases/tag/v3.1.2) (2018-10-25)
- Merge desde 3.0.31 con fix a toba_ini
- Se agrega metodo para acceder al objeto db que se pasa al modelo durante la instanciacion via toba_modelo_catalogo
- Se agrega advertencia en pantalla inicia de toba_editor cuando se usa el id desarrolo por defecto

[3.1.0](https://github.com/SIU-Toba/framework/releases/tag/v3.1.0) (2018-09-19)
- Se actualiza el link de la pantalla inicial del editor para que apunte al changelog
- Se cambia el tipo del campo estilo en eventos y el proyecto para permitir incluir mas clases css
- Se actualiza facebook/webdriver: v1.6.0
- Fix a nombre de variable de entorno para indicar el item de inicio en toba_selenium_utilidades
- Se modifica operatoria del metodo toba_db_postgres7::pgdump_limpiar para que no elimine lineas que son continuacion de string (nota: ahora puede incluir comentarios la salida)
- Se agrega modo --no-interactivo al comando toba_base::registrar y toba_base::desregistrar
- Fix en toba_ei_arbol para evitar notice cuando no se envian utilerias
- Se utiliza el formato datetime provisto por PHPSpreadsheet para la salida excel
- El comando base registrar recibe sus parametros por stdin via modificadores
- El comando instalacion cambia_id_desarrollador recibe su parametro por stdin via modificador
- El mecanismo de autenticacion saml_onelogin comienza a usar el parametro full_url cuando esta disponible
- Se elimina el uso de Halite del Hook para Arai-Cli y se delega el manejo de claves a ese paquete
- Se elimina soporte a SSL de los tipos de autenticacion soportados por Arai-Cli en el Hook
- Fix en toba_ap_tabla_db para evitar notice por variable no inicializada
- Fix en toba_cargador y toba_modelo_proyecto para componentes que no tienen datos en todas las tablas
- PHP 7.1 se transforma en requerimiento minimo para la version
- Se agrega chequeo de topes de versiones compatibles del paquete siu/arai-cli
- Se corrige bug en toba_proyecto::get_version, ahora devuelve un objeto toba_version nuevamente
- Se corrige un bug en toba_extractor_clases que tenia incidencia en la generacion del archivo autoload
- Mueve el paquete siu/arai-cli a sugerido y elimina el paquete siu/arai-json-migrator de las dependencias
- Se agrega el metodo toba_recurso::link_css_proyecto para permitir la utilización de archivos css no presentes en el framework
- Se agrega control especifico sobre el destino del servicio solicitado, un valor incorrecto significa que le pide el servicio a todos los objetos de la operación.
- Se cambia el tipo de dato a bigint para el id de solicitud en las tablas de auditoria (ATENCION!! requiere actualizar el schema mediante comando administrativo) 
- Fix warnings varios en PHP 7.2
- Se anticipa el envio de headers para poder fijar cache_limiter y evitar error en php 7.2
- Se ordenan los nombres de metodos y propiedades de sesion recuperadas via Reflection
- Actualización de librerias:
  * phpmailer/phpmailer: v6.0.3
  * onelogin/php-saml: v2.13
- Se modifican clases del runtime para hacer uso de toba_config
- Se agrega toba_config como concentrador de configuraciones, se instancia via el lanzador toba::config()
- Nuevas clases para darle un marco a los test via Selenium
  * toba_selenium_basics_proyecto (se encarga de cuestiones basicas del testeo)
  * toba_selenium_conector_base (conecta bd via parametros en constantes)
  * toba_selenium_utilidades (permite gestionar parametros del proyecto para facilitar la generacion de urls, paths, etc)
  * toba_selenium_monje_negro (gestiona la interaccion con webdriver)
- Se agregan casos de test para login y mantenimiento de usuario en el proyecto toba_usuarios
- Modificacion de librerias:
  * Elimina element-34/php-webdriver 
  * Agrega facebook/webdriver: v1.5.0
- Se cambia Bower por Yarn como manejador de assets JS

[3.0.31](https://github.com/SIU-Toba/framework/releases/tag/v3.0.31) (2018-10-25):
- Se corrige bug en toba_ini que en ciertas secuencias de uso mezclaba configuraciones generales con secciones al guardar

[3.0.30](https://github.com/SIU-Toba/framework/releases/tag/v3.0.30) (2018-08-28):
- Se cambia el scope de los métodos que implementan el selector de ordenamiento múltiple del cuadro a protected
- Se corrige bug por merge desde develop

[3.0.29](https://github.com/SIU-Toba/framework/releases/tag/v3.0.29) (2018-08-27):
- Fix a bug introducido al enviar los headers para el contexto de ejecucion de consola
- Fix a ER que eliminaba los comentarios durante la creacion del archivo autoload 

[3.0.28](https://github.com/SIU-Toba/framework/releases/tag/v3.0.28) (2018-08-21):
- Fix a los metodos toba_varios::acceso_post y toba_varios::acceso_get

[3.0.27](https://github.com/SIU-Toba/framework/releases/tag/v3.0.27) (2018-08-21):
- Actualiza el archivo .lock
- Agrega método para iniciar el contexto de ejecución en pedidos SOAP
- Anticipo del envío de headers para poder fijar el parámetro cache_limiter
- Fix a warnings para PHP 7.2

[3.0.26](https://github.com/SIU-Toba/framework/releases/tag/v3.0.26) (2018-05-22):
- Fix en toba_editor al verificar si se encuentra activo

[3.0.25](https://github.com/SIU-Toba/framework/releases/tag/v3.0.25) (2018-05-16):
- Bugfix a ef_multi_check al ser utilizado en un ei_filtro como campo obligatorio
- Ordena la lista de metodos recuperados de una clase registrada como consulta_php

[3.0.24](https://github.com/SIU-Toba/framework/releases/tag/v3.0.24) (2018-05-08):
- Se fixea error en commit anterior y agrega scroll a la operacion de seleccion de usuarios
- Se agrega paginado y se solicita al menos un filtro para la seleccion de usuarios via arai en toba_usuarios
- Se mejora la validacion al agregar o sincronizar proyectos con arai-registry
- Se implementa un metodo para acceder a la metadata del SP en toba_autenticacion_saml_onelogin
- Se corrige bug al usar un ef_html en un formulario_ml e intercambiar filas

[3.0.23](https://github.com/SIU-Toba/framework/releases/tag/v3.0.23) (2018-04-27):
- Agrega csrf token al form que cambia el perfil funcional en runtime
- Bugfix en el seteo del perfil funcional activo, existian diferencias cuando pertenecia a una membresia.
- Actualiza el paquete siu/text_Highlighter: v0.8
- Se corrigen notices en toba_editor

[3.0.22](https://github.com/SIU-Toba/framework/releases/tag/v3.0.22) (2018-04-06):
- Se actualiza el paquete siu/arai-cli a v2.3.1
- Se corrigen bugs en los hooks de Toba para conectar a Araí
- Se corrige XSS via toba_notificacion (IMPORTANTE!!)
- Se agrega recuperación de exportación de personalización incompleta

[3.0.21](https://github.com/SIU-Toba/framework/releases/tag/v3.0.21) (2018-03-13):
- Se agrega conversion via Psr7\str al mensaje proveniente de un error rest
- Se agrega el metodo toba_auditoria_tablas_postgres::get_tablas_triggers_desactivados (credits FMartinez)

[3.0.20](https://github.com/SIU-Toba/framework/releases/tag/v3.0.20) (2018-03-07):
- Se corrige un bug en ef_upload que afectaba su uso en formularios_ml
- Se desactiva el log de WS durante el testing via phpunit

[3.0.19](https://github.com/SIU-Toba/framework/releases/tag/v3.0.19) (2018-02-26):
- Se corrige notice en toba_solicitud para pedidos rest
- Se corrige problema de transacciones anidadas al usar la utilidad de recordatorio de password

[3.0.18](https://github.com/SIU-Toba/framework/releases/tag/v3.0.18) (2018-02-09):
- Se modifica la configuración por defecto del toolbar en CKEditor
- Se agrega nuevo mecanismo de sincronizacion de credenciales via arai-cli

[3.0.17](https://github.com/SIU-Toba/framework/releases/tag/v3.0.17) (2018-02-05):
- Se modifica el metodo toba_db_postgres7::insert_masivo para que utilice la conexion de PDO

[3.0.16](https://github.com/SIU-Toba/framework/releases/tag/v3.0.16) (2018-02-02):
- Se corrige decodificacion respuesta REST en toba_usuarios debido a cambio en API Guzzle

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
