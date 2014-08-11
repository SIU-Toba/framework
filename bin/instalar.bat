@echo off
set TOBA_INSTANCIA=desarrollo
set TOBA_DIR=%~dp0..
php "%TOBA_DIR%\php\consola\run.php" instalacion instalar %*
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