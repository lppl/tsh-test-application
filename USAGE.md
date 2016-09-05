#How to use this app


## Installation

Composer is required. So run:
```bash
    $ composer install
```

Then edit config/database.php for local data storage and run php server.
```bash
    $ php -S localhost:8889 -t ./
``` 


Then navigate for 
- http://localhost:8889/ for application   
- http://localhost:8889/json for json data

Querying for data is the same in application and data versions:
- page=x for pagination
- supplier=xxx for supplier filtering
- cost_rating=x for cost_rating filtering


## Runnit test suite

At this moment unit tests are running over database (I also don't like 
this). So its necessary to have an access to a nice place where payments table
could be created and destroyed without harmful side effects for application.
Write access data into file: ./config/database.test.php

Then run command:
```bash
    $ composer test
```

It will:
- install required packages, but only if they are missing
- run phpunit test suite


## Development of frontend

When dev-server is running there is acive development version of site.
Navigate to 
- http://localhost:8889/dev.php/ for application   
- http://localhost:8889/dev.php/json for json data.


## Important files

#### Entry points - customize app
    index.php   -> production entry points
    dev.php     -> development entry point
    app.php     -> glue Silex with PaymentsController and templates
    
#### Core files
    lib/Local
    ├── PaymentsController.php  -> contains application logic
    ├── PaymentsModel.php       -> database table model
    ├── PaymentsPage.php        -> response data model
    └── PaymentsRequest.php     -> request data model

#### Configuration files
    config
    ├── config.dev.php
    ├── config.php
    ├── database.php
    └── database.test.php

#### Frontend files
    .babelrc                        -> compiler options
    templates
    └── index.phtml                 -> html5 template
    javascript
    ├── jquery.min.js
    ├── jquery.simplemodal.min.js
    ├── payments.es6                -> source file
    ├── payments.js                 -> compiled file
    └── payments.js.map

#### Test files
    test
    └── backend
        ├── bootstrap.php
        ├── phpunit.xml
        ├── Test
        │   ├── ControllerProduceCorrectData.php
        │   ├── FrontendPageIsRunning.php
        │   ├── PullingPaymantsData.php
        │   └── TSH_DbTest.php
        └── TestUtil
            └── DBTools.php
