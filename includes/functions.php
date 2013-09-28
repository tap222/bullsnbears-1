<?php	
	function clean($value){//mysql_real_escape_string
		$value = trim($value);
		$escape = function_exists("mysql_real_escape_string");
		$magic = get_magic_quotes_gpc();
		if($escape){
			//undo any magic quote effect so mysql_real_escape_string can do its work
			if($magic){
				$value = stripslashes($value);
			}
			$value = mysql_real_escape_string($value);
		}else{
			if($magic){
				$value = addslashes($value);
			}
		}
		return $value;
	}
	
	function isnum($value){
		for($i=0;$i<10;$i++){
			$value = str_replace($i,"",$value);
		}
		return $value;
	}
	
	function isalpha($value){
		for($i='a';$i<'z';$i++){
			$value = str_ireplace($i,"",$value);
		}
		$value = str_ireplace('z',"",$value);
		return $value;
	}
	
	function alphanum($value){//returns value after stripping numbers and alphabets(case insensitive) 
		for($i=0;$i<10;$i++){
			$value = str_replace($i,"",$value);
		}
		for($i='a';$i<'z';$i++){
			$value = str_ireplace($i,"",$value);
		}
		$value = str_ireplace('z',"",$value);
		return $value;
	}
	
	function password_check($value){//checks if password is allowed(returns true if correct)
		$special_chars = array('.' , '_');
		$value = alphanum($value);
		$value = str_replace($special_chars,"",$value);
		if($value == ""){
			return true;
		}else{
			return false;
		}
	}
	
	function addarrow($value){//adds up arrow if +ve else down arrow
		if($value > 0){
			$out = "<img src=\"images/up_g.gif\" /> <span style=\"color:green;\">";
		}else	if($value < 0){
					$out = "<img src=\"images/down_r.gif\" /> <span style=\"color:red;\">";
				}else{
					$out = "<img src=\"images/up_g.gif\" /> <span style=\"color:green;\">";
				}
		
		$value = $out.number_format(abs($value),2,'.','')."</span>";
		return $value;
	}
	
	function present_value(){
		$sql = "select symbol from symbols";
		$result = mysql_query($sql);
		$n = mysql_num_rows($result);
		//$sql = "select symbols.symbol, a.value from symbols, (select * from stockval order by time_stamp desc limit $n) as a where symbols.symbol = a.symbol";
		$sql="select stockval.symbol as symbol, value from stockval, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u, symbols where stockval.symbol=u.symbol  and stockval.time_stamp=u.lt and stockval.symbol = symbols.symbol order by stockval.symbol";
		$stocks = mysql_query($sql);
		$value = array();
		while($stock = mysql_fetch_array($stocks)){
			$value[$stock['symbol']] = $stock['value'];
		}
		return $value;
	}
	

	
	function convertcash($num, $currency){
	    $currency = "Rs. ";
	    if(strlen($num)>3){
		    $lastthree = substr($num, strlen($num)-3, strlen($num));
		    $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
		    $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.

		    $expunit = str_split($restunits, 2);
		    
		    for($i=0; $i<sizeof($expunit); $i++){
		      
		        $explrestunits .= $expunit[$i].","; // creates each of the 2's group and adds a comma to the end
		    }   

		    $thecash = $explrestunits.$lastthree;
		    if ($thecash[1]=='-'&&$thecash[2]==',')
		     {  
		     	 $thecash[1]=' '; 
		         $thecash[2]='-';
	    		}
	    	    
	    	   }
	     else {
		   $thecash = $num;
	    }
	    if($thecash[0]=="0") $thecash = substr($thecash,1,strlen($thecash));
	    return $currency.$thecash; // writes the final format where $currency is the currency symbol.
	} 
	function metadetails(){
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
		<html>
		<head>
		  <title>Bulls n' Bears | Tathva '13</title>
		  <meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" >
		  <meta name=\"description\" content=\"bandb home\" />
		  <meta name=\"keywords\" content=\"stock simulator\" />
		  <link rel=\"stylesheet\" type=\"text/css\" href=\"stylesheets/global.css\" />

		  <link rel=\"shortcut icon\"  href=\"images/logo.jpg\" />";	
	}
	function navigation($page){

		$out= "<div id=\"banner\">
			<h1>Bulls And Bears</h1>
		</div><!-- banner -->
		<div>
			<ul id='navigation'>
				<li id=\"homebutton\" class=\"";
                                if($page=="home")
                                       $out .="page";
                                $out .= "\" ><a href=\"home.php\"><!--<img src = \"images/homeicon.png\">--> Home</a></li><li class=\"";
				if($page == "portfolio")
					$out .= "page";
				$out .= "\"><a href=\"portfolio.php\">Portfolio</a></li>
				<li class=\"";
				if($page == "trade")
					$out .= "page";
				$out .= "\"><a href=\"trade.php\">Trade</a></li>
				<li class =\"";
				if($page == "schedule")
					$out .="page";
				$out .= "\"><a href=\"schedule.php\">Schedule</a></li>
				<li class=\"";
				if($page == "rankings")
					$out .= "page";
				$out .= "\"><a href=\"rankings.php\">Rankings</a></li>
				<li class=\"";
				if($page == "markets")
					$out .= "page";
				$out .= "\"><a href=\"markets.php\">Markets</a></li>
				<li class=\"";
				if($page == "lookup")
					$out .= "page";
				$out .= "\"><a href=\"lookup.php\">Look Up</a></li>
				<li class=\"";
				if($page == "history")
					$out .="page";
				$out .= "\"><a href=\"history.php\">History</a></li>
				<li class=\"";
				if($page=="help")
					$out .="page";
				$out .="\"><a href=\"help_out.php\">Help</a></li>";
				$admins = parse_ini_file("config.ini");
				$admins = explode(' ', $admins['admin_ids']);
				if (in_array($_SESSION['playerid'], $admins)) {
					$out .= "<li class=\"";
					if ($page == "feedback") $out .= "page";
					$out .= "\"><a href=\"feedbacks.php\">Feedback</a></li>";
				}
                $out .= "<li  id=\"logoutbutton\"><a href=\"logout.php\"><img src = \"../images/logout.png\"> &nbsp; Logout</a></li>
			</ul>
		</div><!--navigation-->
		<div id=\"content_main\">";
		echo $out;
		if( ($page != "markets") && ($page!="lookup") &&($page!="history")  )
			require("includes/ticker_tape.php");	
	}
	
?>