# Script PowerShell pour commit et push Git
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  COMMIT ET PUSH VERS GIT" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier si Git est installé
try {
    $gitVersion = git --version 2>&1
    Write-Host "[OK] Git detecte: $gitVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERREUR] Git n'est pas installe ou pas dans le PATH" -ForegroundColor Red
    Write-Host "Installez Git depuis https://git-scm.com/" -ForegroundColor Yellow
    exit 1
}

# Aller dans le dossier du projet
Set-Location "c:\wamp64\www\M-moire_officielle-"
Write-Host "[1/5] Repertoire: $(Get-Location)" -ForegroundColor Cyan
Write-Host ""

# Initialiser le repo si nécessaire
if (-not (Test-Path .git)) {
    Write-Host "[2/5] Initialisation du depot Git..." -ForegroundColor Yellow
    git init
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ERREUR] Impossible d'initialiser le depot Git" -ForegroundColor Red
        exit 1
    }
    Write-Host "[OK] Depot Git initialise" -ForegroundColor Green
} else {
    Write-Host "[2/5] Depot Git deja initialise" -ForegroundColor Green
}
Write-Host ""

# Configurer Git si nécessaire
$userName = git config user.name 2>&1
$userEmail = git config user.email 2>&1
if (-not $userName -or $userName -match "error") {
    Write-Host "[INFO] Configuration Git necessaire" -ForegroundColor Yellow
    Write-Host "Configurez avec: git config user.name 'Votre Nom'" -ForegroundColor Yellow
    Write-Host "              git config user.email 'votre@email.com'" -ForegroundColor Yellow
}
Write-Host ""

# Ajouter tous les fichiers
Write-Host "[3/5] Ajout de tous les fichiers modifies..." -ForegroundColor Yellow
git add -A
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERREUR] Impossible d'ajouter les fichiers" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] Fichiers ajoutes" -ForegroundColor Green
Write-Host ""

# Afficher les fichiers modifiés
Write-Host "Fichiers modifies:" -ForegroundColor Cyan
git status --short
Write-Host ""

# Créer le commit
Write-Host "[4/5] Creation du commit..." -ForegroundColor Yellow
$commitMessage = "Ajout choix role inscription + systeme commentaires + corrections suivi soumissions"
git commit -m $commitMessage
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ATTENTION] Aucun changement a commiter ou erreur lors du commit" -ForegroundColor Yellow
    Write-Host "Voulez-vous continuer pour le push quand meme ? (O/N)" -ForegroundColor Yellow
    $continue = Read-Host
    if ($continue -ne "O" -and $continue -ne "o") {
        Write-Host "Operation annulee" -ForegroundColor Yellow
        exit 0
    }
} else {
    Write-Host "[OK] Commit cree avec succes" -ForegroundColor Green
}
Write-Host ""

# Vérifier le remote
Write-Host "[5/5] Verification du depot distant..." -ForegroundColor Yellow
$remote = git remote -v 2>&1
if ($remote -and -not ($remote -match "error")) {
    Write-Host "Depot distant configure:" -ForegroundColor Cyan
    Write-Host $remote
    Write-Host ""
    Write-Host "Voulez-vous pousser vers le depot distant ? (O/N)" -ForegroundColor Yellow
    $pushChoice = Read-Host
    if ($pushChoice -eq "O" -or $pushChoice -eq "o") {
        Write-Host ""
        Write-Host "Quelle est la branche distante ? (main/master/autre)" -ForegroundColor Yellow
        $branch = Read-Host
        if (-not $branch) { $branch = "main" }
        
        Write-Host "Push vers origin/$branch..." -ForegroundColor Yellow
        git push origin $branch
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[OK] Push reussi vers origin/$branch" -ForegroundColor Green
        } else {
            Write-Host "[ERREUR] Le push a echoue" -ForegroundColor Red
            Write-Host "Verifiez que vous avez configure le remote avec:" -ForegroundColor Yellow
            Write-Host "  git remote add origin [URL_DU_DEPOT]" -ForegroundColor Yellow
            Write-Host "Ou que vous avez les droits d'ecriture sur le depot" -ForegroundColor Yellow
        }
    } else {
        Write-Host "Push annule" -ForegroundColor Yellow
    }
} else {
    Write-Host "[INFO] Aucun depot distant configure" -ForegroundColor Yellow
    Write-Host "Pour configurer un depot distant:" -ForegroundColor Yellow
    Write-Host "  git remote add origin [URL_DU_DEPOT]" -ForegroundColor Yellow
    Write-Host "Puis executez: git push -u origin main" -ForegroundColor Yellow
}
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  OPERATION TERMINEE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Pour voir l'historique: git log --oneline" -ForegroundColor Cyan
Write-Host "Pour voir les changements: git status" -ForegroundColor Cyan
