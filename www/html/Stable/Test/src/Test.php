<?php
define('AWS_ACCESS_KEY_ID',     'AKIAJTTGBUMUZ6VHLA3A');
define('AWS_SECRET_ACCESS_KEY', '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');  
define('MERCHANT_ID',           'A3LIDTGANAESGU');
define('MARKETPLACE_ID',        'A1VC38T7YXB528');
// set_include_path( get_include_path() . PATH_SEPARATOR . 'MarketplaceWebServiceProducts/.' );    
set_include_path( get_include_path() . PATH_SEPARATOR . 'MarketplaceWebServiceProducts/.' );


 
// メイン処理の実行
main();
 
//****************************************************************************
// Function     : main
// Description  : メイン処理
//****************************************************************************
function main() {
 
    //--------------------------------
    // Webサービス情報の取得
    //--------------------------------
    $service = getService();
    //単体テスト用(MarketplaceWebServiceProducts/Mockのxmlファイルを使用)
    //$service = new MarketplaceWebServiceProducts_Mock();
 
 
    //-------------------------------
    // リクエストパラメータのセット
    //-------------------------------
    $asinList = new MarketplaceWebServiceProducts_Model_ASINListType();
    $asinList->setASIN( array( '4478312141', '4797330058' ) );
 
    $request = new MarketplaceWebServiceProducts_Model_GetLowestOfferListingsForASINRequest();
    $request->setSellerId( MERCHANT_ID );
    $request->setMarketplaceId( MARKETPLACE_ID );
    $request->setASINList( $asinList );
 
 
    //-------------------------------
    // MWSリクエストAPIの実行
    //-------------------------------
    try {
        $response = $service->getLowestOfferListingsForASIN($request);
    } catch (MarketplaceWebServiceProducts_Exception $ex) {
        echo("Caught Exception: "       . $ex->getMessage()    . "<br>");
        echo("Response Status Code: "   . $ex->getStatusCode() . "<br>");
        echo("Error Code: "             . $ex->getErrorCode() . "<br>");
        echo("Error Type: "             . $ex->getErrorType() . "<br>");
        echo("Request ID: "             . $ex->getRequestId() . "<br>");
        echo("XML: "                    . $ex->getXML() . "<br>");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "<br>");
    }
 
    //-------------------------------
    // API応答情報を表示する
    //-------------------------------
    showResponse( $response );
}
 
 
 
//****************************************************************************
// Function     : getService
// Description  : Webサービス情報の取得
// Return       : MarketplaceWebServiceProducts_Client  サービス情報
//****************************************************************************
function getService() {
 
    //---------------------------------
    // クラスのオートローディングを行う
    //---------------------------------
    function __autoload($className){
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach($includePaths as $includePath){
            if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
                require_once $filePath;
                return;
            }
        }
    }
 
    // Web APIのエンドポイント(japan)
    $serviceUrl = "https://mws.amazonservices.jp/Products/2011-10-01";
 
    // proxy/retryの設定
    $config = array (
       'ServiceURL'    => $serviceUrl,
       'ProxyHost'     => null,
       'ProxyPort'     => -1,
       'MaxErrorRetry' => 3,
    );
 
    // Webサービスオブジェクトを生成
    $service = new MarketplaceWebServiceProducts_Client(
                AWS_ACCESS_KEY_ID,
                AWS_SECRET_ACCESS_KEY,
                'nanoappli.com_SampleApp',
                '1.0.0.0',
                $config);
 
    return $service;
}
 
 
 
//****************************************************************************
// Function     : showResponse
// Description  : Webサービス実行結果の表示
// Params       : $response WebAPI実行結果
//****************************************************************************
function showResponse( $response ) {
    $getLowestOfferListingsForASINResultList = $response->getGetLowestOfferListingsForASINResult();
 
    //--------------------------------------
    // 全てのASIN情報を表示するまで繰り返し
    //--------------------------------------
    foreach ($getLowestOfferListingsForASINResultList as $getLowestOfferListingsForASINResult) {
        echo ("=============================================================================<br>");
 
        // 検索結果
        if ($getLowestOfferListingsForASINResult->isSetStatus()) {
            echo( "Status :" . $getLowestOfferListingsForASINResult->getStatus() . "<br>");
        }
 
 
        //----------------
        // 商品の情報
        //----------------
        if ($getLowestOfferListingsForASINResult->isSetASIN()) {
            echo( "ASIN   :" . $getLowestOfferListingsForASINResult->getASIN() . "<br>");
        }
        if ($getLowestOfferListingsForASINResult->isSetProduct()) { 
            $product = $getLowestOfferListingsForASINResult->getProduct();          
            if ($product->isSetSalesRankings()) { 
                $salesRankings = $product->getSalesRankings();
                $salesRankList = $salesRankings->getSalesRank();
                foreach ($salesRankList as $salesRank) {
                    if ($salesRank->isSetProductCategoryId()) {
                        echo("カテゴリID :" . $salesRank->getProductCategoryId() . "<br>");
                    }
                    if ($salesRank->isSetRank()) {
                        echo("ランク :" . $salesRank->getRank() . "<br>");
                    }
                }
            } 
 
            //-------------------
            // 出品情報
            //-------------------
            if ($product->isSetLowestOfferListings()) { 
                $lowestOfferListings = $product->getLowestOfferListings();
                $lowestOfferListingList = $lowestOfferListings->getLowestOfferListing();
 
                //-----------------------------------------
                // 取得した全出品情報を表示するまで繰り返し
                //-----------------------------------------
                foreach ($lowestOfferListingList as $lowestOfferListing) {
                    echo( "------------------------------------------------<br>" );
 
                    //------------------
                    // 商品の状態
                    //------------------
                    if ($lowestOfferListing->isSetQualifiers()) { 
                        $qualifiers = $lowestOfferListing->getQualifiers();
                        if ($qualifiers->isSetItemCondition()) {
                            $condName    = getConditionName( $qualifiers->getItemCondition() );
                            $subCondName = "";
                            if ($qualifiers->isSetItemSubcondition()) {
                                $subCondName = "(" . getItemSubconditionName( $qualifiers->getItemSubcondition() ) . ")";
                            }
                            echo("コンディション   :" . $condName . $subCondName . "<br>" );
                        }
 
 
                        if ($qualifiers->isSetFulfillmentChannel()) {
                            $name = getFulfillmentChannelName( $qualifiers->getFulfillmentChannel() );
                            echo("出荷元           :" . $name . "<br>");
                        }
                        if ($qualifiers->isSetShipsDomestically()) {
                            echo("国内より発送     :" . $qualifiers->getShipsDomestically() . "<br>");
                        }
                        if ($qualifiers->isSetShippingTime()) { 
                            $shippingTime = $qualifiers->getShippingTime();
                            if ($shippingTime->isSetMax()) {
                                echo("発送日数(最大)   :" . $shippingTime->getMax() . "<br>");
                            }
                        } 
 
                        if ($lowestOfferListing->isSetSellerFeedbackCount()) {
                            echo("フィードバック数 :" . $lowestOfferListing->getSellerFeedbackCount() );
 
                            if ($qualifiers->isSetSellerPositiveFeedbackRating()) {
                                echo(" (高評価:" . $qualifiers->getSellerPositiveFeedbackRating() . ")");
                            }
                            echo ( "<br>" );
                        }
                    }
                    if ($lowestOfferListing->isSetNumberOfOfferListingsConsidered()) {
                        echo("出品数           :" . $lowestOfferListing->getNumberOfOfferListingsConsidered() . "<br>");
                    }
 
                    //------------------
                    // 価格情報
                    //------------------
                    if ($lowestOfferListing->isSetPrice()) { 
                        $price1 = $lowestOfferListing->getPrice();
                        if ($price1->isSetLandedPrice()) { 
                            echo("総額             :" . getPriceName( $price1->getLandedPrice() ) );
 
                        } 
                        if ($price1->isSetShipping()) { 
                            echo(" (送料:" . getPriceName( $price1->getShipping() ) . ")" );
                        } 
                        echo ( "<br>" );
                    } 
                }
            } 
        } 
 
        if ($getLowestOfferListingsForASINResult->isSetError()) { 
            echo("エラーが発生しました<br>");
            $error = $getLowestOfferListingsForASINResult->getError();
            if ($error->isSetType()) {
                echo("Type:" . $error->getType() . "<br>");
            }
            if ($error->isSetCode()) {
                echo("Code:" . $error->getCode() . "<br>");
            }
            if ($error->isSetMessage()) {
                echo("Message:" . $error->getMessage() . "<br>");
            }
        } 
    }
}
 
 
//****************************************************************************
// Function     : getConditionName
// Description  : コンディション名(日本語)を取得する
//****************************************************************************
function getConditionName( $inStr ) {
    switch( $inStr ) {
        case "New":
            return "新品";
            break;
        case "Used":
            return "中古品";
            break;
        case "Collectible":
            return "コレクター品";
            break;
        case "Refurbished":
            return "再生品";
            break;
        default:
            return $inStr;
            break;
    }
}
 
 
//****************************************************************************
// Function     : getItemSubconditionName
// Description  : サブコンディション名(日本語)を取得する
//****************************************************************************
function getItemSubconditionName( $inStr ) {
    switch( $inStr ) {
        case "New":
            return "新品";
            break;
        case "Mint":
            return "ほぼ新品";
            break;
        case "Very Good":
        case "VeryGood":
            return "非常に良い";
            break;
        case "Good":
            return "良い";
            break;
        default:
            return $inStr;
            break;
    }
}
 
 
//****************************************************************************
// Function     : getFulfillmentChannelName
// Description  : 出荷元(日本語)を取得する
//****************************************************************************
function getFulfillmentChannelName( $inStr ) {
    switch( $inStr ) {
        case "Amazon":
            return "FBA";
            break;
        case "Merchant":
            return "出品者";
            break;
        default:
            return $inStr;
            break;
    }
}
 
 
//****************************************************************************
// Function     : getPriceName
// Description  : 価格(通貨単位付き)を取得する
//****************************************************************************
function getPriceName( $inData ) {
    $outStr = "";
 
    // 通貨単位のセット
    if ( $inData->isSetCurrencyCode() ) {
        switch ( $inData->getCurrencyCode() ) {
            case "JPY":
                $outStr .= "\\";
                break;
            case "USD":
                $outStr .= "$";
                break;
            default:
                $outStr .= "(" . $inData->getCurrencyCode() . ")";
                break;
        }
    }
 
    // 金額のセット
    if ($inData->isSetAmount()) {
        $outStr .= sprintf( "%.0lf", $inData->getAmount() );
    }
 
    return $outStr;
}