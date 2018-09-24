# Symfony 4 REST API example/boilerplate/demo (no authentication)

This is a boilerplate implementation of Symfony 4 REST API (without authentication). 
It is created with best REST API practices in mind (except authentification). 
REST API interaction more or less follows guidline/summary provided by this excellent 
article: https://blog.mwaysolutions.com/2014/06/05/10-best-practices-for-better-restful-api/

Regarding project itself. Several ideas were in mind, like thin-controller and TDD approach. SOLID principles, speaking names and other good design 
practices were also kept in mind (thankfully Symfony itself is a good primer of this). 
Most business logic is moved from controllers to corresponding services, 
which in turn use other services and Doctrine repositories to execute various DB queries.

That said, there is always room for improvement, so use it as a starting point and modify
according to your requirements. P.S. if you are looking for JWT token based REST API, 
please look at my other Symfony 4 REST API JWT based project located here (which is very
similar to the current one): https://github.com/vgrankin/symfony_4_jwt_restapi_demo

## What this REST API is doing?

This is a simple service app which allows interviewers and candidates to to create availability slots
in the calendar. 

Here are some examples/scenarios of what this API allows to do: 
- interviewer Philipp is available next week each day from 9 AM through 4 PM 
without breaks.
- interviewer Sarah is available from 12 PM to 6 PM on Monday and Wednesday next week, and from
9 AM to 12 PM on Tuesday and Thursday.
- candidate Carl is available for the interview from 9 AM to 10 AM any weekday next week from 10 AM to
12 PM on Wednesday.
- query API for candidate Carl and interviewers Philipp and Sarah for the intersecting time slots.
 
This is a simple project which is used to demonstrate 
how to create and structure REST API services using Symfony 4. 
See "Usage/testing" section.

## Technical details / Requirements:
- Current project is built using Symfony 4.1 framework
- It is based on microservice/API symfony project (symfony/skeleton)
	- https://symfony.com/download
- PHPUnit is used for tests	
	* Note: it is better to run symfony's built-in PHPUnit, not the global one you have on your system, 
			  because different versions of PHPUnit expect different syntax. Tests for this project 
			  were built using preinstalled PHPUnit which comes with Symfony (located in bin folder). 
			  You can run all tests by running this command from project directory: 
			  ./bin/phpunit (php bin/phpunit on Windows). 
			  * Read more here: https://symfony.com/doc/current/testing.html			 
- PHP 7.2.9 is used so you will need something similar available on your system (there are many options to install it: Docker/XAMPP/standalone version etc.)
- MariaDB (MySQL) is required (10.1.31-MariaDB was used during development)
- Guzzle composer package is used to test REST API endpoints
- additional Symfony components were installed for the base skeleton version:
    - doctrine: https://symfony.com/doc/current/doctrine.html
    - ParamConverter: https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-paramconverter
      * to automatically fetch objects

## Installation:
	
    - git clone https://github.com/vgrankin/symfony_4_interview_calendar_rest_api
    
    - go to project directory and run: composer install
    
    * at this point make sure MySQL is installed and is running	
    - open .env filde in project directory (copy .env.dist and create .env file out of it (in same location) if not exists)
    
    - configure DATATABSE_URL
        - This is example of how my .env config entry looks: DATABASE_URL=mysql://root:@127.0.0.1:3306/interview_calendar # user "root", no db pass
    * more infos:
        - https://symfony.com/doc/current/configuration.html#the-env-file-environment-variables
        - https://symfony.com/doc/current/doctrine.html#configuring-the-database
        - https://symfony.com/doc/current/configuration/environments.html
        
    - go to project directory and run following commands to create database using Doctrine:
        - php bin/console doctrine:database:create (to create database called `customer_service`, it will figure out db name based on your DATABASE_URL config)		
        - php bin/console doctrine:schema:update --force (executes queries to create/update all Entities in the database in accordance to latest code)
        
        * example of command execution on Windows machine: C:\Users\admin\PhpProjects\symfony_restapi>php bin/console doctrine:database:create
        * you can preview SQL queries Doctrine will run (without actually executing queries). To do so, run: php bin/console doctrine:schema:update --dump-sql
        * if you need to start from scratch, you can drop database like this: php bin/console doctrine:database:drop --force
        * Run php bin/console list doctrine to see a full list of commands available.
        
    - In order to run PHPUnit tests yourself, you will need to create local version of phpunit.xml:
        - for that, just copy phpunit.xml.dist and rename it to phpunit.xml
        - then add record to phpunit.xml which will tell Symfony which database server (and DB) you want to use specifically for tests:
            * add it right below where it says: "<!-- define your env variables for the test env here -->"
            <env name="DATABASE_URL" value="mysql://root:@127.0.0.1/interview_calendar" /><!-- this is how my config looks like -->
            * read more here: https://symfony.com/doc/4.0/testing/database.html
    - If you want to try this API without manually inserting new records, here are some example records to start with:
    
        DELETE FROM `repeatable_interviewer_slot`;
        DELETE FROM `single_interviewer_slot`;
        DELETE FROM `candidate_slot`;
        DELETE FROM `interviewer`;
        DELETE FROM `candidate`;
                
        INSERT INTO `interviewer` (`id`, `name`) VALUES (1, 'Philipp');
        INSERT INTO `interviewer` (`id`, `name`) VALUES (2, 'Sarah');
                
        INSERT INTO `candidate` (`id`, `name`) VALUES (1, 'Carl');        
    
        * These records are required in order to create interviewer slots and candidate slots using REST API. 
          This is because `repeatable_interviewer_slot` and `single_interviewer_slot` tables require interviewer id
          and `candidate_slot` table requires candidate id (so existing candidate created in advance is required)
          
    
## Implementation details:

- No external libraries are used for this REST API. 
Everything is intentionally coded from scratch 
(as a demo project to explicitly demonstrate REST API application design) 
- In terms of workflow the following interaction is used: to get the job done for any 
given request usually something like this is happening: Controller uses Service 
(which uses Service) which uses Repository which uses Entity. This way we have a good 
thin controller along with practices like Separation of Concerns, Single responsibility 
principle etc.
- App\EventSubscriber\ExceptionSubscriber is used to process all Symfony-thrown exceptions 
and turn them into nice REST-API compatible JSON response (instead of HTML error pages 
shown by default in case of exception like 404 (Not Found) or 500 (Internal Server Error))
- App\Service\ResponseErrorDecoratorService is a simple helper to prepare error responses 
and to make this process consistent along the framework. It is used every time error 
response (such as status 400 or 404) is returned.
- HTTP status codes and REST API url structure is implemented in a way similar to 
described here (feel free to reshape it how you wish): 
https://blog.mwaysolutions.com/2014/06/05/10-best-practices-for-better-restful-api/
- No authentication (like JWT) is used. Application is NOT secured
- All application code is in /src folder
- All tests are located in /tests folder
- In most cases the following test-case naming convention is used: MethodUnderTest____Scenario____Behavior()


## Usage/testing:

    First of all, start your MySQL server and PHP server. Here is example of how to start local PHP server on Windows 10:
    C:\Users\admin\PhpProjects\symfony_restapi>php -S 127.0.0.1:8000 -t public
    * After that http://localhost:8000 should be up and running
    
    * If you use docker, make sure PHP and MySQL (with required database) containers are up and running

You can simply look at and run PHPUnit tests (look at tests folder where all test files are located) 
to execute all possible REST API endpoints. (To run all tests execute this command from project's root folder: 
"php bin/phpunit"), but if you want, you can also use tools like POSTMAN 
(or PHPStorm REST Client/Guzzle/Symfony components etc.) to manually access REST API endpoints. 
Here is how to test all currently available API endpoints:
    
We can use POSTMAN to access all endpoints:

    * Here is a table of possible operations:
    
     -------------------------------------------------- -------- ---------------------------------------
      Name                                               Method   Path
     -------------------------------------------------- -------- ---------------------------------------
      Create candidate slot(s)                           POST     /api/candidate-slots
      Delete candidate slot                              DELETE   /api/candidate-slot/{id}
      Get intersecting candidate/interviewer(s) slots    GET      /api/interview-calendar/{id}
      Create repeatable interviewer slot(s)              POST     /api/repeatable-interviewer-slots
      Delete repeatable interviewer slot                 DELETE   /api/repeatable-interviewer-slot/{id}
      Create single interviewer slot(s)                  POST     /api/single-interviewer-slots
      Delete single interviewer slot                     DELETE   /api/single-interviewer-slot/{id}
     -------------------------------------------------- -------- ---------------------------------------
    
        
    * First of all, clear DB and install some sample data using SQL queries provided above.
    
    ===========================================================    
    - Here is how to access REST API endpoint to create candidate slot:
    
    * let's say we want to execute the following scenario:
    "candidate Carl is available for the interview from 9 AM to 10 AM any weekday next week from 10 AM to
     12 PM on Wednesday." In that case we will run the following request:
    
    method: POST
    url: http://localhost:8000/api/candidate-slots
    Body (select raw) and add this line: 
    
    {
        "candidate_id":1,
        "candidate_slots":[
            {"date":"2018-10-01 09:00 AM"},
            {"date":"2018-10-02 09:00 AM"},
            {"date":"2018-10-03 09:00 AM"},
            {"date":"2018-10-03 10:00 AM"},
            {"date":"2018-10-03 11:00 AM"},
            {"date":"2018-10-04 09:00 AM"},
            {"date":"2018-10-05 09:00 AM"}
        ]
    }            
    
    Response should look similar to this:
    
    {
        "data": {
            "candidate_slots": [
                {
                    "id": 1,
                    "candidate_id": 1,
                    "date": "2018-10-01 09:00 AM"
                },
                {
                    "id": 2,
                    "candidate_id": 1,
                    "date": "2018-10-02 09:00 AM"
                },
                {
                    "id": 3,
                    "candidate_id": 1,
                    "date": "2018-10-03 09:00 AM"
                },
                {
                    "id": 4,
                    "candidate_id": 1,
                    "date": "2018-10-03 10:00 AM"
                },
                {
                    "id": 5,
                    "candidate_id": 1,
                    "date": "2018-10-03 11:00 AM"
                },
                {
                    "id": 6,
                    "candidate_id": 1,
                    "date": "2018-10-04 09:00 AM"
                },
                {
                    "id": 7,
                    "candidate_id": 1,
                    "date": "2018-10-05 09:00 AM"
                }
            ]
        }
    }   
    
    ===========================================================
    - create single interviewer slot:
    
    * "single" type of interviewer slot works similar to candidate slots. We run corresponding endpoint
      and provide data with list of dates and hours when interviewer is available for the interview. 
      We can also specify let's call it "blocking" dates, when interviewer can explicitly specify that
      he/she is unavailable. For that we need to use `is_blocked_slot` set to TRUE.
    
    * let's say we want to execute the following scenario:
    "interviewer Sarah is available from 12 PM to 6 PM on Monday and Wednesday next week, and from
     9 AM to 12 PM on Tuesday and Thursday." In that case we will run the following request:
    
    method: POST
    url: http://localhost:8000/api/single-interviewer-slots
    Body (select raw) and add this line: 
    
    {
        "interviewer_id":2,
        "single_interviewer_slots":[
            {"date":"2018-10-01 12:00 PM","is_blocked_slot":false},
            {"date":"2018-10-01 01:00 PM","is_blocked_slot":false},
            {"date":"2018-10-01 02:00 PM","is_blocked_slot":false},
            {"date":"2018-10-01 03:00 PM","is_blocked_slot":false},
            {"date":"2018-10-01 04:00 PM","is_blocked_slot":false},
            {"date":"2018-10-01 05:00 PM","is_blocked_slot":false},
            {"date":"2018-10-03 12:00 PM","is_blocked_slot":false},
            {"date":"2018-10-03 01:00 PM","is_blocked_slot":false},
            {"date":"2018-10-03 02:00 PM","is_blocked_slot":false},
            {"date":"2018-10-03 03:00 PM","is_blocked_slot":false},
            {"date":"2018-10-03 04:00 PM","is_blocked_slot":false},
            {"date":"2018-10-03 05:00 PM","is_blocked_slot":false},
            {"date":"2018-10-02 09:00 AM","is_blocked_slot":false},
            {"date":"2018-10-02 10:00 AM","is_blocked_slot":false},
            {"date":"2018-10-02 11:00 AM","is_blocked_slot":false},
            {"date":"2018-10-04 09:00 AM","is_blocked_slot":false},
            {"date":"2018-10-04 10:00 AM","is_blocked_slot":false},
            {"date":"2018-10-04 11:00 AM","is_blocked_slot":false}
        ]
    }            
    
    Response should look similar to this:
    
    {
        "data": {
            "single_interviewer_slots": [
                {
                    "id": 1,
                    "interviewer_id": 2,
                    "date": "2018-10-01 12:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 2,
                    "interviewer_id": 2,
                    "date": "2018-10-01 01:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 3,
                    "interviewer_id": 2,
                    "date": "2018-10-01 02:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 4,
                    "interviewer_id": 2,
                    "date": "2018-10-01 03:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 5,
                    "interviewer_id": 2,
                    "date": "2018-10-01 04:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 6,
                    "interviewer_id": 2,
                    "date": "2018-10-01 05:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 7,
                    "interviewer_id": 2,
                    "date": "2018-10-03 12:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 8,
                    "interviewer_id": 2,
                    "date": "2018-10-03 01:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 9,
                    "interviewer_id": 2,
                    "date": "2018-10-03 02:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 10,
                    "interviewer_id": 2,
                    "date": "2018-10-03 03:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 11,
                    "interviewer_id": 2,
                    "date": "2018-10-03 04:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 12,
                    "interviewer_id": 2,
                    "date": "2018-10-03 05:00 PM",
                    "is_blocked_slot": false
                },
                {
                    "id": 13,
                    "interviewer_id": 2,
                    "date": "2018-10-02 09:00 AM",
                    "is_blocked_slot": false
                },
                {
                    "id": 14,
                    "interviewer_id": 2,
                    "date": "2018-10-02 10:00 AM",
                    "is_blocked_slot": false
                },
                {
                    "id": 15,
                    "interviewer_id": 2,
                    "date": "2018-10-02 11:00 AM",
                    "is_blocked_slot": false
                },
                {
                    "id": 16,
                    "interviewer_id": 2,
                    "date": "2018-10-04 09:00 AM",
                    "is_blocked_slot": false
                },
                {
                    "id": 17,
                    "interviewer_id": 2,
                    "date": "2018-10-04 10:00 AM",
                    "is_blocked_slot": false
                },
                {
                    "id": 18,
                    "interviewer_id": 2,
                    "date": "2018-10-04 11:00 AM",
                    "is_blocked_slot": false
                }
            ]
        }
    }
    
    ===========================================================
    - create repeatable interviewer slot:
    
    * "repeatable" type of interviewer slot is different than "single" type of interviewer slot. Here,
    instead of specifying list of custom dates of availability we provide days of the week and hours when
    interviewer is available. For example we can use "repeatable" slots to tell that interviewer is
    available every Monday from 9 AM to 10 AM. Here Monday is not some specific date, but day of the week
    so the slot will be available EVERY Monday in the future (not only once as it is in "single" type slot).
    NOTE: we can achive same result using single interviewer slots, but then we will need to set new slots
          each week    
    See example below:     
    
    * let's say we want to execute the following scenario:
    "interviewer Philipp is available next week each day from 9 AM through 4 PM 
     without breaks." In that case we can achieve it using repeatable interviewer slots and will run the 
     following request:
    
    method: POST
    url: http://localhost:8000/api/repeatable-interviewer-slots
    Body (select raw) and add this line: 
    
    {
        "interviewer_id":1,
        "repeatable_interviewer_slots":[
            {"day_number":1,"start_time":"09:00 AM"},
            {"day_number":1,"start_time":"10:00 AM"},
            {"day_number":1,"start_time":"11:00 AM"},
            {"day_number":1,"start_time":"12:00 PM"},
            {"day_number":1,"start_time":"01:00 PM"},
            {"day_number":1,"start_time":"02:00 PM"},
            {"day_number":1,"start_time":"03:00 PM"},
            {"day_number":2,"start_time":"09:00 AM"},
            {"day_number":2,"start_time":"10:00 AM"},
            {"day_number":2,"start_time":"11:00 AM"},
            {"day_number":2,"start_time":"12:00 PM"},
            {"day_number":2,"start_time":"01:00 PM"},
            {"day_number":2,"start_time":"02:00 PM"},
            {"day_number":2,"start_time":"03:00 PM"},
            {"day_number":3,"start_time":"09:00 AM"},
            {"day_number":3,"start_time":"10:00 AM"},
            {"day_number":3,"start_time":"11:00 AM"},
            {"day_number":3,"start_time":"12:00 PM"},
            {"day_number":3,"start_time":"01:00 PM"},
            {"day_number":3,"start_time":"02:00 PM"},
            {"day_number":3,"start_time":"03:00 PM"},
            {"day_number":4,"start_time":"09:00 AM"},
            {"day_number":4,"start_time":"10:00 AM"},
            {"day_number":4,"start_time":"11:00 AM"},
            {"day_number":4,"start_time":"12:00 PM"},
            {"day_number":4,"start_time":"01:00 PM"},
            {"day_number":4,"start_time":"02:00 PM"},
            {"day_number":4,"start_time":"03:00 PM"},
            {"day_number":5,"start_time":"09:00 AM"},
            {"day_number":5,"start_time":"10:00 AM"},
            {"day_number":5,"start_time":"11:00 AM"},
            {"day_number":5,"start_time":"12:00 PM"},
            {"day_number":5,"start_time":"01:00 PM"},
            {"day_number":5,"start_time":"02:00 PM"},
            {"day_number":5,"start_time":"03:00 PM"}                                             
        ]
    }
    
    Response should look similar to this:
    
    {
        "data": {
            "repeatable_interviewer_slots": [
                {
                    "id": 1,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 9
                },
                {
                    "id": 2,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 10
                },
                {
                    "id": 3,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 11
                },
                {
                    "id": 4,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 12
                },
                {
                    "id": 5,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 13
                },
                {
                    "id": 6,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 14
                },
                {
                    "id": 7,
                    "interviewer_id": 1,
                    "day_number": 1,
                    "start_time": 15
                },
                {
                    "id": 8,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 9
                },
                {
                    "id": 9,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 10
                },
                {
                    "id": 10,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 11
                },
                {
                    "id": 11,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 12
                },
                {
                    "id": 12,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 13
                },
                {
                    "id": 13,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 14
                },
                {
                    "id": 14,
                    "interviewer_id": 1,
                    "day_number": 2,
                    "start_time": 15
                },
                {
                    "id": 15,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 9
                },
                {
                    "id": 16,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 10
                },
                {
                    "id": 17,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 11
                },
                {
                    "id": 18,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 12
                },
                {
                    "id": 19,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 13
                },
                {
                    "id": 20,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 14
                },
                {
                    "id": 21,
                    "interviewer_id": 1,
                    "day_number": 3,
                    "start_time": 15
                },
                {
                    "id": 22,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 9
                },
                {
                    "id": 23,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 10
                },
                {
                    "id": 24,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 11
                },
                {
                    "id": 25,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 12
                },
                {
                    "id": 26,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 13
                },
                {
                    "id": 27,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 14
                },
                {
                    "id": 28,
                    "interviewer_id": 1,
                    "day_number": 4,
                    "start_time": 15
                },
                {
                    "id": 29,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 9
                },
                {
                    "id": 30,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 10
                },
                {
                    "id": 31,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 11
                },
                {
                    "id": 32,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 12
                },
                {
                    "id": 33,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 13
                },
                {
                    "id": 34,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 14
                },
                {
                    "id": 35,
                    "interviewer_id": 1,
                    "day_number": 5,
                    "start_time": 15
                }
            ]
        }
    }
    
    ===========================================================
    - query API for intersecting slots between given candidate and given interviewers:
    
    * Finally, after inserting availability slots for a candidate and couple of interviewers using 
      above provided REST API endpoints, we can access following endpoint to see if there are 
      common dates between interviewers and candidate (intersections) to make interview "reservations"   
    
    * let's say we want to execute the following scenario:
    "query API to get a collection of periods of time when it's possible to arrange an interview 
     for (candidate) Carl and interviewers Philipp and Sarah." We run the following request for that:
    
    * Assuming that Carl id is 1, Philipp id = 1, Sarah id = 2
    
    method: GET
    url: http://localhost:8000/api/interview-calendar/1
    Body: none (this is a GET request, so we pass params via query string)
    
    Response should look similar to this:
    
    {
        "data": {
            "intersections": [
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "1",
                    "start_time": "09:00 AM",
                    "date": "2018-10-01 09:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "2",
                    "start_time": "09:00 AM",
                    "date": "2018-10-02 09:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 2,
                        "name": "Sarah"
                    },
                    "weekday": "2",
                    "start_time": "09:00 AM",
                    "date": "2018-10-02 09:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "3",
                    "start_time": "09:00 AM",
                    "date": "2018-10-03 09:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "3",
                    "start_time": "10:00 AM",
                    "date": "2018-10-03 10:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "3",
                    "start_time": "11:00 AM",
                    "date": "2018-10-03 11:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "4",
                    "start_time": "09:00 AM",
                    "date": "2018-10-04 09:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 2,
                        "name": "Sarah"
                    },
                    "weekday": "4",
                    "start_time": "09:00 AM",
                    "date": "2018-10-04 09:00 AM"
                },
                {
                    "candidate": {
                        "id": 1,
                        "name": "Carl"
                    },
                    "interviewer": {
                        "id": 1,
                        "name": "Philipp"
                    },
                    "weekday": "5",
                    "start_time": "09:00 AM",
                    "date": "2018-10-05 09:00 AM"
                }
            ]
        }
    }
    
## To improve this REST API you can implement:
- authentication (JWT etc.)
- pagination
- customize App\EventSubscriber to also support debug mode during development (to debug status 500 etc.) 
 (currently you need to manually go to processException() and just use "return;" on the first line of this method's body to avoid exception "prettyfying")
- SSL (https connection)
- there are many strings returned from services in case of various errors (see try/catch cases in ListingService.php for example). It will be probably better to convert these to exceptions instead.

    