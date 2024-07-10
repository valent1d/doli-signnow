<?php
require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Vérifiez la signature du webhook (à implémenter selon la documentation de SignNow)

if (isset($input['event']) && $input['event'] == 'document.signed') {
    $documentId = $input['document_id'];

    // Trouvez le devis correspondant
    $sql = "SELECT fk_object FROM " . MAIN_DB_PREFIX . "signdevis_signature WHERE sign_link LIKE '%" . $db->escape($documentId) . "%'";
    $resql = $db->query($sql);
    if ($resql && $db->num_rows($resql) > 0) {
        $obj = $db->fetch_object($resql);
        $propalId = $obj->fk_object;

        // Mise à jour du statut de signature
        $sql = "UPDATE " . MAIN_DB_PREFIX . "signdevis_signature SET sign_status = 'signed', date_signature = NOW() WHERE fk_object = " . $propalId;
        $db->query($sql);

        // Mise à jour du statut du devis
        $propal = new Propal($db);
        $propal->fetch($propalId);
        $propal->valid($user);
        $propal->cloture($user, 2); // 2 = signé
    }
}

http_response_code(200);