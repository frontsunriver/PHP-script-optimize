<?php
	//FOR GETTING THE DATABASE CONNECTION
	function get_connection($transaction_support = false){
		global $database_host, $database_name, $database_user, $database_user_password;

		if(empty($transaction_support)){
			try {
			    $dbh = new PDO("mysql:host=$database_host;dbname=$database_name", $database_user, $database_user_password);
			} catch (PDOException $e) {
			    exit("<center>database connection failure</center>");
			}
		} else {
			try {
			    $dbh = new PDO("mysql:host=$database_host;dbname=$database_name", $database_user, $database_user_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			} catch (PDOException $e) {
			    exit("<center>database connection failure</center>");
			}
		}

		return $dbh;
	}

	//FOR CLEANING USER INPUT
	function clean($poison, $size = 0){
		if(empty($size)) return trim(strip_tags($poison));
		return substr(trim(strip_tags($poison)), 0, $size);
	}

	//FOR SENDING FEEDBACK TO THE USER
	function update_user($address = "", $message = "redirecting", $time = 2){
		global $base_url;
		exit("<center><h2>{$message}<meta http-equiv=\"refresh\" content=\"{$time};URL='{$base_url}{$address}'\" /></h2></center>");
	}

	//FOR EXTRACTING FORMATTED DATETIME
	function extract_datetime($datetime){
		if(strlen($datetime) >= 19){
			return str_replace("T", " ", substr($datetime, 0, 19));
		}

		return null;
	}

	//FOR IMPORTING JSON DATA
	function import_data($document) {
		if(!empty($document)){
			$extension = pathinfo($document, PATHINFO_EXTENSION);

			//CHECKING TO SEE IF THE UPLOADED DOCUMENT HAS A JSON EXTENSION
			if($extension == 'json'){
				$data = json_decode(file_get_contents($document), true);

				if(!empty($data)){
					if(!empty($data['projects']) && is_array($data['projects'])){

						foreach($data['projects'] AS $project_data){

							if($project_data['VersionNumber'] == "v2"){

								//SECTION FOR IMPORTING INTO THE PROJECTS TABLE
								if(array_key_exists('ProjectId', $project_data['Data']) && array_key_exists('ProjectName', $project_data['Data']) && array_key_exists('Code', $project_data['Data']) && array_key_exists('CreateDate', $project_data['Data']) && array_key_exists('ModifiedDate', $project_data['Data']) && array_key_exists('BidDate', $project_data['Data']) && array_key_exists('GoodUntilDate', $project_data['Data']) && array_key_exists('JobRef', $project_data['Data']) && array_key_exists('ProjectAddressId', $project_data['Data']) && array_key_exists('FreightSellOverride', $project_data['Data']) && array_key_exists('InstallationSellOverride', $project_data['Data']) && array_key_exists('FreightTaxable', $project_data['Data']) && array_key_exists('InstallationTaxable', $project_data['Data']) && array_key_exists('LockSell', $project_data['Data']) && array_key_exists('SalesTaxPercent', $project_data['Data']) && array_key_exists('SalesTaxOverride', $project_data['Data']) && array_key_exists('MarketingCategory', $project_data['Data']) && array_key_exists('ReadOnly', $project_data['Data']) && array_key_exists('ReadOnlyDescription', $project_data['Data']) && array_key_exists('PasswordProtected', $project_data['Data']) && array_key_exists('Status', $project_data['Data']) && array_key_exists('CustomFilter', $project_data['Data']) && array_key_exists('Memo', $project_data['Data']) && array_key_exists('OpportunityId', $project_data['Data']) && array_key_exists('CustomColumn1Name', $project_data['Data'])){

									$project_id = clean($project_data['Data']['ProjectId']);
									$project_name = clean($project_data['Data']['ProjectName']);
									$code = clean($project_data['Data']['Code']);
									$create_date = extract_datetime(clean($project_data['Data']['CreateDate']));
									$modified_date = extract_datetime(clean($project_data['Data']['ModifiedDate']));
									$bid_date = extract_datetime(clean($project_data['Data']['BidDate']));
									$good_until_date = extract_datetime(clean($project_data['Data']['GoodUntilDate']));
									$job_ref = clean($project_data['Data']['JobRef']);
									$project_address_id = clean($project_data['Data']['ProjectAddressId']);
									$freight_sell_override = filter_var(clean($project_data['Data']['FreightSellOverride']), FILTER_VALIDATE_FLOAT);
									$installation_sell_override = filter_var(clean($project_data['Data']['InstallationSellOverride']), FILTER_VALIDATE_FLOAT);
									$freight_taxable = filter_var(clean($project_data['Data']['FreightTaxable']), FILTER_VALIDATE_INT);
									$installation_taxable = filter_var(clean($project_data['Data']['InstallationTaxable']), FILTER_VALIDATE_INT);
									$lock_sell = filter_var(clean($project_data['Data']['LockSell']), FILTER_VALIDATE_INT);
									$sales_tax_percent = filter_var(clean($project_data['Data']['SalesTaxPercent']), FILTER_VALIDATE_FLOAT);
									$sales_tax_override = filter_var(clean($project_data['Data']['SalesTaxOverride']), FILTER_VALIDATE_FLOAT);
									$marketing_category = clean($project_data['Data']['MarketingCategory']);
									$read_only = filter_var(clean($project_data['Data']['ReadOnly']), FILTER_VALIDATE_INT);
									$read_only_description = clean($project_data['Data']['ReadOnlyDescription']);
									$password_protected = filter_var(clean($project_data['Data']['PasswordProtected']), FILTER_VALIDATE_INT);
									$status = clean($project_data['Data']['Status']);
									$custom_filter = clean($project_data['Data']['CustomFilter']);
									$memo = clean($project_data['Data']['Memo']);
									$opportunity_id = clean($project_data['Data']['OpportunityId']);
									$custom_column_1_name = clean($project_data['Data']['CustomColumn1Name']);
									$is_type = 1; // project :1, contract: 2

									//FOR PROCESSED VENDORS
									if(strtolower($code) == "contract" || strtoLower($code) == "project"){
										$where = " And `is_type` = 1 ";
										if(strtolower($code) == "contract") {
											$where = " And `is_type` = 2 ";
											$is_type = 2;
										}
 										//IF JOBREF = NULL, DISCARD RECORD
										if($job_ref != null){

											$connection = get_connection(true);
											$connection->beginTransaction();

											try {
												if(strtoLower($code) == "project") {
													$old_sub_line_items = array();
													$statement = $connection->prepare("SELECT `sub_line_item_id`, `quantity`, `is_exported` FROM `sub_line_items` INNER JOIN `line_items` ON `sub_line_items`.`line_item_id` = `line_items`.`line_item_id` WHERE `project_id` = ? and `sub_line_items`.`is_type` = $is_type");
													$statement->execute([$project_id]);
													if($statement->rowCount() > 0){
														foreach($statement AS $row){
															$old_sub_line_items[$row['sub_line_item_id']] = ["quantity" => $row['quantity'], "is_exported" => $row['is_exported']];
														}
													}
													$statement->closeCursor();

													//LOADING PROCESSED PURCHASE ORDERS, WILL LATER BE USED ON DETERMINING THE ACTUAL VALUES COMING IN FOR THE FIRST TIME
													$unexported_processed_purchase_orders = array();
													$statement = $connection->prepare("SELECT DISTINCT `purchase_order_id` FROM `processed_purchase_orders` WHERE `project_id` = ? AND `is_exported` = 0");
													$statement->execute([$project_id]);
													if($statement->rowCount() > 0){
														foreach($statement AS $row){
															$unexported_processed_purchase_orders[$row['purchase_order_id']] = true;
														}
													}
													$statement->closeCursor();

													$statement = $connection->prepare("DELETE FROM `purchase_orders` WHERE `project_id` = ?");
													$statement->execute([$project_id]);
													$statement->closeCursor();

													$statement = $connection->prepare("DELETE FROM `processed_purchase_orders` WHERE `project_id` = ?");
													$statement->execute([$project_id]);
													$statement->closeCursor();

												}else{
													$statement = $connection->prepare("DELETE FROM `processed_vendors` WHERE `project_id` = ?");
													$statement->execute([$project_id]);
													$statement->closeCursor();
												}

												//DELETING OUTDATED RECORDS
												$statement = $connection->prepare("DELETE FROM `address_repository` WHERE `project_id` = ?". $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();

												$statement = $connection->prepare("DELETE FROM `contacts` WHERE `project_id` = ?" . $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();

												$statement = $connection->prepare("DELETE FROM `customers` WHERE `project_id` = ?" . $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();
												$statement = $connection->prepare("DELETE `sub_line_items` FROM `sub_line_items` INNER JOIN `line_items` ON `sub_line_items`.`line_item_id` = `line_items`.`line_item_id` WHERE `project_id` = ? and `sub_line_items`.`is_type` = $is_type");
												$statement->execute([$project_id]);
												$statement->closeCursor();

												$statement = $connection->prepare("DELETE FROM `line_items` WHERE `project_id` = ?" . $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();

												$statement = $connection->prepare("DELETE FROM `vendors` WHERE `project_id` = ?" . $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();
												

												$statement = $connection->prepare("DELETE FROM `totals` WHERE `project_id` = ?" . $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();

												$statement = $connection->prepare("DELETE FROM `projects` WHERE `project_id` = ?" . $where);
												$statement->execute([$project_id]);
												$statement->closeCursor();

												//NOW PROCEDING TO IMPORT INTO THE PROJECTS TABLE
												$statement = $connection->prepare("INSERT INTO `projects` (`project_id`, `project_name`, `code`, `create_date`, `modified_date`, `bid_date`, `good_until_date`, `job_ref`, `project_address_id`, `freight_sell_override`, `installation_sell_override`, `freight_taxable`, `installation_taxable`, `lock_sell`, `sales_tax_percent`, `sales_tax_override`, `marketing_category`, `read_only`, `read_only_description`, `password_protected`, `status`, `custom_filter`, `memo`, `opportunity_id`, `custom_column_1_name`, `is_type`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$is_type)");
												$statement->execute([$project_id, $project_name, $code, $create_date, $modified_date, $bid_date, $good_until_date, $job_ref, $project_address_id, $freight_sell_override, $installation_sell_override, $freight_taxable, $installation_taxable, $lock_sell, $sales_tax_percent, $sales_tax_override, $marketing_category, $read_only, $read_only_description, $password_protected, $status, $custom_filter, $memo, $opportunity_id, $custom_column_1_name]);
												if($statement->rowCount() == 1){
													$statement->closeCursor();

													$purchase_order_aggregates = array();
													$vendor_aggregates = array();
													$flagged_vendor_ids = array();

													//FLAGGING IF VENDOR.NAME = "BY OWNER"
													if(isset($project_data['Data']['Vendors']) && is_array($project_data['Data']['Vendors'])){
														foreach($project_data['Data']['Vendors'] AS $item){
															//IF VENDOR AGGREGATED SELLTOTAL = 0, DO NOT ADD VENDOR TO VENDOR TABLE
															if(strtolower(clean($item["Name"])) == "by owner"){
																$flagged_vendor_ids[] = clean($item["VendorId"]);
															}
														}
													}

													//SECTION FOR IMPORTING INTO THE LINE ITEMS TABLE
													if(isset($project_data['Data']['LineItems']) && is_array($project_data['Data']['LineItems'])){
														$line_statement = $connection->prepare("INSERT INTO `line_items`(`project_id`, `line_item_id`, `item_number`, `category`, `is_type`) VALUES (?,?,?,?,$is_type)");

														foreach($project_data['Data']['LineItems'] AS $line_item){
															$line_statement->execute([$project_id, $line_item["LineItemId"], $line_item["ItemNumber"], $line_item["Category"]]);

															if($line_statement->rowCount() == 1){
																$line_statement->closeCursor();

																//SECTION FOR IMPORTING INTO THE SUB LINE ITEMS TABLE
																if(isset($line_item['SubLineItems']) && is_array($line_item['SubLineItems'])){
																	$questions = array();
																	$arguments = array();

																	foreach($line_item['SubLineItems'] AS $sub_line_item){
																		
																		//IF VENDOR.NAME = "BY OWNER", DISCARD ALL PURCHASEORDERS/LINEITEMS/SUBLINEITEMS WITH MATCHING VENDORID
																		if(!in_array(clean($sub_line_item["VendorId"]), $flagged_vendor_ids)){

																			//IF SUBLINEITEMS.ITEMTYPEDESCRIPTION = "SPARE NUMBER", DISCARD SUBLINEITEM
																			if(strtolower($sub_line_item['ItemTypeDescription']) != "Spare Number"){

																				if(strtoLower($code) == "project") {
																					if(filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT) != 0){
																						$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$is_type)";
	
																						$is_exported = 1;
																						//UPDATING THE QUANTITY VALUE
																						if(isset($old_sub_line_items[$sub_line_item["SubLineItemId"]])){
	
																							if(filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) != $old_sub_line_items[$sub_line_item["SubLineItemId"]]["quantity"]){
																								if(filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) < 0){
																									$sub_line_item["Quantity"] = filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) + $old_sub_line_items[$sub_line_item["SubLineItemId"]]["quantity"];
																								}
																								
																								$is_exported = 0;
																							}
	
																							if(isset($unexported_processed_purchase_orders[clean($sub_line_item["PurchaseOrderId"])])){
																								$is_exported = 0;
																							}
																						} else {
																							$is_exported = 0;
																						}
	
																						$arguments[] = clean($line_item["LineItemId"]);
																						$arguments[] = clean($sub_line_item["SubLineItemId"]);
																						$arguments[] = filter_var(clean($sub_line_item["ItemTypeCode"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["ItemTypeDescription"]);
																						$arguments[] = clean($sub_line_item["VendorId"]);
																						$arguments[] = clean($sub_line_item["PurchaseOrderId"]);
																						$arguments[] = clean($sub_line_item["FreightDataId"]);
																						$arguments[] = filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["Model"]);
																						$arguments[] = clean($sub_line_item["StockModel"]);
																						$arguments[] = clean($sub_line_item["AltStockModel"]);
																						$arguments[] = clean($sub_line_item["AltModel"]);
																						$arguments[] = clean($sub_line_item["MfrModel"]);
																						$arguments[] = clean($sub_line_item["CatalogProdId"]);
																						$arguments[] = filter_var(clean($sub_line_item["CustomItem"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["FromConfiguration"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["StockItem"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["Spec"]);
																						$arguments[] = clean($sub_line_item["Notes"]);
																						$arguments[] = filter_var(clean($sub_line_item["StatusCode"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["StatusDescription"]);
																						$arguments[] = clean($sub_line_item["SellingUnit"]);
																						$arguments[] = filter_var(clean($sub_line_item["UnitsPerCase"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["SpecialCode"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["SpecialDescription"]);
																						$arguments[] = filter_var(clean($sub_line_item["CallForPricing"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["SellPrice"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["FreightSell"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["InstallationSell"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["NetPrice"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["FreightNet"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["InstallationNet"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = clean($sub_line_item["Discount"]);
																						$arguments[] = filter_var(clean($sub_line_item["ListPrice"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["IsNetPricedItem"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["Rebate"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["CashDiscount"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = clean($sub_line_item["FreightClass"]);
																						$arguments[] = filter_var(clean($sub_line_item["Weight"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Cube"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Width"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Depth"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Height"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = clean($sub_line_item["SerialNbr"]);
																						$arguments[] = clean($sub_line_item["GTIN"]);
																						$arguments[] = clean($sub_line_item["ShipFromAddressId"]);
																						$arguments[] = clean($sub_line_item["SpecRemarks"]);
																						$arguments[] = clean($sub_line_item["Prime"]);
																						$arguments[] = clean($sub_line_item["Equal1"]);
																						$arguments[] = clean($sub_line_item["Equal2"]);
																						$arguments[] = clean($sub_line_item["Alt"]);
																						$arguments[] = $is_exported;
	
																						//AGGREGATING VENDOR SELL TOTALS WHICH WILL BE IMPORTANT ON DISCARDING VENDORS WITH 0 AS THEIR AGGREGATES ON IMPORTING INTO THE VENDORS TABLE
																						if(array_key_exists($sub_line_item["VendorId"], $vendor_aggregates)){
																							$vendor_aggregates[$sub_line_item["VendorId"]] += filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																						} else {
																							$vendor_aggregates[$sub_line_item["VendorId"]] = filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																						}
	
	
																						//AGGREGATING PURCHASE ORDER TOTALS WHICH WILL BE IMPORTANT ON FREIGHT ROW IN PROCESSED PURCHASE ORDERS
																						if(isset($purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT)])){
																							$purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT)]["net_price"] += filter_var(clean($sub_line_item["NetPrice"]), FILTER_VALIDATE_FLOAT) * (filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) < 0 ? 0 : filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT));
																							$purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT)]["freight_net"] += filter_var(clean($sub_line_item["FreightNet"]), FILTER_VALIDATE_FLOAT) * (filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) < 0 ? 0 : filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT));
																						} else {
																							$purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT)]["net_price"] = filter_var(clean($sub_line_item["NetPrice"]), FILTER_VALIDATE_FLOAT) * (filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) < 0 ? 0 : filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT));
																							$purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT)]["freight_net"] = filter_var(clean($sub_line_item["FreightNet"]), FILTER_VALIDATE_FLOAT) * (filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT) < 0 ? 0 : filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT));

																							$purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT)]["line_item_category"] = $line_item["Category"];
																						}
																					}
																					
																				}else{
																					if(filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT) != 0){
																						$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$is_type)";
	
																						$arguments[] = clean($line_item["LineItemId"]);
																						$arguments[] = clean($sub_line_item["SubLineItemId"]);
																						$arguments[] = filter_var(clean($sub_line_item["ItemTypeCode"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["ItemTypeDescription"]);
																						$arguments[] = clean($sub_line_item["VendorId"]);
																						$arguments[] = clean($sub_line_item["PurchaseOrderId"]);
																						$arguments[] = clean($sub_line_item["FreightDataId"]);
																						$arguments[] = filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["Model"]);
																						$arguments[] = clean($sub_line_item["StockModel"]);
																						$arguments[] = clean($sub_line_item["AltStockModel"]);
																						$arguments[] = clean($sub_line_item["AltModel"]);
																						$arguments[] = clean($sub_line_item["MfrModel"]);
																						$arguments[] = clean($sub_line_item["CatalogProdId"]);
																						$arguments[] = filter_var(clean($sub_line_item["CustomItem"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["FromConfiguration"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["StockItem"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["Spec"]);
																						$arguments[] = clean($sub_line_item["Notes"]);
																						$arguments[] = filter_var(clean($sub_line_item["StatusCode"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["StatusDescription"]);
																						$arguments[] = clean($sub_line_item["SellingUnit"]);
																						$arguments[] = filter_var(clean($sub_line_item["UnitsPerCase"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["SpecialCode"]), FILTER_VALIDATE_INT);
																						$arguments[] = clean($sub_line_item["SpecialDescription"]);
																						$arguments[] = filter_var(clean($sub_line_item["CallForPricing"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["SellPrice"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["FreightSell"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["InstallationSell"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["NetPrice"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["FreightNet"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["InstallationNet"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = clean($sub_line_item["Discount"]);
																						$arguments[] = filter_var(clean($sub_line_item["ListPrice"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["IsNetPricedItem"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT);
																						$arguments[] = filter_var(clean($sub_line_item["Rebate"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["CashDiscount"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = clean($sub_line_item["FreightClass"]);
																						$arguments[] = filter_var(clean($sub_line_item["Weight"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Cube"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Width"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Depth"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = filter_var(clean($sub_line_item["Height"]), FILTER_VALIDATE_FLOAT);
																						$arguments[] = clean($sub_line_item["SerialNbr"]);
																						$arguments[] = clean($sub_line_item["GTIN"]);
																						$arguments[] = clean($sub_line_item["ShipFromAddressId"]);
																						$arguments[] = clean($sub_line_item["SpecRemarks"]);
																						$arguments[] = clean($sub_line_item["Prime"]);
																						$arguments[] = clean($sub_line_item["Equal1"]);
																						$arguments[] = clean($sub_line_item["Equal2"]);
																						$arguments[] = clean($sub_line_item["Alt"]);
	
																						//AGGREGATING VENDOR SELL TOTALS WHICH WILL BE IMPORTANT ON DISCARDING VENDORS WITH 0 AS THEIR AGGREGATES ON IMPORTING INTO THE VENDORS TABLE
																						if(array_key_exists($sub_line_item["VendorId"], $vendor_aggregates)){
																							$vendor_aggregates[$sub_line_item["VendorId"]] += filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																						} else {
																							$vendor_aggregates[$sub_line_item["VendorId"]] = filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																						}

																					}
																				}
																			}
																		}																
																	}	
																	
																	if(strtoLower($code) == "project") {
																		if(!empty($questions) && !empty($arguments) && (count($questions)) == count($arguments) / 54){
																			$sub_line_statement = $connection->prepare("INSERT INTO `sub_line_items`(`line_item_id`, `sub_line_item_id`, `item_type_code`, `item_type_description`, `vendor_id`, `purchase_order_id`, `freight_data_id`, `quantity`, `model`, `stock_model`, `alt_stock_model`, `alt_model`, `mfr_model`, `catalog_prod_id`, `custom_item`, `from_configuration`, `stock_item`, `spec`, `notes`, `status_code`, `status_description`, `selling_unit`, `units_per_case`, `special_code`, `special_description`, `call_for_pricing`, `sell_price`, `sell_total`, `freight_sell`, `installation_sell`, `net_price`, `freight_net`, `installation_net`, `discount`, `list_price`, `is_net_priced_item`, `taxable`, `rebate`, `cash_discount`, `freight_class`, `weight`, `cube`, `width`, `depth`, `height`, `serial_nbr`, `gtin`, `ship_from_address_id`, `spec_remarks`, `prime`, `equal_1`, `equal_2`, `alt`, `is_exported`, `is_type`) VALUES " . implode(", ", $questions));
																			$sub_line_statement->execute($arguments);
																			$sub_line_statement->closeCursor();
																		}
																	}else {
																		if(!empty($questions) && !empty($arguments) && (count($questions)) == count($arguments) / 53){
																			$sub_line_statement = $connection->prepare("INSERT INTO `sub_line_items`(`line_item_id`, `sub_line_item_id`, `item_type_code`, `item_type_description`, `vendor_id`, `purchase_order_id`, `freight_data_id`, `quantity`, `model`, `stock_model`, `alt_stock_model`, `alt_model`, `mfr_model`, `catalog_prod_id`, `custom_item`, `from_configuration`, `stock_item`, `spec`, `notes`, `status_code`, `status_description`, `selling_unit`, `units_per_case`, `special_code`, `special_description`, `call_for_pricing`, `sell_price`, `sell_total`, `freight_sell`, `installation_sell`, `net_price`, `freight_net`, `installation_net`, `discount`, `list_price`, `is_net_priced_item`, `taxable`, `rebate`, `cash_discount`, `freight_class`, `weight`, `cube`, `width`, `depth`, `height`, `serial_nbr`, `gtin`, `ship_from_address_id`, `spec_remarks`, `prime`, `equal_1`, `equal_2`, `alt`, `is_type`) VALUES " . implode(", ", $questions));
																			$sub_line_statement->execute($arguments);
																			$sub_line_statement->closeCursor();
																		}
																	}
																	
																}
															}
														}
													}

													//SECTION FOR IMPORTING INTO THE VENDORS TABLE
													if(isset($project_data['Data']['Vendors']) && is_array($project_data['Data']['Vendors'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Vendors'] AS $item){
															//IF VENDOR AGGREGATED SELLTOTAL = 0, DO NOT ADD VENDOR TO VENDOR TABLE
															if(!empty($vendor_aggregates[$item['VendorId']])){
																$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$is_type)";

																$arguments[] = $project_id;
																$arguments[] = clean($item["VendorId"]);
																$arguments[] = clean($item["Name"]);
																$arguments[] = clean($item["ShortName"]);
																$arguments[] = clean($item["CatalogVendorId"]);
																$arguments[] = clean($item["VendorAddressId"]);
																$arguments[] = clean($item["RepAddressId"]);
																$arguments[] = clean($item["AgentAddressId"]);
																$arguments[] = clean($item["PrimeSpec"]);
																$arguments[] = clean($item["Terms"]);
																$arguments[] = extract_datetime(clean($item["GoodUntilDate"]));
																$arguments[] = filter_var(clean($item["FreeFreight"]), FILTER_VALIDATE_INT);
																$arguments[] = clean($item["POEmail"]);
																$arguments[] = clean($item["ExportId"]);
																$arguments[] = clean($item["VendorNotes"]);
															}
														}	

														if(!empty($questions) && !empty($arguments) && (count($questions)) == count($arguments) / 15){
															$statement = $connection->prepare("INSERT INTO `vendors`(`project_id`, `vendor_id`, `name`, `short_name`, `catalog_vendor_id`, `vendor_address_id`, `rep_address_id`, `agent_address_id`, `prime_spec`, `terms`, `good_until_date`, `free_freight`, `pop_email`, `export_id`, `vendor_notes`, `is_type`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE CONTACTS TABLE
													if(isset($project_data['Data']['Contacts']) && is_array($project_data['Data']['Contacts'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Contacts'] AS $item){
															$questions[] = "(?,?,?,?,$is_type)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["ContactType"]);
															$arguments[] = clean($item["ContactAddressId"]);
															$arguments[] = clean($item["ExportId"]);
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 4){
															$statement = $connection->prepare("INSERT INTO `contacts`(`project_id`, `contact_type`, `contact_address_id`, `export_id`, `is_type`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE CUSTOMERS TABLE
													if(isset($project_data['Data']['Customers']) && is_array($project_data['Data']['Customers'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Customers'] AS $item){
															$questions[] = "(?,?,?,?,?,?,$is_type)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["CustomerId"]);
															$arguments[] = clean($item["CustomerAddressId"]);
															$arguments[] = filter_var(clean($item["CustomerSpecificSalesTax"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["CustomerSpecificMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = clean($item["ExportId"]);
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 6){
															$statement = $connection->prepare("INSERT INTO `customers`(`project_id`, `customer_id`, `customer_address_id`, `customer_specific_sales_tax`, `customer_specific_markup`, `export_id`, `is_type`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													$purchase_order_ids = array();
													if(strtoLower($code) == "project") {
														if(isset($project_data['Data']['PurchaseOrders']) && is_array($project_data['Data']['PurchaseOrders'])){
															$questions = array();
															$arguments = array();
	
															foreach($project_data['Data']['PurchaseOrders'] AS $item){
																//IF VENDOR.NAME = "BY OWNER", DISCARD ALL PURCHASEORDERS/LINEITEMS/SUBLINEITEMS WITH MATCHING VENDORID
																if(!in_array(clean($item["VendorId"]), $flagged_vendor_ids)){
																	//FOR EACH DATA:PURCHASEORDERS ITEM IF "PO NUMBER" IS NOT NULL, WE PROCESS THAT ITEM, OTHERWISE, IT IS DISCARDED
																	if(clean($item["PONumber"]) != null){
																		$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	
																		$arguments[] = $project_id;
																		$arguments[] = clean($item["PurchaseOrderId"]);
																		$arguments[] = clean($item["VendorId"]);
																		$arguments[] = clean($item["PONumber"]);
																		$arguments[] = clean($item["MailToAddressId"]);
																		$arguments[] = clean($item["ShipToAddressId"]);
																		$arguments[] = clean($item["BillToAddressId"]);
																		$arguments[] = clean($item["BuyerAddressId"]);
																		$arguments[] = extract_datetime(clean($item["CreateDate"]));
																		$arguments[] = extract_datetime(clean($item["EditDate"]));
																		$arguments[] = clean($item["FreightBilling"]);
																		$arguments[] = clean($item["PreferredCarrier"]);
																		$arguments[] = clean($item["ShippingInstructions"]);
																		$arguments[] = clean($item["Terms"]);
																		$arguments[] = clean($item["Status"]);
																		$arguments[] = clean($item["Instructions"]);
																		$arguments[] = clean($item["Notes"]);
																		$arguments[] = clean($item["FOBPoint"]);
																		$arguments[] = extract_datetime(clean($item["RequiredDate"]));
																		$arguments[] = extract_datetime(clean($item["ShipDate"]));
																		$arguments[] = extract_datetime(clean($item["ReceivedDate"]));
																		$arguments[] = extract_datetime(clean($item["POSentDate"]));
	
																		$purchase_order_ids[] = clean($item["PurchaseOrderId"]);
																	}
																}
															}	
	
															if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 22){
																$statement = $connection->prepare("INSERT INTO `purchase_orders`(`project_id`, `purchase_order_id`, `vendor_id`, `po_number`, `mail_to_address_id`, `ship_to_address_id`, `bill_to_address`, `buyer_address_id`, `create_date`, `edit_date`, `freight_billing`, `preferred_carrier`, `shipping_instructions`, `terms`, `status`, `instructions`, `notes`, `fob_point`, `required_date`, `ship_date`, `received_date`, `po_sent_date`) VALUES " . implode(", ", $questions));
																$statement->execute($arguments);
																$statement->closeCursor();
															}
														}
													}

													//SECTION FOR IMPORTING INTO THE ADDRESS REPOSITORY TABLE
													if(isset($project_data['Data']['AddressRepository']) && is_array($project_data['Data']['AddressRepository'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['AddressRepository'] AS $item){
															$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$is_type)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["AddressId"]);
															$arguments[] = clean($item["Name"]);
															$arguments[] = clean($item["FirstName"]);
															$arguments[] = clean($item["LastName"]);
															$arguments[] = clean($item["MiddleName"]);
															$arguments[] = clean($item["Title"]);
															$arguments[] = clean($item["Prefix"]);
															$arguments[] = clean($item["Suffix"]);
															$arguments[] = clean($item["Address1"]);
															$arguments[] = clean($item["Address2"]);
															$arguments[] = clean($item["City"]);
															$arguments[] = clean($item["State"]);
															$arguments[] = clean($item["Zip"]);
															$arguments[] = clean($item["Country"]);
															$arguments[] = clean($item["PhoneCompany"]);
															$arguments[] = clean($item["PhoneTollFree"]);
															$arguments[] = clean($item["PhoneContact"]);
															$arguments[] = clean($item["PhoneCell"]);
															$arguments[] = clean($item["PhoneFax"]);
															$arguments[] = clean($item["PhoneCompanyExt"]);
															$arguments[] = clean($item["PhoneContactExt"]);
															$arguments[] = clean($item["Website"]);
															$arguments[] = clean($item["Email"]);
															$arguments[] = clean($item["CompanyPKey"]);
															$arguments[] = clean($item["PeoplePKey"]);
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 26){
															$statement = $connection->prepare("INSERT INTO `address_repository`(`project_id`, `address_id`, `name`, `first_name`, `last_name`, `middle_name`, `title`, `prefix`, `suffix`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `phone_company`, `phone_toll_free`, `phone_contact`, `phone_cell`, `phone_fax`, `phone_company_ext`, `phone_contact_ext`, `website`, `email`, `company_p_key`, `people_p_key`, `is_type`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													$totals = array();

													//SECTION FOR IMPORTING INTO THE TOTALS TABLE
													if(isset($project_data['Data']['Totals']) && is_array($project_data['Data']['Totals'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Totals'] AS $item){
															$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$is_type)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["CustomerId"]);
															$arguments[] = filter_var(clean($item["MerchandiseSell"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["MerchandiseMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["MerchandiseNet"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["FreightSell"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["FreightMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["FreightNet"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["InstallationSell"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["InstallationMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["InstallationNet"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["GrossProfitPercent"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["GrossProfitAmount"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["SalesTaxTotal"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["SalesTaxPercentage"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = filter_var(clean($item["GrandSellTotal"]), FILTER_VALIDATE_FLOAT);

															$totals[$project_id]["freight_sell"] = filter_var(clean($item["FreightSell"]), FILTER_VALIDATE_FLOAT);
															$totals[$project_id]["sales_tax_total"] = filter_var(clean($item["SalesTaxTotal"]), FILTER_VALIDATE_FLOAT);
														}

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 16){
															$statement = $connection->prepare("INSERT INTO `totals`(`project_id`, `customer_id`, `merchandise_sell`, `merchandise_markup`, `merchandise_net`, `freight_sell`, `freight_markup`, `freight_net`, `installation_sell`, `installation_markup`, `installation_net`, `gross_profit_percent`, `gross_profit_amount`, `sales_tax_total`, `sales_tax_percentage`, `grand_sell_total`, `is_type`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													if(strtoLower($code) == "project") {
														$statement = $connection->prepare("INSERT INTO `processed_purchase_orders`(`id`, `purchase_order_id`, `po_number`, `project_id`, `project_name`, `job_ref`, `name`, `create_date`, `edit_date`, `taxable`, `ap_account_name`, `ar_account_name`, `net_price`, `freight_net`) SELECT * from `purchase_view` WHERE `project_id` = ? ");
														$statement->execute([$project_id]);
														$statement->closeCursor();

														$questions = array();
														$arguments = array();

														#$purchase_order_aggregates[$sub_line_item["VendorId"]][$sub_line_item["PurchaseOrderId"]][$sub_line_item["Taxable"]]
														foreach($purchase_order_aggregates AS $vendor_id => $purchase_order_aggregate){
															foreach($purchase_order_aggregate AS $purchase_order_id => $sub_line_items_2){
																foreach($sub_line_items_2 AS $taxable => $values){
																	if(in_array($purchase_order_id, $purchase_order_ids)){
																		$questions[] = "(?,?,?,?,?,?,?,?)";

																		$arguments[] = $purchase_order_id;
																		$arguments[] = $project_id;
																		$arguments[] = $project_name;
																		$arguments[] = $job_ref;
																		$arguments[] = $taxable;
																		$arguments[] = "COGS " . "Freight" . ($freight_taxable ? " T" : " NT");
																		$arguments[] = "COGS " . "Freight" . ($freight_taxable ? " T" : " NT");
																		$arguments[] = $values["freight_net"];
																	}
																}
															}		
														}

														if(!empty($questions) && !empty($arguments)){
															$statement = $connection->prepare("INSERT INTO `processed_purchase_orders`(`purchase_order_id`, `project_id`, `project_name`, `job_ref`, `taxable`, `ap_account_name`, `ar_account_name`, `net_price`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();

															$statement = $connection->prepare("UPDATE `processed_purchase_orders` INNER JOIN `purchase_orders` ON `processed_purchase_orders`.`purchase_order_id` = `purchase_orders`.`purchase_order_id` INNER JOIN `vendors` ON `purchase_orders`.`vendor_id` = `vendors`.`vendor_id` SET `processed_purchase_orders`.`id` = `purchase_orders`.`id`, `processed_purchase_orders`.`po_number` = `purchase_orders`.`po_number`, `processed_purchase_orders`.`name` = `vendors`.`name` WHERE `processed_purchase_orders`.`project_id` = ?");
															$statement->execute([$project_id]);
															$statement->closeCursor();
														}

														$statement = $connection->prepare("UPDATE `processed_purchase_orders` SET `is_exported` = 1 WHERE `purchase_order_id` IN (SELECT `purchase_order_id` FROM `sub_line_items` INNER JOIN `line_items` ON `sub_line_items`.`line_item_id` = `line_items`.`line_item_id` WHERE `is_exported` = 0 AND `project_id` = ? and `sub_line_items`.`is_type` = $is_type)");
														$statement->execute([$project_id]);
														$statement->closeCursor();

														$statement = $connection->prepare("UPDATE `sub_line_items` INNER JOIN `line_items` ON `sub_line_items`.`line_item_id` = `line_items`.`line_item_id` SET `is_exported` = 1 WHERE `sub_line_items`.`is_type` = $is_type and `project_id` = ?");
														$statement->execute([$project_id]);
														$statement->closeCursor();
													} else {
														$statement = $connection->prepare("INSERT INTO `processed_vendors`(`id`, `is_type`, `project_id`, `project_name`, `job_ref`, `code`, `name`, `export_id`, `freight_sell`, `sales_tax_total`, `sell_total`, `texable`, `ap_account_name`) SELECT * from `vendors_view` WHERE `project_id` = ? and is_type = $is_type");
														$statement->execute([$project_id]);

														//INSERTING THE MANUALLY CREATED ROW
														$statement = $connection->prepare("INSERT INTO `processed_vendors`(`project_id`, `project_name`, `job_ref`, `code`, `name`, `sell_total`, `texable`, `ap_account_name`) VALUES (?,?,?,?,?,?,?,?)");
														
														$statement->execute([$project_id, $project_name, $job_ref, $code, "FREIGHT", $totals[$project_id]["freight_sell"], 0, "COGS FREIGHT" . ($freight_taxable ? " T" : " NT")]);
														$statement->execute([$project_id, $project_name, $job_ref, $code, "Sales Tax", $totals[$project_id]["sales_tax_total"], 1, "COGS Sales Tax T"]);
														$statement->closeCursor();
													}
													
													
													//SECTION FOR CACHING PROCESSED VENDORS END

													$connection->commit();
												}
											} catch (Exception $e){
												//UNCOMMENT BELOW LINE ON DEBUGGING
												exit($e->getMessage());
											}
										}
									}
								}
							}
						}

						return true;
					}
				}
			}
		}

		return false;
	}