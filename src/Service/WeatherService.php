<?php

namespace App\Service;

#use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;


class WeatherService
{
    private $client;
    private $apiKey;

    private $urlIcon = "http://openweathermap.org/img/wn/";
    private $cityDefault = "Toulouse";

    public function __construct($apiKey)
    {
        $this->client = HttpClient::create();
        $this->apiKey = $apiKey;
    }

    function prepareParam($data) {
        $str_param = "q=".$this->cityDefault;
        if($data != null) {

            $isCheck = $data['position'];
            
            if($isCheck) {
                $str_param = 'lat='.$data['lat'].'&lon='.$data['lon'];
            }
            else {
                $city = $data['ville'];
                $str_param = 'q='.$city;
            }
        }

        return $str_param;
    }

    /**
     * @return array
     */
    public function getWeather($data = null)
    {
        $str_param = $this->prepareParam($data);
        
        try {

            $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/weather?'.$str_param.'&lang=fr&units=metric&appid=' . $this->apiKey);

            $statusCode = $response->getStatusCode($response->getContent());
            if($statusCode == Response::HTTP_OK) {
                $result = json_decode($response->getContent(), true);
                $this->checkHasIcon($result);

                return $result;
            }
            else {
                return array("status"=>"errors", "message"=>"Le status de la requête n'est pas 200");
            }

        } catch(TransportException  | ClientException $e) {
            return array("status"=>"errors", "message"=>$e->getMessage());
        }
    }

    /**
     * Récupere l'icône associé à la météo
     * l'enregistre si celui ci n'existe pas encore
     * @param content : json récupéré
     */
    public function checkHasIcon($content) {

        if(is_array($content) && array_key_exists('weather', $content)) {
            $icon = $content['weather'][0]['icon'];
            $this->uploadIcon($icon);
            
        }
        
        if(is_array($content) && array_key_exists("list", $content)) {
            foreach($content["list"] as $key => $value ) {
                if(is_array($value) && array_key_exists('weather', $value)) {
                    $icon = $value['weather'][0]['icon'];
                    $this->uploadIcon($icon);
                }
            }
        }
    }

    /**
     * enregistre l'îcone dans uploads pour eviter de appeler le service
     * @param icon : nom de l'icone
     */
    function uploadIcon($icon) {
        // si le fichier n'existe pas on le récupère
        if(!file_exists('uploads/'.$icon.'.png')) {
            //echo("le fichier n'existe pas");
            $files = file_get_contents($this->urlIcon.'/'.$icon.'.png');
            file_put_contents('../public/uploads/'.$icon.'.png',$files);
        }

    }

    /**
     * Retourne les prévisions sous plusieurs jours
     * @param city nom de la ville
     * @param cnt nombre de jours de prévision demandé 1 jours = 8 moments
     */
    function getPrevisionalWheather($data = null, $cnt=24) {
        $str_param = $this->prepareParam($data);

        try {

            $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/forecast?'.$str_param.'&cnt='.$cnt.'&lang=fr&units=metric&appid=' . $this->apiKey);

            $statusCode = $response->getStatusCode($response->getContent());
            if($statusCode == Response::HTTP_OK) {
                $result = json_decode($response->getContent(), true);
                $this->checkHasIcon($result);

                return $result;
            }
            else {
                return array("status"=>"errors", "message"=>"Le status de la requête n'est pas 200");
            }

        } catch(TransportException  | ClientException $e) {
            return array("status"=>"errors", "message"=>$e->getMessage());
        }
    }

    // function transformKelvinInDegres($content) {
    //     if(array_key_exists("main", ))
    //     273,15
    // }
}
