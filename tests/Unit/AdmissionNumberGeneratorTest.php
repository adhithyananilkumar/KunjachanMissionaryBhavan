<?php

use App\Services\AdmissionNumberGenerator;
use Illuminate\Support\Facades\DB;

it('generates unique admission numbers', function(){
    $generated = [];
    for($i=0; $i<1000; $i++){
        $n = AdmissionNumberGenerator::generate(function($c) use (&$generated){ return in_array($c, $generated, true); });
        expect($n)->toStartWith('ADM'.now()->format('Y')); 
        expect(strlen($n))->toBeGreaterThan(10);
        $generated[] = $n;
    }
    expect(count($generated))->toBe(1000);
    expect(count(array_unique($generated)))->toBe(1000);
});
