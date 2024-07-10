<?php
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

class modsigndevis extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs,$conf;

        $this->db = $db;
        $this->numero = 500000; // TODO: Remplacer par un numéro unique
        $this->rights_class = 'signdevis';
        $this->family = "other";
        $this->module_position = 500;
        $this->name = preg_replace('/^mod/i','',get_class($this));
        $this->description = "Integration of SignNow for electronic signatures";
        $this->version = '1.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto='signdevis@signdevis';

        $this->dirs = array();
        $this->config_page_url = array("setup.php@signdevis");
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->phpmin = array(5,6);
        $this->need_dolibarr_version = array(11,0);
        $this->langfiles = array("signdevis@signdevis");

        $this->const = array();
        $this->tabs = array();
        $this->cronjobs = array();
        $this->rights = array();
        $this->menu = array();

        $this->dictionaries=array();
    }

    public function init($options = '')
{
    $result = $this->_load_tables('/signdevis/sql/');
    
        // Vérifier et corriger le préfixe si nécessaire
    $dir = dol_buildpath('/signdevis/sql/');
    $files = scandir($dir);
    foreach ($files as $file) {
        if (preg_match('/\.sql$/i', $file)) {
            $fullpath = $dir . $file;
            $content = file_get_contents($fullpath);
            $content = str_replace('llx_', MAIN_DB_PREFIX, $content);
            file_put_contents($fullpath, $content);
        }
    }
    
    if ($result < 0) return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')

    // Create extrafields during init
    //include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
    //$extrafields = new ExtraFields($this->db);
    //$result1=$extrafields->addExtraField('signdevis_myattr1', "New Attr 1 label", 'boolean', 1, 3, 'thirdparty', 0, 0, '', '', 1, '', 0, 0, '', '', 'signdevis@signdevis', '$conf->signdevis->enabled');
    //$result2=$extrafields->addExtraField('signdevis_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project', 0, 0, '', '', 1, '', 0, 0, '', '', 'signdevis@signdevis', '$conf->signdevis->enabled');

    $sql = array();
    return $this->_init($sql, $options);
}
}