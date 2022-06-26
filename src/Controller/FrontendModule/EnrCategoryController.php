<?php

declare(strict_types=1);

namespace Hhcom\ContaoEventNewsRegistration\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\Template;
use Contao\PageModel;
use Contao\StringUtil;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\Exception\PageNotFoundException;


class EnrCategoryController extends AbstractFrontendModuleController
{
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $categories = [];

        if ($model->jumpTo == 0) {
            global $objPage;
            $eventListReader = $objPage;
        } else {
            $eventListReader = PageModel::findByPk($model->jumpTo);
        }

        if ($model->enr_categories) {

            $categoryIds = StringUtil::deserialize($model->enr_categories);

            // load all categories once
            $categoriesArr = [];
            $categoriesObjs = EnrCategoryModel::findAll();
            foreach ($categoriesObjs as $c) {
                $categoriesArr[$c->id] = $c;
            }
            $this->checkIfCategoriesHasEvent($categoriesArr);

            foreach($categoryIds as $catId) {
                
                $catObj = $categoriesArr[$catId];
                if ($catObj == null) {
                    continue;
                }

                if($model->hideEmptyCategory == "1" AND !$catObj->hasEvent) {
                    continue;
                }

                $catObj->activeCategory = $this->findActiveCategory($model, $catObj);
                $catObj->url = $eventListReader->getFrontendUrl( "/".$catObj->alias);
                $categories[] = $catObj;
            }
        }

        $template->categories = $categories;

        return $template->getResponse();
    }

    /**
     * Get current active category
     */
    public function findActiveCategory($model, $catObj) {

        global $objPage;
        /**
         * IF we are on a LIST-PAGE
         */
        if ($objPage->id == $model->jumpTo) {
            if (\Input::get('auto_item') == $catObj->alias) {
                return true;
            }
        } else {
            /**
             * 
             * IF we are on a DETAIL-PAGE
             */
            $listViewModel = ModuleModel::findById($model->listViewModule);
            if ($objPage->id == $listViewModel->jumpTo) {

                $eventObj = EnrModel::findOneByAlias(\Input::get('auto_item'));

                if ($eventObj == null) {
                    $objHandler = new $GLOBALS['TL_PTY']['error_404']();
                    $objHandler->generate($objPage->id);
                    die();
                    return false;
                }

                $eventObj->loadDetails();

                if ( $catObj->id == $eventObj->mainCategory->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check all Categories if there is at lease one event per category
     */
    public function checkIfCategoriesHasEvent(&$categories) {
        
        $whereCat = [];
        foreach ($categories as $cat) {
            $whereCat[] = ' concat(",",categories,",") like "%%,'.$cat->id.',%%" ';
        }
    
        $whereCategories = " (" . implode(" OR  ", $whereCat) . " ) ";
        
        $sql = "
            SELECT id, categories FROM `tl_enr` 
            WHERE ".  $whereCategories."
            AND published = '1'
            AND ( start = '' OR start <= UNIX_TIMESTAMP(NOW()) )
            AND ( stop = '' OR stop >= UNIX_TIMESTAMP(NOW()) )
        ";

        $events = \Database::getInstance()->prepare( $sql ) 
        ->execute();

      
  
        if ($events->numRows > 0) {
            $existingCats = [];
            foreach ($events->fetchAllAssoc() AS $event) {
                $existingCats = array_merge($existingCats, explode(",", $event['categories']));
            }
            $existingCats =array_filter(array_unique($existingCats));
          
            foreach ($categories as $k => $cat) {
                
                if (in_array($cat->id,$existingCats)) {
                    $categories[$k]->hasEvent = true;
                }
            }
        }

        return $categories;

    }

    /**
     * Check if one categorie has at least on event
     */
    public function checkIfCategoryHasEvent($categoryId) {
        
        $whereCategories = ' concat(",",categories,",") like "%%,'.$categoryId.',%%" ';
        
        $sql = "
            SELECT id FROM `tl_enr` 
            WHERE ".  $whereCategories."
            AND published = '1'
            AND ( start = '' OR start <= UNIX_TIMESTAMP(NOW()) )
            AND ( stop = '' OR stop >= UNIX_TIMESTAMP(NOW()) )
        ";

        $events = \Database::getInstance()->prepare( $sql ) 
        ->execute();

        if ($events->numRows > 0) {
            return true;
        }

        return false;

    }

    
}
