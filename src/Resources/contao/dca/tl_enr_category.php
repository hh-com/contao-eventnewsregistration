<?php

$GLOBALS['TL_DCA']['tl_enr_category'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'onload_callback' => array
        (
            #array('tl_enr_category', 'checkPermission')
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'alias' => 'index'
            )
        ),
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 0,
            'fields'                  => array('title'),
            'flag'                    => 0,
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('title', 'alias'),
            'showColumns'             => true,
            'format'                  => '%s <i>%s</i>',
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr_category']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr_category']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr_category']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr_category']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default'                     => 'information;{enr_legend},title,alias;'
    ),

    // Subpalettes
    'subpalettes' => array
    (
        ''                            => ''
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
        (
            'exclude'                 => false,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50 '),
            'sql'                     => "varchar(255) NOT NULL default ''",
        ),
        'alias' => array
        (
            'exclude'                 => false,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>50, 'rgxp'=>'alias', 'tl_class'=>'w50', 'unique'=>true, 'doNotCopy'=>true),
            'load_callback' => array
			(
				array('tl_enr_category', 'generateAlias')
			),
            'sql'                     => "varchar(50) NOT NULL default ''"
        ),
        'information' => array
        (
            'input_field_callback'    => array('tl_enr_category', 'information'),
        ),
    )
);

class tl_enr_category extends Backend {

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        #parent::__construct();
        #$this->import('BackendUser', 'User');
    }

    public function generateAlias($varValue, Contao\DataContainer $dc)
	{
        if (!Validator::isAlias($varValue)) {
            return StringUtil::generateAlias($varValue);
        }

        return $varValue;
    }

    public function information(\DataContainer $dc, $label)
    {
            $icon = $this->generateImage('show.gif', 'Information', ' style="vertical-align:-4px"');
            return '<div style="margin-left: 15px;margin-right: 15px;line-height:1.3rem;">
                <h3><label style="color:#8ab858">'.$icon. ' Information </label></h3>
                <div style="margin:8px 0">
                Categories are not automaticaly visible. Don\'t forget to activate them in the categories module.
                </div>
            </div>';
    } 
}


?>