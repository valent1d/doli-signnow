<?php
class ActionsSignDevis
{
public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
{
    global $db, $langs;

    if ($parameters['currentcontext'] == 'propalcard') {
        $sql = "SELECT sign_status, sign_link FROM " . MAIN_DB_PREFIX . "signdevis_signature WHERE fk_object = " . $object->id . " AND type_object = 'propal' ORDER BY date_creation DESC LIMIT 1";
        $resql = $db->query($sql);
        if ($resql && $db->num_rows($resql) > 0) {
            $obj = $db->fetch_object($resql);
            if ($obj->sign_status == 'pending') {
                print '<div class="inline-block divButAction"><span class="butActionRefused classfortooltip" title="' . $langs->trans("SignaturePending") . '">' . $langs->trans("SignaturePending") . '</span></div>';
                print '<div class="inline-block divButAction"><a class="butAction" href="' . $obj->sign_link . '" target="_blank">' . $langs->trans("ViewSignLink") . '</a></div>';
            } elseif ($obj->sign_status == 'signed') {
                print '<div class="inline-block divButAction"><span class="butActionRefused classfortooltip" title="' . $langs->trans("SignatureCompleted") . '">' . $langs->trans("SignatureCompleted") . '</span></div>';
            }
        }
    }
    return 0;
}
}