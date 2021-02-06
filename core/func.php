<?php  

function is_post_request(){
	return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function allcap($data){
	$data = trim($data);
	$data = $data = str_replace('    ', ' ', $data);
	$data = $data = str_replace('   ', ' ', $data);
	$data = $data = str_replace('  ', ' ', $data);
	$data = strtoupper($data);
	
	return $data;
}

function space($data){
	$data = trim($data);
	$data = str_replace('    ', ' ', $data);
	$data = str_replace('   ', ' ', $data);
	$data = str_replace('  ', ' ', $data);
	return $data;
}

function noSpace($data){
	$data = trim($data);
	$data = str_replace(' ', '', $data);
	return $data;
}

function firstWordCap($data){
	$data = trim($data);
	$data = str_replace('    ', ' ', $data);
	$data = str_replace('   ', ' ', $data);
	$data = str_replace('  ', ' ', $data);
	$data = strtoupper($data);
	return $data;
}

function isAlpha($data){
	$data = space($data);
	$pass = true;
	if ( strlen($data) > 0 ) {
		$data = explode(" ", $data);
		foreach ($data as $key) {
			if ( !ctype_alpha($key) ) {
				$pass = false;
				break;	
			}
		}
		return $pass;
	}
	else {
		return false;
	}
}

function isDate($date_time) {
	$date_time = noSpace($date_time);
    $date_time = explode('-', $date_time);

    if (count($date_time) != 3) 	
    	return false;
    elseif ( checkdate($date_time[1], $date_time[2], $date_time[0]) )
    	return true;
    else
    	return false;
}

function activeStatus($data) {
	if ($data == '1')
		return 'Active';
	elseif ($data == '0')
		return 'In-active';
	else
		return '';
}

function gender($data) {
	$data = strtolower($data);
	if ($data == 'm')
		return 'Male';
	elseif ($data == 'f')
		return 'Female';
	else
		return '';
}

function formStatus($data) {
	$data = strtolower($data);
	if ($data == 'a')
		return 'Completed';
	elseif ($data == 'p')
		return 'Pending';
	elseif ($data == 'r')
		return 'Rejected';
	else
		return '';
}



//SMS Function
function getRandomUserAgent() {  //get random useragent
    $userAgents=array(
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
        "Opera/9.20 (Windows NT 6.0; U; en)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
        "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
        "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
        "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"       
    );
    $random = rand(0,count($userAgents)-1);
 
    return $userAgents[$random];
}



function sendSMS($to, $msg) { //send message
    try {
	    $ch = curl_init(); 
        
        $from = urlencode('FEDPOLY Ilaro');
        $msg = urldecode($msg);
        
        $url = "https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=YOUR_API_CODE&from={$from}&to={$to}&body={$msg}"; 
            
     
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        
        //add random user agent
        curl_setopt($ch,CURLOPT_USERAGENT,getRandomUserAgent());
        
        //execute curl
        $output = curl_exec($ch);
     
        curl_close($ch);
        //return $output;
    } catch (Exception $e) {
    	//do nothing
    }
}

?>