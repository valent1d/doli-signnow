<?php
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once 'lib/signdevis.lib.php';

$id = GETPOST('id', 'int');

// Vérification des droits
if (!$user->rights->propale->lire) accessforbidden();

$object = new Propal($db);
$result = $object->fetch($id);

if ($result <= 0) {
    dol_print_error($db, $object->error);
    exit;
}

// Générer le lien de signature via l'API SignNow
$signLink = generateSignNowLink($object);

if ($signLink) {
    // Enregistrer le lien dans la base de données
    $sql = "INSERT INTO ".MAIN_DB_PREFIX."signdevis_signature (fk_object, type_object, sign_link, date_creation) ";
    $sql.= "VALUES (".$object->id.", 'propal', '".$db->escape($signLink)."', '".$db->idate(dol_now())."')";
    $resql = $db->query($sql);

    if ($resql) {
        setEventMessages($langs->trans("SignLinkGenerated"), null, 'mesgs');
    } else {
        setEventMessages($langs->trans("ErrorSavingSignLink"), null, 'errors');
    }
} else {
    setEventMessages($langs->trans("ErrorGeneratingSignLink"), null, 'errors');
}

header("Location: ".$_SERVER["HTTP_REFERER"]);
exit;