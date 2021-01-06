<?php 

require("vendor/autoload.php");

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Zing {

    private $baseURL = "https://zingmp3.vn";
    private $limitSong = 10;
    private $version =  "1.0.16";
    private $apiKey = "kI44ARvPwaqL7v0KuDSM0rGORtdY1nnw";
    private $secretKey = "882QcNXV4tUZbvAsjmFOHqNC1LpcBRKW";
    private $cookie = null;

    // "882QcNXV4tUZbvAsjmFOHqNC1LpcBRKW"

    private $endPoint = [
        "search" => "/api/v2/search/multi",
        // "song" => "/api/song/get-song-info"
        "song" => "/api/v2/song/getStreaming"


    ];

    public function hash256($str){
        return hash('sha256', $str);
    }

    public function hash512($str, $key){
        return hash_hmac("sha512", $str, $key);
    }

    public function getSig($url, $str, $key) {
        return $this->hash512($url.$this->hash256($str), $key);
    }

    public function searchSong($songName, $page = 1) {
        $client = new Client(['verify' => false]);

        $cookieJar = CookieJar::fromArray($this->getCookies(), 'zingmp3.vn');

        $listSong = [];

        $ctime = strtotime('now');

        $str = "ctime={$ctime}page={$page}type=songversion={$this->version}";
        
        $sig = $this->getSig($this->endPoint['search'], $str, $this->secretKey);

        $urlParams = http_build_query(
            [
                "q" => $songName,
                "type" => "song",
                "page" => $page,
                "ctime" => $ctime,
                "version" => $this->version,
                "sig" => $sig,
                "apiKey" => $this->apiKey
            ]
        );

        $url = $this->baseURL.$this->endPoint['search']."?".$urlParams;
        
        $res = $client->request("GET", $url, [
            "headers" => [
                "Host" => "zingmp3.vn"                
            ],
            'cookies' => $cookieJar
        ]);


        $arrData = json_decode($res->getBody()->getContents());

        $listSong = [];

        $track = 1;

        foreach ($arrData->data->songs as $value) {
            if (is_array($value) || is_object($value)) {
                $song = $this->songDetail($value->encodeId);
                array_push($listSong, 
                    [
                        "id" => $value->encodeId,
                        "title" => $value->title,
                        "singer" => $value->artistsNames,
                        "link" => $song,
                        "file" => $song,
                        "name" => $value->title.' - '. $value->artistsNames,
                        "track" => $track
                    ]
                );
            }
            $track++;
        }


        if(!empty($arrData->data->topSuggest)) {
            foreach ($arrData->data->topSuggest as $value) {
                if (is_array($value) || is_object($value)) {
                    $song = $this->songDetail($value->encodeId);
                    array_push($listSong, 
                        [
                            "id" => $value->encodeId,
                            "title" => $value->title,
                            "singer" => '',
                            "link" => $song,
                            "file" => $song,
                            "name" => $value->title,
                            "track" => $track
                        ]
                    );
                }
            }
            $track++;
        }

        return $listSong;
    }

    public function getCookies() {
        $client = new Client(['verify' => false, 'cookies' => true]);         

        $res = $client->request("GET", "https://zingmp3.vn/",[]);
        $arrData = $client->getConfig('cookies')->toArray();
        $cookies = [];
        foreach ($arrData as $key => $value) {
            $cookies[$value['Name']] = $value["Value"];
        }
        return $cookies;
    }

    public function songDetail($songID) {

        $client = new Client(['verify' => false]);

        $cookieJar = CookieJar::fromArray($this->getCookies(), 'zingmp3.vn');

        $ctime = strtotime('now');
        // $ctime = 1609841751;

        $str = "ctime={$ctime}id={$songID}version={$this->version}";

        $sig = $this->getSig($this->endPoint['song'], $str, $this->secretKey);

        $urlParams = http_build_query(
            [
                "id" => $songID,
                "ctime" => $ctime,
                "version" => $this->version,
                "sig" => $sig,
                "apiKey" => $this->apiKey
            ]
        );

       $url = $this->baseURL.$this->endPoint['song']."?".$urlParams;

        $res = $client->request("GET", $url, [
            "headers" => [
                "Host" => "zingmp3.vn",
            ],
            'cookies' => $cookieJar
        ]);

        $arrData = json_decode($res->getBody()->getContents());

        return (!empty($arrData->data) && !empty($arrData->data->{'128'})) ? $arrData->data->{'128'} : '';
    }
}



// const crypto = require('crypto');


// const getHash256 = (a) => {
//     return crypto.createHash('sha256').update(a).digest('hex');
// }
// const getHmac512 = (str, key) => {
//     let hmac = crypto.createHmac("sha512", key);
//     return hmac.update(Buffer.from(str, 'utf8')).digest("hex");
// }

// var str = "ctime=160718421id=ZOW0OBU8";
// var hash256 = getHash256(str);

// console.log(getHmac512("/song/get-song-info"+hash256, "10a01dcf33762d3a204cb96429918ff6"));