# HyperBC Extension for Magento 2

This is the official Magento en for the HyperBC [cryptocurrency payment gateway](https://www.hyperbc.com/). Accept and settle payments in digital currencies in your Magento shop.

This extension for Magento 2 implements the REST API documented at https://www.hyperbc.com/doc/api/en/#h5-deposit/

## Requirements

* A HyperBC merchant account -> Contact Us [here](https://www.hyperbc.com/).
* App ID, Hyperbc Public Key, and way to generate Your own Private Key can get it from the account manager.
* Download the extension from the Magento Marketplace [here](https://marketplace.magento.com/hyperbc-paymentgateway.html).


## Extension installation

* Create a folder structure in Magento root as app/code/Hyperbc/PaymentGateway.
* Download and extract the zip folder from the Magento Marketplace and upload the extension files to app/code/Hyperbc/PaymentGateway.
* Login to your SSH and run below commands:

    ```bash
    php bin/magento setup:upgrade
  
    // For Magento version 2.0.x to 2.1.x
    php bin/magento setup:static-content:deploy
  
    // For Magento version 2.2.x & above
    php bin/magento setup:static-content:deploy –f
   
    php bin/magento cache:flush
    
    rm -rf var/cache var/generation var/di var/page_cache generated/*
  
    ```

Support and Feedback
--------------------
Magento 2.4.4

Your feedback is appreciated! If you have specific problems or bugs with this Magento module, please file an issue on Github. For general feedback and support requests, send an email to support@hyperbc.com
