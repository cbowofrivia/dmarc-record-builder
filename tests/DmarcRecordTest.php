<?php

use CbowOfRivia\DmarcRecordBuilder\DmarcRecord;
use Webmozart\Assert\InvalidArgumentException;

beforeEach(function () {
    $this->record = new DmarcRecord;
});

it('sets defaults', function () {
    expect($this->record)
        ->version->toEqual('DMARC1')
        ->policy->toEqual('none');
});

it('provides a string output', function () {
    $record = new DmarcRecord;

    $record->policy('none')
        ->subdomainPolicy('none')
        ->pct(100)
        ->rua('mailto:charlesrbowen93@gmail.com')
        ->ruf('mailto:charlesrbowen93@gmail.com')
        ->adkim('relaxed')
        ->aspf('relaxed')
        ->reporting('any')
        ->interval(3600);

    expect((string) $record)
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
    $record = new DmarcRecord;

    expect((string) $record)
        ->toBeString()
        ->toEqual('v=DMARC1; p=none;');
});

it('parses a record', function () {
    $record = 'v=DMARC1; p=none; sp=none; pct=100; rua=mailto:example@example.com; ruf=mailto:example@example.com; adkim=r; aspf=r; ri=3600;';
    $instance = DmarcRecord::parse($record);

    expect($instance)
        ->toBeInstanceOf(DmarcRecord::class)
        ->version->toEqual('DMARC1')
        ->policy->toEqual('none')
        ->subdomain_policy->toEqual('none')
        ->pct->toEqual(100)
        ->rua->toEqual('mailto:example@example.com')
        ->ruf->toEqual('mailto:example@example.com')
        ->adkim->toEqual('relaxed')
        ->aspf->toEqual('relaxed')
        ->interval->toEqual(3600)
        ->and((string) $instance)
        ->toEqual($record);
});

it('can create a record statically', function () {
    $record = DmarcRecord::create(
        version: 'none',
        policy: 'reject',
        subdomain_policy: 'reject',
        pct: 100,
        rua: 'mailto:example@example.com',
        ruf: 'mailto:example@example.com',
        adkim: 'relaxed',
        aspf: 'relaxed',
        reporting: 'any',
        interval: 3600,
    );

    expect($record)
        ->toBeInstanceOf(DmarcRecord::class)
        ->version->toEqual('none')
        ->policy->toEqual('reject')
        ->subdomain_policy->toEqual('reject')
        ->pct->toEqual(100)
        ->rua->toEqual('mailto:example@example.com')
        ->ruf->toEqual('mailto:example@example.com')
        ->adkim->toEqual('relaxed')
        ->aspf->toEqual('relaxed')
        ->interval->toEqual(3600)
        ->reporting->toEqual('any');
});

it('rejects invalid policies', function () {
    DmarcRecord::create(
        policy: 'invalid'
    );
})->throws(InvalidArgumentException::class);

it('rejects invalid subdomain policies', function () {
    DmarcRecord::create(
        subdomain_policy: 'invalid'
    );
})->throws(InvalidArgumentException::class);

it('rejects if the rua is malformed', function () {
    DmarcRecord::create(
        rua: 'no-mailto@mailto.com'
    );
})->throws(InvalidArgumentException::class);

it('rejects if the ruf is malformed', function () {
    DmarcRecord::create(
        ruf: 'test@test.com'
    );
})->throws(InvalidArgumentException::class);

it('rejects invalid adkim records', function () {
    DmarcRecord::create(adkim: 'naughty'
    );
})->throws(InvalidArgumentException::class);

it('rejects invalid aspf', function () {
    DmarcRecord::create(
        aspf: 'naughty'
    );
})->throws(InvalidArgumentException::class);

it('rejects invalid reporting levels', function () {
    DmarcRecord::create(
        reporting: 5
    );
})->throws(InvalidArgumentException::class);

it('fails to parse malformed records', function (string $record, string $exception) {
    expect(fn () => DmarcRecord::parse($record))
        ->toThrow(InvalidArgumentException::class, $exception);
})->with([
    ['p=none;', 'DMARC version is required'],
    ['v=DMARC1;', 'DMARC policy is required'],
]);
