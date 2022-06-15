<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        // Custom tags
        Telescope::tag(function (IncomingEntry $entry) {
            $tags = [];

            // Tag by response status.
            if ($entry->type === 'request')
                $tags[] = 'status:' . $entry->content['response_status'];

            // Tag by request uri.
            $uri = Arr::get($entry->content ?: [], 'uri');
            if (!empty($uri))
                $tags[] = 'uri:' . $uri;

            // Tag by client ip.
            $ipAddress = Arr::get($entry->content ?: [], 'ip_address');
            if (!empty($ipAddress))
                $tags[] = 'clientIp:' . $ipAddress;

            return $tags;
        });

        Telescope::filter(function (IncomingEntry $entry) {
            if (!$this->app->environment('production')) {
                return true;
            }

            return $entry->isReportableException() ||
                $entry->isFailedJob() ||
                $entry->isFailedRequest() ||
                $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define(
            'viewTelescope',
            fn($user = null) => ipCheck(request()->ip(), ['150.95.104.186/32', '163.44.206.87/32'])
        );
    }
}
