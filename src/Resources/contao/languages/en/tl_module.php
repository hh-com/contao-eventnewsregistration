<?php


$GLOBALS['TL_LANG']['tl_module']['enr_legend'] = "Event/News Registration settings";

$GLOBALS['TL_LANG']['tl_module']['enr_defaultreader'] = 
    ['Default Reader-Page',
     'If you use more than one reader page, you need a default reader page. This is used for insert tags and redirects.'
    ];

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Listview'] = 
    ['Redirect to the event reader',
     'Select the event reader page here.'
    ];

$GLOBALS['TL_LANG']['tl_module']['overviewPage_Readerview'] = 
    ['Back bottom to list view',
    'Select the list view page here.'
    ];

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Readerview'] = 
    ['Forwarding to the registration form',
     'Select the page for the registration form here. DO NOT make a selection if the form is on the same page.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Categories'] = 
    ['Redirect to the event list overview',
     'Select the page on which the event list module is integrated.'
    ]; 
$GLOBALS['TL_LANG']['tl_module']['hideEmptyCategory'] = 
    ['Don\'t show empty categories',
    'Hide categories that have no assignment.'
    ];

$GLOBALS['TL_LANG']['tl_module']['listViewModule'] = 
    ['Select list view',
    'The category links to this list view of events/news.'
    ];

$GLOBALS['TL_LANG']['tl_module']['enr_categories_Listview'] = 
    ['Show teasers of the following categories',
     'Here you select the categories whose teaser is displayed in the list if no category is passed in the URL.'
    ];

$GLOBALS['TL_LANG']['tl_module']['headline_Listview'] = 
    ['Fallback Headline is displayed if no category has been passed.',
     'This headline is displayed if no category has been passed. Otherwise, the category is displayed.'
    ];

$GLOBALS['TL_LANG']['tl_module']['numberOfItems_Listview'] = 
    ['Number of elements',
     'Here you can limit the total number of items. Enter 0 to show all. Pagination is activated when necessary.'
    ];
$GLOBALS['TL_LANG']['tl_module']['hardLimit_Listview'] = 
    ['Don\'t show pagination',
     'Disable pagination.'
    ];
$GLOBALS['TL_LANG']['tl_module']['fuzzy_Listview'] = 
    ['Don\'t show pagination if no category is passed',
     'If no category has been passed, the pagination will not be displayed.'
    ];
    
$GLOBALS['TL_LANG']['tl_module']['enr_categories_Categories'] = 
    ['Activate categories',
     'Choose the categories to be displayed in the navigation.'
    ];

$GLOBALS['TL_LANG']['tl_module']['overviewPage_afterConfirmation'] = 
    ['Redirect after the registration confirmation',
     'Select the page here to which the visitor should be redirected after he has sent the registration.'
    ];

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Registration'] = 
    ['Redirect after sending the registration form.',
     'Select the page on which the event list module is integrated.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['registrationGroup'] = 
    ['User groups that can register',
     'Select the groups that are allowed to register here.'
    ]; 
    
$GLOBALS['TL_LANG']['tl_module']['enr_sorting'] = 
    ['Sorting',
     'Select the sorting order here.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['enr_sorting']['ENR_SORTING_TYPE'] = [
    'byDateASC' => 'Ascending by date ',
    'byDateDESC' => 'Descending by date',
    'byNameASC' => 'Ascending by name and date',
    'byNameDESC' => 'Descending by name and date',
    'byRegistrationStopDateASC' => 'Registration date ascending', 
    'byRegistrationStopDateDESC' => 'Registration date descending', 
];

$GLOBALS['TL_LANG']['tl_module']['conditionsAreMandatory'] = ['Terms and Conditions are mandatory','It is imperative that you agree to the terms and conditions. Do not set this checkbox if you want to remove the checkbox in the template.'];
$GLOBALS['TL_LANG']['tl_module']['gdprAreMandatory'] = ['Data protection regulations are mandatory','You must agree to the data protection regulations. Do not set this checkbox if you want to remove the checkbox in the template.'];



?>