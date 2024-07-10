<?php
/* Copyright (C) 2024 [Your Name]
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    signdevis/lib/signdevis.lib.php
 * \ingroup signdevis
 * \brief   Library files with common functions for SignDevis
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function signdevisAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("signdevis@signdevis");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/signdevis/admin/setup.php", 1);
    $head[$h][1] = $langs->trans("Settings");
    $head[$h][2] = 'settings';
    $h++;

    $head[$h][0] = dol_buildpath("/signdevis/admin/about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    complete_head_from_modules($conf, $langs, null, $head, $h, 'signdevis');

    return $head;
}

/**
 * Generate SignNow link for document signing
 *
 * @param   Object  $object     Object to sign (typically a Propal)
 * @return  string              URL for signing or false if error
 */
function generateSignNowLink($object)
{
    global $conf, $db;

    // Check if API information is configured
    if (empty($conf->global->SIGNDEVIS_SIGNNOW_API_KEY) || empty($conf->global->SIGNDEVIS_SIGNNOW_API_SECRET)) {
        return false;
    }

    // Here, implement the call to the SignNow API to generate the signing link
    // This will depend on the specific SignNow API documentation
    // Below is a simplified example:

    $apiUrl = 'https://api.signnow.com/document/embedded-invite';
    
    // You'll need to first upload the proposal to SignNow and get its ID
    // This part is not implemented here and would require additional API calls
    $documentId = uploadProposalToSignNow($object);
    
    if (!$documentId) {
        return false;
    }

    $data = array(
        'document_id' => $documentId,
        'signer_email' => $object->thirdparty->email,
        'signer_name' => $object->thirdparty->name
    );

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $conf->global->SIGNDEVIS_SIGNNOW_API_KEY,
        'Content-Type: application/json'
    ));

    $response = curl_exec($ch);
    
    if(curl_errno($ch)){
        // Handle cURL error
        return false;
    }
    
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['url'])) {
        return $result['url'];
    }

    return false;
}

/**
 * Upload proposal to SignNow
 * This function needs to be implemented based on SignNow's API for document upload
 *
 * @param   Object  $object     Proposal object
 * @return  string              Document ID in SignNow or false if error
 */
function uploadProposalToSignNow($object)
{
    global $conf;

    // Implement document upload to SignNow here
    // This is a placeholder and needs to be replaced with actual implementation

    return 'PLACEHOLDER_DOCUMENT_ID';
}

/**
 * Check signature status
 *
 * @param   int     $objectId   Object ID (typically proposal ID)
 * @param   string  $type       Object type (typically 'propal')
 * @return  string              Signature status or false if not found
 */
function checkSignatureStatus($objectId, $type = 'propal')
{
    global $db;

    $sql = "SELECT sign_status FROM " . MAIN_DB_PREFIX . "signdevis_signature 
            WHERE fk_object = " . $objectId . " AND type_object = '" . $db->escape($type) . "' 
            ORDER BY date_creation DESC LIMIT 1";
    
    $resql = $db->query($sql);
    if ($resql && $db->num_rows($resql) > 0) {
        $obj = $db->fetch_object($resql);
        return $obj->sign_status;
    }

    return false;
}