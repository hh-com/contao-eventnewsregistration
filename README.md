## Event and News with Registration for Contao


## Install

Copy to:  
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


***Backend configuration
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

