<?php

    /**
     * Backend modules
     */
    array_insert($GLOBALS['BE_MOD'], 1, array(
        'eventnewsregistration' => array(
            'eventnewsregistration' => array
            (
                'tables'            => array('tl_enr', 'tl_enr_category', 'tl_enr_organiser', 'tl_enr_location')
            ),
            'enrRegistration' => array
            (
                'tables'            => array('tl_enr_registration')
            ),
            'enrSettings' => array
            (
                'tables'            => array('tl_enr_settings', 'tl_enr_category',)
            )
        ),
    ));

    $GLOBALS['TL_AUTO_ITEM'][] = "simevent";
    
    $GLOBALS['TL_MODELS']['tl_enr'] = Hhcom\ContaoEventNewsRegistration\Model\EnrModel::class;
    $GLOBALS['TL_MODELS']['tl_enr_category'] = Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel::class;
    $GLOBALS['TL_MODELS']['tl_enr_location'] = Hhcom\ContaoEventNewsRegistration\Model\EnrLocationModel::class;
    $GLOBALS['TL_MODELS']['tl_enr_organiser'] = Hhcom\ContaoEventNewsRegistration\Model\EnrOrganiserModel::class;
    $GLOBALS['TL_MODELS']['tl_enr_registration'] = Hhcom\ContaoEventNewsRegistration\Model\EnrRegistrationModel::class;
    
    


?>