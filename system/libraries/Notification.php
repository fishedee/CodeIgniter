<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');

class CI_Notification {
	protected $appkey           = NULL; 
	protected $appMasterSecret     = NULL;
	protected $timestamp        = NULL;
	protected $validation_token = NULL;

	function __construct($option) {
		$this->appkey = $option['appKey'];
		$this->appMasterSecret = $option['appSecret'];
		$this->timestamp = strval(time());
	}

	/*
	 *	predefinedField keys: ["ticker", "title", "text", "after_open", "production_mode"]
	 *	extraField keys: ["any"]
	 */
	function sendAndroidBroadcast($predefinedField, $extraField) {
		try {
			$brocast = new AndroidBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			foreach( $predefinedField as $key=>$value ){
				$brocast->setPredefinedKeyValue($key,           $value);
			}

			// [optional]Set extra fields
			foreach( $extraField as $key=>$value ){
				$brocast->setExtraField($key, $value);
			}

			return $brocast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["device_tokens", "ticker", "title", "text", "after_open", "production_mode"]
	 *	exception keys: ["any"]
	 */
	function sendAndroidUnicast($predefinedField, $extraField) {
		try {
			$unicast = new AndroidUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			// Set your device tokens here
			foreach( $predefinedField as $key=>$value ){
				$unicast->setPredefinedKeyValue($key, $value); 
			}

			// Set extra fields
			foreach( $extraField as $key=>$value ){
				$unicast->setExtraField($key, $value);
			}

			return $unicast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["ticker", "title", "text", "after_open"]
	 *	deviceTokens keys: deviceToken1."\n".deviceToken2
	 */
	function sendAndroidFilecast($predefinedField, $deviceTokens) {
		try {
			$filecast = new AndroidFilecast();
			$filecast->setAppMasterSecret($this->appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			foreach( $predefinedField as $key=>$value ){
				$filecast->setPredefinedKeyValue($key, $value);
			}

			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents($deviceTokens);

			return $filecast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["ticker", "title", "text", "after_open", "production_mode"]
	 */
	function sendAndroidGroupcast($predefinedField) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"test"},
      	 	 *			{"tag":"Test"}
    	 	 *		]
		 	 *	}
		 	 */
			$filter = 	array(
				"where" => 	array(
					"and" 	=>  array(
						array(
							"tag" => "test"
						),
						array(
							"tag" => "Test"
						)
					)
				)
			);
					  
			$groupcast = new AndroidGroupcast();
			$groupcast->setAppMasterSecret($this->appMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set the filter condition
			$groupcast->setPredefinedKeyValue("filter",           $filter);

			foreach( $predefinedField as $key=>$value ){
				$groupcast->setPredefinedKeyValue($key, $value);
			}

			return $groupcast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["alias", "alias_type", "ticker", "title", "text", "after_open"]
	 */
	function sendAndroidCustomizedcast($predefinedField) {
		try {
			$customizedcast = new AndroidCustomizedcast();
			$customizedcast->setAppMasterSecret($this->appMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);
			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			foreach( $predefinedField as $key=>$value ){
				$customizedcast->setPredefinedKeyValue($key, $value);
			}
			
			return $customizedcast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["alert", "badge", "sound", "production_mode"]
	 *	customizedField keys: ["any"]
	 */
	function sendIOSBroadcast($predefinedField, $customizedField) {
		try {
			$brocast = new IOSBroadcast();
			$brocast->setAppMasterSecret($this->appMasterSecret);
			$brocast->setPredefinedKeyValue("appkey",           $this->appkey);
			$brocast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			foreach( $predefinedField as $key=>$value ){
				$brocast->setPredefinedKeyValue($key, $value);
			}
			
			// Set customized fields
			foreach( $customizedField as $key=>$value ){
				$brocast->setCustomizedField($key, $value);
			}

			return $brocast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["device_tokens", "alert", "badge", "sound", "production_mode"]
	 *	customizedField keys: ["any"]
	 */
	function sendIOSUnicast($predefinedField, $customizedField) {
		try {
			$unicast = new IOSUnicast();
			$unicast->setAppMasterSecret($this->appMasterSecret);
			$unicast->setPredefinedKeyValue("appkey",           $this->appkey);
			$unicast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			// Set your device tokens here
			foreach( $predefinedField as $key=>$value ){
				$unicast->setPredefinedKeyValue($key, $value);
			}

			// Set customized fields
			foreach( $customizedField as $key=>$value ){
				$unicast->setCustomizedField($key, $value);
			}

			return $unicast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["alert", "badge", "sound", "production_mode"]
	 *	deviceTokens: deviceToken1."\n".deviceToken2
	 */
	function sendIOSFilecast($predefinedField, $deviceTokens) {
		try {
			$filecast = new IOSFilecast();
			$filecast->setAppMasterSecret($this->appMasterSecret);
			$filecast->setPredefinedKeyValue("appkey",           $this->appkey);
			$filecast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			foreach( $predefinedField as $key=>$value ){
				$filecast->setPredefinedKeyValue($key, $value);
			}

			// Upload your device tokens, and use '\n' to split them if there are multiple tokens
			$filecast->uploadContents($deviceTokens);

			return $filecast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["alert", "badge", "sound", "production_mode"]
	 */
	function sendIOSGroupcast($predefinedField) {
		try {
			/* 
		 	 *  Construct the filter condition:
		 	 *  "where": 
		 	 *	{
    	 	 *		"and": 
    	 	 *		[
      	 	 *			{"tag":"iostest"}
    	 	 *		]
		 	 *	}
		 	 */
			$filter = 	array(
				"where" => 	array(
					"and" 	=>  array(
						array(
							"tag" => "iostest"
						)
					)
				)
			);
					  
			$groupcast = new IOSGroupcast();
			$groupcast->setAppMasterSecret($this->appMasterSecret);
			$groupcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$groupcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			// Set the filter condition
			$groupcast->setPredefinedKeyValue("filter",           $filter);

			foreach( $predefinedField as $key=>$value ){
				$groupcast->setPredefinedKeyValue($key, $value);
			}

			return $groupcast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}

	/*
	 *	predefinedField keys: ["alias", "alias_type", "alert", "badge", "sound", "production_mode"]
	 */
	function sendIOSCustomizedcast($predefinedField, $customizedField) {
		try {
			$customizedcast = new IOSCustomizedcast();
			$customizedcast->setAppMasterSecret($this->appMasterSecret);
			$customizedcast->setPredefinedKeyValue("appkey",           $this->appkey);
			$customizedcast->setPredefinedKeyValue("timestamp",        $this->timestamp);

			// Set your alias here, and use comma to split them if there are multiple alias.
			// And if you have many alias, you can also upload a file containing these alias, then 
			// use file_id to send customized notification.
			foreach( $predefinedField as $key=>$value ){
				$customizedcast->setPredefinedKeyValue($key, $value);
			}

			// Set customized fields
			foreach( $customizedField as $key=>$value ){
				$customizedcast->setCustomizedField($key, $value);
			}

			return $customizedcast->send();
		} catch (Exception $e) {
			log_message("error", "Caught exception: " . $e->getMessage());
		}
	}
}
