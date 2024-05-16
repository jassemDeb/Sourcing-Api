<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\DashboardWidget;
use App\Entity\DashboardConfigurationWidget;
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

 
    public function addwidget(Request $request): JsonResponse
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

        $widget_config = new DashboardConfigurationWidget();

     
        $widget_config->setDashboardWidgetId($widget)
                    ->setNameFr($name_fr)
                    ->setNameEn($name_eng)
                    ->setWidgetStyle([])
                    ->setWidgetWidth('')
                    ->setWidgetHeight('')
                    ->setWidgetRank('');

        $em->persist($widget_config);
        $em->flush();

        return new JsonResponse(['message' => 'Widget added']);

    }
}