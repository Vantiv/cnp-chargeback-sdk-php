Vantiv eCommerce PHP Chargeback SDK
=====================
#### WARNING:
##### All major version changes require recertification to the new version. Once certified for the use of a new version, Vantiv modifies your Merchant Profile, allowing you to submit transaction to the Production Environment using the new version. Updating your code without recertification and modification of your Merchant Profile will result in transaction declines. Please consult you Implementation Analyst for additional information about this process.
About Vantiv eCommerce
------------
[Vantiv eCommerce](https://developer.vantiv.com/community/ecommerce) powers the payment processing engines for leading companies that sell directly to consumers through  internet retail, direct response marketing (TV, radio and telephone), and online services. Vantiv eCommerce is the leading authority in card-not-present (CNP) commerce, transaction processing and merchant services.


About this SDK
--------------
The Vantiv eCommerce PHP Chargeback SDK is a PHP implementation of the [Vantiv eCommerce](https://developer.vantiv.com/community/ecommerce) Chargeback API. This SDK was created to make it as easy as possible to manage your chargebacks using Vantiv eCommerce API. This SDK utilizes the HTTPS protocol to securely connect to Vantiv eCommerce. Using the SDK requires coordination with the Vantiv eCommerce team in order to be provided with credentials for accessing our systems.

Each PHP SDK release supports all of the functionality present in the associated Vantiv eCommerce Chargeback API version (e.g., SDK v2.1.0 supports Vantiv eCommerce Chargeback API v2.1). Please see the Chargeback API reference guide to get more details on what the Vantiv eCommerce chargeback engine supports.

This SDK was implemented to support the PHP programming language and was created by Vantiv eCommerce. Its intended use is for online and batch transaction processing utilizing your account on the Vantiv eCommerce payments engine.

See LICENSE file for details on using this software.

Please contact [Vantiv eCommerce](https://developer.vantiv.com/community/ecommerce) to receive valid merchant credentials in order to run tests successfully or if you require assistance in any way.  We are reachable at sdksupport@Vantiv.com

SDK PHP Dependencies
--------------
Up to date list available at [Packagist](https://packagist.org/packages/litle/payments-sdk)

Setup
============
Using with composer
--------------------
If you are using a composer to manage your dependencies, you can do the following in your project directory:

1) Install the composer using command:
> curl -sS https://getcomposer.org/install | php

2) Install dependencies using the command:
> php composer.phar install

3) Configure the SDK:
> cd cnp/sdk
> php Setup.php

4) Run a sample:
```php

 // Retrieve information about a chargeback
$chargebackRetrieval = new cnp\sdk\ChargebackRetrieval();
$response = $chargebackRetrieval->getChargebacksByDate("2018-01-01");

// You may also use a tree-oriented style to get the response values:
$chargebackRetrieval = new cnp\sdk\ChargebackRetrieval($treeResponse = true);
$response = $chargebackRetrieval->getChargebackByCaseId("12345000");

// Update chargeback case
$chargebackUpdate = new cnp\sdk\ChargebackUpdate();
$chargebackUpdate->representCase("12345000", "Note on activity", $representment_amount = 1000);

// Manage supporting documents for chargeback case
$chargebackDocument = new cnp\sdk\ChargebackDocument();
$chargebackDocument->uploadDocument("12345000", "invoice.pdf");

```
> php your_sample_name

Using without composer
-----------------------
If you're not, you have to add a require for each and every class that's going to be used.

1) Configure the SDK
> cd into cnp/sdk
> php Setup.php

2) Add the cnp folder and require the path for your file

3) run your file 

> php your_file

Clone Repo
---------------

1) Install the Vantiv eCommerce PHP SDK from git. 

> git clone git://github.com/Vantiv/cnp-chargeback-sdk-php.git

> php ~/composer.phar install


2) Once the SDK is downloaded run our setup program to generate a configuration file.

> cd cnp-chargeback-sdk-php/lib

> php Setup.php

Running the above commands will create a configuration file in the lib directory. 


3) Create a symlink to the SDK

>ln -s /path/to/sdk /var/www/html/nameOfLink


4.) Run a sample: 

```php

 // Retrieve information about a chargeback
$chargebackRetrieval = new cnp\sdk\ChargebackRetrieval();
$response = $chargebackRetrieval->getChargebacksByDate("2018-01-01");

// You may also use a tree-oriented style to get the response values:
$chargebackRetrieval = new cnp\sdk\ChargebackRetrieval($treeResponse = true);
$response = $chargebackRetrieval->getChargebackByCaseId("12345000");

// Update chargeback case
$chargebackUpdate = new cnp\sdk\ChargebackUpdate();
$chargebackUpdate->representCase("12345000", "Note on activity", $representment_amount = 1000);

// Manage supporting documents for chargeback case
$chargebackDocument = new cnp\sdk\ChargebackDocument();
$chargebackDocument->uploadDocument("12345000", "invoice.pdf");

```

NOTE: you may have to change the path to match that of your filesystems.  

If you get an error like:
```bash
PHP Fatal error:  require_once(): Failed opening required '/home/user/git/cnp-chargback-sdk-php/../lib/Chargeback.php' (include_path='.:/usr/share/pear:/usr/share/php') in /home/user/git/cnp-chargback-sdk-php/foo.php on line 2
```
You need to change the second line of your script to load the real location of Chargeback.php

5) Next run this file using php on the command line or inside a browser. You should see the following result provided you have connectivity to the Vantiv eCommerce certification environment.  You will see an HTTP error if you don't have access to the Vantiv URL

Please contact Vantiv eCommerce Inc. with any further questions.   You can reach us at SDKSupport@Vantiv.com
