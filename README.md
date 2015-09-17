contactlab-magento-syscheck Utility
===================================

Contactlab Magento Syscheck Utility.

    ```
    Usage: run-check [-c] <check1> [-c] <check2> [args...] [-h] [-l] [-p] <PATH>

      -c --check       Run a single check (can be specified more than once
      -l --list        List all checks
      -p --path        Specify Magento path
      -m --mail        Send report mail
      -h --help        Print this help

    Without options, the script runs all available checks on the current
    magento installation path.
    ```

Installation / Usage
--------------------

1. Download the [`composer.phar`](https://getcomposer.org/composer.phar) executable or use the installer.

    ``` sh
    $ curl -sS https://getcomposer.org/installer | php
    ```

2. Run composer autoupdate.

    ``` sh
    $ php composer.phar self-update
    ```

3. Install script dependencies.

    ``` sh
    $ php composer.phar update
    ```

Example of etc/config.json
--------------------------

This is an example of config.json file (into etc directory).

    ```
    {
      "mail": {
        "report_recipients": [
          {
            "name": "John Smith",
            "mail": "john.smith@example.com"
          }
        ],
        "from": {
            "name": "John Smith",
            "mail": "john.smith@example.com"
        }
      }
    }
    ```
