<?php

declare(strict_types=1);

namespace Hhcom\ContaoEventNewsRegistration\Classes;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\Template;
use Contao\Config;
use Contao\Controller;
use Contao\Date;
use Contao\Environment;
use Contao\Email;
use Contao\FrontendTemplate;
use Contao\System;
use Symfony\Component\Security\Core\Security;
use Contao\FilesModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\StringUtil;
use Hhcom\ContaoEventNewsRegistration\Model\EnrRegistrationModel;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;

class EventNewsRegistration extends Controller
{

    /**
     * Generate the Event URL
     */
    public static function getActive($eventObj) {

        if (false === $eventObj->categoriesCollection)
            return false;
        
        // There could be only ONE Readerpage
        $category = $eventObj->categoriesCollection[0];
        $objPage = \PageModel::findByPk($category->readerpage);

        return $objPage->getFrontendUrl( "/" . $eventObj->alias);
        
    }

    public static function eventIsVisible($eventObj) {
        
        $time = Date::floorToMinute();

        // The target page has not been published (see #5520)
        if (! $eventObj->published || 
            ($eventObj->start != '' && $eventObj->start > $time) || 
            ($eventObj->stop != '' && $eventObj->stop <= ($time + 60))
        )
        {
            return false;
        } 

        return true;
    }

    public static function getEventDate($eventObj) {
        
        $start = (int) $eventObj->startDate;
        $end = (int) $eventObj->endDate;

        $return = [
            'singleDay' => false,
            'dateTimeIsVisible' => false,
            'showTime' => false,
            'showEndDate' => false,
            'startDate' => 0,
            'endDate' => 0,
            'startTime' => 0,
            'endTime' => 0,
            'schemaStartDate' => 0,
            'schemaEndDate' => 0,
        ];

        if ((date('dmy', $start) == date('dmy', $end)) OR $end == 0 ) {
            $return['singleDay'] = true;
        }

        if ($start > 0 || $end > 0) {
            $return['dateTimeIsVisible'] = true;
        }

        // Check if endday should be shown
        if($eventObj->endDate AND $return['singleDay'] == false ) {
            $return['showEndDate'] = true;
        }

        if ($eventObj->showTime == "1") {
            $return['showTime'] = true;
            $dateFormat = Config::get('datimFormat');
        } else {
            $dateFormat = Config::get('dateFormat');
        }

        $return['startDate'] =  Date::parse($dateFormat, $start);
        $return['endDate'] =  Date::parse($dateFormat, $end);
        $return['startTime'] =  Date::parse(Config::get('timeFormat'), $start);
        // Dont forget to set timezone in backend!
        $return['schemaStartDate'] =  $eventObj->showTime ? Date::parse('Y-m-d\TH:i:sP', $start) : Date::parse('Y-m-d', $start); 
        $return['schemaEndDate'] =  $eventObj->showTime ? Date::parse('Y-m-d\TH:i:sP', $end) : Date::parse('Y-m-d', $end); 

        return $return;
    }


    public static function prepareEventImages($imageArray, $imageSize) {

        $preparedImage = false;

        $imageSizeDefault = [ 0 => '1920', 1 => '900', 2 => 'crop' ];

        if ( !empty(array_filter(StringUtil::deserialize($imageSize)))) {

            $size = StringUtil::deserialize($imageSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]) || ($size[2][0] ?? null) === '_')
            {
                $imageSizeDefault = [ 0 => $size[0], 1 => $size[1], 2 => $size[2] ];
            } 

        }
        
        if ( !empty(array_filter(StringUtil::deserialize($imageArray)))) {

            $imageUuids = StringUtil::deserialize($imageArray);
            $imageCollection = FilesModel::findMultipleByUuids($imageUuids);

            $container = \System::getContainer();
            $rootDir = $container->getParameter('kernel.project_dir');

            foreach ($imageCollection as $imgModel) {

                
                $imagePath = $container
                ->get('contao.image.image_factory')
                ->create($rootDir.'/'.$imgModel->path, $imageSizeDefault )
                ->getUrl($rootDir);

                $preparedMeta = ['alt'=>'', 'title'=>''];

                if ($imgModel->meta != null) {
                    $preparedMeta = reset(StringUtil::deserialize($imgModel->meta, true)); 
                }

                $preparedImage[] = [
                    'model' => $imgModel,
                    'path' => $imagePath,
                    'meta' => $preparedMeta
                ];
            }
        }

        return $preparedImage;
    }


    public static function prepareEventFiles($fileArray) {

        $preparedFiles = false;
    
        if ( !empty(array_filter(StringUtil::deserialize($fileArray)))) {

            $fileUuids = StringUtil::deserialize($fileArray);

            $fileCollection = FilesModel::findMultipleByUuids($fileUuids);

            foreach($fileCollection as $file) {

                if ($file->found == 1) {
                    $preparedFiles[] = [
                        'model' => $file,
                        'path' => $file->path,
                        'name' => $file->name,
                    ];
                }
            }

        }

        return $preparedFiles;
    }


    public static function getAllRegistrationInformation($eventObj, Array $usergroupAllowedToRegister) {

        $reg = [
            'registrationIsActive'                  => false,
            'registrationIsPossibleForUser'         => false,
            'registrationIsVisible'                 => false,
            'registrationIsPossibleForLoggedInUser' => false,
            'hideRegistrationFormWhenMemberIsLoggedIn' => false,
            'maxplaces'                             => $eventObj->event_maxplaces,
            'total_places_taken'                    => 0,
            'total_places_free'                     => 0,
            'registration_stop'                     => $eventObj->event_registration_stop,
            'places_already_reserved'               => 0,
            'normalPrice'                           => 0,
        ];

        if ($eventObj->event_registration != "1") {
            return $reg;
        }

        $reg['registrationIsActive'] = true;

        if ($eventObj->event_normalPrice === "0") {
            $reg['normalPrice'] = "free";
        } elseif ($eventObj->event_normalPrice === "") {
            $reg['normalPrice'] = "hide";
        } else {
            $reg['normalPrice'] = floatval($eventObj->event_normalPrice);
        }
        

        $currentRegistration = EnrRegistrationModel::findByEventid($eventObj->id);
        if ($currentRegistration == null) {
            $currentRegistrationCount = 0;
        } else{
            $currentRegistrationCount = @$currentRegistration->count();
        }
      

        $security = System::getContainer()->get('security.helper'); 

        if (
            $eventObj->event_registration && 
            !empty($usergroupAllowedToRegister) && (
                ($security->getUser() == NULL && in_array("loggedout", $usergroupAllowedToRegister)) ||
                ($security->getUser() != NULL && in_array("loggedin", $usergroupAllowedToRegister)) ||
                $security->isGranted('contao_member.groups', $usergroupAllowedToRegister)
                
            )
            
        ) {
            $reg['registrationIsPossibleForUser'] = true;
        }

        if ($security->getUser() != NULL) {
            $frontendUser = $security->getUser();
            $placesReserved = 0;
            $reservations = EnrRegistrationModel::findByEventid($eventObj->id);

            if ($reservations != null) {
                foreach ($reservations as $row) {
                    if ($frontendUser->id == $row->memberid) {
                        $placesReserved = $placesReserved + $row->placesReserved;
                    }
                }
            }
            $reg['places_already_reserved'] = $placesReserved;

        }

        if ($security->getUser() != NULL && \Config::get('hideRegistrationFormWhenMemberIsLoggedIn') == "1") {
            $reg['hideRegistrationFormWhenMemberIsLoggedIn'] = true;
        }



        if ( ($eventObj->event_registration_stop == "" || time() <= $eventObj->event_registration_stop) && 
            time() <  $eventObj->startDate &&
            $currentRegistrationCount < $eventObj->event_maxplaces
        ) {
            $reg['registrationIsVisible'] = true;
        }

        $reg['total_places_taken'] = $currentRegistrationCount;
        $reg['total_places_free'] =  $eventObj->event_maxplaces - $currentRegistrationCount;

        // if ($reg['total_places_free'] == 0) {
        //     $reg['registrationIsPossibleForUser'] = false;
        // }

        $ugtmp = $usergroupAllowedToRegister;
        unset($ugtmp['loggedout']);
        if (!empty($ugtmp)) {
            $reg['registrationIsPossibleForLoggedInUser'] = true;
        }

        return $reg;

    }

    /**
     * Log something
     * $level: error, info
     * EventNewsRegistration::logSomething($message, "error", __CLASS__, __FUNCTION__);
     */
    public static function logSomething($message, $level, $class, $method ) {
        \System::getContainer()
        ->get('monolog.logger.contao')
        ->log($level, $message, array(
        'contao' => new ContaoContext($class.'::'.$method, TL_GENERAL
        )));
        
    }

    /**
     * generate an EAN Code with lenght 12 
     * 1-5 = Event ID (max. 99999 events!)
     * 6-7 = Year of event
     * 8-12 = Unique Code (max. 9999 participants per event!)
    **/
    public static function generateUniqueId($eventId)
    {
        $charSet = "0123456789";

        $position = 0;
        $uniqueId = substr(str_repeat("0", 5).$eventId, - 5); // 4 Stellig: Event Id
        $uniqueId .= date("y", time()); // 2 Stellig: Jahr

        for ($i = 0; $i < 6; $i++)
        {
            $position = ($position + rand(1000, 9999)) % strlen($charSet);
            $uniqueId .= $charSet[$position];
        }

        # UNIQUE ID muss einzigartig sein und darf nicht bereits in der Datenbank vorhanden sein
        $uniquIdResult = \Database::getInstance()
            ->prepare("SELECT * FROM tl_enr_registration WHERE uniqueid like ? ")
            ->execute( $uniqueId );
    
        if ($uniquIdResult->numRows > 0 )
        {
            $uniqueId = self::generateUniqueId($eventId);
        }

        return $uniqueId;
    }
    
    // /**
    //  * generate a unique Key
    //  */
    // public static function generateUniqueId()
    // {
    //     $charSet = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

    //     $position = 0;
    //     $uniqueId = '';
    //     for ($i = 0; $i < 12; $i++)
    //     {
    //         $position = ($position + rand(1000, 9999)) % strlen($charSet);
    //         $uniqueId .= $charSet[$position];
    //     }

    //     # UNIQUE ID muss einzigartig sein und darf nicht bereits in der Datenbank vorhanden sein
    //     $uniquIdResult = \Database::getInstance()
    //         ->prepare("SELECT * FROM tl_enr_registration WHERE uniqueid like ? ")
    //         ->execute( $uniqueId );
    
    //     if ($uniquIdResult->numRows > 0 )
    //     {
    //         $uniqueId = self::generateUniqueId();
    //     }

    //     return $uniqueId;
    // }

    public static function sendMailRegistrationDone($registrationObj, $eventObj)
    {
        $objEmail = new Email();
        $objEmail->from = \Config::get('email_fromEmail');
        $objEmail->fromName = \Config::get('email_fromName');
        $objEmail->subject = \Config::get('sendEmailRegistrationDone_subject');

        $objTemplate = new FrontendTemplate('mail_registration_done');

        $objTemplate->registrationObj =  $registrationObj;
        $objTemplate->eventObj =  $eventObj;
        $objTemplate->confirmRegistration = false;
        
        if (\Config::get('confirmRegistration')) {
            $objTemplate->confirmRegistration = true;
            $objTemplate->confirmationLink = Environment::get('base') . Controller::addToUrl("confirm=".$registrationObj->uniqueid);
        }

        $objEmail->html = $objTemplate->parse();
        
        $sendReturn = $objEmail->sendTo($registrationObj->email);
        
        $message = "Registrierung zum Event " . $eventObj->id . " | UniqueId: " . $registrationObj->uniqueid . " | Mailreturn: " . var_export($sendReturn, true);
        EventNewsRegistration::logSomething($message, "info", __CLASS__, __FUNCTION__);
        
        return;

    }

    /**
     * Liefert das Veröffentlichungs EndDatum anhand der Einstellungen in den Settings
     */
    public static function getPublishingStopDate($eventStartDate, $eventEndDate) {

        if ($eventEndDate) {
            $publishedStopDate = intval($eventEndDate);
        } else {
            $publishedStopDate = intval($eventStartDate);
        }
        
        switch (\Config::get('eventEndPublishingDate')) {
            case 'nothing':
                $publishedStopDate = "";
                break;
            case 'startDate':
                $publishedStopDate = $eventStartDate;
                break;
            case 'endDate':
                $publishedStopDate = $publishedStopDate;
                break;
            case 'midnightEndDate':
                $publishedStopDate = strtotime(date('d.m.Y', $publishedStopDate) . " tomorrow") - 1;
                break;
            case 'plus1day':
                $publishedStopDate = $publishedStopDate + (86400);
                break;
            case 'plus1week':
                $publishedStopDate = $publishedStopDate + (86400 * 7);
                break;
            case 'plus1month':
                $publishedStopDate = $publishedStopDate + (86400 * 30);
                break;
            case 'plus1year':
                $publishedStopDate =  $publishedStopDate + (86400 * 365);
                break;
            default:
                $publishedStopDate = "";
                break;
        }
			
    
        return $publishedStopDate; 
      
    }


    
   


    


}
