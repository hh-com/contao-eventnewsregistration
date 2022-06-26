<?php

namespace Hhcom\ContaoEventNewsRegistration\Model;

use Contao\Model;
use Contao\StringUtil;

class EnrCategoryModel extends Model
{
    protected static $strTable = 'tl_enr_category';

    public static function findMultipleByIdsWithLink($catArray)
    {
        $categoryCollection = EnrCategoryModel::findMultipleByIds( $catArray );

      
        if ($categoryCollection == null)
            return false;

        foreach($categoryCollection as $catObj) {
            $temp = $catObj->getFrontendUrl();
            $catObj->url = $temp['url'];
            $catObj->categoryListPageId = $temp['categoryListPageId'];
            $catObj->categoryListModuleId = $temp['categoryListModuleId'];
        }

        return $categoryCollection;

    }

    public function getFrontendUrl() {
        
        $objDatabase = \Database::getInstance();
        $objEntity = $objDatabase->prepare( 'SELECT * FROM tl_module WHERE type like "enrCategory" ' )->execute();

        $categoryListPages = [];
        foreach ($objEntity->fetchAllAssoc() as $catModule) {
            $categoryIds = StringUtil::deserialize($catModule['enr_categories'], true);
            foreach($categoryIds as $id) {
                if (!isset($categoryListPages[$id])) {
                    $categoryListPages[$id] = $catModule;
                }
            }
        }

        $objPage = \PageModel::findByPk($categoryListPages[$this->id]['jumpTo']);
        if ($objPage) {
           
            return  [
                'categoryListModuleId' => $categoryListPages[$this->id]['id'],
                'categoryListPageId' => $categoryListPages[$this->id]['jumpTo'],
                'url' => $objPage->getFrontendUrl("/".$this->alias), 
            ];
        }

        return  [
            'categoryListModuleId' => 0,
            'categoryListPageId' =>  0,
            'url' => '', 
        ];
    }

}

?>