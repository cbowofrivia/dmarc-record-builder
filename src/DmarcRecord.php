<?php

declare(strict_types=1);

namespace CbowOfRivia\DmarcRecordBuilder;

use CbowOfRivia\DmarcRecordBuilder\Exceptions\InvalidDmarcRecordException;

/**
 * Responsible for building an object representation of a DMARC
 * compliant string value
 *
 * @link https://mxtoolbox.com/dmarc/details/dmarc-tags
 */
class DmarcRecord
{
    public ?string $version = null;

    public ?string $policy = null;

    public ?string $subdomain_policy = null;

    public ?string $rua = null;

    public ?string $ruf = null;

    public ?string $adkim = null;

    public ?string $aspf = null;

    public array $reporting = [];

    public ?string $np = null;

    public ?string $psd = null;

    public ?string $t = null;

    public function __construct(
        string $version = 'DMARC1',
        ?string $policy = 'none',
        ?string $subdomain_policy = null,
        string|array|null $rua = null,
        string|array|null $ruf = null,
        ?string $adkim = null,
        ?string $aspf = null,
        string|array $reporting = [],
        ?string $np = null,
        ?string $psd = null,
        ?string $t = null
    ) {
        $this->version($version);
        $this->policy($policy);
        $this->subdomainPolicy($subdomain_policy);
        $this->rua($rua);
        $this->ruf($ruf);
        $this->adkim($adkim);
        $this->aspf($aspf);
        $this->reporting($reporting);
        $this->nonExistentSubdomainPolicy($np);
        $this->publicSuffixDomainPolicy($psd);
        $this->testingMode($t);
    }

    /**
     * @link https://mxtoolbox.com/dmarc/details/dmarc-tags/dmarc-version
     */
    public function version(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function policy(?string $policy): static
    {
        InvalidDmarcRecordException::inArray($policy, [
            'none', 'quarantine', 'reject', null,
        ]);

        $this->policy = $policy;

        return $this;
    }

    public function subdomainPolicy(?string $policy): static
    {
        InvalidDmarcRecordException::inArray($policy, [
            'none', 'quarantine', 'reject', null,
        ]);

        $this->subdomain_policy = $policy;

        return $this;
    }

    public function rua(string|array|null $mailto): static
    {
        $this->rua = is_null($mailto) ? null : $this->normalizeReportUris($mailto, 'rua');

        return $this;
    }

    public function ruf(string|array|null $mailto): static
    {
        $this->ruf = is_null($mailto) ? null : $this->normalizeReportUris($mailto, 'ruf');

        return $this;
    }

    protected function normalizeReportUris(string|array $value, string $tag): ?string
    {
        $items = is_array($value) ? $value : explode(',', $value);

        $items = array_values(array_filter(
            array_map('trim', $items),
            fn (string $item): bool => $item !== ''
        ));

        foreach ($items as $item) {
            InvalidDmarcRecordException::startsWith(
                value: $item,
                prefix: 'mailto:',
                message: sprintf('%s mailto address should start with "mailto:"', $tag)
            );
        }

        return $items === [] ? null : implode(',', $items);
    }

    public function adkim(?string $value): static
    {
        InvalidDmarcRecordException::inArray($value, [
            'relaxed', 'strict', null,
        ]);

        $this->adkim = $value;

        return $this;
    }

    public function aspf(?string $value): static
    {
        InvalidDmarcRecordException::inArray($value, [
            'relaxed', 'strict', null,
        ]);

        $this->aspf = $value;

        return $this;
    }

    public function reporting(string|array $values = []): static
    {
        if (is_string($values)) {
            $values = [$values];
        }

        $values = array_values(array_unique($values));

        InvalidDmarcRecordException::allInArray($values, ['all', 'any', 'dkim', 'spf']);

        InvalidDmarcRecordException::throwIf(
            condition: in_array('all', $values) && in_array('any', $values),
            message: 'Reporting options "all" (0) and "any" (1) are mutually exclusive.'
        );

        $this->reporting = $values;

        return $this;
    }

    public function nonExistentSubdomainPolicy(?string $policy): static
    {
        InvalidDmarcRecordException::inArray($policy, [
            'none', 'quarantine', 'reject', null,
        ]);

        $this->np = $policy;

        return $this;
    }

    public function publicSuffixDomainPolicy(?string $policy): static
    {
        InvalidDmarcRecordException::inArray($policy, [
            'y', 'n', 'u', null,
        ]);

        $this->psd = $policy;

        return $this;
    }

    public function testingMode(?string $testingMode): static
    {
        InvalidDmarcRecordException::inArray($testingMode, [
            'y', 'n', null,
        ]);

        $this->t = $testingMode;

        return $this;
    }

    public static function create(
        string $version = 'DMARC1',
        ?string $policy = 'none',
        ?string $subdomain_policy = null,
        string|array|null $rua = null,
        string|array|null $ruf = null,
        ?string $adkim = null,
        ?string $aspf = null,
        string|array $reporting = [],
        ?string $np = null,
        ?string $psd = null,
        ?string $t = null
    ): static {
        return new static(
            version: $version,
            policy: $policy,
            subdomain_policy: $subdomain_policy,
            rua: $rua,
            ruf: $ruf,
            adkim: $adkim,
            aspf: $aspf,
            reporting: $reporting,
            np: $np,
            psd: $psd,
            t: $t
        );
    }

    public static function parse(string $record): static
    {
        $builder = new static;

        $properties = [];

        foreach (explode(';', $record) as $part) {
            $property = explode('=', trim($part));

            if (count($property) !== 2) {
                continue;
            }

            $properties[$property[0]] = $property[1];
        }

        InvalidDmarcRecordException::keyExists($properties, 'v', 'DMARC version is required');
        InvalidDmarcRecordException::keyExists($properties, 'p', 'DMARC policy is required');

        foreach ($properties as $key => $value) {
            match ($key) {
                'v' => $builder->version($value),
                'p' => $builder->policy($value),
                'sp' => $builder->subdomainPolicy($value),
                'rua' => $builder->rua($value),
                'ruf' => $builder->ruf($value),
                'adkim' => $builder->adkim($builder->getHumanAdkimValue($value)),
                'aspf' => $builder->aspf($builder->getHumanAspfValue($value)),
                'fo' => $builder->reporting(array_map(
                    fn (string $v) => $builder->getHumanReportingOption(trim($v)),
                    explode(':', $value)
                )),
                'np' => $builder->nonExistentSubdomainPolicy($value),
                'psd' => $builder->publicSuffixDomainPolicy($value),
                't' => $builder->testingMode($value),
                default => null,
            };
        }

        return $builder;
    }

    protected function getHumanAdkimValue(string $value): string
    {
        return match ($value) {
            'r' => 'relaxed',
            's' => 'strict',
        };
    }

    protected function getHumanAspfValue(string $value): string
    {
        return match ($value) {
            'r' => 'relaxed',
            's' => 'strict',
        };
    }

    protected function getHumanReportingOption(string $value): string
    {
        return match ($value) {
            '0' => 'all',
            '1' => 'any',
            'd' => 'dkim',
            's' => 'spf',
        };
    }

    public function __toString(): string
    {
        $record = $this->version ? "v=$this->version; " : '';
        $record .= $this->policy ? "p=$this->policy; " : '';
        $record .= $this->subdomain_policy ? "sp=$this->subdomain_policy; " : '';
        $record .= $this->rua ? "rua=$this->rua; " : '';
        $record .= $this->ruf ? "ruf=$this->ruf; " : '';
        $record .= $this->adkim ? "adkim={$this->getRealAdkimValue($this->adkim)}; " : '';
        $record .= $this->aspf ? "aspf={$this->getRealAspfValue($this->aspf)}; " : '';
        $record .= $this->reporting ? 'fo='.implode(':', array_map(fn (string $v) => $this->getRealReportingOption($v), $this->reporting)).'; ' : '';
        $record .= $this->np ? "np=$this->np; " : '';
        $record .= $this->psd ? "psd=$this->psd; " : '';
        $record .= $this->t ? "t=$this->t; " : '';

        return trim($record);
    }

    protected function getRealAdkimValue(string $value): string
    {
        return match ($value) {
            'relaxed' => 'r',
            'strict' => 's',
        };
    }

    protected function getRealAspfValue(string $value): string
    {
        return match ($value) {
            'relaxed' => 'r',
            'strict' => 's',
        };
    }

    protected function getRealReportingOption(string $value): string
    {
        return match ($value) {
            'all' => '0',
            'any' => '1',
            'dkim' => 'd',
            'spf' => 's',
        };
    }
}
