@echo off
chcp 65001 >nul
title SEIC LBS - Demarrage
setlocal

REM Adresse du projet dans WampServer (le dossier doit rester dans C:\wamp64\www)
for %%I in ("%~dp0.") do set "DOSSIER=%%~nxI"
set "URL=http://localhost/%DOSSIER%/htdocs/"

echo ========================================
echo   SEIC LBS - Demarrage du projet
echo ========================================
echo.

call :port_ouvert 80
if not errorlevel 1 goto verifier_base

echo WampServer n'est pas demarre.
if exist "C:\wamp64\wampmanager.exe" (
    echo Lancement de WampServer... Acceptez la demande d'autorisation de Windows.
    start "" "C:\wamp64\wampmanager.exe"
) else (
    echo WampServer est introuvable dans C:\wamp64. Demarrez-le a la main puis relancez ce fichier.
    goto fin
)

echo Attente du demarrage (jusqu'a 90 secondes)...
set /a ESSAIS=0
:attente
timeout /t 3 /nobreak >nul
set /a ESSAIS+=1
call :port_ouvert 80
if not errorlevel 1 goto verifier_base
if %ESSAIS% lss 30 goto attente
echo.
echo WampServer n'a pas demarre. Verifiez que son icone est verte, puis relancez ce fichier.
goto fin

:verifier_base
call :port_ouvert 3306
if errorlevel 1 (
    echo.
    echo Attention : MySQL ne repond pas sur le port 3306.
    echo Attendez que l'icone WampServer soit verte, puis rechargez la page.
)

echo.
echo Ouverture du projet dans le navigateur :
echo   %URL%
start "" "%URL%"
echo.
echo Premiere installation ? Creez la base avec la commande indiquee dans README.md.

:fin
echo.
echo Appuyez sur une touche pour fermer cette fenetre...
pause >nul
endlocal
exit /b

:port_ouvert
powershell -NoProfile -Command "$c = New-Object Net.Sockets.TcpClient; try { $c.Connect('127.0.0.1', %1); $c.Close(); exit 0 } catch { exit 1 }"
exit /b %errorlevel%
