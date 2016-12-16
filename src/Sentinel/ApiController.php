<?php
namespace EvolveEngine\Sentinel;

use EvolveEngine\Router\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    
    /**
     * Return status information
     *
     * @return JsonResponse
     */
    public function status(Request $request)
    {
        return new JsonResponse([
            'plugins' => [
                'acf' => [
                    'name'    => "Advanced Custom Fields",
                    'version' => "5.1.1"
                ],
                'yoast' => [
                    'name'    => "Yoast SEO",
                    'version' => "2.0.0"
                ]
            ]
        ]);
    }

    /**
     * Update the plugin
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        chdir(app()->rootPath());
        $composerHome = '/Users/jofry/';
        putenv("COMPOSER_HOME=$composerHome");

        ob_start();
        system('composer require symfony/var-dumper:^3.2 2>&1');
        $output = ob_get_clean();
        dump($output);
        die;
    }

}