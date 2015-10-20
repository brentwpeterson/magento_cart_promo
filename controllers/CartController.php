<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Wdc_Cartex_CartController extends Mage_Checkout_CartController
{
	# Overloaded indexAction
	public function indexAction()
	{
		Mage::helper('cartex')->wdcCartChecker(1);
		parent::indexAction();		
	}
	
	public function couponPostAction()
	{
		
		$couponCode = (string) $this->getRequest()->getParam('coupon_code');
				
		if(Mage::getModel('cartex/cart_coupon')->isIncommCoupon($couponCode))
		{			
			//$couponCode = Mage::getModel('checkout/session')
			//	->getQuote()->getCouponCode();
				
			Mage::log('Line 23 Coupon code->'.$couponCode);
			
			$url = 'http://milws.incomm.com:8080/transferedvalue/gateway';
			//$surl = https://milws.incomm.com:8443/transferedvalue/gateway
			//$testurl =  'http://66.147.172.198:8080/transferedvalue/gateway';			
			
			$pst = '<TransferredValueTxn>';
			$pst.= '<TransferredValueTxnReq><ReqCat>TransferredValue</ReqCat><ReqAction>Redeem</ReqAction><Date>20110503</Date><Time>121511</Time>';
			$pst.= '<PartnerName>Toonprint</PartnerName>';
			$pst.= '<CardActionInfo>';
			//$pst.= '<PIN>DEW-7ALZE14N1Z8L</PIN>';
			$pst.= '<PIN>'.$couponCode.'</PIN>'; //sent 4/29/11
			//$pst.= '<AcctNum>jane22</AcctNum>';
			//	$pst.= '<SrcRefNum>000001</SrcRefNum>';
			$pst.= '</CardActionInfo>';
			$pst.= '</TransferredValueTxnReq></TransferredValueTxn>';
			
			/**
			 * Define POST URL and also payload
			 */
			define('XML_PAYLOAD', '<?xml version="1.0"?>'.$pst);
			define('XML_POST_URL', $url);
			
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
			
			$session = Mage::getSingleton('checkout/session');
			$error = true;
			//print_r($oXML->TransferredValueTxnResp->RespCode);
			foreach($oXML->TransferredValueTxnResp as $oEntry){
				
				Mage::log('code from incomm->'.$oEntry->RespCode.'code->'.$couponCode);
				if($oEntry->RespCode == 0){					
					$error = false;
					//echo "Success";	
					$session->addSuccess('Your card was redeemed');
				}
				elseif($oEntry->RespCode == 43){
					$session->addError('Your Card is Invalid');	
							
				}
				elseif($oEntry->RespCode == 46){
					$session->addError('Your Card is Deactivated');
				}
				elseif($oEntry->RespCode == 38){
					$session->addError('Your Card has already been redeemed.');
				}	
				
				if($error)
				{
					parent::_goBack();	
				}	
				
			}
			
			/**
			 * Exit the script
			 */
			//exit(0);
		}		
		
		parent::couponPostAction();
	}
	
	public function addgiftAction()
	{
		$productId = $this->getRequest()->getParam('product');
		$promoId = $this->getRequest()->getParam('promoid');

			
		$product = Mage::getModel('catalog/product')->load($productId);
		
		try {
			
			Mage::getModel('cartex/cart')->addProductLineItem($productId, $promoId);		
								
			$this->_getSession()->setCartWasUpdated(true);

			/**
			 * @todo remove wishlist observer processAddToCart
			 */
			Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);
			$message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
			if (!$this->_getSession()->getNoCartRedirect(true)) {
				$this->_getSession()->addSuccess($message);
				$this->_goBack();
			}
		}
		catch (Mage_Core_Exception $e) {
			if ($this->_getSession()->getUseNotice(true)) {
				$this->_getSession()->addNotice($e->getMessage());
			} else {
				$messages = array_unique(explode("\n", $e->getMessage()));
				foreach ($messages as $message) {
					$this->_getSession()->addError($message);
				}
			}

			$url = $this->_getSession()->getRedirectUrl(true);
			if ($url) {
				$this->getResponse()->setRedirect($url);
			} else {
				$this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
			}
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__($e->getMessage().' Can not add item to shopping cart 2'));
			$this->_goBack();
		}
			
	}
}

?>