<?php

namespace Hhcom\ContaoEventNewsRegistration\Picker;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\CoreBundle\Picker\AbstractPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;

class EnrCategoryPickerProvider extends AbstractPickerProvider implements DcaPickerProviderInterface, FrameworkAwareInterface {

    use FrameworkAwareTrait; 

    public function getName(): string {

        return 'enrCategoryPicker';
    }

    public function supportsContext( $context ): bool {

        return 'link' === $context;
    }


    public function supportsValue( PickerConfig $config ): bool {

        return false !== strpos( $config->getValue(), '{{eventnewscategory_url::');
    }


    public function getDcaTable(): string {

        return 'tl_enr_category'; 
    }


    public function getDcaAttributes(PickerConfig $config): array {

        $attributes = ['fieldType' => 'radio'];

        if ($source = $config->getExtra('source')) {

            $attributes['preserveRecord'] = $source;
        }

        if ($this->supportsValue($config)) {

            $strTag = str_replace('{{', '', $config->getValue() );
            $strTag = str_replace('}}', '', $strTag );
            $arrValues = explode( '::', $strTag );
           
            if ( is_array( $arrValues ) && isset( $arrValues[1] ) ) {
                $arrValues = explode( '|', $arrValues[1] );
                $strValue = $arrValues[1];
            }
            $attributes['value'] = $strValue;
        }
      
        return $attributes;
    }


    public function convertDcaValue( PickerConfig $config, $value ): string {

        $categoryId = "ERRORthisCategorDoesNotExist";

        $categoryModel = EnrCategoryModel::findMultipleByIdsWithLink([$value]);

        if ($categoryModel != null) {
            $categoryId = $categoryModel->id;
        }
        return '{{eventnewscategory_url::'.$categoryId.'}}';
    }


    protected function getRouteParameters(PickerConfig $config = null): array {

        $arrParams = [ 'do' => 'eventnewsregistration', 'table' => 'tl_enr_category'  ];

        return $arrParams;
    }
}