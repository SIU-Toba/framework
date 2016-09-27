# SIU-Toba

Framework para desarrollo rápido de aplicaciones web

## Instalando SIU-Toba

La descarga de SIU-Toba a partir de la version 2.8.0 se lleva a cabo enteramente via composer, para ello se deben agregar al archivo ``composer.json``  las siguientes lineas
```json
"repositories": [
        {
            "type": "composer",
            "url": "http://satis.siu.edu.ar"
        }
    ],
    "require": {
        "siu-toba/framework": "2.7.x-dev"
    }
```
o ejecutando: 
```shell
composer require siu-toba/framework 
```

Una vez ejecutado dicho comando, se procede a la instalación propiamente dicha de SIU-Toba, lo que permitira su uso para desarrollo de la aplicación, la misma se lleva adelante de la forma tradicional via linea de comandos: 
```shell
toba instalacion instalar [-modificadores]
```
Este comando opera de manera interactiva, aunque se pueden usar modificadores para especificar parte de la informacion requerida (para consultar una lista de modificadores teclee ''toba instalacion instalar --help''), por defecto intentara cargar todos aquellos proyectos 
que se encuentren dentro de la carpeta ''vendor/siu-toba/framework/proyectos''. Una vez terminada la instalacion, se le solicitara que incorpore los alias necesarios para la navegacion de los proyectos a la configuracion del web server.

Otra opción para entornos mas scripteables (como podria ser un container docker) involucra el comando: 
```shell
toba instalacion_silenciosa instalar [--archivo_configuracion parameters.yml]
```

Que permite realizar una instalacion de manera totalmente desatendida, con la configuración residente en un archivo YAML, salvo las claves para la base de datos y el administrador de sistema que se proveen via sendos archivos de texto.
Este tipo de instalacion, no carga ningun proyecto en la instancia, con lo cual para poder hacer uso de toba_editor y de toba_usuario se requiere de pasos extra.
Sin embargo puede ser una buena manera de realizar instalaciones ''manuales'' de producción, ya que permitiria generar clones con la misma configuración.

### Creando un proyecto de cero

Para crear un proyecto de cero usando SIU-Toba utilizaremos el template del proyecto vacio que se encuentra en github, se puede bajar el zip directamente o ejecutar el siguiente comando: 
```shell
composer create-project siu-toba/template-proyecto-vacio carpeta_destino --no-install
```
A continuación editamos el archivo ``composer.json`` para definir el nombre del paquete que contendra el proyecto y agregar o modificar opciones sobre los paquetes requeridos por el mismo. Una vez completado este paso, ejecutamos:
```shell
composer install
```




### Pasando un proyecto existente a la instalación via composer




## Migrando un proyecto a la nueva versión