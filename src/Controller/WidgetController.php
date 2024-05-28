<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\DashboardWidget;
use App\Entity\DashboardConfigurationWidget;
use App\Entity\CoreOrganization;
use App\Entity\CoreOrganizationType;
use App\Service\WidgetService;


#[Route('/api', name: 'api_')]
class WidgetController extends AbstractController
{
    private $widgetService;

    public function __construct(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }

    //ADD new Widget
    #[Route('/addwidget', name: 'addwidget', methods: 'post')]
    public function addwidget(Request $request): JsonResponse
    {
        return $this->widgetService->addwidget($request);
    }

    //GET All Widgets
    #[Route('/widgets', name: 'widgets', methods: 'get')]
    public function widgetlist () : JsonResponse
    {
        return $this->widgetService->widgetlist();

    }


    //Widget By ID
    #[Route('/widgetsById/{id}', name: 'widgetsById', methods: 'get')]
    public function widgetlistById ($id) : JsonResponse
    {
        return $this->widgetService->widgetlistById($id);

    }

    //WidgetConfig By ID (Dashboard Widget)
    #[Route('/widgetconfig/{id}', name: 'widgetconfig', methods: 'get')]
    public function widgetconfiglist ($id) : JsonResponse
    {
        return $this->widgetService->widgetconfiglist($id);

    }


    //Delete widget
    #[Route('/deleteWidget/{id}', name: 'widget' ,methods: 'delete')]
    public function deleteWidgetById($id): JsonResponse
    {
        return $this->widgetService->deleteWidgetById($id);

    }

    //Update Widget
    #[Route('/updatewidget/{id}', name: 'updatewidget', methods: 'put')]
    public function updatewidget(Request $request, $id): JsonResponse
    {
        return $this->widgetService->updatewidget($request,$id);
    }

    //GET widgets By organization type
    #[Route('/widgetbyType/{organizationType}', name: 'widgetbyType', methods: 'get')]
    public function WidgetType(string $organizationType): JsonResponse
    {
        return $this->widgetService->WidgetType($organizationType);
    }
    

    //update widget config
    #[Route('/updatewidgetConfig/{id}', name: 'updatewidgetConfig', methods: 'put')]
    public function updatewidgetConfig(Request $request, $id): JsonResponse
    {
        return $this->widgetService->updatewidgetConfig($request, $id);
    }

    // get widget config by id (dashboard configuration)
    #[Route('/widgetconfigByID/{id}', name: 'widgetconfigByID', methods: 'get')]
    public function widgetconfigByID ($id) : JsonResponse
    {
        return $this->widgetService->widgetconfigByID($id);

    }

        // get widget by org id 
    #[Route('/widgetconfigByOrgID/{id}', name: 'widgetconfigByOrgID', methods: 'get')]
    public function widgetsByOrgId ($id) : JsonResponse
    {
       return $this->widgetService->widgetsByOrgId($id);
    
    }
}
