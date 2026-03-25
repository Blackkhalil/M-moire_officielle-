@echo off
echo ========================================
echo   SEIC LBS - Demarrage du projet
echo ========================================
echo.
echo Ouverture de votre navigateur...
echo.

REM Attendre 2 secondes
timeout /t 2 /nobreak >nul

REM Ouvrir explicitement la page d'accueil PHP
start http://localhost/M-moire_officielle-/htdocs/index.php

echo.
echo Projet ouvert dans le navigateur !
echo.
echo ========================================
echo   URL: http://localhost/M-moire_officielle-/htdocs/index.php
echo ========================================
echo.
echo APPUYEZ SUR UNE TOUCHE POUR FERMER...
pause >nul

