<?php namespace fhindsthejewellers\GBCustomsForm;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Address
 *
 * @author paul
 */
class Address {
    public $name;
    public $business;
    public $street;
    public $city;
    public $zip;
    public $country;
    
    public function __construct(array $address) {
        if (isset($address["name"])) {
            $this->name=$address["name"];
        }
        if (isset($address["business"])) {
            $this->business=$address["business"];
        }
        if (empty($this->name) and empty($this->business)) {
            throw new \Exception("No Name or Business in address");
        }
        
        if (isset($address["street"])) {
            $this->street=$address["street"];
        } else {
            throw new \Exception("No Street Set");
        }
                
        if (isset($address["city"])) {
            $this->city=$address["city"];
        } else {
            throw new \Exception("No City Set");
        }
        
        if (isset($address["zip"])) {
            $this->zip=$address["zip"];
        } else {
            throw new \Exception("No Zip Set");
        }
        
        if (isset($address["country"])) {
            $this->country=$address["country"];
        } else {
            throw new \Exception("No Country Set");
        }
    }
}
