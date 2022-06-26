<?php

declare(strict_types=1);

namespace Hhcom\ContaoEventNewsRegistration\EventListener;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\News;
use Contao\NewsFeedModel;
use Contao\NewsModel;
use Contao\StringUtil;
use Hhcom\ContaoEventNewsRegistration\Classes\EventNewsRegistration;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;
use Hhcom\ContaoEventNewsRegistration\Picker\EnrPickerProvider;

/**
 * @internal
 */
class InsertTagsListener
{
    private const SUPPORTED_TAGS = [
        'eventnews_url',
        'eventnewscategory_url'
    ];

    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * @return string|false
     */
    public function __invoke(string $tag, bool $useCache, $cacheValue, array $flags)
    {
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);

        if (\in_array($key, self::SUPPORTED_TAGS, true)) {
            return $this->replaceEventNewsRegistrationInsertTags($key, $elements[1], array_merge($flags, \array_slice($elements, 2)));
        }

        return false;
    }

    private function replaceEventNewsRegistrationInsertTags(string $insertTag, string $idOrAlias, array $arguments): string
    {

        if($insertTag == "eventnews_url") {

            $eventModel = EnrModel::findByPk(intval($idOrAlias));

            if (null === $eventModel) {
                return '';
            }
    
            // Todo: Custom Redirect URL if Event doesnt exist anymore
            if (false === EventNewsRegistration::eventIsVisible($eventModel)) {
                return '';
            }
    
            $eventModel->loadDetails();
    
            switch ($insertTag) {
                case 'eventnews_url':
                    return $eventModel->eventLink;
            }
        }
        
        if($insertTag == "eventnewscategory_url") {

            $categoryModel = EnrCategoryModel::findMultipleByIdsWithLink( [intval($idOrAlias)] );
           
            if (null == $categoryModel) {
                return '';
            }

            switch ($insertTag) {
                case 'eventnewscategory_url':
                    return $categoryModel->url;
            }
        }

        return '';
    }
}