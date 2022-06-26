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
use Contao\Validator;
use Contao\Controller;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrRegistrationModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Hhcom\ContaoEventNewsRegistration\Classes\EventNewsRegistration;
use Symfony\Component\Security\Core\Security;


class EnrReaderFrontendController extends AbstractFrontendModuleController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {

        $eventAlias = \Input::get('auto_item');

        if (!\Validator::isAlias($eventAlias)) {
            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        $eventObj = EnrModel::findOneByAlias($eventAlias);

        if (null === $eventObj ) {
            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
        }

        $eventObj->loadDetails();
        
        if ( ! EventNewsRegistration::eventIsVisible($eventObj) ) {
            throw new \CoreBundle\Exception\PageNotFoundException( 'Page not found: ' . \Environment::get('uri') );
            #Controller::redirect(  \Environment::get('base') );
        }

        global $objPage;

        $usergroupAllowedToRegister = array_filter(StringUtil::deserialize($model->registrationGroup, true));
        $registrationInfos = EventNewsRegistration::getAllRegistrationInformation($eventObj, $usergroupAllowedToRegister);

        if ($eventObj->orderSRC) {
            $eventObj->preparedImages = EventNewsRegistration::prepareEventImages($eventObj->orderSRC, $model->imgSize);
        }

        if ($eventObj->orderSRCFile) {
            $eventObj->preparedFiles = EventNewsRegistration::prepareEventFiles($eventObj->orderSRCFile);
        }


        /**
         * Metadata
         */
        $objPage->pageTitle = $eventObj->title;
        $objPage->description = $eventObj->teaser;
    
        $overviewPage = \PageModel::findByPk($model->overviewPage);
        if ( $overviewPage ) {
            $template->overviewPage = $overviewPage->getFrontendUrl();
        }

        if ($model->jumpTo) {
            $registrationPage = \PageModel::findByPk($model->jumpTo);
            if ( $registrationPage ) {
                $template->jumpToRegistration = $registrationPage->getFrontendUrl( "/" .$eventAlias);
            }
        }
       
        $template->registration = $registrationInfos;
        $template->event = $eventObj;

        return $template->getResponse();
    }

}
