<?php

namespace Hhcom\ContaoEventNewsRegistration\Model;

use Contao\Model;
use Contao\StringUtil;
use Contao\ModuleModel;
use Hhcom\ContaoEventNewsRegistration\Classes\EventNewsRegistration;

class EnrModel extends Model
{
    protected static $strTable = 'tl_enr';

    public function loadDetails()
    {
        $this->categoryCollection = false;
        $this->locationData = false;
        $this->organiserData = false;

        
        if ($this->categories) {
            $categories = EnrCategoryModel::findMultipleByIdsWithLink(explode(',', $this->categories));
            $this->mainCategory = $categories[0];
            $this->categoriesCollection = $categories;
        }

        if ($this->location || $this->location_custom) {

            if ($this->location_custom) {
                $tmpLoc = new \stdClass();
                $tmpLoc->location = $this->location_custom;
            } else {
                $tmpLoc = EnrLocationModel::findById($this->location);
            }
            $this->locationData = $tmpLoc;
        }
 
        if ($this->organiser || $this->organiser_custom) {
            
            if ($this->organiser_custom) {
                $tmpOrga = new \stdClass();
                $tmpOrga->organiser = $this->organiser_custom;
            } else {
                $tmpOrga = EnrOrganiserModel::findById($this->organiser);
            }

            $this->organiserData = $tmpOrga;
        }

        $this->eventDate = EventNewsRegistration::getEventDate($this);

        $this->eventLink = "";
        $this->categoryLink = "";
        if ($this->mainCategory) {

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

            $eventLink = false;
            if ($categoryListPages[$this->mainCategory->id]) {

                $listMod = ModuleModel::findById($categoryListPages[$this->mainCategory->id]['listViewModule']);
                $objPage = \PageModel::findByPk($listMod->jumpTo);
                $eventLink = $objPage->getFrontendUrl( "/" . $this->alias);
            }

            if ($eventLink == false) {
                EventNewsRegistration::logSomething(
                    "Event ID:".$this->id." has no detail link. Has a category been assigned? Have all the necessary categories been selected in the category module?", "info", __CLASS__, __FUNCTION__);
            }

            $this->eventLink = $eventLink;
            $this->categoryLink = $this->mainCategory->url;
        }

        return $this;
    }
}

?>