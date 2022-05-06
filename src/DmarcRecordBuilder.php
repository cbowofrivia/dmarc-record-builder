<?php

namespace CbowOfRivia\DmarcRecordBuilder;

use Webmozart\Assert\Assert;

/**
 * Responsible for building an object representation of a DMARC
 * compliant string value
 *
 * @link https://mxtoolbox.com/dmarc/details/dmarc-tags
 */
class DmarcRecordBuilder
{
    public function __construct(
        public string  $version = 'DMARC1',
        public ?string $policy = 'none', // none, quarantine, reject
        public ?string $subdomain_policy = null, // none, quarantine, reject
        public ?int    $pct = null, // 1 - 100
        public ?string $rua = null, // mailto:
        public ?string $ruf = null, // mailto:
        public ?string $adkim = null, // strict / relaxed
        public ?string $aspf = null, // strict / relaxed
        public ?string $reporting = null, // 0, 1, d, s https://mxtoolbox.com/dmarc/details/dmarc-tags/dmarc-failure-reporting-options
        public ?string $interval = null // Seconds
    )
    {
    }

    /**
     * @param string $version
     *
     * @return $this
     *
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

    public function reporting(string $value): static
    {
        Assert::inArray($value, [
            'all', 'any', 'dkim', 'spf'
        ]);

        $this->reporting = $value;

        return $this;
    }

    public function interval(int|null $interval): static
    {
        $this->interval = $interval;

        return $this;
    }
}
