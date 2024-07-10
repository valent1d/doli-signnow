<?php

// Activez l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once '../lib/signdevis.lib.php';

$langs->loadLangs(array("admin", "signdevis@signdevis"));

$action = GETPOST('action', 'alpha');

// Sécurité
if (!$user->admin) accessforbidden();

// Configuration des paramètres
if ($action == 'update') {
    $signnow_api_key = GETPOST('signnow_api_key', 'alpha');
    $signnow_api_secret = GETPOST('signnow_api_secret', 'alpha');

    dolibarr_set_const($db, "SIGNDEVIS_SIGNNOW_API_KEY", $signnow_api_key, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "SIGNDEVIS_SIGNNOW_API_SECRET", $signnow_api_secret, 'chaine', 0, '', $conf->entity);
    dolibarr_set_const($db, "SIGNDEVIS_WEBHOOK_URL", $webhook_url, 'chaine', 0, '', $conf->entity);

    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
}

// Affichage de l'en-tête
llxHeader('', $langs->trans("SignDevisSetup"));

// Titre de la page
$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("SignDevisSetup"), $linkback, 'title_setup');

// Configuration form
print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

// SignNow API Key
print '<tr class="oddeven"><td>';
print $langs->trans("SignNowAPIKey").'</td><td>';
print '<input type="text" name="signnow_api_key" value="'.$conf->global->SIGNDEVIS_SIGNNOW_API_KEY.'" size="40">';
print '</td></tr>';

// SignNow API Secret
print '<tr class="oddeven"><td>';
print $langs->trans("SignNowAPISecret").'</td><td>';
print '<input type="password" name="signnow_api_secret" value="'.$conf->global->SIGNDEVIS_SIGNNOW_API_SECRET.'" size="40">';
print '</td></tr>';

// Webhook URL
print '<tr class="oddeven"><td>';
print $langs->trans("WebhookURL").'</td><td>';
print '<input type="text" name="webhook_url" value="'.$conf->global->SIGNDEVIS_WEBHOOK_URL.'" size="60">';
print '<br><small>'.$langs->trans("WebhookURLDesc").'</small>';
print '</td></tr>';

print '</table>';

print '<br><div class="center">';
print '<input class="button" type="submit" value="'.$langs->trans("Save").'">';
print '</div>';

print '</form>';

llxFooter();
$db->close();