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


#[Route('/api', name: 'api_')]
class WidgetController extends AbstractController
{

    //ADD new Widget
    #[Route('/addwidget', name: 'addwidget', methods: 'post')]
    public function addwidget(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        $typeorg = $decoded->typeorg;
        $typetrans = $decoded->typetrans;
        $typewid = $decoded->typewid;
        $wid_visi = $decoded->wid_visi;
        $name_fr = $decoded->name_fr;
        $name_eng = $decoded->name_eng;
        $desc_fr = $decoded->desc_fr;
        $desc_eng = $decoded->desc_eng;
        $wid_url = $decoded->wid_url;


        $widget = new DashboardWidget();
        $widget_config = new DashboardConfigurationWidget();
        
        $organizationRepository = $em->getRepository(CoreOrganizationType::class);

        $coreOrganizationType = $organizationRepository->findOneBy(['type' => $typeorg]);

        $coreOrganization = $coreOrganizationType->getCoreOrganizationId()->first();

        $widget->setCoreOrganizationTypeId($coreOrganizationType)
               ->setDescriptionEn($desc_eng)
               ->setIsDefault(true)
               ->setWidgetConditions('')
               ->setDescriptionFr($desc_fr)
               ->setWidgetUrl($wid_url)
               ->setWidgetType($typewid)
               ->setTransactionType($typetrans)
               ->setWidgetVisibility($wid_visi);

        $widget->setCoreOrganization($coreOrganization);
        $em->persist($widget);
        $em->flush();

        $widget_config->setDashboardWidgetId($widget)
                      ->setNameFr($name_fr)
                      ->setNameEn($name_eng)
                      ->setWidgetStyle('')
                      ->setWidgetWidth('')
                      ->setWidgetHeight('')
                      ->setWidgetRank('');
        $em->persist($widget_config);
        $em->flush();
        

        return $this->json(['message' => 'Widget added']);
    }

    //GET All Widgets
    #[Route('/widgets', name: 'widgets', methods: 'get')]
    public function widgetlist (ManagerRegistry $doctrine) : JsonResponse
    {
        $em = $doctrine->getManager();

        $widget_dash_repo = $em->getRepository(DashboardWidget::class);
        $widgets = $widget_dash_repo->findAll();

        $jsonData = $this->serializeWidgets($widgets);

        return new JsonResponse($jsonData);

    }


    //Widget By ID
    #[Route('/widgetsById/{id}', name: 'widgetsById', methods: 'get')]
    public function widgetlistById (ManagerRegistry $doctrine, $id) : JsonResponse
    {
        $em = $doctrine->getManager();

        $widget_dash_repo = $em->getRepository(DashboardWidget::class);
        $widget = $widget_dash_repo->find($id);

        $jsonData = $this->serializeWidget($widget);

        return new JsonResponse($jsonData);

    }
    //WidgetConfig By ID (Dashboard Widget)
    #[Route('/widgetconfig/{id}', name: 'widgetconfig', methods: 'get')]
    public function widgetconfiglist (ManagerRegistry $doctrine, $id) : JsonResponse
    {
        $em = $doctrine->getManager();


        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);

        $widget_config = $widget_dash_config_repo->findOneBy(['dashboard_widget' => $id]);

        $jsonData = $this->serializeWidgetconfig($widget_config);

        return new JsonResponse($jsonData);

    }

    private function serializeWidgets(array $widgets): array
    {
        $serializedWidgets = [];

        foreach ($widgets as $widget) {
            $serializedWidgets[] = $this->serializeWidget($widget);
        }

        return $serializedWidgets;
    }

    //Serialize single widget
    private function serializeWidget(DashboardWidget $widget): array
    {

        return [
            'id' => $widget->getId(),
            'typewid' => $widget->getWidgetType(),
            'typetrans' => $widget->getTransactionType(),
            'wid_visi' => $widget->getWidgetVisibility(),
            'is_default' => $widget->isIsDefault(),
            'desc_fr' =>$widget->getDescriptionFr(), 
            'desc_eng' =>$widget->getDescriptionEn(),
            'wid_url'=>$widget->getWidgetUrl(),
            'typeorg'=>$widget->getCoreOrganizationTypeId()->getType()

        ];
    }

    //Serialize widgets
    private function serializeWidgetconfig(DashboardConfigurationWidget $widget_config): array
    {

        return [
            'id' => $widget_config->getId(),
            'name_fr' => $widget_config->getNameFr(),
            'name_eng' => $widget_config->getNameEn(),
            'wid_style' => $widget_config->getWidgetStyle(),
            'wid_width' => $widget_config->getWidgetWidth(),
            'wid_height' =>$widget_config->getWidgetHeight(), 
            'wid_rank' =>$widget_config->getWidgetRank()

        ];
    }

    //Delete widget
    #[Route('/deleteWidget/{id}', name: 'widget' ,methods: 'delete')]
    public function deleteWidgetById(Request $request, $id, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();

        $widget_dash_repo = $em->getRepository(DashboardWidget::class);
        $widget = $widget_dash_repo->find($id);

        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);

        $widget_config = $widget_dash_config_repo->findOneBy(['dashboard_widget' => $id]);

        if (!$widget_config) {
            return new JsonResponse(['error' => 'Widget configuration not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$widget) {
            return new JsonResponse(['error' => 'Widget not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->remove($widget);
        $em->remove($widget_config);
        $em->flush();

        return new JsonResponse(['message' => 'Widget and their configuration deleted successfully'], JsonResponse::HTTP_OK);


    }

    //Update Widget
    #[Route('/updatewidget/{id}', name: 'updatewidget', methods: 'put')]
    public function updatewidget(ManagerRegistry $doctrine, Request $request, $id): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        $typeorg = $decoded->typeorg;
        $typetrans = $decoded->typetrans;
        $typewid = $decoded->typewid;
        $wid_visi = $decoded->wid_visi;
        $name_fr = $decoded->name_fr;
        $name_eng = $decoded->name_eng;
        $desc_fr = $decoded->desc_fr;
        $desc_eng = $decoded->desc_eng;
        $wid_url = $decoded->wid_url;

        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);
        $widget_config = $widget_dash_config_repo->findOneBy(['dashboard_widget' => $id]);;
        
        $widget_dash_repo = $em->getRepository(DashboardWidget::class);
        $widget = $widget_dash_repo->find($id);
        
        $organizationRepository = $em->getRepository(CoreOrganizationType::class);

        $coreOrganizationType = $organizationRepository->findOneBy(['type' => $typeorg]);

        $coreOrganization = $coreOrganizationType->getCoreOrganizationId()->first();

        $widget->setCoreOrganizationTypeId($coreOrganizationType)
               ->setDescriptionEn($desc_eng)
               ->setIsDefault(true)
               ->setWidgetConditions('')
               ->setDescriptionFr($desc_fr)
               ->setWidgetUrl($wid_url)
               ->setWidgetType($typewid)
               ->setTransactionType($typetrans)
               ->setWidgetVisibility($wid_visi);

        $widget->setCoreOrganization($coreOrganization);
        $em->persist($widget);
        $em->flush();

        $widget_config->setDashboardWidgetId($widget)
                      ->setNameFr($name_fr)
                      ->setNameEn($name_eng)
                      ->setWidgetStyle('')
                      ->setWidgetWidth('')
                      ->setWidgetHeight('')
                      ->setWidgetRank('');
        $em->persist($widget_config);
        $em->flush();
        

        return $this->json(['message' => 'Widget updated']);
    }

    //GET widgets By organization type
    #[Route('/widgetbyType/{organizationType}', name: 'widgetbyType', methods: 'get')]
    public function WidgetType(ManagerRegistry $doctrine, string $organizationType): JsonResponse
    {
        $em = $doctrine->getManager();
    
        $organizationRepository = $em->getRepository(CoreOrganizationType::class);
        $organizationTypeEntity = $organizationRepository->findOneBy(['type' => $organizationType]);
    
        if ($organizationTypeEntity) {
            $dashboardWidgets = $organizationTypeEntity->getDashboardWidgets();
    
            $dashboardWidgetsArray = [];
    
            foreach ($dashboardWidgets as $dashboardWidget) {
                $configurationWidgetId = null;
                $configurationWidgets = $dashboardWidget->getDashboardConfigurationWidgetId();
    
                foreach ($configurationWidgets as $configurationWidget) {
                    $configurationWidgetId = $configurationWidget->getId();
                    break; // Stop iteration after the first ID
                }
    
                $dashboardWidgetsArray[] = [
                    'id' => $configurationWidgetId,
                    'widget_type' => $dashboardWidget->getWidgetType(),
                ];
            }
    
            return new JsonResponse($dashboardWidgetsArray);
        } else {
            return new JsonResponse(['error' => 'Organization type not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    }
    
    


    //update widget config
    #[Route('/updatewidgetConfig/{id}', name: 'updatewidgetConfig', methods: 'put')]
    public function updatewidgetConfig(ManagerRegistry $doctrine, Request $request, $id): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        $name_fr = $decoded->name_fr;
        $name_eng = $decoded->name_eng;
        $wid_style = $decoded->wid_style;
        $wid_width = $decoded->wid_width;
        $wid_height = $decoded->wid_height;
        $wid_rank = $decoded->wid_rank;

        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);
        $widget_config = $widget_dash_config_repo->find($id);
        

        $widget_config->setNameFr($name_fr)
                      ->setNameEn($name_eng)
                      ->setWidgetStyle($wid_style)
                      ->setWidgetWidth($wid_width )
                      ->setWidgetHeight($wid_height)
                      ->setWidgetRank( $wid_rank);
        $em->persist($widget_config);
        $em->flush();
        

        return $this->json(['message' => 'Widget configuration updated']);
    }

    // get widget config by id (dashboard configuration)
    #[Route('/widgetconfigByID/{id}', name: 'widgetconfigByID', methods: 'get')]
    public function widgetconfigByID (ManagerRegistry $doctrine, $id) : JsonResponse
    {
        $em = $doctrine->getManager();


        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);

        $widget_config = $widget_dash_config_repo->find($id);

        $jsonData = $this->serializeWidgetconfig($widget_config);

        return new JsonResponse($jsonData);

    }
}
