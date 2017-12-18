<?php
    include ('validation.php');
    class riotClass
        {
          public $limit;
          public $vali;
          public $x;

          public function getID($summonername,$region,$apikey)
              {
                  $vali = new validation();
                  $namevalidation=$vali->emptyPost($summonername);
                  $regionvalidation=$vali->emptyPost($region);
                  $summonerurl = "https://". $region . ".api.riotgames.com/lol/summoner/v3/summoners/by-name/". $summonername ."?api_key=". $apikey;
                  $result = file_get_contents($summonerurl);
                  $resultDecoded = json_decode($result, true);
                  $summonerid = $resultDecoded['id'];
                  return $summonerid;
              }
          public function riotConnectionID($summonername1)
              { 

                $summoner =  json_decode($summonername1,true);
                $summonername = $summoner['summonname'];
				$region= $summoner['summonerregion'];
                $apikey ='>>>>>>>>Dummy API KEY PLEASE ADD ONE IN <<<<<<';      // Input API KEY HERE
                $summonerid=$this->getID($summonername,$region,$apikey);
                $this->riotConnectionmatch($region,$summonerid,$apikey);
              }

          public function riotConnectionRank($regionvalidation,$summonerid,$apikey,$name,$x)
              {     $valid  = 0;
                    $valid1 = 0;
                    $x1 = 1;
					
                    $summonerurl = "https://". $regionvalidation . ".api.riotgames.com/lol/league/v3/positions/by-summoner/". $summonerid ."?api_key=". $apikey;
                    $result = file_get_contents($summonerurl);
                    $resultDecoded['summonurl'] = json_decode($result, true);
                      if(isset($resultDecoded['summonurl'][0]['tier']))
                        {
                          $playerOrTeamName = $resultDecoded['summonurl'][0]['playerOrTeamName'];
                          $ftier = $resultDecoded['summonurl'][0]['tier'];
                          $fqueuetype= $resultDecoded['summonurl'][0]['queueType'];
                          $fleaguepoints= $resultDecoded['summonurl'][0]['leaguePoints'];
                          $fwins= $resultDecoded['summonurl'][0]['wins'];
                          $flosses= $resultDecoded['summonurl'][0]['losses'];
                          $object = array("solotier"=>$ftier,"soloqueuetype"=>$fqueuetype,"sololeaguepoints"=>$fleaguepoints,"playerOrTeamName"=>$playerOrTeamName,"swins"=>$fwins,"sloss"=>$flosses);
                        }
                      else
                        {
                          $valid = 1;
                        }
                        if(isset($resultDecoded['summonurl'][$x1]['tier']))
                        {
                          $stier = $resultDecoded['summonurl'][$x1]['tier'];
                          $squeuetype = $resultDecoded['summonurl'][$x1]['queueType'];
                          $sleaguepoints = $resultDecoded['summonurl'][$x1]['leaguePoints'];
                          $swins = $resultDecoded['summonurl'][$x1]['wins'];
                          $slosses = $resultDecoded['summonurl'][$x1]['losses'];
                          $object1 = array("tier"=>$stier,"queuetype"=>$squeuetype,"leaguepoints"=>$sleaguepoints,"fwins"=>$swins,"flosses"=>$slosses);
                        }
                      else
                        {
                          $valid1 = 1;
                        }

                     if ($valid ==1 and $valid1 ==1)
                        {
                          echo "Error 404";
                        }
                 else if ($valid == 1 && $valid1 == 0)
                        {
                          $mergearray = $object1;
                        }
                 else if ($valid == 0 && $valid1 == 1)
                        {
                          $mergearray = $object;
                        }
                else  if($valid ==0 && $valid1 ==0)
                        {
                          $mergearray = array_merge($object,$object1);
                        }
                          $mergearray1[$x] = $mergearray;
                          $json = json_encode($mergearray1);
                          return $json;
						}


        public function riotConnectionmatch($region,$summonerid,$apikey)
              {	  $champinfo = array();
				  				                    
                  $summonerurl = "https://". $region . ".api.riotgames.com//lol/spectator/v3/active-games/by-summoner/". $summonerid ."?api_key=". $apikey;
                  $result = file_get_contents($summonerurl);
                  $resultDecoded = json_decode($result, true);
			
					// Champion information data Cache
					$champ_file_name = 'champdata-text.txt';					
					$champUrl = "https://". $region . ".api.riotgames.com/lol/static-data/v3/champions?locale=en_US&dataById=false&api_key=". $apikey;
					$champData1 = $this->get_content($champ_file_name,$champUrl,24,'format_champlist',array('file'=>$champ_file_name));	
					$champData = $this->format_file($champData1);	
					// Summoner spell info Cache					
					$summoner_spell_file = 'summoner-spell-file.txt';
					$SummonerSpellUrl = "https://". $region . ".api.riotgames.com/lol/static-data/v3/summoner-spells?locale=en_US&dataById=false&api_key=". $apikey;
					$summonerSpelldata1 = $this->get_content($summoner_spell_file,$SummonerSpellUrl,3,'format_champlist',array('file'=>$summoner_spell_file));	
					$summonerSpelldata = $this->format_file($summonerSpelldata1);

                    $x = 0;
                    $i = 1;
					
                 while ($x<=9) // Data Loops 10 times to get each user information
                    {
                      $namer  = $resultDecoded['participants'][$x]['summonerName'];
                      $name = $this->replacespace($namer);
                      $getID = $this->getID($name,$region,$apikey);
                      $listcharacterinfo=$this->riotConnectionRank($region,$getID,$apikey,$name,$x);
                      $listdecode = json_decode($listcharacterinfo, true);
						// SOLO QUEUE DATA 	
                        if(isset($listdecode[$x]['soloqueuetype'])){
                          $summonername['champinfo'][$x]['name'] = $champinfo['playerOrTeamName'][$x] = $listdecode[$x]['playerOrTeamName'];
                          $summonername['champinfo'][$x]['soloqueuetype'] = $champinfo['soloqueuetype'][$x] = $listdecode[$x]['soloqueuetype'];
                          $summonername['champinfo'][$x]['sololeaguepoints'] = $champinfo['sololeaguepoints'][$x] = $listdecode[$x]['sololeaguepoints'];
                          $summonername['champinfo'][$x]['solotier'] = $champinfo['solotier'][$x] = $listdecode[$x]['solotier'];
                          }
						// Flex queue data
                        if(isset($listdecode[$x]['queuetype'])){
                        $summonername['champinfo'][$x]['tier'] = $champinfo['tier'][$x] = $listdecode[$x]['tier'];
                        $summonername['champinfo'][$x]['queuetype'] = $champinfo['queuetype'][$x] = $listdecode[$x]['queuetype'];
                        $summonername['champinfo'][$x]['leaguepoints'] = $champinfo['leaguepoints'][$x] = $listdecode[$x]['leaguepoints'];
                          }
                        $summonername['teamID'][$x] = $resultDecoded['participants'][$x]['teamId'];
						$summonerID1 =$resultDecoded['participants'][$x]['spell1Id'];
						$summonerID2 =$resultDecoded['participants'][$x]['spell2Id'];
						$champID=$resultDecoded['participants'][$x]['championId'];												
						$champname = $this->getChampName($champID,$champData);											
						$summonername['champID'][$x]=$champimageURL[$x]= $this->getImageUrl($champname,'champion');					
						$summoneringame1 = $this->getSummonerName($summonerID1,$summonerSpelldata); //summoner-spell 1
						$summoneringame2 = $this->getSummonerName($summonerID2,$summonerSpelldata);	//summoner-spell 2
                        $summonername['spellID'][$x]=$champimageURL[$x]= $this->getImageUrl($summoneringame1,'spell');
						$summonername['spellID2'][$x]=$champimageURL[$x]= $this->getImageUrl($summoneringame2,'spell');						
                      $x++; // Counter
                    }
                        echo  $summonname=json_encode($summonername);

            }
		public function getChampName($champID,$champData)
			{	
				$resultDecoded = json_decode(utf8_encode($champData), true);			
				foreach($resultDecoded as $data){
					foreach($data as $name => $details){
						 if ($details["id"]=== $champID)
						 {	//echo $details;
							return $details["name"];
						 }						 
					}
				}											
			}
		public function getSummonerName($summonerID,$summonerSpelldata)
			{		
				$resultDecoded = json_decode($summonerSpelldata, true);			
				foreach($resultDecoded as $data){
					foreach($data as $name => $details){
						 if ($details["id"]=== $summonerID)
						 {
							return $details["key"];
						 }						 
					}
				}
			}	
		
			
		public function getImageUrl ($champname,$info)
			{	//echo $info;
				if (!$champname)
				{
					echo "404";
				}
				else
				{	
					return	$imageurl = "http://ddragon.leagueoflegends.com/cdn/6.24.1/img/".$info."/".$champname.".png";
				}
			}
	
        public function replacespace($string)
			{
				  $name = str_replace(' ', '', $string);
				  return $name;
			}
				  
		public function format_file($file)
			{
				$content = preg_replace('/<!--(.|\s)*?-->/', '', $file);
				return $content;
			}
				  



		public function format_champlist($content,$args)
			{				
				return $content;
			}						
		public function get_content($file,$url,$hours,$fn,$fn_args) {
			$current_time = time(); $expire_time = $hours * 60 * 60; $file_time = filemtime($file);
			if(file_exists($file) && ($current_time - $expire_time < $file_time)) {			
				return file_get_contents($file);
			}
			else {
					$content = $this->get_url($url);
					if($fn) { $content = $this->$fn($content,$fn_args); }
					$content.= '<!-- cached:  '.time().'-->';
					
					file_put_contents($file,$content);

					
					return $content;
			}
		}
		/* gets content from a URL via File get contents */
			public function get_url($url) {			
			$content = file_get_contents($url); 
			return $content;
		}
			
			

			
			
			
		}
$postdata = file_get_contents("php://input"); // Input Angular js data
$runtime = new riotClass(); 
$runtime ->riotConnectionID($postdata);


?>
