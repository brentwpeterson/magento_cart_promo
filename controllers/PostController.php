<?php

class Wdc_Cartex_PostController extends Mage_Core_Controller_Front_Action
{
	public function removeAction()
	{
		$itemId = $this->getRequest()->getParam('item_id');
		if(isset($itemId) && !empty($itemId))
		{
			$row = Mage::getresourceModel('cartex/cart_idx')->fetchRowbyItemId($itemId);	
			$idx = Mage::getModel('cartex/cart_idx')->load($row['item_idx_id']);
			$idx->setIsCurrent(0)->save();	
				
			
			$cart = Mage::getModel('checkout/cart');
			$cart->removeItem($itemId)->save();
			$this->_redirect('checkout/cart/');
		}
	}
	
	public function addAction()
	{
		$itemId = $this->getRequest()->getParam('item_id');
		
		if(isset($itemId) && !empty($itemId))
		{
			$row = Mage::getresourceModel('cartex/cart_idx')->fetchRowbyItemId($itemId);	
			$idx = Mage::getModel('cartex/cart_idx')->load($row['item_idx_id']);
			$idx->setIsCurrent(1)->save();				
			
			$cart = Mage::getModel('checkout/cart');
			$cart->addProduct((int)$row['product_id'], $row['qty']);
			$cart->save();
			$this->_redirect('checkout/cart/');
		}
	}
	
	public function curlAction()
	{

  $base_url = 'http://ws.audioscrobbler.com/2.0/?user=bachya&period=&api_key=8066d2ebfbf1e1a8d1c32c84cf65c91c&method=user.getTopTracks';
  $options = array_merge(array(
    'user' => 'bachya',
    'period' => NULL,
    'api_key' => 'xxxxx...', // obfuscated, obviously
  ));

  $options['method'] = 'user.getTopTracks';

  // Initialize cURL request and set parameters
  $ch = curl_init($base_url);
  curl_setopt_array($ch, array(
    CURLOPT_URL            => $base_url,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
  ));

  $results = curl_exec($ch);
 
print_r($results);
	
	}
	
	
	public function ttestAction()
	{
		$url =  "http://66.147.172.198:8080/transferedvalue/gateway";
		
		$pst = '<?xml version="1.0" encoding="UTF-8"?><TransferredValueTxn xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance">';
		$pst.= '<TransferredValueTxnReq><ReqCat>TransferredValue</ReqCat><ReqAction>Redeem</ReqAction><Date>20110503</Date><Time>121511</Time>';
		$pst.= '<PartnerName>Toonprint</PartnerName><CardActionInfo>';		
		$pst.= '<PIN>DEW-H43RUVTANI7L'; //sent 4/18/11
		$pst.= '<AcctNum>jane22</AcctNum>';
		$pst.= '<SrcRefNum>000001</SrcRefNum>';
		$pst.= '</CardActionInfo>';
		$pst.= '</TransferredValueTxnReq></TransferredValueTxn>';
		
		$header = array();
		
		$header[] = "POST HTTP/1.0 \r\n";
		$header[] = "Content-type: text/xml \r\n";
		$header[] = "Content-length: ".strlen($pst) . "\r\n";
		$header[] = $pst;
		
		//print_r($header);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url); # URL to post to
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); # return into a variable
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header ); # custom headers, see above
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' ); # This POST is special, and uses its specified Content-type
		$result = curl_exec( $ch ); # run!
		curl_close($ch); 

		echo $result;	
	}
	
	
	public function testoldAction()
	{
		
		//$url =  "http://66.147.172.198:8080/transferedvalue/gateway";
		//$url =  "https://66.147.172.198:8443/transferedvalue/gateway";
		//$url =  "https://milws.incomm.com:8443/transferedvalue/gateway";
		//$url =  "http://milws.incomm.com:8080/transferedvalue/gateway";
		$url =  'http://66.147.172.198:8080/transferedvalue/gateway';
		
		/*$pst = '<?xml version="1.0" encoding="UTF-8"?>';*/
		$pst = '<TransferredValueTxn%20xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance">';		
		$pst.= '<TransferredValueTxnReq><ReqCat>TransferredValue</ReqCat><ReqAction>Redeem</ReqAction><Date>20110404</Date><Time>121511</Time>';
		$pst.= '<PartnerName>Toonprint</PartnerName>';
		$pst.= '<CardActionInfo>';
		$pst.= '<PIN>DEW-VULVUC1SJ24B</PIN>'; //sent 4/29/11
        $pst.= '<AcctNum>jane22</AcctNum>';
        $pst.= '<SrcRefNum>000001</SrcRefNum>';
		$pst.= '</CardActionInfo>';
		$pst.= '</TransferredValueTxnReq></TransferredValueTxn>';
		
		$header  = 'POST HTTP/1.0 \r\n';
		$header .= 'Content-type: text/xml \r\n';
		$header .= 'Content-length: ".strlen($pst)." \r\n';
		$header .= 'Content-transfer-encoding: text \r\n';
		//$header .='Connection: close \r\n\r\n';
		$header .= $pst;
		
		//print_r($pst);
		//'=' => %3D
		//' ' => %20
		//'(' => %28
		//')' => %29
		//'&' => %26
		//'@' => %40
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);
//		
////		curl_setopt_array($ch, array(
////			CURLOPT_URL            => $url,
////			CURLOPT_RETURNTRANSFER => TRUE,
////			CURLOPT_TIMEOUT        => 120,
////			//CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
////			CURLOPT_CUSTOMREQUEST  => $header
////			));
//
		$data = curl_exec($ch); 
		
		var_dump($data);
		
//		if(curl_errno($ch))
//			print curl_error($ch);
//		else
//			curl_close($ch);	
		
	}
	
	public function testAction()
	{
		
		$url =  'http://66.147.172.198:8080/transferedvalue/gateway';
		
		//$pst = '<TransferredValueTxn xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance">';
		$pst = '<TransferredValueTxn>';
		$pst.= '<TransferredValueTxnReq><ReqCat>TransferredValue</ReqCat><ReqAction>Redeem</ReqAction><Date>20110503</Date><Time>121511</Time>';
		$pst.= '<PartnerName>Toonprint</PartnerName>';
		$pst.= '<CardActionInfo>';
		$pst.= '<PIN>DEW-2Q2CIR2H3CB2</PIN>'; //sent 4/29/11
		//$pst.= '<AcctNum>jane22</AcctNum>';
	//	$pst.= '<SrcRefNum>000001</SrcRefNum>';
		$pst.= '</CardActionInfo>';
		$pst.= '</TransferredValueTxnReq></TransferredValueTxn>';
		
		/**
		 * Define POST URL and also payload
		 */
		define('XML_PAYLOAD', '<?xml version="1.0"?>'.$pst);
		define('XML_POST_URL', 'http://66.147.172.198:8080/transferedvalue/gateway');
		
		/**
		 * Initialize handle and set options
		 */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, XML_POST_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		curl_setopt($ch, CURLOPT_POSTFIELDS, XML_PAYLOAD);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: close'));
		
		/**
		 * Execute the request and also time the transaction
		 */
		$start = array_sum(explode(' ', microtime()));
		$result = curl_exec($ch);
		$stop = array_sum(explode(' ', microtime()));
		$totalTime = $stop - $start;
		
		/**
		 * Check for errors
		 */
		if ( curl_errno($ch) ) {
			$result = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
		} else {
			$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			switch($returnCode){
				case 404:
					$result = 'ERROR -> 404 Not Found';
					break;
				default:
					break;
			}
		}
		
		/**
		 * Close the handle
		 */
		curl_close($ch);
		
		/**
		 * Output the results and time
		 */
		//echo 'Total time for request: ' . $totalTime . "\n";
		//echo $result;   
		
		$oXML = new SimpleXMLElement($result);
		
		//print_r($oXML->TransferredValueTxnResp->RespCode);
		foreach($oXML->TransferredValueTxnResp as $oEntry){
			if($oEntry->RespCode == 0){
				echo "Success";	
			}
			elseif($oEntry->RespCode == 43){
				echo "Your Card is Invalid";
			}
			elseif($oEntry->RespCode == 46){
				echo "Your Card is Deactivated";
			}
			elseif($oEntry->RespCode == 38){
				echo "Your Card has already been redeemed.";
			}
		}
		
		/**
		 * Exit the script
		 */
		exit(0);
	
		
	}
	
	public function flkAction()
	{
		$base_url = "http://milws.incomm.com:8080/transferedvalue/gateway";
		$options = array_merge(
			array(
					'TransferredValueTxn' => 
					array(
						'<TransferredValueTxnReq>' =>
						array(
							'ReqCat' => 'TransferredValue',
							'ReqAction' => 'Redeem',
							'Date' => '20110429',
							'Time' => '121511',
							'PartnerName' => 'Toonprint',
							array('CardActionInfo' =>
								array('PIN' => 'DEW-QZ1MUULI1YVD',
									'AcctNum' => 'jane22',
									'SrcRefNum' => '000001'
									)
								)
							)
						)
					)			
			);

	
		// Initialize cURL request and set parameters
		$ch = curl_init($base_url);
		curl_setopt_array($ch, array(
			CURLOPT_URL            => $base_url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
			));

		$results = curl_exec($ch);
		
		print_r($results);
	}
	
	
	public function test1Action()
	{
		$html = '';
		$html.= '<form action="http://66.147.172.198:8080/transferedvalue/gateway" method="post">';
		$html.= '<textarea name="XMLrequest" cols="90" rows="20">';
		$html.= '<?xml version="1.0" encoding="UTF-8"?>< TransferredValueTxn xmlns:xsi="http://www.w3.org/2001/XMLSchemainstance">';
		$html.= '<TransferredValueTxnReq><ReqCat>TransferredValue</ReqCat><ReqAction>Redeem</ReqAction><Date>20110404</Date><Time>121511</Time>';
		$html.= '<PartnerName>Partner</PartnerName><CardActionInfo><PIN>DEW-VL38TFMHTQJV</PIN><AcctNum>jane22</AcctNum><SrcRefNum>000001</SrcRefNum></CardActionInfo>';
		$html.= '</TransferredValueTxnReq></TransferredValueTxn>';
		$html.= '</textarea>';
		$html.= '<input type="submit"></form>';
		
		echo $html;
	}
	
	
		
}

?>