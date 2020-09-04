<?php

require_once('.config.inc.php');
// Japan
$serviceUrl = "https://mws.amazonservices.jp/Products/2011-10-01";

 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'ProxyUsername' => null,
   'ProxyPassword' => null,
   'MaxErrorRetry' => 3,
 );

 $service = new MarketplaceWebServiceProducts_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);


 $request = new MarketplaceWebServiceProducts_Model_GetCompetitivePricingForASINRequest();
 $request->setSellerId(MERCHANT_ID);

 $request->setMarketplaceId(MARKETPLACE_ID);
 $asinList = new MarketplaceWebServiceProducts_Model_ASINListType();
 $asinList->setASIN(array('B0035ESZBY', 'B00TRUOD5C', 'B01MSK1R3O'));
 $request->setAsinList($asinList);

 // object or array of parameters
 invokeGetCompetitivePricingForASIN($service, $request);

/**
  * Get Get Competitive Pricing For ASIN Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MarketplaceWebServiceProducts_Interface $service instance of MarketplaceWebServiceProducts_Interface
  * @param mixed $request MarketplaceWebServiceProducts_Model_GetCompetitivePricingForASIN or array of parameters
  */

  function invokeGetCompetitivePricingForASIN(MarketplaceWebServiceProducts_Interface $service, $request)
  {
      try {
        $response = $service->GetCompetitivePricingForASIN($request);

        // echo ("Service Response\n");
        // echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        // echo $dom->saveXML();
        // echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

     } catch (MarketplaceWebServiceProducts_Exception $ex) {
        // echo("Caught Exception: " . $ex->getMessage() . "\n");
        // echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        // echo("Error Code: " . $ex->getErrorCode() . "\n");
        // echo("Error Type: " . $ex->getErrorType() . "\n");
        // echo("Request ID: " . $ex->getRequestId() . "\n");
        // echo("XML: " . $ex->getXML() . "\n");
        // echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
    //  API応答情報を表示する
    require_once dirname(__FILE__) . '/cart_price_disp.php';
 }

