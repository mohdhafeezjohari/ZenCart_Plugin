MOLPay ZenCart Plugin
===============

MOLPay Plugin for ZenCart Shopping Cart developed by MOLPay R&D team.


Supported version
-----------------

ZenCart version 1.3.x


Notes
-----

MOLPay Sdn. Bhd. is not responsible for any problems that might arise from the use of this module. 
Use at your own risk. Please backup any critical data before proceeding. For any query or 
assistance, please email support@molpay.com 


Installations
-------------

- Download this plugin, Extract/Unzip the files. 

- Upload or copy those file and folder into your cart root folder

- (Skip this if your cart is not hosted in UNIX environment). 
Please ensure the file permission is correct. It's recommended to CHMOD to 644

- Login as ZenCart store admin, go to `Modules` -> `Payment (Payment Modules)`
    You'll see there is a MOLPay payment option, click on it and press `[Install]` button.

- Please provide all the necessary details into the respective fields. Please refer below :
    1.MOLPay Merchant ID : Merchant ID provided by MOLPay
    2.MOLPay Verify Key : Please refer your MOLPay merchant profile for this key.
    3.MOLPay Multi Return URL : Define return url for this module to update order after payment has been made.
     Otherwise you may need to define on your MOLPay merchant profile.
    4.Sort order of display : set to 0
    5.Set Order Status : set to Processing [2]

- Click on `[Update]` button to save your setting.

- Now, access your MOLPay merchant account using the loginID and password provided to you.

- Click on the `Merchant Profile` tab above and fill in the Return URL & Callback URL for your shopping cart.

    `E.g :`

    `Return URL : http://www.yourdomain.com.my/process.php`

    `Callback URL : http://www.yourdomain.com.my/process_callback.php`

- Click on `[Update]`

- Now you can try to use MOLPay at the shop front by going thru a complete purchase procedure.
 


Contribution
------------

You can contribute to this plugin by sending the pull request to this repository.


Issues
------------

Submit issue to this repository or email to our support@molpay.com


Support
-------

Merchant Technical Support / Customer Care : support@molpay.com <br>
Sales/Reseller Enquiry : sales@molpay.com <br>
Marketing Campaign : marketing@molpay.com <br>
Channel/Partner Enquiry : channel@molpay.com <br>
Media Contact : media@molpay.com <br>
R&D and Tech-related Suggestion : technical@molpay.com <br>
Abuse Reporting : abuse@molpay.com
