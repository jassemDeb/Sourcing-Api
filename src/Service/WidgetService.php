<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\DashboardWidget;
use App\Entity\DashboardConfigurationWidget;
use App\Entity\DashboardConfiguration;
use App\Entity\CoreOrganization;
use App\Entity\CoreOrganizationType;

class WidgetService 
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
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

    //Serialize widgets Config
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

    //Serialize widgets 
    private function serializeWidgets(array $widgets): array
    {
        $serializedWidgets = [];

        foreach ($widgets as $widget) {
            $serializedWidgets[] = $this->serializeWidget($widget);
        }

        return $serializedWidgets;
    }

    private function serializeWidgetconfigs(array $widgets): array
    {
        $serializedWidgets = [];

        foreach ($widgets as $widget) {
            $serializedWidgets[] = $this->serializeWidgetconfig($widget);
        }

        return $serializedWidgets;
    }




    // ADD DEFAULT WIDGET
    public function addwidget(Request $request): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $decoded = json_decode($request->getContent());
    
        // Existing code to fetch and set various parameters
        $typeorg = $decoded->typeorg;
        $typetrans = $decoded->typetrans;
        $typewid = $decoded->typewid;
        $wid_visi = $decoded->wid_visi;
        $name_fr = $decoded->name_fr;
        $name_eng = $decoded->name_eng;
        $desc_fr = $decoded->desc_fr;
        $desc_eng = $decoded->desc_eng;
        $wid_url = $decoded->wid_url;
    
        $coreOrganizationType = $em->getRepository(CoreOrganizationType::class)->findOneBy(['type' => $typeorg]);
        $coreOrganization = null;
        if ($coreOrganizationType) {
            $coreOrganizations = $coreOrganizationType->getCoreOrganizationId();
            $coreOrganization = $coreOrganizations->first();
        }
    
        $widget = new DashboardWidget();
        $widget->setCoreOrganizationTypeId($coreOrganizationType)
               ->setDescriptionEn($desc_eng)
               ->setIsDefault(true)
               ->setWidgetConditions('')
               ->setDescriptionFr($desc_fr)
               ->setWidgetUrl($wid_url)
               ->setWidgetType($typewid)
               ->setTransactionType($typetrans)
               ->setWidgetVisibility($wid_visi)
               ->setCoreOrganization($coreOrganization);
    
        $em->persist($widget);
        $em->flush();
    
        // Set default widget style with defined attributes
        $defaultStyle = [
            ['backgroundColor' => '#FFFFFF'],  // default white background
            ['textColor' => '#000000'],        // default black text
            ['textFont' => 'Arial'],           // default Arial font
            ['textSize' => '12px']             // default text size of 12px
        ];

        $widget_width='';
        $widget_height='';
        $widget_rank='';

        
    
        $widget_config = new DashboardConfigurationWidget();
        $widget_config->setDashboardWidgetId($widget)
                      ->setNameFr($name_fr)
                      ->setNameEn($name_eng)
                      ->setWidgetStyle($defaultStyle)
                      ->setWidgetWidth($widget_width)
                      ->setWidgetHeight( $widget_height)
                      ->setWidgetRank($widget_rank);
    
        $em->persist($widget_config);
        $em->flush();
    
        return new JsonResponse(['message' => 'Widget added']);
    }
    
    //ADD WIDGET
    public function addWidgetWithConfig(Request $request): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $decoded = json_decode($request->getContent());

        // Extract parameters from the decoded request
        $id = $decoded->id;
        $typeorg = $decoded->typeorg;
        $typetrans = $decoded->typetrans;
        $typewid = $decoded->typewid;
        $wid_visi = $decoded->wid_visi;
        $name_fr = $decoded->name_fr;
        $name_eng = $decoded->name_eng;
        $desc_fr = $decoded->desc_fr;
        $desc_eng = $decoded->desc_eng;
        $wid_url = $decoded->wid_url;
        $widget_style = $decoded->wid_style;
        $widget_width = $decoded->wid_width;
        $widget_height = $decoded->wid_height;
        $widget_rank = $decoded->wid_rank;
        $dashboardConfigurationId = $decoded->dashboardConfigurationId;

        // Retrieve the CoreOrganizationType entity
        $coreOrganizationType = $em->getRepository(CoreOrganizationType::class)->findOneBy(['type' => $typeorg]);
        if (!$coreOrganizationType) {
            return new JsonResponse(['error' => 'CoreOrganizationType not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Retrieve the CoreOrganization entity
        $coreOrganizations = $coreOrganizationType->getCoreOrganizationId();
        $coreOrganization = $coreOrganizations ? $coreOrganizations->first() : null;

        $dashboardConfiguration = $em->getRepository(DashboardConfiguration::class)->find($dashboardConfigurationId);
        $dashboardConfigurationWidget = $em->getRepository(DashboardConfigurationWidget::class)->find($id);

        $default_widget= $dashboardConfigurationWidget->getDashboardWidgetId();
        // Create the DashboardWidget entity
        $widget = new DashboardWidget();
        $widget->setCoreOrganizationTypeId($coreOrganizationType)
            ->setDescriptionEn($default_widget->getDescriptionEn())
            ->setIsDefault(false) // Set isDefault to false
            ->setWidgetConditions('')
            ->setDescriptionFr($default_widget->getDescriptionFr())
            ->setWidgetUrl($default_widget->getWidgetUrl())
            ->setWidgetType($default_widget->getWidgetType())
            ->setTransactionType($default_widget->getTransactionType())
            ->setWidgetVisibility($default_widget->getWidgetVisibility())
            ->setCoreOrganization($coreOrganization);

        $em->persist($widget);
        $em->flush();

        // Retrieve the DashboardConfiguration entity

        if (!$dashboardConfiguration) {
            return new JsonResponse(['error' => 'DashboardConfiguration not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Create the DashboardConfigurationWidget entity
        $widgetConfig = new DashboardConfigurationWidget();
        $widgetConfig->setDashboardWidgetId($widget)
                    ->setCoreDashboardConfigurationId($dashboardConfiguration) // Link to the provided DashboardConfiguration
                    ->setNameFr($name_fr)
                    ->setNameEn($name_eng)
                    ->setWidgetStyle($widget_style)
                    ->setWidgetWidth($widget_width)
                    ->setWidgetHeight($widget_height)
                    ->setWidgetRank($widget_rank);

        $em->persist($widgetConfig);
        $em->flush();

        return new JsonResponse(['message' => 'Widget added']);
    }

    //GET All Widgets
    public function widgetlist () : JsonResponse
    {
        $em = $this->doctrine->getManager();

        $widget_dash_repo = $em->getRepository(DashboardWidget::class);
        $widgets = $widget_dash_repo->findAll();

        $jsonData = $this->serializeWidgets($widgets);

        return new JsonResponse($jsonData);

    }

    //Widget By ID
    public function widgetlistById ($id) : JsonResponse
    {
        $em = $this->doctrine->getManager();

        $widget_dash_repo = $em->getRepository(DashboardWidget::class);
        $widget = $widget_dash_repo->find($id);

        $jsonData = $this->serializeWidget($widget);

        return new JsonResponse($jsonData);

    }

    //Widget by org ID
    public function widgetsByOrgId(int $orgId): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $widgetRepo = $em->getRepository(DashboardWidget::class);
        
        // Retrieve widgets based on the organization ID
        $widgets = $widgetRepo->findBy(['core_organization' => $orgId]);

        // Serialize the list of widgets
        $jsonData = $this->serializeWidgets($widgets);

        return new JsonResponse(['status' => true, 'data' => $jsonData]);
    }

    //WidgetConfig By ID (Dashboard Widget)
    public function widgetconfiglist ($id) : JsonResponse
    {
        $em = $this->doctrine->getManager();


        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);

        $widget_config = $widget_dash_config_repo->findOneBy(['dashboard_widget' => $id]);

        $jsonData = $this->serializeWidgetconfig($widget_config);

        return new JsonResponse($jsonData);

    }

    
    //WidgetConfig By ID (Dashboard configuration)
    public function widgetconfiglistbyDashConfig ($id) : JsonResponse
    {
        $em = $this->doctrine->getManager();


        $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);

        $widget_config = $widget_dash_config_repo->findBy(['core_dashboard_configuration' => $id]);

        $jsonData = $this->serializeWidgetconfigs($widget_config);

        return new JsonResponse(['status' => true, 'data' => $jsonData]);

    }


    //Delete widget
    // Delete widget or widget configuration
    public function deleteWidgetById($id): JsonResponse
    {
        $em = $this->doctrine->getManager();

        // Check if ID belongs to a DashboardConfigurationWidget
        $widget_config_repo = $em->getRepository(DashboardConfigurationWidget::class);
        $widget_config = $widget_config_repo->find($id);

        // Check if ID belongs to a DashboardWidget
        $widget_repo = $em->getRepository(DashboardWidget::class);
        $widget = $widget_repo->find($id);

        if ($widget_config) {
            // If it's a DashboardConfigurationWidget, delete it along with its associated DashboardWidget
            $associatedWidget = $widget_config->getDashboardWidgetId();
            $em->remove($widget_config);  // Remove the configuration first
            if ($associatedWidget) {
                $em->remove($associatedWidget);  // Then remove the associated widget
            }
            $em->flush();
            return new JsonResponse(['message' => 'Dashboard Configuration Widget and associated Dashboard Widget deleted successfully'], JsonResponse::HTTP_OK);
        } elseif ($widget) {
            // If it's a DashboardWidget, delete it and all related DashboardConfigurationWidgets
            $configurations = $widget_config_repo->findBy(['dashboard_widget' => $widget]);
            foreach ($configurations as $config) {
                $em->remove($config);
            }
            $em->remove($widget);
            $em->flush();
            return new JsonResponse(['message' => 'Dashboard Widget and all related configurations deleted successfully'], JsonResponse::HTTP_OK);
        } else {
            // If neither, return an error
            return new JsonResponse(['error' => 'No widget or widget configuration found with provided ID'], JsonResponse::HTTP_NOT_FOUND);
        }
    }


    //Update Widget
    public function updatewidget($request, $id): JsonResponse
    {
        $em = $this->doctrine->getManager();
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
                      ->setNameEn($name_eng);
        $em->persist($widget_config);
        $em->flush();
        

        return new JsonResponse(['message' => 'Widget updated']);
    }

    //GET widgets By organization type
    public function WidgetType(string $organizationType): JsonResponse
    {
        $em = $this->doctrine->getManager();
    
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
    public function updatewidgetConfig($request, $id): JsonResponse
    {
        $em = $this->doctrine->getManager();
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
        
        
        return new JsonResponse(['message' => 'Widget configuration updated']);
    }

     // get widget config by id (dashboard widget configuration)
     public function widgetconfigByID ($id) : JsonResponse
     {
        $em = $this->doctrine->getManager();
 
         $widget_dash_config_repo = $em->getRepository(DashboardConfigurationWidget::class);
 
         $widget_config = $widget_dash_config_repo->find($id);
 
         $jsonData = $this->serializeWidgetconfig($widget_config);
 
         return new JsonResponse($jsonData);
 
     }

     // get default widget
     public function getDefaultConfigWidgetsByOrgType($orgId): JsonResponse
     {
         $em = $this->doctrine->getManager();
         $orgRepo = $em->getRepository(CoreOrganization::class);
         $orgTypeRepo = $em->getRepository(CoreOrganizationType::class);
         $widgetRepo = $em->getRepository(DashboardWidget::class);
         $widgetConfigRepo = $em->getRepository(DashboardConfigurationWidget::class);
     
         // Retrieve the organization
         $organization = $orgRepo->find($orgId);
         if (!$organization) {
             return new JsonResponse(['error' => 'Organization not found'], JsonResponse::HTTP_NOT_FOUND);
         }
     
         // Get the CoreOrganizationType of the given organization
         $orgTypes = $organization->getCoreOrganizationTypes();
         if ($orgTypes->isEmpty()) {
             return new JsonResponse(['error' => 'No organization types found for this organization'], JsonResponse::HTTP_NOT_FOUND);
         }
     
         // Get the 'type' field from the CoreOrganizationType(s)
         $orgTypeValues = [];
         foreach ($orgTypes as $orgType) {
             $orgTypeValues[] = $orgType->getType();
         }
     
         // Find all CoreOrganizationType entities with the same 'type' field value
         $matchingOrgTypes = $orgTypeRepo->findBy(['type' => $orgTypeValues]);
     
         // Get all DashboardWidgets associated with these CoreOrganizationTypes where is_default is true
         $filteredWidgetConfigs = [];
         foreach ($matchingOrgTypes as $matchingOrgType) {
             $widgets = $widgetRepo->findBy(['core_organization_type' => $matchingOrgType, 'is_default' => true]);
     
             foreach ($widgets as $widget) {
                 // Get all DashboardConfigurationWidgets linked to this widget
                 $widgetConfigs = $widget->getDashboardConfigurationWidgetId();
                 foreach ($widgetConfigs as $widgetConfig) {
                     $filteredWidgetConfigs[] = $this->serializeWidgetconfig($widgetConfig);
                 }
             }
         }
     
         return new JsonResponse(['status' => true, 'data' => $filteredWidgetConfigs]);
     }
     
     //get dashboard widget by config id 
     public function widgetConfigDashboardWidgetIdByID($id): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $widgetDashConfigRepo = $em->getRepository(DashboardConfigurationWidget::class);

        // Retrieve the DashboardConfigurationWidget entity
        $widgetConfig = $widgetDashConfigRepo->find($id);
        if (!$widgetConfig) {
            return new JsonResponse(['error' => 'DashboardConfigurationWidget not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Get the DashboardWidget 
        $dashboardWidgetId = $widgetConfig->getDashboardWidgetId();

        $jsonData = $this->serializeWidget($dashboardWidgetId);

        return new JsonResponse([$jsonData]);
    }

     
     
     
     
     
}