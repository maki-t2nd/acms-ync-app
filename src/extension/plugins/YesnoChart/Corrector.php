<?php

namespace Acms\Plugins\Yesnochart;

class Corrector
{
    /**
     * jencode
     * jsonエンコードします
     */
    public function jencode($txt, $args = array()) {
        return json_encode($txt);
        
    }

    /**
     * rawurlencode
     * rawurlエンコードします
     */
    public function rawurlencode($txt, $args = array()) {
        return rawurlencode($txt);
    }
}