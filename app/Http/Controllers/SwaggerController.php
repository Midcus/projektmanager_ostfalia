<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Response as ResponseFacade;
use L5Swagger\GeneratorFactory;

class SwaggerController extends \L5Swagger\Http\Controllers\SwaggerController
{
    public function api(Request $request)
    {
        $documentation = $request->offsetGet('documentation') ?? 'default';
        $config = $request->offsetGet('config') ?? config('l5-swagger.documentations.'.$documentation);


        Log::info('SwaggerController api method', [
            'documentation' => $documentation,
            'config' => $config,
            'path' => $request->path(),
        ]);

        if ($proxy = $config['proxy'] ?? []) {
            if (!is_array($proxy)) {
                $proxy = [$proxy];
            }
            Request::setTrustedProxies(
                $proxy,
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB
            );
        }

        $urlToDocs = $this->generateDocumentationFileURL($documentation, $config);
        $useAbsolutePath = config('l5-swagger.documentations.'.$documentation.'.paths.use_absolute_path', true);

        return ResponseFacade::make(
            view('l5-swagger::index', [
                'documentation' => $documentation,
                'secure' => RequestFacade::secure(),
                'urlToDocs' => $urlToDocs,
                'operationsSorter' => $config['operations_sort'] ?? null,
                'configUrl' => $config['additional_config_url'] ?? null,
                'validatorUrl' => $config['validator_url'] ?? null,
                'useAbsolutePath' => $useAbsolutePath,
            ]),
            200
        );
    }
}