<?php

declare(strict_types=1);

namespace CbowOfRivia\DmarcRecordBuilder;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

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

    public ?int $pct = null;

    public ?string $rua = null;

    public ?string $ruf = null;

    public ?string $adkim = null;

    public ?string $aspf = null;

    public ?string $reporting = null;

    public ?int $interval = null;

    public ?string $np = null;

    public ?string $psd = null;

    public ?string $t = null;

    public function __construct(
        string $version = 'DMARC1',
        ?string $policy = 'none',
        ?string $subdomain_policy = null,
        ?int $pct = null,
        ?string $rua = null,
        ?string $ruf = null,
        ?string $adkim = null,
        ?string $aspf = null,
        ?string $reporting = null,
        ?int $interval = null,
        ?string $np = null,
        ?string $psd = null,
        ?string $t = null
    ) {
        $this->version($version);
        $this->policy($policy);
        $this->subdomainPolicy($subdomain_policy);
        $this->pct($pct);
        $this->rua($rua);
        $this->ruf($ruf);
        $this->adkim($adkim);
        $this->aspf($aspf);
        $this->reporting($reporting);
        $this->interval($interval);
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
        Assert::inArray($policy, [
            'none', 'quarantine', 'reject', null,
        ]);

        $this->policy = $policy;

        return $this;
    }

    public function subdomainPolicy(?string $policy): static
    {
        Assert::inArray($policy, [
            'none', 'quarantine', 'reject', null,
        ]);

        $this->subdomain_policy = $policy;

        return $this;
    }

    public function nonExistentSubdomainPolicy(?string $policy): static
    {
        Assert::inArray($policy, [
            'none', 'quarantine', 'reject', null,
        ]);

        $this->np = $policy;

        return $this;
    }

    public function publicSuffixDomainPolicy(?string $policy): static
    {
        Assert::inArray($policy, [
            'y', 'n', 'u', null,
        ]);

        $this->psd = $policy;

        return $this;
    }

    public function testingMode(?string $testingMode): static
    {
        Assert::inArray($testingMode, [
            'y', 'n', null,
        ]);

        $this->t = $testingMode;

        return $this;
    }

    public function pct(?int $percentage): static
    {
        $this->pct = $percentage;

        return $this;
    }

    public function rua(?string $mailto): static
    {
        if (is_null($mailto)) {
            $this->rua = $mailto;

            return $this;
        }

        Assert::startsWith(
            value: $mailto,
            prefix: 'mailto:',
            message: 'rua mailto address should start with "mailto:"'
        );

        $this->rua = $mailto;

        return $this;
    }

    public function ruf(?string $mailto): static
    {
        if (is_null($mailto)) {
            $this->ruf = $mailto;

            return $this;
        }

        Assert::startsWith(
            value: $mailto,
            prefix: 'mailto:',
            message: 'ruf mailto address should start with "mailto:"'
        );

        $this->ruf = $mailto;

        return $this;
    }

    public function adkim(?string $value): static
    {
        Assert::inArray($value, [
            'relaxed', 'strict', null,
        ]);

        $this->adkim = $value;

        return $this;
    }

    public function aspf(?string $value): static
    {
        Assert::inArray($value, [
            'relaxed', 'strict', null,
        ]);

        $this->aspf = $value;

        return $this;
    }

    public function reporting(?string $value): static
    {
        Assert::inArray($value, [
            'all', 'any', 'dkim', 'spf', null,
        ]);

        $this->reporting = $value;

        return $this;
    }

    public function interval(?int $interval): static
    {
        $this->interval = $interval;

        return $this;
    }

    public static function create(
        string $version = 'DMARC1',
        ?string $policy = 'none',
        ?string $subdomain_policy = null,
        ?int $pct = null,
        ?string $rua = null,
        ?string $ruf = null,
        ?string $adkim = null,
        ?string $aspf = null,
        ?string $reporting = null,
        ?int $interval = null,
        ?string $np = null,
        ?string $psd = null,
        ?string $t = null
    ): static {
        return new static(
            version: $version,
            policy: $policy,
            subdomain_policy: $subdomain_policy,
            pct: $pct,
            rua: $rua,
            ruf: $ruf,
            adkim: $adkim,
            aspf: $aspf,
            reporting: $reporting,
            interval: $interval,
            np: $np,
            psd: $psd,
            t: $t
        );
    }

    public static function parse(string $record): static
    {
        $builder = new static;

        collect(explode(';', $record))
            ->mapWithKeys(function (string $part) {
                $property = explode('=', trim($part));

                if (count($property) !== 2) {
                    return [];
                }

                return [$property[0] => $property[1]];
            })
            ->tap(function (Collection $properties) {
                Assert::keyExists($properties->toArray(), 'v', 'DMARC version is required');
                Assert::keyExists($properties->toArray(), 'p', 'DMARC policy is required');
            })
            ->each(fn (string $value, $key) => match ($key) {
                'v' => $builder->version($value),
                'p' => $builder->policy($value),
                'sp' => $builder->subdomainPolicy($value),
                'np' => $builder->subdomainPolicy($value),
                'pct' => $builder->pct((int) $value),
                'rua' => $builder->rua($value),
                'ruf' => $builder->ruf($value),
                'adkim' => $builder->adkim($builder->getHumanAdkimValue($value)),
                'aspf' => $builder->aspf($builder->getHumanAspfValue($value)),
                'ro' => $builder->reporting($builder->getHumanReportingOption($value)),
                'ri' => $builder->interval((int) $value),
                'np' => $builder->nonExistentSubdomainPolicy($value),
                'psd' => $builder->publicSuffixDomainPolicy($value),
                't' => $builder->testingMode($value),
            });

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
        $record .= $this->pct ? "pct=$this->pct; " : '';
        $record .= $this->rua ? "rua=$this->rua; " : '';
        $record .= $this->ruf ? "ruf=$this->ruf; " : '';
        $record .= $this->adkim ? "adkim={$this->getRealAdkimValue($this->adkim)}; " : '';
        $record .= $this->aspf ? "aspf={$this->getRealAspfValue($this->aspf)}; " : '';
        $record .= $this->reporting ? "ro={$this->getRealReportingOption($this->reporting)}; " : '';
        $record .= $this->interval ? "ri=$this->interval; " : '';
        $record .= $this->np ? "sp=$this->np; " : '';
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
