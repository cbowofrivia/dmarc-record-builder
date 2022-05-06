<?php

use CbowOfRivia\DmarcRecordBuilder\DmarcRecord;
use Webmozart\Assert\InvalidArgumentException;

beforeEach(function () {
    $this->record = new DmarcRecord();
});

it('sets reasonable defaults', function () {
    expect($this->record)
        ->version->toEqual('DMARC1')
        ->policy->toEqual('none');
});

it('can set the version', function () {
    $this->record->version('DMARC1');

    expect($this->record)
        ->version->toEqual('DMARC1');
});

it('can set the policy', function () {
    $this->record->policy('reject');

    expect($this->record)
        ->policy->toEqual('reject');
});

it('rejects invalid policies', function () {
    $this->record->policy('invalid');
})->throws(InvalidArgumentException::class);

it('can nullify the policy', function () {
    $this->record->policy(null);

    expect($this->record)
        ->policy->toBeNull();
});

it('can set the subdomain policy', function () {
    $this->record->subdomainPolicy('reject');

    expect($this->record)
        ->subdomain_policy->toEqual('reject');
});

it('rejects invalid subdomain policies', function () {
    $this->record->subdomainPolicy('invalid');
})->throws(InvalidArgumentException::class);

it('can nullify the subdomain policy', function () {
    $this->record->subdomainPolicy(null);

    expect($this->record)
        ->subdomain_policy->toBeNull();
});

it('can set the sample rate', function () {
    $this->record->pct(25);

    expect($this->record)
        ->pct->toBe(25);
});

it('can nullify the sample rate', function () {
    $this->record->pct(null);

    expect($this->record)
        ->pct->toBeNull();
});

it('can set the rua', function () {
    $this->record->rua('mailto:test@test.com');

    expect($this->record)
        ->rua->toEqual('mailto:test@test.com');
});

it('can nullify the rua', function () {
    $this->record->rua(null);

    expect($this->record)
        ->rua->toBeNull();
});

it('rejects if the rua is malformed', function () {
    $this->record->rua('no-mailto@mailto.com');
})->throws(InvalidArgumentException::class);

it('can set the ruf', function () {
    $this->record->ruf('mailto:test@test.com');

    expect($this->record)
        ->ruf->toEqual('mailto:test@test.com');
});

it('can nullify the ruf', function () {
    $this->record->ruf(null);

    expect($this->record)
        ->ruf->toBeNull();
});

it('rejects if the ruf is malformed', function () {
    $this->record->ruf('test@test.com');
})->throws(InvalidArgumentException::class);

it('can set the adkim', function () {
    $this->record->adkim('relaxed');

    expect($this->record)
        ->adkim->toEqual('relaxed');
});

it('rejects invalid adkim records', function () {
    $this->record->adkim('naughty');
})->throws(InvalidArgumentException::class);

it('can nullify the adkim record', function () {
    $this->record->adkim(null);

    expect($this->record)
        ->adkim->toBeNull();
});

it('can set the aspf', function () {
    $this->record->aspf('relaxed');

    expect($this->record)
        ->aspf->toEqual('relaxed');
});

it('rejects invalid aspf', function () {
    $this->record->aspf('naughty');
})->throws(InvalidArgumentException::class);

it('can nullify the aspf', function () {
    $this->record->aspf(null);

    expect($this->record)
        ->aspf->toBeNull();
});

it('can set the reporting level', function () {
    $this->record->reporting('all');

    expect($this->record)
        ->reporting->toEqual('all');
});

it('rejects invalid reporting levels', function () {
    $this->record->reporting(5);
})->throws(InvalidArgumentException::class);

it('can nullify the reporting level', function () {
    $this->record->reporting(null);

    expect($this->record)
        ->reporting->toBeNull();
});

it('can set the reporting interval', function () {
    $this->record->interval(3600);

    expect($this->record)
        ->interval->toEqual(3600);
});

it('can nullify the reporting interval', function () {
    $this->record->interval(null);

    expect($this->record)
        ->interval->toBeNull();
});

it('provides a string output', function () {
    $record = new DmarcRecord();

    $record->policy('none')
        ->subdomainPolicy('none')
        ->pct(100)
        ->rua('mailto:charlesrbowen93@gmail.com')
        ->ruf('mailto:charlesrbowen93@gmail.com')
        ->adkim('relaxed')
        ->aspf('relaxed')
        ->reporting('any')
        ->interval(3600);

    expect((string)$record)
        ->toBeString()
        ->toContain('v=DMARC1;')
        ->toContain('p=none;')
        ->toContain('sp=none;')
        ->toContain('rua=mailto:charlesrbowen93@gmail.com;')
        ->toContain('ruf=mailto:charlesrbowen93@gmail.com;')
        ->toContain('adkim=r')
        ->toContain('aspf=r')
        ->toContain('ro=1')
        ->toContain('ri=3600;');
});

it('includes spaces between records', function () {
    $record = new DmarcRecord();

    expect((string)$record)
        ->toBeString()
        ->toEqual('v=DMARC1; p=none;');
});
