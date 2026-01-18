@echo off
echo ========================================
echo   COMMIT ET PUSH VERS GIT
echo ========================================
echo.

REM Vérifier si git est installé
git --version >nul 2>&1
if errorlevel 1 (
    echo ERREUR: Git n'est pas installe ou pas dans le PATH
    echo Veuillez installer Git depuis https://git-scm.com/
    pause
    exit /b 1
)

echo [1/4] Verification de l'etat du depot...
git status --short
echo.

echo [2/4] Ajout de tous les fichiers modifies...
git add -A
if errorlevel 1 (
    echo ERREUR lors de l'ajout des fichiers
    pause
    exit /b 1
)
echo OK - Fichiers ajoutes
echo.

echo [3/4] Creation du commit...
git commit -m "Ajout choix role inscription + systeme commentaires + corrections suivi soumissions"
if errorlevel 1 (
    echo ATTENTION: Aucun changement a commiter ou erreur lors du commit
    echo.
    echo Voulez-vous continuer pour le push quand meme ? (O/N)
    set /p continue=
    if /i not "%continue%"=="O" (
        echo Operation annulee
        pause
        exit /b 0
    )
) else (
    echo OK - Commit cree avec succes
)
echo.

echo [4/4] Push vers le depot distant...
echo.
echo Voulez-vous pousser vers le depot distant ? (O/N)
set /p push_choice=
if /i "%push_choice%"=="O" (
    echo.
    echo Quelle est la branche distante ? (main/master/autre)
    set /p branch=
    if "%branch%"=="" set branch=main
    
    git push origin %branch%
    if errorlevel 1 (
        echo.
        echo ATTENTION: Le push a echoue
        echo Verifiez que vous avez configure le remote avec:
        echo   git remote add origin [URL_DU_DEPOT]
        echo.
        echo Ou que vous avez les droits d'ecriture sur le depot
    ) else (
        echo.
        echo OK - Push reussi vers origin/%branch%
    )
) else (
    echo Push annule
)
echo.

echo ========================================
echo   OPERATION TERMINEE
echo ========================================
pause
