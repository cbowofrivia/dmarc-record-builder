<?php

namespace CbowOfRivia\DmarcRecordBuilder;

use Webmozart\Assert\Assert;

/**
 * Responsible for building an object representation of a DMARC
 * compliant string value
 *
 * @link https://mxtoolbox.com/dmarc/details/dmarc-tags
 */
class DmarcRecord
{
    public function __construct(
        public string  $version = 'DMARC1',
        public ?string $policy = 'none',
        public ?string $subdomain_policy = null,
        public ?int    $pct = null,
        public ?string $rua = null,
        public ?string $ruf = null,
        public ?string $adkim = null,
        public ?string $aspf = null,
        public ?string $reporting = null,
        public ?string $interval = null
    )
    {
    }

    /**
     * @link https://mxtoolbox.com/dmarc/details/dmarc-tags/dmarc-version
     */
    public function version(string|null $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function policy(string|null $policy): static
    {
        Assert::inArray($policy, [
            'none', 'quarantine', 'reject', null
        ]);

        $this->policy = $policy;

        return $this;
    }

    public function subdomainPolicy(string|null $policy): static
    {
        Assert::inArray($policy, [
            'none', 'quarantine', 'reject', null
        ]);

        $this->subdomain_policy = $policy;

        return $this;
    }

    public function pct(int|null $percentage): static
    {
        $this->pct = $percentage;

        return $this;
    }

    public function rua(string|null $mailto): static
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

    public function ruf(string|null $mailto): static
    {
        if (is_null($mailto)) {
            $this->rua = $mailto;

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

    public function adkim(string|null $value): static
    {
        Assert::inArray($value, [
            'relaxed', 'string', null
        ]);

        $this->adkim = $value;

        return $this;
    }

    public function aspf(string|null $value): static
    {
        Assert::inArray($value, [
            'relaxed', 'string', null
        ]);

        $this->aspf = $value;

        return $this;
    }

    public function reporting(string|null $value): static
    {
        Assert::inArray($value, [
            'all', 'any', 'dkim', 'spf', null
        ]);

        $this->reporting = $value;

        return $this;
    }

    public function interval(int|null $interval): static
    {
        $this->interval = $interval;

        return $this;
    }

    public function __toString(): string
    {
        $record = $this->version ? "v=$this->version; " : '';
        $record .= $this->policy ? "p=$this->policy; " : '';
        $record .= $this->subdomain_policy ? "sp=$this->subdomain_policy; " : '';
        $record .= $this->pct ? "pct=$this->pct; " : '';
        $record .= $this->rua ? "rua=$this->rua; " : '';
        $record .= $this->ruf ? "ruf=$this->ruf; " : '';
        $record .= $this->adkim ? "adkim={$this->getRealAdkimValue()}; " : '';
        $record .= $this->aspf ? "aspf={$this->getRealAspfValue()}; " : '';
        $record .= $this->reporting ? "ro={$this->getRealReportingOption()}; " : '';
        $record .= $this->interval ? "ri=$this->interval; " : '';

        return trim($record);
    }

    private function getRealAdkimValue(): string
    {
        return match ($this->adkim) {
            'relaxed' => 'r',
            'strict' => 's',
        };
    }

    private function getRealAspfValue(): string
    {
        return match ($this->aspf) {
            'relaxed' => 'r',
            'strict' => 's',
        };
    }

    private function getRealReportingOption(): string
    {
        return match ($this->reporting) {
            'all' => '0',
            'any' => '1',
            'dkim' => 'd',
            'spf' => 's',
        };
    }
}
