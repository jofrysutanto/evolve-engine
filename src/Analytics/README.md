## Analytics Service

### 1. Inject `Google Analytics` & `Google Tag Manager`
- Set services key in your `.env` file. Use comma `.` to separate out multiple codes
- Set which section the service script need to be injected in `config/analytics.php`, which is under your theme folder
- By using `Analytics::render('google-analytics')` or `Analytics::render('google-tag-manager')`, it can be manually injected as well

### 2. Register `Custom Service`
- Register custom service in `AppServiceProvider`, which is under theme folder `src/AppServiceProvider.php`. Sample code as below
    ```
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        \Analytics::extend('custom-service', function ($code)
        {
            return "Your script";
        });
    }
    ```
- Set service key in `config/analytics.php`, which is under your theme folder
    ```
    'services' => [
		'google-analytics'   => explode(',', env('GA', '')),
		'google-tag-manager' => explode(',', env('GTM', '')),
		'custom-service' => 'xxx'
    ],
    ```
- Inject the custome service anywhere by using `Analytics::render('custom-service')`