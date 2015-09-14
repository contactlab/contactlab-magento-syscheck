# contactlab-magento-syscheck Utility

Contactlab Magento Syscheck Utility.

<pre>Usage: run-check [-c] <check1> [-c] <check2> [args...] [-h] [-l] [-p] <PATH>

  -c --check       Run a single check (can be specified more than once
  -l --list        List all checks
  -p --path        Specify Magento path
  -m --mail        Send report mail
  -h --help        Print this help

Without options, the script runs all available checks on the current
magento installation path.</pre>

### Example of etc/config.json

This is an example of config.json file (into etc directory).

<pre>{
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
}</ore>
