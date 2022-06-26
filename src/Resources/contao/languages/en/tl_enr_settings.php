<?php
$GLOBALS['TL_LANG']['tl_enr_settings']['deactivateJS'] = ['Disable Bundle JS','Disables the supplied JavaScript (e.g. for registration).'];
$GLOBALS['TL_LANG']['tl_enr_settings']['deactivateCSS'] = ['Disable Bundle CSS','Disables the supplied style'];
$GLOBALS['TL_LANG']['tl_enr_settings']['sendEmailRegistrationDone'] = ['Send registration via email','Sends the registration confirmation to the registrant by email.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['sendEmailRegistrationDone_subject'] = ['Subject in the registration confirmation','Enter the subject that is displayed in the registration confirmation.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['email_fromEmail'] = ['Sender email address','Enter the sender email address here.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['email_fromEmail'] = ['Sender name','Enter the sender\'s name here.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['confirmRegistration'] = ['Confirmation of Registration','Select this checkbox if the registrant needs to confirm their registration. A link will appear in the registration confirmation email.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['allowMultipleCategories'] = ['Allow multiple categories per event','Select this checkbox if you want to assign multiple categories to an event.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['eventEndPublishingDate'] = ['END date of publication','Select the automatic insertion of the publication end date here. (*) If there is no event end date, the event start date is used.'];
$GLOBALS['TL_LANG']['tl_enr_settings']['hideRegistrationFormWhenMemberIsLoggedIn'] = ['Hide address data of the registration form for members','Hide the form with the personal data for registered members. The member\'s address data is displayed directly.'];




$GLOBALS['TL_LANG']['tl_enr_settings']['eventEndPublishingDate']['options'] = [
    'nothing' => 'Do not enter a value in the release end date.',
    'startDate' => 'Event start date is entered in publication end date.',
    'endDate' => 'Event end date* is entered in the publication end date.',
    'midnightEndDate' => 'Event end date* (MIDNIGHT) is entered in the release end date.',
    'plus1day' => 'Event end date* plus 1 day is entered in the release end date.',
    'plus1week' => 'Event end date* plus 1 week is entered in the release end date.',
    'plus1month' => 'Event end date* plus 1 month is entered in the release end date.',
    'plus1year' => 'Event end date* plus 1 year is entered in the release end date.',
];
?>