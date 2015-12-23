<?
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/.." ) ;
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('CHK_EVENT', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" ) ;

@set_time_limit(0);
@ignore_user_abort(true); 


CModule::IncludeModule('sale');
$arBasketItemsToUser = array();

$dbBasketItems = CSaleBasket::GetList(
    array(),
    array(
        ">DATE_INSERT" => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime()-60*60*24*30),
		">USER_ID" => 0,
		"DELAY" => "Y"
    )
);//get list of positions inserted in 30 days

while ($arItem = $dbBasketItems->Fetch())
{  
  $arUser = CSaleUser::GetList(array('ID' => $arItem['FUSER_ID']));
  $arItem["USER_ID"]=$arUser["USER_ID"];
  $dbBasketItemsCheck = CSaleBasket::GetList(
    array(),
    array(
      "PRODUCT_ID" => $arItem["PRODUCT_ID"],
      "USER_ID" => $arItem["USER_ID"],
      ">ORDER_ID" => 0
    )
  );  
  if (!$dbBasketItemsCheck->Fetch())
  {
    $arBasketItemsToUser[$arItem["USER_ID"]][] = $arItem;//filling array with list of positions to user
  }
}

$ORDERLIST = '';
$arEventFields = array();//Parameters for post template
foreach ($arBasketItemsToUser as $USER_ID => $basketItems) 
{
  $rsUser = CUser::GetByID($USER_ID);
  if ($arUser = $rsUser->Fetch()) 
  {
    foreach ($basketItems as $basketItem) 
	{
      $ORDERLIST .= $basketItem["NAME"].' - '.$basketItem["PRICE"]." ".$basketItem["CURRENCY"]."<br />";
    }
    $arEventFields=array(
      "USER_NAME" => $arUser["NAME"]." ".$arUser["LAST_NAME"],
      "ORDERLIST" => $ORDERLIST
    );
	/*?><pre><?print_r($arEventFields);?></pre><?
	*/
  
	CEvent::Send("SEND_REMEMBER_BASKET", SITE_ID, $arEventFields);
  
  }
}
?>