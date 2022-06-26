<?php

declare(strict_types=1);

namespace Hhcom\ContaoEventNewsRegistration\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Contao\StringUtil;
use Contao\Config;
use Contao\Input;
use Contao\Validator;
use Contao\Pagination;
use Contao\Controller;
use Hhcom\ContaoEventNewsRegistration\Classes\EventNewsRegistration;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrRegistrationModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;


class EnrRegistrationController extends AbstractFrontendModuleController
{
    
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {

        if (\Input::get('confirm')) {
            $this->confirmRegistration($model);
        }

        $eventAlias = \Input::get('auto_item');

        if (!\Validator::isAlias($eventAlias)) {
            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        $eventObj = EnrModel::findOneByAlias($eventAlias);

        if (null === $eventObj ) {
            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        #$eventObj->loadDetails();
        if ( ! EventNewsRegistration::eventIsVisible($eventObj)) {
            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
            #Controller::redirect(  \Environment::get('base') );
        }

        $frontendUser = $this->security->getUser();

        $usergroupAllowedToRegister = array_filter(StringUtil::deserialize($model->registrationGroup, true));
        $registrationInfos = EventNewsRegistration::getAllRegistrationInformation($eventObj, $usergroupAllowedToRegister);

        if ($registrationInfos['registrationIsVisible'] == "true" && $registrationInfos['registrationIsPossibleForUser'] == "true") {
            
            $showRegistrationForm = true;

            $GLOBALS['TL_HEAD'][] = "
            <script>
                let userstatus = '".($frontendUser?'loggedin':'loggedout')."';
                let total_places_free = ".($registrationInfos['total_places_free']?:0).";
                let normalPrice = ".(floatval($registrationInfos['normalPrice'])?:0).";
            </script>";

            if ($registrationInfos['total_places_free'] > 0 && \Input::post('FORM_SUBMIT') == 'ENRREGISTRATION') {
                
                $savedRegistration = $this->saveEventRegistration($eventObj, $registrationInfos, $model, $frontendUser);

            
                if ($savedRegistration['somethingIsWrong'] == true) {

                    $message = "Error on register to Event " . $eventObj->id . " | User: " . $frontendUser->id;
                    EventNewsRegistration::logSomething($message, "error", __CLASS__, __FUNCTION__);
                    #echo "Fehler beim Anmeldunge zum Event - wurde geloggt. ";
                   
                    Controller::redirect(  \Environment::get('request') );

                    $showRegistrationForm = false;
                }

            }

        } else {
            $showRegistrationForm = false;
        }

        $template->eventObj = $eventObj;
        $template->frontendUser = $frontendUser;
        $template->showRegistrationForm = $showRegistrationForm;
        $template->registrationInfos = $registrationInfos;
        $template->registrationModulModel = $model;
        $template->conditionsAreMandatory = $model->conditionsAreMandatory;
        $template->gdprAreMandatory = $model->gdprAreMandatory;
        
        return $template->getResponse();
    }

    /**
     * save registration
     */
    public function saveEventRegistration($eventObj, $registrationInfos, $model, $frontendUser) {

        $return = [
            'somethingIsWrong' => false,
            'allData' => [],
            'allDataCount' => 0,
        ];
        /**
         * Honeypot prevention 
         * and time check 
         */
        $somethingIsWrong = false;
        foreach ($_POST['contact'] as $contact) {
            if ($contact['hpot'] != "") {
                $somethingIsWrong = true;
            }
        }
        
        // Dont allow send form within x seconds after opening a page
        if ( time() < (strtotime(\Input::post('SECVAL')) + 5) ) {
            $somethingIsWrong = true;
        }
        $places = intval(\Input::post('places'));

        if ($places > $registrationInfos['total_places_free']) {
            $somethingIsWrong = true;
        }

        /**
         * Clean the input
         */
        $cleanInput = [];
        foreach ($_POST['contact'][0] as $key => $field) {
            if ($key != "hpot") {
                $field = Input::decodeEntities($field);
                $field = Input::xssClean($field, true);
                $field = Input::stripTags($field);
                $field = Input::encodeSpecialChars($field);
                $field = Input::encodeInsertTags($field);
                $cleanInput[$key] = $field;
            }
        }

        if (! Validator::isEmail($cleanInput['email']) ) {
            $somethingIsWrong = true;
        }

        /**
         * If conditions and gdpr are mandatory, the fields has to be submitted
         */
        if ($model->conditionsAreMandatory AND null == $_POST['contact'][0]['conditions']) {
            $somethingIsWrong = true;
        }
        if ($model->gdprAreMandatory AND null == $_POST['contact'][0]['gdpr']) {
            $somethingIsWrong = true;
        }

        if ($somethingIsWrong == true) {
            $return['somethingIsWrong'] = true;
            return $return;
        }

        if (floatval($registrationInfos['normalPrice']) > 0) {
            $totalPrice_incl = $places * floatval($registrationInfos['normalPrice']);
        } else {
            $totalPrice_incl = 0;
        }


        /**
         * Save the data
         */
        $registrationObj = new EnrRegistrationModel();

        $registrationObj->firstname = $cleanInput['firstname'];
        $registrationObj->lastname = $cleanInput['lastname'];
        $registrationObj->email = $cleanInput['email'];
        $registrationObj->street = $cleanInput['street'];
        $registrationObj->postal = $cleanInput['postal'];
        $registrationObj->city = $cleanInput['city'];

        $registrationObj->placesReserved = $places;
        $registrationObj->totalPrice_incl = $totalPrice_incl;
        $registrationObj->regDate = time();
        $registrationObj->eventid = $eventObj->id;
        $registrationObj->uniqueid = EventNewsRegistration::generateUniqueId($eventObj->id);

        if ($frontendUser) {
            $registrationObj->memberid = $frontendUser->id;
        } else {
            $registrationObj->memberid = 0;
        }
        
        $registrationObj->confirmed = 0;

        $registrationObj->conditions = intval(null != $_POST['contact'][0]['conditions']?1:0);
        $registrationObj->gdpr = intval(null != $_POST['contact'][0]['gdpr']?1:0);

        $registrationObj->save();

        if (\Config::get('sendEmailRegistrationDone')){

            EventNewsRegistration::sendMailRegistrationDone($registrationObj, $eventObj);
        }

        $objPage = \PageModel::findByPk($model->jumpTo);
        if ( $objPage ) {
            $jumpToAfter = $objPage->getFrontendUrl() ;
            Controller::redirect(  $jumpToAfter );
        } else {
            global $objPage;
            $jumpToAfter = $objPage->getFrontendUrl() ;
            
        }

        Controller::redirect(  $jumpToAfter );

    }

    /**
     * Confirm a registration and redirect
     */
    public function confirmRegistration($model) {

        $uniqueCode = \Input::get('confirm');

        if (!$uniqueCode || !Validator::isAlphanumeric($uniqueCode)) {
            return;
        }

        $registration = EnrRegistrationModel::findByUniqueid($uniqueCode);

        if (!$registration) {
            return;
        }

        if ( $registration->confirmed == 0) {
            $registration->confirmed = time();
            $registration->save();
        }

        /**
         * Registration Confirmation Link
         */
        $objPageConfirmation = \PageModel::findByPk($model->overviewPage);
        if ( $objPageConfirmation ) {
            $regConfirmationLink = $objPageConfirmation->getFrontendUrl() ;
        } else {
            global $objPage;
            $regConfirmationLink = $objPage->getFrontendUrl() ;
        }

        Controller::redirect(  $regConfirmationLink );

    }



}
