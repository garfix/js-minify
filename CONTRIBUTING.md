# Contributing

When you make a contribution, please check:

- If your code runs using PHP 5.6 (and up)
- If the tests run

## Run the tests

To run both unit tests and real-life tests on actual libraries, do

    /usr/bin/php7.3 ./vendor/bin/phpunit

To just run the unit tests, do

    /usr/bin/php7.3 ./vendor/bin/phpunit --group unit

To just run the real-life tests, do

    /usr/bin/php7.3 ./vendor/bin/phpunit --group libraries

The specified version of the PHP runtime is only important for the real-life tests, which test execution time.

If you find that your computer is slower or faster than mine, change the maxDuration values of the real-life tests. 
Then, after you made your change, check that it hasn't made the code slower.
