@echo off
chcp 65001 >nul
echo ========================================
echo   COMMIT ET PUSH VERS GIT
echo ========================================
echo.

cd /d "%~dp0"

REM Vérifier si git est installé
where git >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] Git n'est pas installé ou pas dans le PATH
    echo Veuillez installer Git depuis https://git-scm.com/
    echo.
    pause
    exit /b 1
)

echo [OK] Git détecté
echo.

REM Initialiser le dépôt si nécessaire
if not exist .git (
    echo [1/5] Initialisation du dépôt Git...
    git init
    if errorlevel 1 (
        echo [ERREUR] Impossible d'initialiser le dépôt Git
        pause
        exit /b 1
    )
    echo [OK] Dépôt Git initialisé
) else (
    echo [1/5] Dépôt Git déjà initialisé
)
echo.

REM Ajouter tous les fichiers
echo [2/5] Ajout de tous les fichiers modifiés...
git add -A
if errorlevel 1 (
    echo [ERREUR] Impossible d'ajouter les fichiers
    pause
    exit /b 1
)
echo [OK] Fichiers ajoutés
echo.

REM Afficher les fichiers modifiés
echo Fichiers modifiés :
git status --short
echo.

REM Créer le commit
echo [3/5] Création du commit...
git commit -m "Ajout choix role inscription + systeme commentaires + corrections suivi soumissions"
if errorlevel 1 (
    echo [ATTENTION] Aucun changement à commiter ou erreur lors du commit
    echo Voulez-vous continuer pour le push quand même ? (O/N)
    set /p continue=
    if /i not "%continue%"=="O" (
        echo Opération annulée
        pause
        exit /b 0
    )
) else (
    echo [OK] Commit créé avec succès
)
echo.

REM Vérifier le remote
echo [4/5] Vérification du dépôt distant...
git remote -v >nul 2>&1
if errorlevel 1 (
    echo [INFO] Aucun dépôt distant configuré
    echo Pour configurer un dépôt distant :
    echo   git remote add origin [URL_DU_DEPOT]
    echo Puis exécutez : git push -u origin main
    echo.
) else (
    echo Dépôt distant configuré :
    git remote -v
    echo.
    echo [5/5] Push vers le dépôt distant...
    echo Voulez-vous pousser vers le dépôt distant ? (O/N)
    set /p push_choice=
    if /i "%push_choice%"=="O" (
        echo.
        echo Quelle est la branche distante ? (main/master/autre)
        set /p branch=
        if "%branch%"=="" set branch=main
        
        echo Push vers origin/%branch%...
        git push origin %branch%
        if errorlevel 1 (
            echo.
            echo [ERREUR] Le push a échoué
            echo Vérifiez que vous avez les droits d'écriture sur le dépôt
        ) else (
            echo.
            echo [OK] Push réussi vers origin/%branch%
        )
    ) else (
        echo Push annulé
    )
)
echo.

echo ========================================
echo   OPÉRATION TERMINÉE
echo ========================================
echo.
echo Pour voir l'historique : git log --oneline
echo Pour voir les changements : git status
echo.
pause
