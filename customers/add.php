<?php
/*
	customers/add.php
	
	access: customers_write

	Allows the creation of a new customer account - this form
	requests mininal details, then directs the user to the view/edit form
	so that they can complete the changes.


*/

if (user_permissions_get('customers_write'))
{
	function page_render()
	{
		$id = security_script_input('/^[0-9]*$/', $_GET["id"]);

		/*
			Title + Summary
		*/
		print "<h2>ADD CUSTOMER RECORD</h2><br>";
		print "<p>This page allows you to add a new customer.</p>";

		/*
			Define form structure
		*/
		$form = New form_input;
		$form->formname = "customer_view";
		$form->language = $_SESSION["user"]["lang"];

		$form->action = "customers/edit-process.php";
		$form->method = "post";
		

		// general
		$structure = NULL;
		$structure["fieldname"] 	= "name_customer";
		$structure["type"]		= "input";
		$structure["options"]["req"]	= "yes";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "name_contact";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "name_contact";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "contact_email";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "contact_phone";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "contact_fax";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "date_start";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "date_end";
		$structure["type"]	= "input";
		$form->add_input($structure);


		// tax options
		$structure = NULL;
		$structure["fieldname"] = "tax_included";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "tax_number";
		$structure["type"]	= "input";
		$form->add_input($structure);


		// billing address
		$structure = NULL;
		$structure["fieldname"] = "address1_street";
		$structure["type"]	= "textarea";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "address1_city";
		$structure["type"]	= "input";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "address1_state";
		$structure["type"]	= "input";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "address1_country";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "address1_zipcode";
		$structure["type"]	= "input";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "pobox";
		$structure["type"]	= "textarea";
		$form->add_input($structure);


		// shipping address
		$structure = NULL;
		$structure["fieldname"] = "address2_street";
		$structure["type"]	= "textarea";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "address2_city";
		$structure["type"]	= "input";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "address2_state";
		$structure["type"]	= "input";
		$form->add_input($structure);
		
		$structure = NULL;
		$structure["fieldname"] = "address2_country";
		$structure["type"]	= "input";
		$form->add_input($structure);

		$structure = NULL;
		$structure["fieldname"] = "address2_zipcode";
		$structure["type"]	= "input";
		$form->add_input($structure);
		

		// submit button
		$structure = NULL;
		$structure["fieldname"] 	= "submit";
		$structure["type"]		= "submit";
		$structure["defaultvalue"]	= "Create Customer";
		$form->add_input($structure);
		

		// define subforms
		$form->subforms["customer_view"]	= array("name_customer", "name_contact", "contact_phone", "contact_fax", "contact_email", "date_start", "date_end", "tax_included", "tax_number");
		$form->subforms["address_billing"]	= array("address1_street", "address1_city", "address1_state", "address1_country", "address1_zipcode", "pobox");
		$form->subforms["address_shipping"]	= array("address2_street", "address2_city", "address2_state", "address2_country", "address2_zipcode");
		$form->subforms["submit"]		= array("submit");
		
		// fetch the form data
		$form->sql_query = "SELECT * FROM `customers` WHERE id='$id' LIMIT 1";		
		$form->load_data();

		// display the form
		$form->render_form();

	} // end page_render

} // end of if logged in
else
{
	error_render_noperms();
}

?>