<?php
/*
	tables.php

	Provides classes/functions used for generating all the tables and forms
	used.

	Some of the handy features it provides:
	* Ability to select/unselect columns to display on tables
	* Lookups of column names against word database allows for different language translations.
	* CSV export function

	Class provided is "table"

*/

class table
{
	var $tablename;			// name of the table - used for internal purposes, not displayed
	var $language = "en_us";	// language to use for the form labels.
	
	var $columns;			// array containing all columns to be displayed
	var $columns_order;		// array containing columns to order by
	var $columns_available;		// used for the options form for displays columns
	
	var $data;			// table content
	var $data_num_rows;		// number of rows

	var $sql_table;			// SQL table to get the data from
	var $sql_query;			// SQL query used

	var $render_columns;		// human readable column names


	/*
		generate_sql()

		This function generates the SQL query to be used for generating the table.
	*/
	function generate_sql()
	{
		// prepare the select statement
		$this->sql_query = "SELECT ";
		
		foreach ($this->columns as $column)
		{
			$this->sql_query .= "$column, ";
		}

		$this->sql_query .= "id FROM `". $this->sql_table ."` ";
		
		
		
		if ($this->columns_order)
		{
			$this->sql_query .= "ORDER BY ";

			// add the order statements - make sure we don't add an extra comma on the end
			$count = 0;
			foreach ($this->columns_order as $column_order)
			{
				$count++;
			
				if ($count < count($this->columns_order))
				{
					$this->sql_query .= $column_order .", ";
				}
				else
				{
					$this->sql_query .= $column_order;
				}
			}
			
			$this->sql_query .= " ASC";
		}
		

		return 1;
	}


	/*
		generate_data()

		This function executes the SQL statement and fetches all the data from
		MySQL into an associate array.

		This data can then be used directly to generate the table, or can be
		modified by other code to produce the desired result before creating
		the final output.

		Returns the number of rows found.
	*/
	function generate_data()
	{
		$mysql_result		= mysql_query($this->sql_query);
		$mysql_num_rows		= mysql_num_rows($mysql_result);
		$this->data_num_rows	= $mysql_num_rows;

		if (!$mysql_num_rows)
		{
			return 0;
		}
		else
		{
			while ($mysql_data = mysql_fetch_array($mysql_result))
			{
				$this->data[] = $mysql_data;
			}

			return $mysql_num_rows;
		}
	}


	/*
		load_options_form()

		Imports data from POST or SESSION which matches this form to be used for the options.
	*/
	function load_options_form()
	{
		/*
			Form options can be passed in two ways:
			1. POST - this occurs when the options have been passed at the last reload
			2. SESSION - if the user goes away and returns.

		*/
		if ($_GET["table_display_options"])
		{
			$this->columns		= array();
			$this->columns_order	= array();

			// load checkboxes
			foreach ($this->columns_available as $column)
			{
				$column_setting = security_script_input("/^[a-z]*$/", $_GET[$column]);
				
				if ($column_setting == "on")
				{
					$this->columns[] = $column;
				}
			}

			// load orderby options
			$num_cols = count($this->columns_available);
			for ($i=0; $i < $num_cols; $i++)
			{
				if ($_GET["order_$i"])
				{
					$this->columns_order[] = security_script_input("/^\S*$/", $_GET["order_$i"]);
				}
			}
		}
		elseif ($_SESSION["form"][$this->tablename]["columns"])
		{
			// load checkboxes
			$this->columns		= $_SESSION["form"][$this->tablename]["columns"];
			$this->columns_order	= $_SESSION["form"][$this->tablename]["columns_order"];
		}

		// save options to session data
		$_SESSION["form"][$this->tablename]["columns"]		= $this->columns;
		$_SESSION["form"][$this->tablename]["columns_order"]	= $this->columns_order;

		return 1;
	}


	/*
		render_column_names($language)

		This function looks up the human-translation of the column names and returns the results.

		Defaults to US english (en_us) if no language is specified.
	*/
	function render_column_names()
	{
		$this->render_columns = language_translate($this->language, $this->columns);
		return 1;
	}


	/*
		render_options_form()
		
		Displays a list of all the avaliable columns for the user to select from.
	*/
	function render_options_form()
	{	
		// get labels for all the columns
		$labels = language_translate($this->language, $this->columns_available);


		// start the form
		print "<form method=\"get\" class=\"form_standard\">";
		
		$form = New form_input;
		$form->formname = $this->tablename;
		$form->language = $this->language;

		// include page name
		$structure = NULL;
		$structure["fieldname"] 	= "page";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $_GET["page"];
		$form->add_input($structure);
		$form->render_field("page");


		// flag this form as the table_display_options form
		$structure = NULL;
		$structure["fieldname"] 	= "table_display_options";
		$structure["type"]		= "hidden";
		$structure["defaultvalue"]	= $this->tablename;
		$form->add_input($structure);
		$form->render_field("table_display_options");



		// configure all the checkboxes
		$num_cols	= count($this->columns_available);
		$num_cols_half	= sprintf("%d", $num_cols / 2);
		
		for ($i=0; $i < $num_cols; $i++)
		{
			$column = $this->columns_available[$i];
			
			// define the checkbox
			$structure = NULL;
			$structure["fieldname"]		= $column;
			$structure["type"]		= "checkbox";
			
			if (in_array($column, $this->columns))
				$structure["defaultvalue"] = "on";
				
			$form->add_input($structure);

			// split the column options boxes into two different columns
			if ($i < $num_cols_half)
			{
				$column_a1[] = $column;
			}
			else
			{
				$column_a2[] = $column;
			}
			
		}
		


		// structure table
		print "<table width=\"100%\" style=\"border: 1px solid #000000;\"><tr>";
	
		print "<td width=\"25%\" valign=\"top\">";
		
			print "<b>Fields to display:</b><br><br>";

			// display the checkbox(s)
			foreach ($column_a1 as $column)
			{
				$form->render_field($column);
			}

		print "</td>";

		print "<td width=\"25%\" valign=\"top\">";
		
			print "<br><br>";
			
			// display the checkbox(s)
			foreach ($column_a2 as $column)
			{
				$form->render_field($column);
			}

		print "</td>";

		
		print "<td width=\"25%\" valign=\"top\"></td>";
		

		
		print "<td width=\"25%\" valign=\"top\">";

			print "<b>Order By (in the following order):</b><br><br>";

			// limit the number of order boxes to 4
			$num_cols = count($this->columns_available);

			if ($num_cols > 4)
				$num_cols = 4;

			
			for ($i=0; $i < $num_cols; $i++)
			{
				// define dropdown
				$structure = NULL;
				$structure["fieldname"]		= "order_$i";
				$structure["type"]		= "dropdown";
				
				if ($this->columns_order[$i])
					$structure["defaultvalue"] = $this->columns_order[$i];

				$structure["values"] = $this->columns_available;

				$form->add_input($structure);

				// display drop down
				$form->render_field($structure["fieldname"]);
				print "<br>";
			}
			
		print "</td>";

		
		// end of structure table
		print "</tr></table>";
	
		$structure = NULL;
		$structure["fieldname"]		= "submit";
		$structure["type"]		= "submit";
		$structure["defaultvalue"]	= "Apply Options";
		$form->add_input($structure);

		print "<br>";
		$form->render_field("submit");
		print "<br><br>";

		print "</form>";
	}



} // end of table class



?>