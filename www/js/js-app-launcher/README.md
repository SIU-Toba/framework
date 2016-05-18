# js-app-launcher
Librería JS para graficar un lanzador de aplicaciones. La misma requiere jQuery para su funcionamiento.

### Inicialización

Para inicializar la librería se debe configurar el objeto `appLauncherDataParam` que contiene los siguientes datos:

* container: Identificador del tag html que contendrá el app-launcher, para el siguiente ejemplo el valor de container es `enc-usuario`:

  ```<div id="enc-usuario"> </div>```
  
* data: objeto que contiene información del usuario y aplicaciones a las que tiene acceso. Solo se muestran los datos que estan definidos en el mismo. El formato es el sigueinte:

```
  {
        'usuario_id' : 'admin',
        'usuario_nombre' : 'Usuario Administrador',
        'usuario_foto':    'http://aplicacion.com/perfil/admin.png',
        'perfil_url': 'http://aplicacion.com/perfil/',
        'usuario_perfil_url_target' : '_blank',
        'leyenda_btn_salir' : 'Vía de escape',
        'aplicaciones' :
        [
            {
              'url' : 'http://aplicacion1.com',
              'icono_url' : 'http://aplicacion1.com/logo.png',
              'etiqueta' : 'Aplicacion 1',
              'descripcion': 'descripcion de aplicacion 1'
            },
            {
              'url' : 'http://aplicacion2.com',
              'icono_url' : 'http://aplicacion2.com/logo.png',
              'etiqueta' : 'Aplicacion 2',
              'descripcion': 'descripcion de aplicacion 2'
            },
            {
            ...
            }
        ],
        'cuentas':
        [
            {
              'id': 'pepito',
              'nombre': 'Rudecindo Malacara'
            }
            {
              'id': 'marciano87',
              'nombre': 'Nombre cuenta 2'
            }
        ],
        'usuario_preferencias' : {
            'url' : 'http://aplicacion.com/preferencias/',
            'label' : 'Seteos'
        },


}
```
* urlAppUsrChg : Indica la direccion de la aplicacion a la que deberia dirigirse en caso de un cambio de cuenta.

* usrChangeParam: Indica el nombre del parametro que marca un cambio de cuenta de usuario.
 
* js_salir: Lógica javaScript que se aplica al boton de `salir`.


### Modo de uso

Para utilizar la librería debemos tener en cuenta lo siguiente:

* Incluir archivos CSS, en la carpeta `css` se encuentra el css por defecto:

  ```
  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:300,400,700" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="css/app_launcher.css" type="text/css" />
  ```

* Incluir la librería propiamente dicha:

  ```
  <script type="text/javascript" src="app_launcher.js"></script>  
  ```

* Definir un tag html que contendrá el app-launcher, por ejemplo un `<div>`:

  ```
  <div id="enc-usuario" >   
  </div>
  ```

* Inicializar el app-launcher mediante código javaScript:

  ```
  <script>
    // defino el div que contine el appLauncher
    var divContainer = "#enc-usuario";
    
    // defino los datos para armar el appLauncher
    var appLauncherData = { "usuario_id":"admin",
                            "usuario_nombre":"Usuario Administrador",
                            "usuario_foto":"img\/foto_perfil_defecto.png",
                            "perfil_url":"#",
                            "aplicaciones":[],
                            "cuentas":[]
                        };
    
    // funcion js que contiene la logica js para logout
    var fnc_js_salir = function() { alert("Salir de la aplicación!!!")};
    
    appLauncherData.aplicaciones[0] = {"url":"https:\/\/drive.google.com\/","etiqueta":"Google drive","icono_url":"img\/google_drive.png","descripcion":"Aplicaci\u00f3n de google drive"};
    appLauncherData.aplicaciones[1] = {"url":"https:\/\/mail.google.com\/","etiqueta":"Google gmail","icono_url":"img\/google_gmail.png","descripcion":"Aplicaci\u00f3n de google gmail"};
    appLauncherData.aplicaciones[2] = {"url":"https:\/\/plus.google.com\/","etiqueta":"Google plus","icono_url":"img\/google_plus.png","descripcion":"Aplicaci\u00f3n de google plus"};
    
    appLauncher.init({
                    container: divContainer,
                    data: appLauncherData,
                    urlAppUsrChg: "https:\/\/plus.google.com\/",
                    usrChangeParam: "notAvailable",
                    js_salir: fnc_js_salir
                });
  </script>
  ```
  


