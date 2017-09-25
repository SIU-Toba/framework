[![Latest Stable Version](https://poser.pugx.org/siu-toba/framework/v/stable?format=flat)](https://packagist.org/packages/siu-toba/framework)
[![Total Downloads](https://poser.pugx.org/siu-toba/framework/downloads?format=flat)](https://packagist.org/packages/siu-toba/framework)

# SIU-Toba

Framework para desarrollo rápido de aplicaciones web

## Instalando SIU-Toba

La descarga de SIU-Toba a partir de la version 3.0 se lleva a cabo enteramente via composer, para ello se deben agregar al archivo ``composer.json``  las siguientes lineas
```json
"repositories": [
        {
            "type": "composer",
            "url": "https://satis.siu.edu.ar"
        }
    ],
    "require": {
        "siu-toba/framework": "^3.0"
    },
   "scripts": {
        "post-install-cmd": [
            "composer run-script post-install-cmd -d ./vendor/siu-toba/framework/"
        ],
        "post-update-cmd": [
            "composer run-script post-install-cmd -d ./vendor/siu-toba/framework/"
        ]
    },
    "minimum-stability": "dev",
    "prefer-stable" : true
```

Luego de ello realizamos el download propiamente dicho de acuerdo a como vengamos trabajando con composer. Para ello podemos utilizar el comando: 
```shell
composer install
```
o 

```shell
composer update siu-toba/framework
```

Una vez ejecutados dichos comandos, se procede a la instalación propiamente dicha de SIU-Toba, primero definiremos el nombre de la instancia y la ubicación donde deseamos resida la carpeta instalación como variables de entorno mediante
```shell
export TOBA_INSTANCIA=$nombre_instancia
export TOBA_INSTALACION_DIR=$carpeta_instalacion
``` 
Luego iniciaremos la instalación (lo que permitira su uso para el desarrollo de la aplicación), de la forma tradicional via linea de comandos desde la carpeta bin (del proyecto si existe o de toba): 
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


Para crear un proyecto de cero usando SIU-Toba utilizaremos el template del proyecto vacio que se encuentra en github, se recomienda bajar el zip directamente desde ``https://github.com/SIU-Toba/template-proyecto-toba`` tener en cuenta el numero de versión.
A continuación editamos el archivo ``composer.json`` para definir el nombre del paquete que contendra el proyecto y agregar o modificar opciones sobre los paquetes requeridos por el mismo. Una vez completado este paso, ejecutamos:
```shell
composer install
```
Luego de ello, proseguir con la instalación de SIU-Toba como se especifica anteriormente.

El paso final es la creación del proyecto propiamente dicho mediante el comando

```shell
toba proyecto crear -p nombre_proyecto -d `pwd`
```
Una vez finalizado este paso y luego de reiniciar el web server, dirigirse con el browser a ``http://localhost/toba_editor/$nro_version$``  y comenzar a construir el proyecto.

### Pasando un proyecto existente a la instalación via composer

Para pasar un proyecto existente al nuevo mecanismo de instalación de SIU-Toba se pueden tomar dos caminos: 

*  Seguir el procedimiento para la creación de un proyecto de cero y luego sobreescribir con el contenido actual del proyecto.
*  Otra alternativa es realizar los siguientes pasos:

Crear dentro del directorio del proyecto un archivo ``composer.json``, dentro del cual se especificarán los datos del mismo y luego continuar con los pasos mencionados para incorporar el repositorio necesario.

Luego de ello se realiza la instalación de SIU-Toba como se menciona anteriormente y se procede a la carga del proyecto con el comando:
```shell
toba proyecto cargar -p nombre_proyecto -d `pwd`
```

## Migrando un proyecto a la nueva versión

Para migrar un proyecto a la nueva versión se recomienda lo siguiente: 

- Realizar la copia de la carpeta del proyecto a un nuevo directorio
- Realizar la instalacion de la nueva version de SIU-Toba
- Realizar la importacion del proyecto mediante el siguiente comando indicando
```shell
toba proyecto importar -p nombre_proyecto -d dir_instalacion_anterior --destino `pwd`
```
