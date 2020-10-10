<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use App\Services\TranslationService;

/**
 * Class TranslationController
 * @package App\Http\Controllers
 */
class TranslationController extends Controller
{
    /**
     * @var $translateService
     */
    protected $translateService;

    /**
     * TranslationController Controller Constructor
     *
     * @param TranslationController $translateService
     *
     */
    public function __construct(TranslationService $translateService)
    {
        $this->translateService = $translateService;
    }


    /**
     * @param TranslationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(TranslationRequest $request)
    {

        $data = $request->only([
            'language',
            'expression',
        ]);

        $result = ['status' => 200];

        try {
            $result['data'] = $this->translateService->translate($data);
        } catch (Exception $e) {
            $result = [
                'status' => $e->getCode(),
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result['data'], $result['status']);
    }
}
