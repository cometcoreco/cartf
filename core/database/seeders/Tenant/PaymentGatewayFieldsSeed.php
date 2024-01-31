<?php

namespace Database\Seeders\Tenant;


use App\Helpers\ImageDataSeedingHelper;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewayFieldsSeed extends Seeder
{
    public function run()
    {
        DB::statement("INSERT INTO `payment_gateways` (`id`, `name`, `image`, `description`, `status`, `test_mode`, `credentials`, `created_at`, `updated_at`) VALUES
        (1,'paypal','465','if your currency is not available in paypal, it will convert you currency value to USD value based on your currency exchange rate.',1,1,'{\"sandbox_client_id\":null,\"sandbox_client_secret\":null,\"sandbox_app_id\":null,\"live_client_id\":null,\"live_client_secret\":null,\"live_app_id\":null}','2022-04-17 07:54:18','2023-08-17 16:29:35'),
        (2,'paytm','312','if your currency is not available in paytm, it will convert you currency value to INR value based on your currency exchange rate.',1,1,'{\"merchant_key\":null,\"merchant_mid\":null,\"merchant_website\":null,\"channel\":null,\"industry_type\":null}','2022-04-17 07:54:18','2023-08-17 16:29:54'),
        (3,'stripe','315','',1,1,'{\"public_key\":null,\"secret_key\":null}','2022-04-17 07:54:18','2023-08-17 16:30:01'),
        (4,'razorpay','313','if your currency is not available in Razorpay, it will convert you currency value to INR value based on your currency exchange rate.',1,1,'{\"api_key\":null,\"api_secret\":null}','2022-04-17 07:54:18','2023-08-17 16:30:12'),
        (5,'paystack','311','if your currency is not available in Paystack, it will convert you currency value to NGN value based on your currency exchange rate.',1,1,'{\"public_key\":null,\"secret_key\":null,\"merchant_email\":null}','2022-04-17 07:54:18','2023-08-17 16:30:24'),
        (6,'mollie','307','if your currency is not available in mollie, it will convert you currency value to USD value based on your currency exchange rate.',1,1,'{\"public_key\":null}','2022-04-17 07:54:18','2023-08-17 16:30:32'),
        (8,'midtrans','305','',1,1,'{\"merchant_id\":null,\"server_key\":null,\"client_key\":null}','2022-04-17 07:54:18','2023-08-17 16:30:39'),
        (10,'cashfree','316','',1,1,'{\"app_id\":null,\"secret_key\":null}','2022-04-17 07:54:18','2023-08-17 16:30:46'),
        (11,'instamojo','314','',1,1,'{\"client_id\":null,\"client_secret\":null,\"username\":null,\"password\":null}','2022-04-17 07:54:18','2023-08-17 16:30:52'),
        (12,'marcadopago','306','',1,1,'{\"client_id\":null,\"client_secret\":null}','2022-04-17 07:54:18','2023-08-17 16:30:59'),
        (13,'zitopay','441','',1,1,'{\"username\":null}','2022-07-26 12:34:58','2023-08-17 16:31:05'),
        (14,'squareup','442','',1,1,'{\"location_id\":null,\"access_token\":null}','2022-07-26 12:34:58','2023-08-17 16:31:11'),
        (15,'cinetpay','443','',1,1,'{\"apiKey\":null,\"site_id\":null}','2022-07-26 12:34:58','2023-08-17 16:31:16'),
        (16,'paytabs','444','',1,1,'{\"profile_id\":null,\"region\":null,\"server_key\":null}','2022-07-26 12:34:58','2023-08-17 16:31:23'),
        (17,'billplz','445','',1,1,'{\"key\":null,\"version\":null,\"x_signature\":null,\"collection_name\":null}','2022-07-26 12:34:58','2023-08-17 16:31:35'),
        (19,'toyyibpay','446','',1,1,'{\"client_secret\":null,\"category_code\":null}','2022-12-05 17:54:12','2023-08-17 16:31:42'),
        (20,'flutterwave','447','if your currency is not available in flutterwave, it will convert you currency value to USD value based on your currency exchange rate.',1,1,'{\"public_key\":null,\"secret_key\":null,\"secret_hash\":null}','2022-12-05 17:56:40','2023-08-17 16:31:50'),
        (21,'payfast','308','',1,1,'{\"merchant_id\":null,\"merchant_key\":null,\"passphrase\":null,\"itn_url\":null}','2022-12-05 17:56:40','2023-08-17 16:32:01'),
        (22,'manual_payment','310','',1,1,'{\"name\":\"Manual Payment\",\"description\":\"Manual Payment Here\"}','2022-04-17 07:54:18','2022-12-21 11:31:31')");

//        DB::statement("INSERT INTO `payment_gateways` (`id`, `name`, `image`, `description`, `status`, `test_mode`, `credentials`, `created_at`, `updated_at`) VALUES
//        (1,'paypal','106','if your currency is not available in paypal, it will convert you currency value to USD value based on your currency exchange rate.',1,1,'{\"sandbox_client_id\":\"AUP7AuZMwJbkee-2OmsSZrU-ID1XUJYE-YB-2JOrxeKV-q9ZJZYmsr-UoKuJn4kwyCv5ak26lrZyb-gb\",\"sandbox_client_secret\":\"EEIxCuVnbgING9EyzcF2q-gpacLneVbngQtJ1mbx-42Lbq-6Uf6PEjgzF7HEayNsI4IFmB9_CZkECc3y\",\"sandbox_app_id\":null,\"live_client_id\":null,\"live_access_token\":null,\"live_app_id\":null}','2022-04-17 07:54:18','2023-05-15 11:43:38'),
//    	(2,'paytm','312','if your currency is not available in paytm, it will convert you currency value to INR value based on your currency exchange rate.',1,1,'{\"merchant_key\":\"dv0XtmsPYpewNag&\",\"merchant_mid\":\"Digita57697814558795\",\"merchant_website\":\"WEBSTAGING\",\"channel\":null,\"industry_type\":null}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(3,'stripe','315','',1,1,'{\"public_key\":\"pk_test_51GwS1SEmGOuJLTMsIeYKFtfAT3o3Fc6IOC7wyFmmxA2FIFQ3ZigJ2z1s4ZOweKQKlhaQr1blTH9y6HR2PMjtq1Rx00vqE8LO0x\",\"secret_key\":\"sk_test_51GwS1SEmGOuJLTMs2vhSliTwAGkOt4fKJMBrxzTXeCJoLrRu8HFf4I0C5QuyE3l3bQHBJm3c0qFmeVjd0V9nFb6Z00VrWDJ9Uw\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(4,'razorpay','313','if your currency is not available in Razorpay, it will convert you currency value to INR value based on your currency exchange rate.',1,1,'{\"api_key\":\"rzp_test_SXk7LZqsBPpAkj\",\"api_secret\":\"Nenvq0aYArtYBDOGgmMH7JNv\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(5,'paystack','311','if your currency is not available in Paystack, it will convert you currency value to NGN value based on your currency exchange rate.',1,1,'{\"public_key\":\"pk_test_0a2cea63c4a34691fae697fb8f6b72a856e96e12\",\"secret_key\":\"sk_test_bfb4d04c41f8bcfa9fb6dac84eeb6ea54e1a93b4\",\"merchant_email\":\"testxgenious@gmail.com\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(6,'mollie','307','if your currency is not available in mollie, it will convert you currency value to USD value based on your currency exchange rate.',1,1,'{\"public_key\":\"test_fVk76gNbAp6ryrtRjfAVvzjxSHxC2v\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(8,'midtrans','305','',1,1,'{\"merchant_id\":\"G770543580\",\"server_key\":\"SB-Mid-server-9z5jztsHyYxEdSs7DgkNg2on\",\"client_key\":\"SB-Mid-client-iDuy-jKdZHkLjL_I\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(10,'cashfree','316','',1,1,'{\"app_id\":\"94527832f47d6e74fa6ca5e3c72549\",\"secret_key\":\"ec6a3222018c676e95436b2e26e89c1ec6be2830\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(11,'instamojo','314','',1,1,'{\"client_id\":\"test_nhpJ3RvWObd3uryoIYF0gjKby5NB5xu6S9Z\",\"client_secret\":\"test_iZusG4P35maQVPTfqutbCc6UEbba3iesbCbrYM7zOtDaJUdbPz76QOnBcDgblC53YBEgsymqn2sx3NVEPbl3b5coA3uLqV1ikxKquOeXSWr8Ruy7eaKUMX1yBbm\",\"username\":null,\"password\":null}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(12,'marcadopago','306','',1,1,'{\"client_id\":\"TEST-0a3cc78a-57bf-4556-9dbe-2afa06347769\",\"client_secret\":\"TEST-4644184554273630-070813-7d817e2ca1576e75884001d0755f8a7a-786499991\"}','2022-04-17 07:54:18','2023-01-02 16:46:07'),
//    	(13,'zitopay','441','',1,1,'{\"username\":\"Suzon\"}','2022-07-26 12:34:58','2023-01-02 16:46:07'),
//    	(14,'squareup','442','',1,1,'{\"location_id\":\"LE9C12TNM5HAS\",\"access_token\":\"EAAAEOuLQObrVwJvCvoio3H13b8Ssqz1ighmTBKZvIENW9qxirHGHkqsGcPBC1uN\"}','2022-07-26 12:34:58','2023-01-02 16:46:07'),
//    	(15,'cinetpay','443','',1,1,'{\"apiKey\":\"12912847765bc0db748fdd44.40081707\",\"site_id\":\"445160\"}','2022-07-26 12:34:58','2023-01-02 16:46:07'),
//    	(16,'paytabs','444','',1,1,'{\"profile_id\":\"96698\",\"region\":\"GLOBAL\",\"server_key\":\"SKJNDNRHM2-JDKTZDDH2N-H9HLMJNJ2L\"}','2022-07-26 12:34:58','2023-01-02 16:46:07'),
//    	(17,'billplz','445','',1,1,'{\"key\":\"b2ead199-e6f3-4420-ae5c-c94f1b1e8ed6\",\"version\":\"v4\",\"x_signature\":\"S-HDXHxRJB-J7rNtoktZkKJg\",\"collection_name\":\"kjj5ya006\"}','2022-07-26 12:34:58','2023-01-02 16:46:07'),
//    	(19,'toyyibpay','446','',1,1,'{\"client_secret\":\"wnbtrqle-9t9l-m02j-e2bz-iaj2tkp52sfo\",\"category_code\":\"0m0j9yc4\"}','2022-12-05 17:54:12','2023-01-02 16:46:07'),
//    	(20,'flutterwave','447','if your currency is not available in flutterwave, it will convert you currency value to USD value based on your currency exchange rate.',1,1,'{\"public_key\":\"FLWPUBK_TEST-86cce2ec43c63e09a517290a8347fcab-X\",\"secret_key\":\"FLWSECK_TEST-d37a42d8917db84f1b2f47c125252d0a-X\",\"secret_hash\":\"nazmart\"}','2022-12-05 17:56:40','2023-01-02 16:46:07'),
//    	(21,'payfast','308','',1,1,'{\"merchant_id\":\"10024000\",\"merchant_key\":\"77jcu5v4ufdod\",\"passphrase\":\"testpayfastsohan\",\"itn_url\":\"https:\\/\\/fundorex.test\\/nazmart-payfast\"}','2022-12-05 17:56:40','2023-01-02 16:46:07'),
//    	(22,'manual_payment','310','',1,1,'{\"name\":\"Manual Payment\",\"description\":\"Manual Payment Here\"}','2022-04-17 07:54:18','2023-01-02 16:46:07')");
    }
}
