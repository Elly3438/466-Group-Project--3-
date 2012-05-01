<?php
	/* Which page should be 0, unless you are planning on picking a specific page*/
	function pass_var($value, $var)
	{
		$hyperlink = "?";
		
		if(isset(${$var}))
		{
			$temp = ${$var};
		}
		${$var} = $value;
		
		if(isset($item_searched))
		{
			$hyperlink = $hyperlink . "p1=" . $item_searched;  
			
		}
		if(isset($item_limit_brand))
		{
			$hyperlink = $hyperlink . "p2=" . $item_limit_brand;  
			
		}
		if(isset($item_limit_price))
		{
			$hyperlink = $hyperlink . "p3=" . $item_limit_price;  
			
		}
		if(isset($item_limit_type))
		{
			$hyperlink = $hyperlink . "p4=" . $item_limit_type;  
			
		}
		
		if(isset($item_page) )
		{
			$hyperlink = $hyperlink . "p5=" . $item_page;  
			
		}
		
		unset($item_page);
	 	unset(${$var});
	 	if(isset($temp))
		{
			${$var} = $temp;
		}
	 	
		return $hyperlink;
	}
	function pass_varp( $value)
	 {
	 	$hyperlink = "?";
		
		if(isset(${$var}))
		{
			$temp = ${$var};
		}
		${$var} = $value;
	
		
		if(isset($item_searched))
		{
			$hyperlink = $hyperlink . "p1=" . $item_searched;  
			
		}
		if(isset($item_searched))
		{
			$hyperlink = $hyperlink . "p2=" . $item_brand;  
			
		}
		if(isset($item_searched))
		{
			$hyperlink = $hyperlink . "p3=" . $item_price;  
			
		}
		if(isset($item_searched))
		{
			$hyperlink = $hyperlink . "p4=" . $item_type;  
			
		}
		if(isset($item_searched) )
		{
			$hyperlink = $hyperlink . "p5=" . $item_page;  
			
		}
	 	
		return $hyperlink;
	 }
?>