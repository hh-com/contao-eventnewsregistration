## Event and News with Registration for Contao

This module adds standalone, simple event management that is not linked to the Contao event bundle.
Create categories and assign them (one or more) to the events. An event registration (with the number of places available and the price) can be activated for each event.
Currently this module is not multilanugage.
A registration email/confirmation email can be activated in the settings.
Registration is possible for registered and non-registered users.
Non-registered users need Javascript to register for an event.


## Install

``` code
$ composer require hh-com/contao-eventnewsregistration
```

or copy to:  
root  
\- src  
\- - hh-com  
\- - - contao-eventnewsregistration  

Update your contao installation composer.json
``` code
"repositories": [
    {
        "type": "path",
        "url": "src/hh-com/contao-eventnewsregistration",
        "options": {
                "symlink": true
        }
    }
],
"require": {
    ...
    "hh-com/contao-eventnewsregistration": "@dev",
    ... 
}
```


**Backend configuration**
1. create categories (and/or location, organiser)
2. create a reader page
3. create a listview page
4. create a registration-form page (you can also insert the registration form on the reader page)
5. create a registration-thank-you page
6. create a registration-confirmed page
5. create the listview module and insert it into page
6. create the reader module and insert it into page
7. create the categories module and insert it into page
8. create the registration module and insert it into reader page or on the registration-form page

# Screenshots
![eventnewsregistration](https://user-images.githubusercontent.com/8200853/175809937-908e0aef-8c35-4b41-a1ff-963fc2904d94.png)

![image](https://user-images.githubusercontent.com/8200853/175811026-2895a2a4-5b89-44fc-8057-b87fceed29f7.png)

![image](https://user-images.githubusercontent.com/8200853/175810958-93c10408-e464-43f0-96a8-4c8fcab53b0f.png)



**Changelog**
* init
