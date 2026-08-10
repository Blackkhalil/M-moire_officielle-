<?php
// Fichier de sécurité pour empêcher l'accès direct au dossier uploads/presentations
header('HTTP/1.0 403 Forbidden');
exit('Accès interdit');
?>
