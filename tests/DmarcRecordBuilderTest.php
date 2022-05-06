<?php

use CbowOfRivia\DmarcRecordBuilder\DmarcRecordBuilder;

it('can', function () {
    $builder = new DmarcRecordBuilder();

    $builder->policy('none')
        ->subdomainPolicy('none')
        ->pct(100)
        ->rua('mailto:charlesrbowen93@gmail.com')
        ->ruf('mailto:charlesrbowen93@gmail.com')
        ->adkim('relaxed')
        ->aspf('relaxed')
        ->reporting('any')
        ->interval(3600);
});
