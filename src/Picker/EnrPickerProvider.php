<?php

namespace Hhcom\ContaoEventNewsRegistration\Picker;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\CoreBundle\Picker\AbstractPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;

class EnrPickerProvider extends AbstractPickerProvider implements DcaPickerProviderInterface, FrameworkAwareInterface {
    
    use FrameworkAwareTrait;
    
    public function getName(): string {

        return 'enrPicker';
    }

    public function supportsContext( $context ): bool {

        return 'link' === $context;
    }


    public function supportsValue( PickerConfig $config ): bool {

        return false !== strpos( $config->getValue(), '{{eventnews_url::');
    }


    public function getDcaTable(): string {

        return 'tl_enr';
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
                $strValue = $arrValues[0];
            }
            $attributes['value'] = $strValue;
        }
       
        return $attributes;
    }


    public function convertDcaValue( PickerConfig $config, $value ): string {

        $eventId = "ERRORpleaseAddCategoriesToThisEvent";
        $objDatabase = \Database::getInstance();
        $objEntity = $objDatabase->prepare( 'SELECT * FROM tl_module WHERE type like "enrList" AND jumpTo > 0 ORDER BY enr_defaultreader DESC' )->execute( );

        if ($objEntity->numRows < 1) {
            die("Please create a EventNewsRegistrationList with EventNewsRegistrationReader-RedirectLink!");
        }

        $eventObj = EnrModel::findOneById(intval($value));
        $eventObj->loadDetails();

        if ($eventObj->mainCategory != null) {
            $eventId = $eventObj->id;
        }

        return '{{eventnews_url::'.$eventId.'}}';

    }


    protected function getRouteParameters(PickerConfig $config = null): array {

        $arrParams = [ 'do' => 'eventnewsregistration' ];

        return $arrParams;
    }
}