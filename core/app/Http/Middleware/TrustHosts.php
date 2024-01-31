<?php

namespace App\Http\Middleware;

use App\Models\CustomDomain;
use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts()
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
            $this->customDomains()
        ];
    }

    private function customDomains()
    {
        $customDomains = CustomDomain::where('custom_domain_status', 'connected')->pluck('custom_domain')->toArray();
        return count($customDomains) > 2 ? implode(',', $customDomains) : current($customDomains);
    }
}
