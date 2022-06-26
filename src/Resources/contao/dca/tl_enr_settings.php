<?php

$GLOBALS['TL_DCA']['tl_enr_settings'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'File',
        'closed'                      => true
    ),
     // Palettes
    'palettes' => array
    (
        '__selector__'                => array(''),
        'default'                     => "
        information;
        deactivateJS,deactivateCSS;
        eventEndPublishingDate,allowMultipleCategories;
        sendEmailRegistrationDone,sendEmailRegistrationDone_subject,
        email_fromEmail,email_fromName;
        hideRegistrationFormWhenMemberIsLoggedIn,confirmRegistration;
        "
    ),

    'fields' => array
	(
		'deactivateJS' => array
		(
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12')
		),
        'deactivateCSS' => array
		(
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12')
		),
        'confirmRegistration' => array
		(
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12')
		),
        'sendEmailRegistrationDone' => array
		(
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12')
		),
        'sendEmailRegistrationDone_subject' => array
		(
			'inputType'               => 'text',
		    'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
		),
        'email_fromEmail' => array
		(
			'inputType'               => 'text',
		    'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
		),
        'email_fromName' => array
		(
			'inputType'               => 'text',
		    'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
		),
        'allowMultipleCategories' => array
		(
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12')
		),
        'eventEndPublishingDate' => array
		(
			'inputType'               => 'select',
            'default'                 => 'endDate',
			'options'                 => [
                'startDate',
                'endDate',
                'midnightEndDate',
                'plus1day',
                'plus1week',
                'plus1month',
                'plus1year',
            ],
            'reference'               => &$GLOBALS['TL_LANG']['tl_enr_settings']['eventEndPublishingDate']['options'],
			'eval'                    => array('includeBlankOption'=>false, 'tl_class'=>'w50', 'mandatory'=>true )
		),
        'hideRegistrationFormWhenMemberIsLoggedIn' => array
		(
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12')
		),
        'information' => array
        (
            'input_field_callback'    => array('tl_enr_settings', 'information'),
        ),
    )
);



class tl_enr_settings extends Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function information(\DataContainer $dc, $label)
    {
            $icon = $this->generateImage('show.gif', 'Information', ' style="vertical-align:-4px"');
            return '<div style="margin-left: 15px;margin-right: 15px;line-height:1.3rem;">
                <h3><label style="color:#8ab858">'.$icon. ' Information </label></h3>
                <div style="margin:10px 0">
                If you create new categories they are not automaticaly visible. Don\'t forget to activate them in the categories module.<br>
                Dont\'t forget to set the timezone! and dateformat in the contao settings!<br>
                </div>
            </div>';
    } 

}
?>