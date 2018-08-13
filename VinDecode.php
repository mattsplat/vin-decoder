<?php
class VinDecode
{
    public $base_url = 'https://vpic.nhtsa.dot.gov/api/';

    public $vin;
    public $results;
    public $make;
    public $model;
    public $year;


    public function __construct()
    {

    }

    public function setVIN(string $vin)
    {
        if(strlen($vin) != 17) return false;

        $this->vin = $vin;
        return true;

    }

    public function searchVIN($format = 'json')
    {
        $url = $this->base_url . 'vehicles/DecodeVin/'. $this->vin . '?format=' . $format; 
        $results = file_get_contents($url);
   

        $results = json_decode($results, true)['Results'];
        $temp_results=[];
        foreach($results as $result){
            if(!empty($result['Value'])){
                $key = $this->toSnakeCase( $result['Variable'] );
                $temp_results += [$key => $result['Value'] ];
            }
        }

        $this->setResults($temp_results);

        return $this->results = $temp_results;
    }

    public function toSnakeCase($string)
    {

        $string = strtolower($string);
        return str_replace(' ', '_', $string);

    }

    protected function setResults($results)
    {        
        $this->make = $results['make'];
        $this->model = $results['model'];
        $this->year = $results['model_year'];
    }

}