<?php

/* Infos Config Twitter
   ==================== */
global $ConfigTwitter;
$ConfigTwitter = array(
    'oauth_access_token' => "58830999-tBODRfUUbd2cjemzUTKuJHQYxvqeJQDttg1xBtVx6",
    'oauth_access_token_secret' => "CI5iAXIomNueburSYL7I3Ux0HgQsVoiJlBqfODeQ5FCTy",
    'consumer_key' => "jVnOks5XvezhOA2y7BoNQ",
    'consumer_secret' => "Q5Gb6sHkicShf0TWzcEAu18LvwFmddBFeybQqSMMOE8"
);

/* Intercepter pour test
   ===================== */
/*if($_GET['action']=='fluxtwitterjson'){
	header('Content-Type: text/html; charset=utf-8');
	$compte = ($_GET['flux']) ? urldecode($_GET['flux']) : '@museomix';
	$flux = FluxTwitter($compte);
	#$flux = DonneesRechercheTwitter('#museomix');
	echo($flux);
	exit;
}*/

/* Flux Agenda Google
   ================== */
function FluxAgenda($uri){
	$aujourdhui = date('n, j, Y');
	$dateMin = urlencode(date(DATE_ATOM, mktime(0, 0, 0, $aujourdhui)));	
	$dateMax = urlencode(date(DATE_ATOM, mktime(0, 0, 0, 8, 8, 2013)));	
	$uri = 'https://www.googleapis.com/calendar/v3/calendars/6uc6o59rvjremcj7jqiobu21ng@group.calendar.google.com/events?sendNotifications=false&timeMin='.$dateMin.'&timeMax='.$dateMax.'&maxResults=100&key=AIzaSyBzPxqUS6om7OV_ItLq1irusy1XgyBH0Io'; 
	$agenda = ConnecterPage($uri);
	$agenda = json_decode($agenda);
	foreach($agenda->items as $evt){
		$triDates[$evt->start->dateTime] = $evt; 
	}
	ksort($triDates);
	foreach($triDates as $evt){
		$r[] = FormatEvenement($evt); 
	}
	$r = implode('</li><li class="evt-agenda">',$r);
	$r = '<ul class="lst-flux resultat-import"><li class="evt-agenda">'.$r.'</li></ul>';
	return $r;
}

/* Flux Twitter
   ============ */
function FluxTwitter($requete){
	$requete = trim($requete);
	if(strpos($requete,'@')===0){
		$flux = DonneesFluxTwitter(substr($requete,1,strlen($requete)));		
		$flux = json_decode($flux);
	}elseif(strpos($requete,'#')===0){
		$flux = DonneesRechercheTwitter($requete);
		$flux = json_decode($flux);
	}
	if($_GET['action']=='fluxtwitterjson'){
		return json_encode($flux); 
	}
	if(strpos($requete,'#')===0){
		$flux = $flux->statuses;
	}
	foreach($flux as $tweet){
		$r[] = FormatTweet($tweet);
	}
	$r = implode('</li><li class="tweet">',$r);
	$r = '<ul class="lst-flux resultat-import"><li class="tweet">'.$r.'</li></ul>';
	return $r;
}

/* Formatage d'événement Agenda Google
   =================================== */
function FormatEvenement($evt){
	$debut = $evt->start->dateTime;
	$debut = strtotime($debut);
	$datedebut = date_i18n('l, j F',$debut);
	$heuredebut = date_i18n('G\h',$debut);
	$fin = $evt->end->dateTime;
	$fin = strtotime($fin);
	$datefin = date_i18n('l, j F',$fin);
	$heurefin = date_i18n('G\h',$fin);
	$texte = $evt->summary;
	$r .= '<div class="contenu-evenement">';
	$r .= ' <strong class="dt-evenement">'.$datedebut.'</strong>';
	$r .= '<p class="tx-evenement"><span class="hr-evenement">'.$heuredebut.'</span> '.$texte.'</p>';
	#$r .= ' <span class="dt-evenement">'.$datefin.' '.$heurefin.'</span>';
	$r .= '</div>';
	return $r;
}

/* Formatage de tweet
   ================== */
function FormatTweet($tweet){
	$origin = (!$tweet->retweeted_status) ? $tweet : $tweet->retweeted_status;
	$compte = $origin->user->name;
	$idCompte = $origin->user->screen_name;
	$date = FormatDateTwitter($origin->created_at);
	$avatar = $origin->user->profile_image_url_https;
	$texte = FormatTexteTweet($origin->text,$origin->entities);
	$lien = 'https://twitter.com/'.$idCompte.'/status/'.$origin->id_str;
	$r .= '<div class="contenu-tweet">';
	$r .= '<a target="ext"class="ln-compte-twitter" href="https://twitter.com/'.$idCompte.'">';
	$r .= '<img class="im-profil-twitter" src="'.$avatar.'" />';
	$r .= '<strong class="nom-compte-twitter">'.$compte.'</strong> <span class="tagnom">@'.$compte.'</span>';
	$r .= '</a>';
	$r .= ' <small class="dt-tweet"><a class="ln-tweet" target="ext" href="'.$lien.'">'.$date.'</a></small>';
	$r .= '<p class="tx-tweet">'.$texte.'</p>';
	$r .= '<small><a class="ln-tweet" target="ext" href="'.$lien.'">Détails</a></small>';
	$r .= '</div>';
	return $r;
}

/* Formatage texte tweet
   ===================== */
function FormatTexteTweet($texte,$entites){

	$caracteres = str_split(utf8_decode($texte));
	
	foreach($entites->user_mentions as $ent){
		$lien = 'https://twitter.com/'.$ent->screen_name;
		$caracteres[$ent->indices[0]] = "<span class=\"blc-ln-twitter\">@<a class=\"ln-twitter\" target=\"ext\" href=\"$lien\">";
		$caracteres[$ent->indices[1]-1] .= '</a></span>';
	}
	foreach($entites->hashtags as $ent){
		$lien = 'https://twitter.com/search?q=%23'.$ent->text;
		$caracteres[$ent->indices[0]] = "<span class=\"blc-ln-twitter\">#<a class=\"ln-twitter\" target=\"ext\" href=\"$lien\">";
		$caracteres[$ent->indices[1]-1] .= '</a></span>';
	}
	foreach($entites->urls as $ent){
		$lien = $ent->expanded_url;
		$caracteres[$ent->indices[0]] = '<a class="ln-twitter" target="ext" href="'.$lien.'">';
		$caracteres[$ent->indices[1]-1] .= '</a>';
	}
	foreach($entites->media as $ent){
		$lien = $ent->expanded_url;
		$caracteres[$ent->indices[0]] = '<a class="ln-twitter" target="ext" href="'.$lien.'">';
		$caracteres[$ent->indices[1]-1] .= '</a>';
	}
	 
	return utf8_encode(implode($caracteres));

}

/* Formatage date Twitter
   ====================== */
function FormatDateTwitter($dateTwitter){
	$temps = strtotime($dateTwitter);
	if(date('d m Y',$temps)==date('d m Y')){
		$date = date_i18n('G \h',$temps);
	}else{
		$date = date_i18n('d M',$temps);
	}
	return $date; 
}

/* Requête compte Twitter
   ====================== */
function DonneesFluxTwitter($compte){
	global $ConfigTwitter;
	$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$getfield = '?screen_name='.$compte.'&count=3'; 
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($ConfigTwitter);
	$flux = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
	return $flux;
}

function DonneesRechercheTwitter($question){
	global $ConfigTwitter;
	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$getfield = '?q='.urlencode($question).'&count=3'; 
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($ConfigTwitter);
	$flux = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
	return $flux;
}



/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ 
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$requestMethod = 'POST';
 **/
/** POST fields required by the URL above. See relevant docs as above
$postfields = array(
    'screen_name' => 'twitterapi', 
    'count' => '2'
);
 **/
/** Perform a POST request and echo the response **/
#$twitter = new TwitterAPIExchange($settings);
#echo $twitter->buildOauth($url, $requestMethod)
 #            ->setPostfields($postfields)
 #            ->performRequest();

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/


/**
 * Twitter-API-PHP : Simple PHP wrapper for the v1.1 API
 * 
 * PHP version 5.3.10
 * 
 * @category Awesomeness
 * @package  Twitter-API-PHP
 * @author   James Mallison <me@j7mbo.co.uk>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://github.com/j7mbo/twitter-api-php
 */
class TwitterAPIExchange 
{
    private $oauth_access_token;
    private $oauth_access_token_secret;
    private $consumer_key;
    private $consumer_secret;
    private $postfields;
    private $getfield;
    protected $oauth;
    public $url;

    /**
     * Create the API access object. Requires an array of settings::
     * oauth access token, oauth access token secret, consumer key, consumer secret
     * These are all available by creating your own application on dev.twitter.com
     * Requires the cURL library
     * 
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        if (!in_array('curl', get_loaded_extensions())) 
        {
            exit('You need to install cURL, see: http://curl.haxx.se/docs/install.html');
        }
        
        if (!isset($settings['oauth_access_token'])
            || !isset($settings['oauth_access_token_secret'])
            || !isset($settings['consumer_key'])
            || !isset($settings['consumer_secret']))
        {
            exit('Make sure you are passing in the correct parameters');
        }

        $this->oauth_access_token = $settings['oauth_access_token'];
        $this->oauth_access_token_secret = $settings['oauth_access_token_secret'];
        $this->consumer_key = $settings['consumer_key'];
        $this->consumer_secret = $settings['consumer_secret'];
    }
    
    /**
     * Set postfields array, example: array('screen_name' => 'J7mbo')
     * 
     * @param array $array Array of parameters to send to API
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function setPostfields(array $array)
    {
        if (!is_null($this->getGetfield())) 
        { 
            exit('You can only choose get OR post fields.'); 
        }
        $this->postfields = $array;
        return $this;
    }
    
    /**
     * Set getfield string, example: '?screen_name=J7mbo'
     * 
     * @param string $string Get key and value pairs as string
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function setGetfield($string)
    {
        if (!is_null($this->getPostfields())) 
        { 
            exit('You can only choose get OR post fields.'); 
        }
        
        $this->getfield = $string;
        return $this;
    }
    
    /**
     * Get getfield string (simple getter)
     * 
     * @return string $this->getfields
     */
    public function getGetfield()
    {
        return $this->getfield;
    }
    
    /**
     * Get postfields array (simple getter)
     * 
     * @return array $this->postfields
     */
    public function getPostfields()
    {
        return $this->postfields;
    }
    
    /**
     * Build the Oauth object using params set in construct and additionals
     * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
     * 
     * @param string $url The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json
     * @param string $requestMethod Either POST or GET
     * @return \TwitterAPIExchange Instance of self for method chaining
     */
    public function buildOauth($url, $requestMethod)
    {
        if (strtolower($requestMethod) !== 'post' && strtolower($requestMethod) !== 'get')
        {
            exit('Request method must be either POST or GET');
        }
        
        $consumer_key = $this->consumer_key;
        $consumer_secret = $this->consumer_secret;
        $oauth_access_token = $this->oauth_access_token;
        $oauth_access_token_secret = $this->oauth_access_token_secret;
        
        $oauth = array( 
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );
        
        $getfield = $this->getGetfield();
        
        if (!is_null($getfield))
        {
            $getfields = str_replace('?', '', explode('&', $getfield));
            foreach ($getfields as $g)
            {
                $split = explode('=', $g);
                $oauth[$split[0]] = $split[1];
            }
        }
        
        $base_info = $this->buildBaseString($url, $requestMethod, $oauth);
        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;
        
        $this->url = $url;
        $this->oauth = $oauth;
        
        return $this;
    }
    
    /**
     * Perform the acual data retrieval from the API
     * 
     * @param boolean optional $return If true, returns data. 
     * @return json If $return param is true, returns json data.
     */
    public function performRequest($return = true)
    {
        if (!is_bool($return)) 
        { 
            exit('performRequest parameter must be true or false'); 
        }
        
        $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');
        
        $getfield = $this->getGetfield();
        $postfields = $this->getPostfields();

        $options = array( 
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );

        if (!is_null($postfields))
        {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }
        else
        {
            if ($getfield !== '')
            {
                $options[CURLOPT_URL] .= $getfield;
            }
        }

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        if ($return) { return $json; }
    }
    
    /**
     * Private method to generate the base string used by cURL
     * 
     * @param string $baseURI
     * @param string $method
     * @param string $params
     * @return string Built base string
     */
    private function buildBaseString($baseURI, $method, $params) 
    {
        $return = array();
        ksort($params);
        
        foreach($params as $key=>$value)
        {
            $return[] = "$key=" . $value;
        }
        
        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return)); 
    }
    
    /**
     * Private method to generate authorization header used by cURL
     * 
     * @param array $oauth Array of oauth data generated by buildOauth()
     * @return string $return Header used by cURL for request
     */    
    private function buildAuthorizationHeader($oauth) 
    {
        $return = 'Authorization: OAuth ';
        $values = array();
        
        foreach($oauth as $key => $value)
        {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        $return .= implode(', ', $values);
        return $return;
    }

}
