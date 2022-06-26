<?php


$GLOBALS['TL_LANG']['tl_module']['enr_legend'] = "Event/News Registration Einstellungen";

$GLOBALS['TL_LANG']['tl_module']['enr_defaultreader'] = 
    ['Auswahl ist die Default Reader-Seite',
     'Wenn Sie mehr als eine Reader-Seite verwenden, benötigen Sie eine Default Reader Seite. Diese wird für InsertTags und Weiterleitungen verwendet.'
    ];

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Listview'] = 
    ['Weiterleitung zum Event-Reader',
     'Wählen Sie hier die Seite des Event-Readers aus.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Readerview'] =
    ['Weiterleitung zum Registrierungs-Formular',
    'Wählen Sie hier die Seite zum Registrierungs-Formular aus. Treffen Sie KEINE Auswahl, wenn das Formular auf der selben Seite ist.'
    ]; 
    
$GLOBALS['TL_LANG']['tl_module']['overviewPage_Readerview'] = 
    ['Zurück-Buttom zur Listenansicht',
    'Wählen Sie hier die Seite der Listenansicht aus.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Categories'] = 
    ['Weiterleitung zur Eventlisten-Übersicht',
     'Wählen Sie hier die Seite aus, auf der das Eventlisten Modul eingebunden ist.'
    ]; 
$GLOBALS['TL_LANG']['tl_module']['hideEmptyCategory'] = 
    ['Leere Kategorien nicht anzeigen',
    'Kategorien, die keine Zuweisung haben ausblenden.'
    ];

$GLOBALS['TL_LANG']['tl_module']['listViewModule'] = 
    ['Listenansicht auswählen',
    'Die Kategorie verlinkt zu dieser Listenansicht der Events/News.'
    ];

$GLOBALS['TL_LANG']['tl_module']['enr_categories_Listview'] = 
    ['Teaser folgender Kategorien anzeigen',
     'Wählen Sie hier die Kategorien aus, deren Teaser in der Liste angezeigt wird, wenn keine Kategorie in der URL übergeben wird.'
    ];

$GLOBALS['TL_LANG']['tl_module']['headline_Listview'] = 
    ['Fallback Headline wird angezeigt, wenn keine Kategorie übergeben wurde.',
     'Diese Headline wird angezeigt, wenn keine Kategorie übergeben wurde. Andernfalls wird die Kategorie angezeigt.'
    ];

$GLOBALS['TL_LANG']['tl_module']['numberOfItems_Listview'] = 
    ['Anzahl an Elementen',
     'Hier können Sie die Gesamtzahl der Elemente begrenzen. Geben Sie 0 ein, um alle anzuzeigen. Pagination wird, wenn nötig aktiviert.'
    ];
$GLOBALS['TL_LANG']['tl_module']['hardLimit_Listview'] = 
    ['Pagination nicht anzeigen',
     'Pagination deaktiveren.'
    ];
$GLOBALS['TL_LANG']['tl_module']['fuzzy_Listview'] = 
    ['Pagination nicht anzeigen, wenn keine Kategorie übergeben wurde',
     'Wenn keine Kategorie übergeben wurde, wird die Pagination nicht angezeigt.'
    ];
    
$GLOBALS['TL_LANG']['tl_module']['enr_categories_Categories'] = 
    ['Kategorien aktivieren',
     'Wählen Sie die Kategorien, die in der Navigation angezeigt werden sollen.'
    ];

$GLOBALS['TL_LANG']['tl_module']['overviewPage_afterConfirmation'] = 
    ['Weiterleitung nach der Registrierungs-Bestätigung',
     'Wählen Sie hier die Seite, auf der der Besucher weitergeleitet werden soll, nachdem er die Registrierung abgeschickt hat.'
    ];

$GLOBALS['TL_LANG']['tl_module']['jumpTo_Registration'] = 
    ['Weiterleitung nach Absenden des Registrierungs-Formulars.',
     'Wählen Sie hier die Seite aus, auf der das Eventlisten Modul eingebunden ist.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['registrationGroup'] = 
    ['Usergruppen, die sich Anmelden können',
     'Wählen Sie hier die Gruppen, aus, die sich anmelden dürfen.'
    ]; 
    
$GLOBALS['TL_LANG']['tl_module']['enr_sorting'] = 
    ['Sortierung',
     'Wählen Sie hier die Reihenfolge der Sortierung aus.'
    ]; 

$GLOBALS['TL_LANG']['tl_module']['enr_sorting']['ENR_SORTING_TYPE'] = [
    'byDateASC' => 'Nach Datum aufsteigend',
    'byDateDESC' => 'Nach Datum absteigend',
    'byNameASC' => 'Nach Name und Datum aufsteigend',
    'byNameDESC' => 'Nach Name und Datum absteigend',
    'byRegistrationStopDateASC' => 'Registrierungs Datum aufsteigend', 
    'byRegistrationStopDateDESC' => 'Registrierungs Datum absteigend', 
];

$GLOBALS['TL_LANG']['tl_module']['conditionsAreMandatory'] = ['AGB sind verpflichtend','Den AGB\'s muss zwingend zugestimmt werden. Setzten Sie diese Checkbox nicht, wenn sie im Template die Checkbox entfernen möchten.'];
$GLOBALS['TL_LANG']['tl_module']['gdprAreMandatory'] = ['Datenschutzbestimmungen sind verpflichtend','Den Datenschutzbestimmungen muss zwingend zugestimmt werden. Setzten Sie diese Checkbox nicht, wenn sie im Template die Checkbox entfernen möchten.'];





?>