<?php

use Hhcom\ContaoEventNewsRegistration\Classes\EventNewsRegistration;
use Hhcom\ContaoEventNewsRegistration\Classes\Tests;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;

Tests::run();

$GLOBALS['TL_DCA']['tl_enr'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        #'ctable'                      => array('tl_enr_category','tl_enr_location','tl_enr_organiser'),
        'onload_callback' => array
        (
            #array('tl_enr', 'checkPermission')
        ),
        'onsubmit_callback' => array
        (
            #array('tl_enr', 'xy')
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'alias' => 'index',
                'start,stop' => 'index',
                'categories' => 'index',
                'published' => 'index',
                'startDate' => 'index',
                'endDate' => 'index',
            )
        ),
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('startDate'),
            'flag'                    => 6,
            'panelLayout'             => 'sort,filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('title'),
            'format'                  => '%s',
            'label_callback'          => array('tl_enr', 'listOverview'),
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ),
            'enr_categories' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['categories_label'],
                'href'                => 'do=eventnewsregistration&table=tl_enr_category',
                'class'               => 'header_category',
                'attributes'          => 'onclick="Backend.getScrollOffset()" style="padding-left: 22px;"',
                'icon'                => 'show.svg',
                'button_callback'     => array('tl_enr', 'buttonCategories')
            ),
            'enr_organiser' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['organiser_label'],
                'href'                => 'do=eventnewsregistration&table=tl_enr_organiser',
                'class'               => 'header_organiser',
                'icon'                => 'show.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset()" style="padding-left: 22px;"',
                'button_callback'     => array('tl_enr', 'buttonOrganiser')
            ),
            'enr_location' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['location_label'],
                'href'                => 'do=eventnewsregistration&table=tl_enr_location',
                'class'               => 'header_location',
                'icon'                => 'show.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset()" style="padding-left: 22px;"',
                'button_callback'     => array('tl_enr', 'buttonLocation')
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_enr']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(''),
        'default'                     => '
        backend_title;
        {content_legend},
        startDate,endDate,showTime;
        title,alias,teaser,description;
        images;
        files;
        categories;
        organiser,organiser_custom;
        location,location_custom;
        {registration_legend},event_registration,event_maxplaces,event_registration_stop,event_normalPrice;
        {published_legend:hide},published,start,stop;
        '
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
        'backend_title' => array
        (
            'exclude'                 => false,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
            'save_callback' => array
			(
				array('tl_enr', 'generateBackendTitle') 
			),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'showTime' => array(
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
        'startDate' => array
		(
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'text',
            'flag'                    => 6,
            'load_callback' => array
			(
				array('tl_enr', 'loadDate')
			),
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard', 'mandatory'=>true),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'endDate' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
        ),
        'title' => array
        (
            'exclude'                 => false,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'teaser' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>true, 'rte'=>'ace', 'helpwizard'=>false, 'tl_class'=>'clr'),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
        'description' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>true, 'rte'=>'tinyMCE', 'helpwizard'=>true),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
        'alias' => array
        (
            'exclude'                 => false,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'rgxp'=>'alias', 'tl_class'=>'w50', 'unique'=>true, 'doNotCopy' => true),
            'save_callback' => array
			(
				array('tl_enr', 'generateAlias')
			),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'images' => array
		(
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'orderField'=>'orderSRC', 'files'=>true, 'isGallery'=>true, 'extensions'=>'%contao.image.valid_extensions%'),
			'sql'                     => "blob NULL",
		),
        'files' => array
		(
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'orderField'=>'orderSRCFile', 'files'=>true, 'isDownloads'=>true, 'extensions'=> Config::get('allowedDownload')),
			'sql'                     => "blob NULL",
		),
        'orderSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['MSC']['sortOrder'],
			'sql'                     => "blob NULL"
		),
        'orderSRCFile' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['MSC']['sortOrder'],
			'sql'                     => "blob NULL"
		),
        'organiser' => array
        (
            'exclude'                 => true,
            'filter'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_enr', 'getOrganiser'),
			'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true,  'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'organiser_custom' => array
        (
            'exclude'                 => false,
            'search'                  => false,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        'location' => array
        (
            'exclude'                 => true,
            'filter'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_enr', 'getLocation'),
			'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true,  'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'location_custom' => array
        (
            'exclude'                 => false,
            'search'                  => false,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        /** Registration */
        'event_registration' => array(
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
        'event_maxplaces' => array(
			'exclude'                 => true,
            'inputType'               => 'text',
            'default'				  => 40,
            'eval'                    => array('mandatory'=>true, 'tl_class'=>'clr w50', 'rgxp'=>'natural', 'minval'=>1, 'maxval'=>1000 ),
            'sql'                     => "int(10) unsigned NOT NULL default '40'"
		),
        'event_registration_stop' => array(
            'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
        'event_normalPrice' => array(
            'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>13, 'rgxp'=>'digit', 'tl_class'=>'w50 wizard'),
			'sql'                   => "varchar(255) NOT NULL default ''",
		),
        'published' => array
		(
			'exclude'                 => true,
			'toggle'                  => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
            'eval'                    => array('doNotCopy' => true, 'tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'start' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'clr w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'stop' => array
		(
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
            'save_callback' => array
			(
				array('tl_enr', 'insertDateInPublishingStopField')
			),
			'sql'                     => "varchar(10) NOT NULL default ''"
        ),
    )
);

if (\Config::get('allowMultipleCategories')) {
    $GLOBALS['TL_DCA']['tl_enr']['fields']['categories'] = [
        'exclude'                 => true,
        'filter'                  => true,
        'inputType'               => 'checkboxWizard',
       # 'foreignKey'              => 'tl_enr_category.title',
        'options_callback'        => array('tl_enr', 'getCategories'),
        

        'eval'                    => array('multiple'=>true, 'csv'=>',', 'mandatory' => false),
        'sql'                     => "text NULL",
    ];
} else {
    $GLOBALS['TL_DCA']['tl_enr']['fields']['categories'] = [
        'exclude'                 => true,
        'filter'                  => true,
        'inputType'               => 'select',
        #'foreignKey'              => 'tl_enr_category.title',
        'options_callback'        => array('tl_enr', 'getCategories'),
        
        'eval'                    => array('includeBlankOption'=>true, 'multiple'=>false, 'csv'=>',', 'mandatory' => false, 'findInSet'=>true),
        'sql'                     => "text NULL",
    ];
}



class tl_enr extends Backend {

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        #parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function loadDate($value)
	{
		return strtotime(date('Y-m-d H:i', $value) );
	}


    /**
     * paint the categories global OP button
     */
    public function buttonCategories($href, $label, $title, $class, $attributes)
    {
        if (!$this->User->isAdmin && !$this->User->maeEventCat) {
            return "";
        }
        else {
            return '<a href="'.$this->addToUrl($href).'" class="'.$class.'" title="'.specialchars($title).'"'.$attributes.'>'.$label.'</a> ';
        }
    }

    /**
     * paint the categories global OP button
     */
    public function buttonOrganiser($href, $label, $title, $class, $attributes)
    {

        if (!$this->User->isAdmin && !$this->User->maeEventCat) {
            return "";
        }
        else {
            return '<a href="'.$this->addToUrl($href).'" class="'.$class.'" title="'.specialchars($title).'"'.$attributes.'>'.$label.'</a> ';
        }
    }


    /**
     * paint the categories global OP button
     */
    public function buttonLocation($href, $label, $title, $class, $attributes)
    {
        
        if (!$this->User->isAdmin && !$this->User->maeEventCat) {
            return "";
        }
        else {
            return '<a href="'.$this->addToUrl($href).'" class="'.$class.'" title="'.specialchars($title).'"'.$attributes.'>'.$label.'</a> ';
        }
    }

    /**
     * Generiert die Bezeichnung im Backend
     * Die Bezeichnung wird bei der Event-Kopier-Funktion angezeigt
     */
    public function generateBackendTitle($varValue, DataContainer $dc)
	{
        if ($varValue != '') {
            return $varValue;
        }

        $backend_title = [];
        
        $backend_title[] = $dc->activeRecord->title;

        $dateFormat = "dateFormat";
        if($dc->activeRecord->showTime) {
            $dateFormat = "datimFormat";
        }

        $backend_title[] = Date::parse(Config::get($dateFormat), $dc->activeRecord->startDate);
        $backend_title[] = StringUtil::substr($dc->activeRecord->teaser, 70);

        return implode(" | ", $backend_title);
    }


    /**
     * get all Organiser
     */
    public function getOrganiser()
	{
        $arr = [];
        $objDb = \Database::getInstance()->prepare("SELECT id, organiser FROM tl_enr_organiser ORDER BY organiser")
        ->execute();

        foreach ($objDb->fetchAllAssoc() as $row)
		{
            $arr[$row['id']] = $row['organiser'];
        }

        return $arr;
    }

    
    /**
     * get all Locations
     */
    public function getLocation()
	{
        $arr = [];
        $objDb = \Database::getInstance()->prepare("SELECT id, location FROM tl_enr_location ORDER BY location")
        ->execute();

        foreach ($objDb->fetchAllAssoc() as $row)
		{
            $arr[$row['id']] = $row['location'];
        }

        return $arr;
    }


    /**
     * get all categories
     */
    public function getCategories()
	{
        $arr = [];
        $objDb = \Database::getInstance()->prepare("SELECT id, title FROM tl_enr_category ORDER BY id")
        ->execute();

        foreach ($objDb->fetchAllAssoc() as $row)
		{
            $arr[$row['id']] = $row['title'];
        }

        return $arr;
    }
    
    
    /**
	 * Add an image to each record
	 *
	 * @param array                $row
	 * @param string               $label
	 * @param Contao\DataContainer $dc
	 * @param array                $args
	 *
	 * @return array
	 */
	public function listOverview($row, $label, Contao\DataContainer $dc, $args)
	{
        $style = "";

        $eventObj = EnrModel::findById($row['id']);
        $boolIsVisible = EventNewsRegistration::eventIsVisible($eventObj);
        $dateAndVisibility = EventNewsRegistration::getEventDate( $eventObj  );
        #$eventObj = EnrModel($row['id']);

        if (!$boolIsVisible) {
            $style = " color: #aaa; ";
        }

        if ($dateAndVisibility['showEndDate']) {
            $eventDate = $dateAndVisibility['startDate'] . " -> " . $dateAndVisibility['endDate'];
        } else {
            $eventDate = $dateAndVisibility['startDate'];
        }

        $categories = array_filter(explode(",", $row['categories']));

        $categoryString = ["No Category selected!"];
        if (!empty($categories)) {
            $categoryString = [];
            foreach ($categories as $id) {   
                $cat = EnrCategoryModel::findById($id);
                $categoryString[] = $cat->title;
            }
        }
        
        return "
        <div style='".$style."'>
            <span style='width:200px;'>".$row['title']."</span> |
            <span style='width:85px;'> Active: ".($boolIsVisible?'ja':'nein')."</span> | 
            <span style='width:65px;'> ".$eventDate."</span> | 
            <span style='width:85px;'> Registrierung: ".($row['event_registration']?'ja':'nein')."</span> 
            <br>
            <span style='width:85px;'> ".implode(", ", $categoryString)."</span>  
        </div>
        ";
	}

    public function generateAlias($varValue, Contao\DataContainer $dc)
	{
        if ($varValue == "") {
            $varValue = StringUtil::generateAlias($dc->activeRecord->title);

            $obj = \Database::getInstance()->prepare( 'SELECT * FROM tl_enr WHERE alias like ? ' )
            ->execute($varValue);
    
            if ($obj->numRows > 0) {
                
                $postalias = StringUtil::generateAlias(Date::parse(Config::get('dateFormat'), $dc->activeRecord->startDate));
                $varValue = $this->generateAlias($varValue . "-" .$postalias . "-" . rand(0,9) , $dc);
            }
    
        }

        return $varValue;
    
    }


    /**
     * 
     * Generate the "end" date by the configuration in the settings 
     */
    public function insertDateInPublishingStopField($varValue, Contao\DataContainer $dc) {

        if ($varValue != "")
            return $varValue;

        return EventNewsRegistration::getPublishingStopDate( $dc->activeRecord->startDate, $dc->activeRecord->endDate);
        
    }

}


?>