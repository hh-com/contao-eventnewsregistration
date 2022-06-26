<?php

$GLOBALS['TL_DCA']['tl_module']['fields']['enr_defaultreader'] = [ 
    'exclude'                 => true,
    'default'                 => '1',
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['enr_categories'] = [ 
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkboxWizard',
    #'foreignKey'              => 'tl_enr_category.title',
    'options_callback'        => array('tl_enr_module', 'getCategories'),
    'eval'                    => array('multiple'=>true, 'mandatory'=>true, 'tl_class'=>'w50'),
    'sql'                     => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['enr_sorting'] = [ 
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'select',
    'options'                 => [
        'byDateASC',
        'byDateDESC',
        'byNameASC',
        'byNameDESC',
        'byRegistrationStopDateASC', 
        'byRegistrationStopDateDESC', 
    ],
    'reference'                 => &$GLOBALS['TL_LANG']['tl_module']['enr_sorting']['ENR_SORTING_TYPE'],
    'eval'                    => array('multiple'=>false, 'tl_class'=>'w50'),
    'sql'                     => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['listViewModule'] = [ 
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'select',
    'eval'                    => array('multiple'=>false, 'tl_class'=>'w50', 'mandatory'=>true),
    'options_callback'        => ['tl_enr_module', 'getListViewModules'],
    'sql'                     => "smallint(5) unsigned NOT NULL default 0"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['hideEmptyCategory'] = [ 
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['registrationGroup'] = [ 
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkboxWizard',
    'eval'                    => array('multiple'=>true),
    'sql'                     => "blob NULL",
    'options_callback'        => ['tl_enr_module','prepareGroup'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['conditionsAreMandatory'] = [ 
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['gdprAreMandatory'] = [ 
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['palettes']['enrReader'] = '
    {title_legend},name,type;
    jumpTo,overviewPage;
    imgSize;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID
';

$GLOBALS['TL_DCA']['tl_module']['palettes']['enrList'] = '
    {title_legend},name,type;
    headline,fuzzy;
    {enr_legend},enr_categories,enr_sorting,numberOfItems,hardLimit;
    jumpTo,enr_defaultreader;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID
';

$GLOBALS['TL_DCA']['tl_module']['palettes']['enrCategory'] = '
    {title_legend},name,type;
    {enr_legend},enr_categories;hideEmptyCategory;
    jumpTo,listViewModule;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID
';

$GLOBALS['TL_DCA']['tl_module']['palettes']['enrRegistration'] = '
    {title_legend},name,type;
    {enr_legend},registrationGroup;
    jumpTo,overviewPage;
    conditionsAreMandatory,gdprAreMandatory;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},guests,cssID
';

/**
 * change label, eval and co... 
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['load_callback'][] = array('tl_enr_module','updateSettingsOnLoad');
$GLOBALS['TL_DCA']['tl_module']['fields']['headline']['load_callback'][] = array('tl_enr_module','updateSettingsOnLoad');
$GLOBALS['TL_DCA']['tl_module']['fields']['enr_categories']['load_callback'][] = array('tl_enr_module','updateSettingsOnLoad');


class tl_enr_module extends Backend {

    public function __construct()
    {
        parent::__construct();
        #$this->import('BackendUser', 'User');
    }

    /**
     * Update Settings
     */
    public function updateSettingsOnLoad($varValue, DataContainer $dc)
	{
       
        if ($dc->activeRecord->type == "enrList") {
            
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['label'] = $GLOBALS['TL_LANG']['tl_module']['jumpTo_Listview'];
     
            $GLOBALS['TL_DCA']['tl_module']['fields']['numberOfItems']['label'] = $GLOBALS['TL_LANG']['tl_module']['numberOfItems_Listview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['hardLimit']['label'] = $GLOBALS['TL_LANG']['tl_module']['hardLimit_Listview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['fuzzy']['label'] = $GLOBALS['TL_LANG']['tl_module']['fuzzy_Listview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['headline']['label'] = $GLOBALS['TL_LANG']['tl_module']['headline_Listview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['enr_categories']['label'] = $GLOBALS['TL_LANG']['tl_module']['enr_categories_Listview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] = "w50 ";
        }

        if ($dc->activeRecord->type == "enrReader") {
            
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['label'] = $GLOBALS['TL_LANG']['tl_module']['jumpTo_Readerview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['overviewPage']['label'] = $GLOBALS['TL_LANG']['tl_module']['overviewPage_Readerview'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] = "w50 ";
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['mandatory'] = true;
        }
        
        if ($dc->activeRecord->type == "enrCategory") {
            
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['label'] = $GLOBALS['TL_LANG']['tl_module']['jumpTo_Categories'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['enr_categories']['label'] = $GLOBALS['TL_LANG']['tl_module']['enr_categories_Categories'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['tl_class'] = "w50";
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['mandatory'] = true;
        }

        if ($dc->activeRecord->type == "enrRegistration") {
            $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['label'] = $GLOBALS['TL_LANG']['tl_module']['jumpTo_Registration'];
            $GLOBALS['TL_DCA']['tl_module']['fields']['overviewPage']['label'] = $GLOBALS['TL_LANG']['tl_module']['overviewPage_afterConfirmation'];
        }

        

        return $varValue;
    }

    public function getListViewModules()
	{
        $objDatabase = \Database::getInstance();
        $objEntity = $objDatabase->prepare( 'SELECT * FROM tl_module WHERE type like "enrList"' )->execute( );

        if ($objEntity->numRows < 1) {
            return [];
        }

        foreach($objEntity->fetchAllAssoc() as $row) {
            $return[$row['id']] = $row['name'];
        }
        
		return $return;
	}

    public function prepareGroup($varValue)
	{
        $return['loggedout'] = "Not logged in Users";
        $return['loggedin'] = "All logged in Users";
        
       
        $allMemberGroups = MemberGroupModel::findAll();

        if ($allMemberGroups) {
            foreach ($allMemberGroups as $group) {
                $return[$group->id] = $group->name;
            }
        }
        
        return $return;
    }
    
    /**
     * get all categories
     */
    public function getCategories()
	{
        $arr = [];
        $objDb = \Database::getInstance()->prepare("SELECT id, title FROM tl_enr_category ORDER BY title")
        ->execute();

        foreach ($objDb->fetchAllAssoc() as $row)
		{
            $arr[$row['id']] = $row['title'];
        }

        return $arr;
    }

}