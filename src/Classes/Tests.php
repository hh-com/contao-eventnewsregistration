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
use Contao\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\StringUtil;
use Hhcom\ContaoEventNewsRegistration\Model\EnrRegistrationModel;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Hhcom\ContaoEventNewsRegistration\Model\EnrCategoryModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrLocationModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrModel;
use Hhcom\ContaoEventNewsRegistration\Model\EnrOrganiserModel;

// Tests::run();
class Tests extends Controller
{

    public function __construct() {}

    public static function run () {

        if (true) {
            #Tests::createCategories();
            #Tests::createOrganiser();
            #Tests::createLocations();
            #Tests::createEvents();
            #Tests::createRegistrations(); 
        }

        // Remove all from model
        // foreach(EnrRegistrationModel::findAll() as $m) {
        //     $m->delete();
        // }

    }

    public static function createRegistrations () {


        for ($i=0; $i < 2000; $i++) { 

            $event = EnrModel::findBy(['id > ?', 'event_registration > ?'], ['0', '0'], ['order' => ' rand() ', 'limit' => '1', 'return' => 'Model']);
            
            $person = Tests::oneFakeData();    
            
            $registrationObj = new EnrRegistrationModel();

            $registrationObj->firstname = explode(" ", $person['name'])[0] . substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 1);
            $registrationObj->lastname =  explode(" ", $person['name'])[1] . substr(str_shuffle("abcdefghiklmnopqrstuvwxyz"), 0, 3);
            $registrationObj->email = $person['email'];
            $registrationObj->street = $person['address'];
            $registrationObj->postal = $person['postalZip'];
            $registrationObj->city = $person['region'];

            $places = rand(2,10);
    
            $registrationObj->placesReserved = $places;
            $registrationObj->totalPrice_incl = $event->event_normalPrice * $places;
            $registrationObj->regDate = $event->event_registration_stop - rand(777600, 25920000);
            $registrationObj->eventid = $event->id;
            $registrationObj->uniqueid = EventNewsRegistration::generateUniqueId($event->id);
            $registrationObj->memberid = "0";
            $registrationObj->confirmed = 0;
            $registrationObj->conditions = "1";
            $registrationObj->gdpr = "1";
    
            $registrationObj->save();

        }
    }


    public static function createEvents () {

        for ($i=0; $i < 1000; $i++) { 
            
            $model = new EnrModel();

            $teaser = file_get_contents('https://loripsum.net/api/1/short/plaintext');
            $headline = substr($teaser, 0, rand(16, 45));

            $startDate = rand(1624643748, 1719338148);
            $endDate = $startDate + rand(14400, 432000);

            $model->backend_title = $headline . " - " . date("d.m.Y H:i", $startDate);
            $model->title = $headline;
            $model->alias = substr(StringUtil::generateAlias($model->backend_title), 0 , 100);

            $model->teaser = $teaser;
            $model->description = file_get_contents('https://loripsum.net/api/'.rand(1,6).'/short/headers/link');
            $model->startDate = $startDate;
            $model->endDate = $endDate;
            
            $truefalseShowTime = ['','','1'];
            if ($truefalseShowTime[array_rand($truefalseShowTime, 1)]) 
            {
            
                $image = new File( "files/test/" . $model->alias. ".jpg" );
                $image->write( file_get_contents('https://picsum.photos/320/240?nocache='.microtime()) );
                $image->close();

                $fileModel = \Dbafs::addResource( $image->path );
                $model->images = serialize([$fileModel->uuid]);
                $model->orderSRC = serialize([$fileModel->uuid]);
                
            }
          
            $model->categories = EnrCategoryModel::findBy(['id > ?'], ['0'], ['order' => ' rand() ', 'limit' => '1', 'return' => 'Model'])->id;
            $model->organiser = EnrOrganiserModel::findBy(['id > ?'], ['0'], ['order' => ' rand() ', 'limit' => '1', 'return' => 'Model'])->id;
            $model->location = EnrLocationModel::findBy(['id > ?'], ['0'], ['order' => ' rand() ', 'limit' => '1', 'return' => 'Model'])->id;
            
            $model->showTime = $truefalseShowTime[array_rand($truefalseShowTime, 1)];

            if ( array_rand(['','1'], 1) == '1') {
                $model->event_registration = 1;
                $model->event_maxplaces = rand(20,80);
                $model->event_normalPrice = rand(45,99);
            }

            $model->event_registration_stop = $startDate - 86400;
            
            $truefalsePublished = ['1','1','1','1','1','1','1',''];
            $model->published = $truefalsePublished[array_rand($truefalsePublished, 1)];

            #$model->start = $startDate - 7776000;
            $model->stop = $endDate;


            $model->save();

        }
    }

    public static function createCategories () {
        
        $categories = ['Apps & Games','Arts, Crafts, & Sewing','Automotive Parts & Accessories','Computers','Electronics','Movies & TV','Music','Office','Sport','Tools','Home','Garden','Outdoor','Food','Restaurants','Beauty & Personal Care','Handmade','Books','Animals','Software'];
        foreach ($categories as $category) {
            $model = EnrCategoryModel::findByTitle($category);
            if (!$model) {
                $model = new EnrCategoryModel();
                $category = $category . " " . rand(100,999);
            } 
            $model->title = $category;
            $model->alias = StringUtil::generateAlias($model->title);
            $model->save();
        }
    }

    public static function createOrganiser () {
        
        $organiser = ['Isabella R.','Frank X.','Harry H.','Georg G.','Bill Tor','Bart S.','Albert Einstein','Edward M.','Leo F.','Kelly F','JBO','Sandra Bullock','Thomas','Company X','Organisation O.','Procter','Ashley','Andrew','Julie Kuper','Mongo DB'];
        foreach ($organiser as $orga) {

            $model = EnrOrganiserModel::findByOrganiser($orga);
            if (!$model) {
                $model = new EnrOrganiserModel();
                $orga = $orga . " " . rand(100,999);
            } 
            $model->organiser = $orga;
            $model->alias = StringUtil::generateAlias($model->organiser);
            $model->url = "https://www.google.com";
            $model->save();
        }
    }

    public static function createLocations () {
        
        $locations = ['London','Madrid','Buenos Aires','Sydney','Cardiff','Mexico City','Berlin','New York','Malibu','Vienna','Linz','Tennessee','Florida','Victoria Falls','Crand Canyon National Park','Schotterteich','Bora Bora','Arashiyama Bamboo Grove','Stonehenge','Amalfi Coast'];
        foreach ($locations as $location) {

            $model = EnrLocationModel::findByLocation($location);
            if (!$model) {
                $model = new EnrLocationModel();
                $location = $location . " " . rand(100,999);
            } 
            $model->location = $location;
            $model->alias = StringUtil::generateAlias($model->location);
            $model->url = "https://www.borabora.com";
            $model->save();
        }
    }

    public static function oneFakeData () {

        $data = [
            [
                "name" => "Geraldine Hewitt",
                "postalZip" => "4192 NR",
                "text" => "est arcu ac orci. Ut semper pretium neque. Morbi quis",
                "numberrange" => "1",
                "region" => "İzmir",
                "phone" => "1-465-640-7527",
                "email" => "fermentum.risus.at@icloud.edu",
                "country" => "Mexico",
                "currency" => "$40.78",
                "alphanumeric" => "GNN51XNG2KL",
                "list" => "17",
                "address" => "7694 Tristique Street"
            ],
            [
                "name" => "Callie Johnson",
                "postalZip" => "8636",
                "text" => "ipsum nunc id enim. Curabitur massa. Vestibulum accumsan neque et",
                "numberrange" => "7",
                "region" => "Ancash",
                "phone" => "(128) 488-2731",
                "email" => "mus.donec@icloud.couk",
                "country" => "India",
                "currency" => "$25.14",
                "alphanumeric" => "MCI33FPC1JH",
                "list" => "5",
                "address" => "6040 Neque. St."
            ],
            [
                "name" => "Rinah Bishop",
                "postalZip" => "631228",
                "text" => "Phasellus elit pede, malesuada vel, venenatis vel, faucibus id, libero.",
                "numberrange" => "3",
                "region" => "Limousin",
                "phone" => "1-211-562-5078",
                "email" => "magna.nec.quam@hotmail.com",
                "country" => "Mexico",
                "currency" => "$3.67",
                "alphanumeric" => "HZX70WFT2NB",
                "list" => "19",
                "address" => "931-5879 Luctus Street"
            ],
            [
                "name" => "Deacon Black",
                "postalZip" => "7163",
                "text" => "vel, vulputate eu, odio. Phasellus at augue id ante dictum",
                "numberrange" => "9",
                "region" => "Tamaulipas",
                "phone" => "(570) 293-3876",
                "email" => "sed.sem.egestas@outlook.net",
                "country" => "Australia",
                "currency" => "$24.35",
                "alphanumeric" => "GPM82VAN3FV",
                "list" => "13",
                "address" => "156-2256 Tristique Av."
            ],
            [
                "name" => "Malik Soto",
                "postalZip" => "5872",
                "text" => "Pellentesque ut ipsum ac mi eleifend egestas. Sed pharetra, felis",
                "numberrange" => "1",
                "region" => "Alsace",
                "phone" => "1-386-770-3135",
                "email" => "et.rutrum.non@icloud.ca",
                "country" => "Brazil",
                "currency" => "$48.13",
                "alphanumeric" => "NEM31CBZ6MG",
                "list" => "1",
                "address" => "818-1705 A Street"
            ]
        ];

        return $data[rand(0, (count($data) - 1) )];
        
    }

}