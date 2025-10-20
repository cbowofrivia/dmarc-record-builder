<?php

use CbowOfRivia\DmarcRecordBuilder\DmarcRecord;
use Webmozart\Assert\InvalidArgumentException;

beforeEach(function () {
    $this->record = new DmarcRecord;
});

describe('constructor', function () {
    it('sets default values', function () {
        expect($this->record)
            ->version->toEqual('DMARC1')
            ->policy->toEqual('none');
    });

    it('constructs with all parameters', function () {
        $record = new DmarcRecord(
            version: 'DMARC1',
            policy: 'quarantine',
            subdomain_policy: 'reject',
            pct: 50,
            rua: 'mailto:test@example.com',
            ruf: 'mailto:test@example.com',
            adkim: 'strict',
            aspf: 'relaxed',
            reporting: 'dkim',
            interval: 7200
        );

        expect($record)
            ->version->toEqual('DMARC1')
            ->policy->toEqual('quarantine')
            ->subdomain_policy->toEqual('reject')
            ->pct->toEqual(50)
            ->rua->toEqual('mailto:test@example.com')
            ->ruf->toEqual('mailto:test@example.com')
            ->adkim->toEqual('strict')
            ->aspf->toEqual('relaxed')
            ->reporting->toEqual('dkim')
            ->interval->toEqual(7200);
    });
});

describe('fluent methods', function () {
    it('supports method chaining and returns self', function () {
        $record = new DmarcRecord;

        $result = $record
            ->version('DMARC1')
            ->policy('quarantine')
            ->subdomainPolicy('reject')
            ->pct(75)
            ->rua('mailto:test@example.com')
            ->ruf('mailto:test@example.com')
            ->adkim('strict')
            ->aspf('relaxed')
            ->reporting('spf')
            ->interval(1800);

        expect($result)->toBe($record);
        expect($record)
            ->version->toEqual('DMARC1')
            ->policy->toEqual('quarantine')
            ->subdomain_policy->toEqual('reject')
            ->pct->toEqual(75)
            ->rua->toEqual('mailto:test@example.com')
            ->ruf->toEqual('mailto:test@example.com')
            ->adkim->toEqual('strict')
            ->aspf->toEqual('relaxed')
            ->reporting->toEqual('spf')
            ->interval->toEqual(1800);
    });

    it('handles null values in all methods', function (string $method, mixed $value, string $property) {
        $record = new DmarcRecord;

        // Test setting to null
        $record->$method(null);
        expect($record->$property)->toBeNull();

        // Test setting to value
        $record->$method($value);
        expect($record->$property)->toEqual($value);

        // Test setting back to null
        $record->$method(null);
        expect($record->$property)->toBeNull();
    })->with([
        ['version', 'DMARC1', 'version'],
        ['policy', 'quarantine', 'policy'],
        ['subdomainPolicy', 'reject', 'subdomain_policy'],
        ['pct', 50, 'pct'],
        ['rua', 'mailto:test@example.com', 'rua'],
        ['ruf', 'mailto:test@example.com', 'ruf'],
        ['adkim', 'relaxed', 'adkim'],
        ['aspf', 'strict', 'aspf'],
        ['reporting', 'all', 'reporting'],
        ['interval', 3600, 'interval'],
    ]);
});

describe('validation', function () {
    it('rejects invalid policy values', function (string $invalidPolicy) {
        expect(fn () => DmarcRecord::create(policy: $invalidPolicy))
            ->toThrow(InvalidArgumentException::class);
    })->with(['invalid', 'bad', 'wrong']);

    it('rejects invalid subdomain policy values', function (string $invalidPolicy) {
        expect(fn () => DmarcRecord::create(subdomain_policy: $invalidPolicy))
            ->toThrow(InvalidArgumentException::class);
    })->with(['invalid', 'bad', 'wrong']);

    it('rejects malformed rua addresses', function (string $invalidRua) {
        expect(fn () => DmarcRecord::create(rua: $invalidRua))
            ->toThrow(InvalidArgumentException::class, 'rua mailto address should start with "mailto:"');
    })->with([
        'no-mailto@mailto.com',
        'test@test.com',
        'invalid-format',
    ]);

    it('rejects malformed ruf addresses', function (string $invalidRuf) {
        expect(fn () => DmarcRecord::create(ruf: $invalidRuf))
            ->toThrow(InvalidArgumentException::class, 'ruf mailto address should start with "mailto:"');
    })->with([
        'test@test.com',
        'invalid-format',
        'no-mailto@example.com',
    ]);

    it('rejects invalid adkim values', function (string $invalidAdkim) {
        expect(fn () => DmarcRecord::create(adkim: $invalidAdkim))
            ->toThrow(InvalidArgumentException::class);
    })->with(['naughty', 'invalid', 'bad']);

    it('rejects invalid aspf values', function (string $invalidAspf) {
        expect(fn () => DmarcRecord::create(aspf: $invalidAspf))
            ->toThrow(InvalidArgumentException::class);
    })->with(['naughty', 'invalid', 'bad']);

    it('rejects invalid reporting values', function (mixed $invalidReporting) {
        expect(fn () => DmarcRecord::create(reporting: $invalidReporting))
            ->toThrow(InvalidArgumentException::class);
    })->with([5, 'invalid', 'bad', 'wrong']);
});

describe('string output', function () {
    it('generates basic record with version and policy only', function () {
        $record = new DmarcRecord('DMARC1', 'quarantine');
        expect((string) $record)->toEqual('v=DMARC1; p=quarantine;');
    });

    it('generates complete record with all fields', function () {
        $record = new DmarcRecord(
            version: 'DMARC1',
            policy: 'reject',
            subdomain_policy: 'quarantine',
            pct: 75,
            rua: 'mailto:test@example.com',
            ruf: 'mailto:test@example.com',
            adkim: 'strict',
            aspf: 'relaxed',
            reporting: 'all',
            interval: 3600
        );

        $expected = 'v=DMARC1; p=reject; sp=quarantine; pct=75; rua=mailto:test@example.com; ruf=mailto:test@example.com; adkim=s; aspf=r; ro=0; ri=3600;';
        expect((string) $record)->toEqual($expected);
    });

    it('excludes null values from output', function () {
        $record = new DmarcRecord;
        $record->version('DMARC1')
            ->policy('none')
            ->subdomainPolicy(null)
            ->pct(null)
            ->rua(null)
            ->ruf(null)
            ->adkim(null)
            ->aspf(null)
            ->reporting(null)
            ->interval(null);

        expect((string) $record)->toEqual('v=DMARC1; p=none;');
    });

    it('generates partial record with selected fields', function () {
        $record = new DmarcRecord;
        $record->version('DMARC1')
            ->policy('quarantine')
            ->pct(50)
            ->rua('mailto:test@example.com')
            ->adkim('relaxed');

        $output = (string) $record;
        expect($output)
            ->toContain('v=DMARC1;')
            ->toContain('p=quarantine;')
            ->toContain('pct=50;')
            ->toContain('rua=mailto:test@example.com;')
            ->toContain('adkim=r')
            ->not->toContain('sp=')
            ->not->toContain('ruf=')
            ->not->toContain('aspf=')
            ->not->toContain('ro=')
            ->not->toContain('ri=');
    });

    it('trims trailing spaces and ends with semicolon', function () {
        $record = new DmarcRecord('DMARC1', 'none');
        $output = (string) $record;

        expect($output)->not->toEndWith(' ');
        expect($output)->not->toEndWith('; ');
        expect($output)->toEndWith(';');
    });

    it('converts values correctly for string output', function (string $method, string $humanValue, string $expectedOutput, string $outputField) {
        $record = new DmarcRecord;
        $record->$method($humanValue);
        expect((string) $record)->toContain("$outputField=$expectedOutput");
    })->with([
        ['adkim', 'relaxed', 'r', 'adkim'],
        ['adkim', 'strict', 's', 'adkim'],
        ['aspf', 'relaxed', 'r', 'aspf'],
        ['aspf', 'strict', 's', 'aspf'],
        ['reporting', 'all', '0', 'ro'],
        ['reporting', 'any', '1', 'ro'],
        ['reporting', 'dkim', 'd', 'ro'],
        ['reporting', 'spf', 's', 'ro'],
    ]);
});

describe('parsing', function () {
    it('parses complete record correctly', function () {
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

    it('parses minimal valid record', function () {
        $record = 'v=DMARC1; p=none;';
        $instance = DmarcRecord::parse($record);

        expect($instance)
            ->version->toEqual('DMARC1')
            ->policy->toEqual('none')
            ->subdomain_policy->toBeNull()
            ->pct->toBeNull()
            ->rua->toBeNull()
            ->ruf->toBeNull()
            ->adkim->toBeNull()
            ->aspf->toBeNull()
            ->reporting->toBeNull()
            ->interval->toBeNull();
    });

    it('handles various parsing edge cases', function (string $record, array $expectedValues) {
        $instance = DmarcRecord::parse($record);

        foreach ($expectedValues as $property => $expectedValue) {
            if ($expectedValue === null) {
                expect($instance->$property)->toBeNull();
            } else {
                expect($instance->$property)->toEqual($expectedValue);
            }
        }
    })->with([
        [
            '  v=DMARC1;  p=quarantine;  sp=reject;  pct=50;  ',
            ['version' => 'DMARC1', 'policy' => 'quarantine', 'subdomain_policy' => 'reject', 'pct' => 50],
        ],
        [
            'v=DMARC1; p=quarantine; ; pct=50;',
            ['version' => 'DMARC1', 'policy' => 'quarantine', 'pct' => 50],
        ],
        [
            'v=DMARC1; p=quarantine; invalid-part; pct=50;',
            ['version' => 'DMARC1', 'policy' => 'quarantine', 'pct' => 50],
        ],
        [
            'v=DMARC1; p=quarantine; adkim=r; aspf=s; ro=d;',
            ['version' => 'DMARC1', 'policy' => 'quarantine', 'adkim' => 'relaxed', 'aspf' => 'strict', 'reporting' => 'dkim'],
        ],
    ]);

    it('converts short form values to human readable during parsing', function (string $dmarcTag, string $shortForm, string $property, string $expected) {
        $record = "v=DMARC1; p=none; $dmarcTag=$shortForm;";
        $instance = DmarcRecord::parse($record);
        expect($instance->$property)->toEqual($expected);
    })->with([
        ['adkim', 'r', 'adkim', 'relaxed'],
        ['adkim', 's', 'adkim', 'strict'],
        ['aspf', 'r', 'aspf', 'relaxed'],
        ['aspf', 's', 'aspf', 'strict'],
        ['ro', '0', 'reporting', 'all'],
        ['ro', '1', 'reporting', 'any'],
        ['ro', 'd', 'reporting', 'dkim'],
        ['ro', 's', 'reporting', 'spf'],
    ]);

    it('fails with missing required fields', function (string $record, string $expectedException) {
        expect(fn () => DmarcRecord::parse($record))
            ->toThrow(InvalidArgumentException::class, $expectedException);
    })->with([
        ['p=none;', 'DMARC version is required'],
        ['v=DMARC1;', 'DMARC policy is required'],
        ['', 'DMARC version is required'],
        ['invalid-record', 'DMARC version is required'],
    ]);

    it('fails with invalid field values', function (string $record, string $expectedException) {
        expect(fn () => DmarcRecord::parse($record))
            ->toThrow(InvalidArgumentException::class, $expectedException);
    })->with([
        ['v=DMARC1; p=invalid;', 'Expected one of: "none", "quarantine", "reject", null. Got: "invalid"'],
        ['v=DMARC1; p=none; sp=invalid;', 'Expected one of: "none", "quarantine", "reject", null. Got: "invalid"'],
        ['v=DMARC1; p=none; rua=invalid@example.com;', 'rua mailto address should start with "mailto:"'],
        ['v=DMARC1; p=none; ruf=invalid@example.com;', 'ruf mailto address should start with "mailto:"'],
    ]);

    it('fails with unhandled match cases', function (string $record, string $expectedException) {
        expect(fn () => DmarcRecord::parse($record))
            ->toThrow(UnhandledMatchError::class, $expectedException);
    })->with([
        ['v=DMARC1; p=none; adkim=invalid;', 'Unhandled match case'],
        ['v=DMARC1; p=none; aspf=invalid;', 'Unhandled match case'],
        ['v=DMARC1; p=none; ro=invalid;', 'Unhandled match case'],
    ]);
});

describe('static factory methods', function () {
    it('creates record using create method', function () {
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
});
