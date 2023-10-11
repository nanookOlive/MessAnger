<?php

namespace App\model;


class User{

    protected $pseudo;


    public function __construct(string $pseudo){

        $this->pseudo = $pseudo;
    }

    public function getPseudo(){

        return $this->pseudo;
    }
}

