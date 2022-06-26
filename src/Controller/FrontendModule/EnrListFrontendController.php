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
use Contao\Pagination;
use Hhcom\ContaoEventNewsRegistration\Classes\EventNewsRegistration;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class EnrListFrontendController extends AbstractFrontendModuleController
{
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        global $objPage;
        $visibleEvents = [];
        $visibleCategoryIds = [];
        $visibleCategory = null;

        $headlineString = StringUtil::deserialize($model->headline, true)['value'];

        if (Input::get('auto_item')) {

            $visibleCategory = EnrCategoryModel::findByAlias( Input::get('auto_item'));
            $headlineString = $visibleCategory->title;
            $visibleCategoryIds[] = $visibleCategory->id;
            $hidePagination = $model->hardLimit; # Fieldname from tl_module

        } else {

            $visibleCategoryIds = StringUtil::deserialize($model->enr_categories, true);
            $hidePagination = $model->fuzzy; # Fieldname from tl_module
        }

        if (!empty($visibleCategoryIds)) {

            $readerPageObj = PageModel::findById($model->jumpTo);
            
            switch ($model->enr_sorting) {
                case 'byDateASC':
                    $sorting = "startDate ASC";
                    break;
                case 'byNameDESC':
                    $sorting = "title DESC";
                    break;
                case 'byNameASC':
                    $sorting = "title ASC";
                    break;
                case 'byRegistrationStopDateASC':
                    $sorting = "event_registration_stop ASC";
                    break;
                case 'byRegistrationStopDateDESC':
                    $sorting = "event_registration_stop DESC";
                    break;
                default:
                    $sorting = "startDate DESC";
            }

               /* Pagination */
            if ($model->numberOfItems > 0) {
                $limit = $model->numberOfItems;
                $getPagination = \Input::get('page') ? \Input::get('page') : 1;
                if ($getPagination) {
                    $offset = ($getPagination - 1) * $limit;
                } else {
                    $offset = 0;
                }
            } else {
                $limit = 999999999;
                $offset = 0;
            }

            $whereCategories = [];
            foreach ($visibleCategoryIds as $ids) {
                $whereCategories[] = ' concat(",",categories,",") like "%%,'.$ids.',%%" ';
            }
        
            $sql = "
                SELECT id FROM `tl_enr` 
                WHERE (".implode(" OR ",  $whereCategories).")
                AND published = '1'
                AND ( start = '' OR start <= UNIX_TIMESTAMP(NOW()) )
                AND ( stop = '' OR stop >= UNIX_TIMESTAMP(NOW()) )
                ORDER BY ".$sorting."
            ";

            $events = \Database::getInstance()->prepare( $sql ) 
            ->limit($limit, $offset)
            ->execute();

            $allEvents = \Database::getInstance()->prepare( $sql ) 
            ->execute();
            

            if ($events->numRows > 0) {

                if ($model->numberOfItems > 0 AND !$hidePagination ) {
                    $objPagination = new Pagination($allEvents->numRows, $model->numberOfItems);
                    $template->pagination = $objPagination->generate("\n  ");
                }
                
                $count = 1;
                foreach ($events->fetchAllAssoc() as $k => $event) {

                    $visibleEvent = EnrModel::findById($event['id']);

                    if($count == 1) {
                        $visibleEvent->firstLastClass = "first";
                    }
                    if($count == $events->numRows) {
                        $visibleEvent->firstLastClass = "last";
                    }
                    
                    $visibleEvents[] = $visibleEvent->loadDetails($readerPageObj);

                    $count++;
                }

            }
        }

        /**
         * Metadata
         */
        if ($visibleCategory) {
            $objPage->pageTitle = $objPage->title . " - " . $visibleCategory->title;
        }

        $template->headline = $headlineString;
        $template->events = $visibleEvents;
        
        return $template->getResponse();
    }
}
