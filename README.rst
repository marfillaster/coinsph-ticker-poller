Coinsph Ticker Poller
=====================

A commandline script that logs coinsph ticker endpoint. Polling is done every
rate expiration.

Requirements
------------

PHP cli and curl command available in PATH

Installation
------------

.. code-block:: bash

    $ ./composer.phar install

Usage
-----

.. code-block:: bash

    $ ./coinsph.php | tee coinsph.log
    "2016-03-13T18:21:38.000Z","BTC-PHP","19530","19087"
    "2016-03-13T18:21:50.000Z","BTC-PHP","19530","19087"
    "2016-03-13T18:22:21.000Z","BTC-PHP","19530","19087"

    

