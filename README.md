tradesmanJobs
=============

A Symfony project created on August 22, 2018, 8:40 pm. API Rest developed by
Frank Quero for MyHammer.


Bundle: MyHammerJobsBundle.

Parameters: isocode.

Environment:
  - SO: Linux Elementary OS Loki.
  - DB: Mysql 5.7.23.
  - PHP 7.1.20.
  - Symfony 3.4.

The project has 2 main controllers:

- JobsController.
- CityController.

End points. 5 end points 4 for Jobs and 1 for City

- Retrieve job info:
    Method: Post.
    Parameters: user: the user thats consult the info; zipcode: zipcode of the city; service: pk value of the service.
    Examples:
      - <server_address>/jobs?user=2
      - <server_address>/jobs?user=2&zipcode=10115&service=411070.

- Retrieve job info by pk:
    Method: GET.
    Parameters: id: pk value of the job.
    Example: <server_address>/jobs/2.  

- Create a job:
    Method: POST.
    Example:
      - <server_address>/jobs/?city=2&service=802030&date=2018/09/01&user=1&title=Get ready for MyHammer&description=Get ready for MyHammer, develop custom made APIs

- Update a job:
    Method: PUT.
    Example:
      - <server_address>/jobs/4?city=2&service=802030&date=2018/09/01&user=1&title=Get ready for MyHammer&description=Get ready for MyHammer, develop custom made APIs

- Retrieve city info:
    Method: Post.
    Parameters:  zipcode: zipcode of the city.
    Examples:
      - <server_address>/city?zipcode=01623
      - <server_address>/city
Test:

Job Controller: ./vendor/phpunit/phpunit/phpunit src/MyHammer/JobsBundle/Tests/Controller/JobsControllerTest.php --verbose

City Controller: ./vendor/phpunit/phpunit/phpunit src/MyHammer/JobsBundle/Tests/Controller/CityControllerTest.php --verbose

Improvements:

- City, country, user and service CRUD. Time estimation: 16 h.
- Cleaner Error string. Time estimation: 4h.
- Security.

This will be enough to test and integrate with for any other details in code documentation and pdf doc.

Best.
