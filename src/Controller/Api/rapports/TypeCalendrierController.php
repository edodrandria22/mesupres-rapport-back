<?php

namespace App\Controller\Api\rapports;

use App\Controller\Api\utils\BaseApiController;
use App\Service\rapports\TypeCalendriersService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;

#[Route('/typeCalendriers')]
class TypeCalendrierController extends BaseApiController
{
    public function __construct(
        private readonly TypeCalendriersService $service
    ) {
    }

    #[Route('', name: 'api_type_calendriers_index', methods: ['GET'])]
    #[TokenRequired]
    public function index(): JsonResponse
    {
        try {
            $types = $this->service->getAllActive();

            $data = array_map(function ($type) {
                return [
                    'id' => $type->getId(),
                    'name' => $type->getName(),
                ];
            }, $types);

            return $this->jsonSuccess($data);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }
    }
}
