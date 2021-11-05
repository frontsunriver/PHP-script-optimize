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
		echo $document . "</br>";
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
									$freight_sell_override = (double) filter_var(clean($project_data['Data']['FreightSellOverride']), FILTER_VALIDATE_FLOAT);
									$installation_sell_override = (double) filter_var(clean($project_data['Data']['InstallationSellOverride']), FILTER_VALIDATE_FLOAT);
									$freight_taxable = (int) filter_var(clean($project_data['Data']['FreightTaxable']), FILTER_VALIDATE_INT);
									$installation_taxable = (int) filter_var(clean($project_data['Data']['InstallationTaxable']), FILTER_VALIDATE_INT);
									$lock_sell = (int) filter_var(clean($project_data['Data']['LockSell']), FILTER_VALIDATE_INT);
									$sales_tax_percent = (double) filter_var(clean($project_data['Data']['SalesTaxPercent']), FILTER_VALIDATE_FLOAT);
									$sales_tax_override = (double) filter_var(clean($project_data['Data']['SalesTaxOverride']), FILTER_VALIDATE_FLOAT);
									$marketing_category = clean($project_data['Data']['MarketingCategory']);
									$read_only = (int) filter_var(clean($project_data['Data']['ReadOnly']), FILTER_VALIDATE_INT);
									$read_only_description = clean($project_data['Data']['ReadOnlyDescription']);
									$password_protected = (int) filter_var(clean($project_data['Data']['PasswordProtected']), FILTER_VALIDATE_INT);
									$status = clean($project_data['Data']['Status']);
									$custom_filter = clean($project_data['Data']['CustomFilter']);
									$memo = clean($project_data['Data']['Memo']);
									$opportunity_id = clean($project_data['Data']['OpportunityId']);
									$custom_column_1_name = clean($project_data['Data']['CustomColumn1Name']);

									//IF CODE <> "CONTRACT" OR "PROJECT", DISCARD RECORD
									if((strtolower($code) == "contract" || strtolower($code) == "project")){

										//IF JOBREF = NULL, DISCARD RECORD
										if($job_ref != null){

											$connection = get_connection(true);
											$connection->beginTransaction();

											try {
												//DELETING OUTDATED RECORDS
												$statement = $connection->prepare("DELETE FROM `address_repository` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `contacts` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `customers` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE `sub_line_items` FROM `sub_line_items` INNER JOIN `line_items` ON `sub_line_items`.`line_item_id` = `line_items`.`line_item_id` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `line_items` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `line_items` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `purchase_orders` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `vendors` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `processed_vendors` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `totals` WHERE `project_id` = ?");
												$statement->execute([$project_id]);

												$statement = $connection->prepare("DELETE FROM `projects` WHERE `project_id` = ?");
												$statement->execute([$project_id]);




												//NOW PROCEDING TO IMPORT INTO THE PROJECTS TABLE
												$statement = $connection->prepare("INSERT INTO `projects` (`project_id`, `project_name`, `code`, `create_date`, `modified_date`, `bid_date`, `good_until_date`, `job_ref`, `project_address_id`, `freight_sell_override`, `installation_sell_override`, `freight_taxable`, `installation_taxable`, `lock_sell`, `sales_tax_percent`, `sales_tax_override`, `marketing_category`, `read_only`, `read_only_description`, `password_protected`, `status`, `custom_filter`, `memo`, `opportunity_id`, `custom_column_1_name`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
												$statement->execute([$project_id, $project_name, $code, $create_date, $modified_date, $bid_date, $good_until_date, $job_ref, $project_address_id, $freight_sell_override, $installation_sell_override, $freight_taxable, $installation_taxable, $lock_sell, $sales_tax_percent, $sales_tax_override, $marketing_category, $read_only, $read_only_description, $password_protected, $status, $custom_filter, $memo, $opportunity_id, $custom_column_1_name]);

												if($statement->rowCount() == 1){
													$statement->closeCursor();

													$vendor_aggregate_sell_totals = array();
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
														$line_statement = $connection->prepare("INSERT INTO `line_items`(`project_id`, `line_item_id`, `item_number`, `category`) VALUES (?,?,?,?)");

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

																				//IF SUBLINEITEMS.SELLTOTAL = 0, DISCARD SUBLINEITEM
																				if(filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT) != 0){
																					$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

																					$arguments[] = clean($line_item["LineItemId"]);
																					$arguments[] = clean($sub_line_item["SubLineItemId"]);
																					$arguments[] = (int) filter_var(clean($sub_line_item["ItemTypeCode"]), FILTER_VALIDATE_INT);
																					$arguments[] = clean($sub_line_item["ItemTypeDescription"]);
																					$arguments[] = clean($sub_line_item["VendorId"]);
																					$arguments[] = clean($sub_line_item["PurchaseOrderId"]);
																					$arguments[] = clean($sub_line_item["FreightDataId"]);
																					$arguments[] = (int) filter_var(clean($sub_line_item["Quantity"]), FILTER_VALIDATE_INT);
																					$arguments[] = clean($sub_line_item["Model"]);
																					$arguments[] = clean($sub_line_item["StockModel"]);
																					$arguments[] = clean($sub_line_item["AltStockModel"]);
																					$arguments[] = clean($sub_line_item["AltModel"]);
																					$arguments[] = clean($sub_line_item["MfrModel"]);
																					$arguments[] = clean($sub_line_item["CatalogProdId"]);
																					$arguments[] = (int) filter_var(clean($sub_line_item["CustomItem"]), FILTER_VALIDATE_INT);
																					$arguments[] = (int) filter_var(clean($sub_line_item["FromConfiguration"]), FILTER_VALIDATE_INT);
																					$arguments[] = (int) filter_var(clean($sub_line_item["StockItem"]), FILTER_VALIDATE_INT);
																					$arguments[] = clean($sub_line_item["Spec"]);
																					$arguments[] = clean($sub_line_item["Notes"]);
																					$arguments[] = (int) filter_var(clean($sub_line_item["StatusCode"]), FILTER_VALIDATE_INT);
																					$arguments[] = clean($sub_line_item["StatusDescription"]);
																					$arguments[] = clean($sub_line_item["SellingUnit"]);
																					$arguments[] = (int) filter_var(clean($sub_line_item["UnitsPerCase"]), FILTER_VALIDATE_INT);
																					$arguments[] = (int) filter_var(clean($sub_line_item["SpecialCode"]), FILTER_VALIDATE_INT);
																					$arguments[] = clean($sub_line_item["SpecialDescription"]);
																					$arguments[] = (int) filter_var(clean($sub_line_item["CallForPricing"]), FILTER_VALIDATE_INT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["SellPrice"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["FreightSell"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["InstallationSell"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["NetPrice"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["FreightNet"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["InstallationNet"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = clean($sub_line_item["Discount"]);
																					$arguments[] = (double) filter_var(clean($sub_line_item["ListPrice"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (int) filter_var(clean($sub_line_item["IsNetPricedItem"]), FILTER_VALIDATE_INT);
																					$arguments[] = (int) filter_var(clean($sub_line_item["Taxable"]), FILTER_VALIDATE_INT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["Rebate"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["CashDiscount"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = clean($sub_line_item["FreightClass"]);
																					$arguments[] = (double) filter_var(clean($sub_line_item["Weight"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["Cube"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["Width"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["Depth"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = (double) filter_var(clean($sub_line_item["Height"]), FILTER_VALIDATE_FLOAT);
																					$arguments[] = clean($sub_line_item["SerialNbr"]);
																					$arguments[] = clean($sub_line_item["GTIN"]);
																					$arguments[] = clean($sub_line_item["ShipFromAddressId"]);
																					$arguments[] = clean($sub_line_item["SpecRemarks"]);
																					$arguments[] = clean($sub_line_item["Prime"]);
																					$arguments[] = clean($sub_line_item["Equal1"]);
																					$arguments[] = clean($sub_line_item["Equal2"]);
																					$arguments[] = clean($sub_line_item["Alt"]);

																					//AGGREGATING VENDOR SELL TOTALS WHICH WILL BE IMPORTANT ON DISCARDING VENDORS WITH 0 AS THEIR AGGREGATES ON IMPORTING INTO THE VENDORS TABLE
																					if(array_key_exists($sub_line_item["VendorId"], $vendor_aggregate_sell_totals)){
																						$vendor_aggregate_sell_totals[$sub_line_item["VendorId"]] += (double) filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																					} else {
																						$vendor_aggregate_sell_totals[$sub_line_item["VendorId"]] = (double) filter_var(clean($sub_line_item["SellTotal"]), FILTER_VALIDATE_FLOAT);
																					}
																				}
																			}
																		}																
																	}	

																	if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 53){
																		$sub_line_statement = $connection->prepare("INSERT INTO `sub_line_items`(`line_item_id`, `sub_line_item_id`, `item_type_code`, `item_type_description`, `vendor_id`, `purchase_order_id`, `freight_data_id`, `quantity`, `model`, `stock_model`, `alt_stock_model`, `alt_model`, `mfr_model`, `catalog_prod_id`, `custom_item`, `from_configuration`, `stock_item`, `spec`, `notes`, `status_code`, `status_description`, `selling_unit`, `units_per_case`, `special_code`, `special_description`, `call_for_pricing`, `sell_price`, `sell_total`, `freight_sell`, `installation_sell`, `net_price`, `freight_net`, `installation_net`, `discount`, `list_price`, `is_net_priced_item`, `taxable`, `rebate`, `cash_discount`, `freight_class`, `weight`, `cube`, `width`, `depth`, `height`, `serial_nbr`, `gtin`, `ship_from_address_id`, `spec_remarks`, `prime`, `equal_1`, `equal_2`, `alt`) VALUES " . implode(", ", $questions));
																		$sub_line_statement->execute($arguments);
																		$sub_line_statement->closeCursor();
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
															if(!empty($vendor_aggregate_sell_totals[$item['VendorId']])){
																$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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
																$arguments[] = (int) filter_var(clean($item["FreeFreight"]), FILTER_VALIDATE_INT);
																$arguments[] = clean($item["POEmail"]);
																$arguments[] = clean($item["ExportId"]);
																$arguments[] = clean($item["VendorNotes"]);
															}
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 15){
															$statement = $connection->prepare("INSERT INTO `vendors`(`project_id`, `vendor_id`, `name`, `short_name`, `catalog_vendor_id`, `vendor_address_id`, `rep_address_id`, `agent_address_id`, `prime_spec`, `terms`, `good_until_date`, `free_freight`, `pop_email`, `export_id`, `vendor_notes`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE CONTACTS TABLE
													if(isset($project_data['Data']['Contacts']) && is_array($project_data['Data']['Contacts'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Contacts'] AS $item){
															$questions[] = "(?,?,?,?)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["ContactType"]);
															$arguments[] = clean($item["ContactAddressId"]);
															$arguments[] = clean($item["ExportId"]);
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 4){
															$statement = $connection->prepare("INSERT INTO `contacts`(`project_id`, `contact_type`, `contact_address_id`, `export_id`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE CUSTOMERS TABLE
													if(isset($project_data['Data']['Customers']) && is_array($project_data['Data']['Customers'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Customers'] AS $item){
															$questions[] = "(?,?,?,?,?,?)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["CustomerId"]);
															$arguments[] = clean($item["CustomerAddressId"]);
															$arguments[] = (double) filter_var(clean($item["CustomerSpecificSalesTax"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["CustomerSpecificMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = clean($item["ExportId"]);
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 6){
															$statement = $connection->prepare("INSERT INTO `customers`(`project_id`, `customer_id`, `customer_address_id`, `customer_specific_sales_tax`, `customer_specific_markup`, `export_id`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE PURCHASE ORDERS TABLE
													if(isset($project_data['Data']['PurchaseOrders']) && is_array($project_data['Data']['PurchaseOrders'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['PurchaseOrders'] AS $item){
															//IF VENDOR.NAME = "BY OWNER", DISCARD ALL PURCHASEORDERS/LINEITEMS/SUBLINEITEMS WITH MATCHING VENDORID
															if(!in_array(clean($item["VendorId"]), $flagged_vendor_ids)){
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
															}
														}	

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 22){
															$statement = $connection->prepare("INSERT INTO `purchase_orders`(`project_id`, `purchase_order_id`, `vendor_id`, `po_number`, `mail_to_address_id`, `ship_to_address_id`, `bill_to_address`, `buyer_address_id`, `create_date`, `edit_date`, `freight_billing`, `preferred_carrier`, `shipping_instructions`, `terms`, `status`, `instructions`, `notes`, `fob_point`, `required_date`, `ship_date`, `received_date`, `po_sent_date`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE ADDRESS REPOSITORY TABLE
													if(isset($project_data['Data']['AddressRepository']) && is_array($project_data['Data']['AddressRepository'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['AddressRepository'] AS $item){
															$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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
															$statement = $connection->prepare("INSERT INTO `address_repository`(`project_id`, `address_id`, `name`, `first_name`, `last_name`, `middle_name`, `title`, `prefix`, `suffix`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `phone_company`, `phone_toll_free`, `phone_contact`, `phone_cell`, `phone_fax`, `phone_company_ext`, `phone_contact_ext`, `website`, `email`, `company_p_key`, `people_p_key`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR IMPORTING INTO THE TOTALS TABLE
													if(isset($project_data['Data']['Totals']) && is_array($project_data['Data']['Totals'])){
														$questions = array();
														$arguments = array();

														foreach($project_data['Data']['Totals'] AS $item){
															$questions[] = "(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

															$arguments[] = $project_id;
															$arguments[] = clean($item["CustomerId"]);
															$arguments[] = (double) filter_var(clean($item["MerchandiseSell"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["MerchandiseMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["MerchandiseNet"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["FreightSell"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["FreightMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["FreightNet"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["InstallationSell"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["InstallationMarkup"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["InstallationNet"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["GrossProfitPercent"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["GrossProfitAmount"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["SalesTaxTotal"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["SalesTaxPercentage"]), FILTER_VALIDATE_FLOAT);
															$arguments[] = (double) filter_var(clean($item["GrandSellTotal"]), FILTER_VALIDATE_FLOAT);
														}

														if(!empty($questions) && !empty($arguments) && count($questions) == count($arguments) / 16){
															$statement = $connection->prepare("INSERT INTO `totals`(`project_id`, `customer_id`, `merchandise_sell`, `merchandise_markup`, `merchandise_net`, `freight_sell`, `freight_markup`, `freight_net`, `installation_sell`, `installation_markup`, `installation_net`, `gross_profit_percent`, `gross_profit_amount`, `sales_tax_total`, `sales_tax_percentage`, `grand_sell_total`) VALUES " . implode(", ", $questions));
															$statement->execute($arguments);
															$statement->closeCursor();
														}
													}

													//SECTION FOR CACHING RESULTS
													$statement = $connection->prepare("INSERT INTO `processed_vendors`(`id`, `project_id`, `project_name`, `code`, `name`, `export_id`, `freight_sell`, `sales_tax_total`, `sell_total`, `texable`, `ap_account_name`) SELECT `vendors`.`id`, `projects`.`project_id`, `projects`.`project_name`, `projects`.`code`, `vendors`.`name`, `vendors`.`export_id`, `totals`.`freight_sell`, `totals`.`sales_tax_total`, SUM(`sub_line_items`.`sell_total`) AS `sell_total`, `sub_line_items`.`taxable`, CONCAT('COGS ', `vendors`.`name`, IF(`sub_line_items`.`taxable` = 0, ' NT', ' T')) AS `ap_account_name` FROM `vendors` INNER JOIN `projects` ON `vendors`.`project_id` = `projects`.`project_id` INNER JOIN `sub_line_items` ON `vendors`.`vendor_id` = `sub_line_items`.`vendor_id` INNER JOIN `totals` ON `projects`.`project_id` = `totals`.`project_id` WHERE `projects`.`project_id` = ? GROUP BY `vendors`.`vendor_id`, `sub_line_items`.`taxable`");
													$statement->execute([$project_id]);

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

	//FOR RETRIEVING PROJECTS
	function get_projects(){
		$statement = get_connection()->prepare("SELECT `id`, `project_name`, `code`, `job_ref`, `create_date`, `modified_date`, `project_address_id`, `freight_taxable`, `installation_taxable`, `status` FROM `projects` WHERE 1");
		$statement->execute();

		if(!isset($statement->errorInfo()[2]) && $statement->rowCount() > 0){
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	//FOR RETRIEVING CONTACTS
	function get_contacts(){
		$statement = get_connection()->prepare("SELECT `contacts`.`id`, `projects`.`job_ref`, `contacts`.`contact_type`, `contacts`.`contact_address_id`, `contacts`.`export_id` FROM `contacts` INNER JOIN `projects` ON `contacts`.`project_id` = `projects`.`project_id` WHERE 1");
		$statement->execute();

		if(!isset($statement->errorInfo()[2]) && $statement->rowCount() > 0){
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	//FOR RETRIEVING CUSTOMERS
	function get_customers(){
		$statement = get_connection()->prepare("SELECT `customers`.`id`, `customers`.`customer_id`, `customers`.`customer_address_id`, `customers`.`export_id` FROM `customers` WHERE 1");
		$statement->execute();

		if(!isset($statement->errorInfo()[2]) && $statement->rowCount() > 0){
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	//FOR RETRIEVING PROCESSED VENDORS
	function get_processed_vendors(){
		$statement = get_connection()->prepare("SELECT * FROM `processed_vendors`");
		$statement->execute();

		if(!isset($statement->errorInfo()[2]) && $statement->rowCount() > 0){
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	//FOR RETRIEVING ADDRESSES
	function get_addresses(){
		$statement = get_connection()->prepare("SELECT `id`, `address_id`, `name`,  `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `phone_company`, `email`, `people_p_key` FROM `address_repository` WHERE 1");
		$statement->execute();

		if(!isset($statement->errorInfo()[2]) && $statement->rowCount() > 0){
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	//FOR RETRIEVING TOTALS
	function get_totals(){
		$statement = get_connection()->prepare("SELECT `id`, `customer_id`, SUM(`merchandise_sell`) AS `merchandise_sell`, SUM(`merchandise_net`) AS `merchandise_net`, SUM(`freight_sell`) AS `freight_sell`, SUM(`freight_net`) AS `freight_net`, SUM(`sales_tax_total`) AS `sales_tax_total`, SUM(`sales_tax_percentage`) AS `sales_tax_percentage`, SUM(`grand_sell_total`) AS `grand_sell_total` FROM `totals` GROUP BY `customer_id`");
		$statement->execute();

		if(!isset($statement->errorInfo()[2]) && $statement->rowCount() > 0){
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
	}