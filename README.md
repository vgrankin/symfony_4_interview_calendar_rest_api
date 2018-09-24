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

