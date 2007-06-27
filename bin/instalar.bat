@echo off
set toba_instancia=desarrollo
set toba_dir=%~dp0..
php %toba_dir%\php\consola\run.php instalacion instalar
if  errorlevel  1  goto error_php;

pause;
goto end

:error_php 
echo.
echo ---------------------------------------------------------------------------
echo  Necesita tener a php.exe en la ruta por defecto del sistema
echo ---------------------------------------------------------------------------
pause

:end