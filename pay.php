<html lang='en'>
    <head>
        <title>Authorize.net SIM Implementation with PHP</title>
    </head>
    <body>
        <!-- This section generates the "Submit Payment" button using PHP -->
        <?php
        $loginID = "YOUR-LOGIN-ID";
        $transactionKey = "YOUR-TRAN-KEY";
        $amount = "19.99";
        $description = "Sample Transaction";
        $label = "Submit Payment"; // The is the label on the 'submit' button
        $testMode = "true";
        // Use following url for production application
        //$url = https://secure.authorize.net/gateway/transact.dll
        // Use following url for development/sandbox test application
        $url = "https://test.authorize.net/gateway/transact.dll";

        // If not using any perticular invoice number then 
        // generating invoice using date time would be a good idea.
        $invoice = date('YmdHis');

        // a sequence number is randomly generated
        $sequence = rand(1, 1000);

        // a timestamp is generated
        $timeStamp = time();
        
        // Following code will generate a fingerprint for transaction.
        // The payment gateway server uses the same merchant information to decrypt the transaction fingerprint and authenticate the transaction.
        // The following lines generate the SIM fingerprint. PHP versions 5.1.2 and
        // newer have the necessary hmac function built in. For older versions, it
        // will try to use the mhash library.
        if (phpversion() >= '5.1.2') {
            $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey);
        } else {
            $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey));
        }
        ?>
        
        <!-- Print the Amount and Description to the screen. -->
        Amount: <?php echo $amount; ?> <br />
        Description: <?php echo $description; ?> <br />
        <!-- Create the HTML form containing necessary SIM post values -->
        <form method='post' action='<?php echo $url; ?>' >
            <!-- Additional fields can be added here as outlined in the SIM integration
            guide at: http://developer.authorize.net -->
            <input type='hidden' name='x_login' value='<?php echo $loginID; ?>' />
            <input type='hidden' name='x_amount' value='<?php echo $amount; ?>' />
            <input type='hidden' name='x_description' value='<?php echo $description; ?>' />
            <input type='hidden' name='x_invoice_num' value='<?php echo $invoice; ?>' />
            <input type='hidden' name='x_fp_sequence' value='<?php echo $sequence; ?>' />
            <input type='hidden' name='x_fp_timestamp' value='<?php echo $timeStamp; ?>' />
            <input type='hidden' name='x_fp_hash' value='<?php echo $fingerprint; ?>' />
            <input type='hidden' name='x_test_request' value='<?php echo $testMode; ?>' />
            <input type='hidden' name='x_show_form' value='PAYMENT_FORM' />
            <input type='submit' value='<?php echo $label; ?>' />
        </form>
    </body>
</html>