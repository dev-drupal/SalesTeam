<?php

namespace Drupal\sales_team\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\sales_team\Getsalesteamdata;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Sales Team routes.
 */
class SalesTeamController extends ControllerBase {
  /**
   * Branch Location helper functions.
   *
   * @var \Drupal\sales_team\Services\salesService
   */
  private $salesServiceData;

  /**
   * {@inheritdoc}
   */
  public function __construct(Getsalesteamdata $salesServiceData) {
    $this->salesServiceData = $salesServiceData;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $salesServiceData = $container->get('sales_team.getsalesteamdata');
    return new static($salesServiceData);
  }

  /**
   * Invoke Function to get Slaes Node Data.
   */
  public function getSalesTeamData() {
    $data = [];
    $data['data'] = $this->salesServiceData->getSalesNode();
    return $data;
  }

  /**
   * Retrive International Team Data to Render on Page .
   */
  public function getInternationalSalesTeamData() {
    $data = [];
    $data['data'] = $this->salesServiceData->getInternationalSalesTeamNode();
    return $data;
  }

  /**
   * Build Html Structure with Sales team Data.
   */
  public function fetchSalesTeamStructure() {
    $data = $this->getSalesTeamData();
    $html = '<div class="sales-team-member-content"><h2> Sales Team </h2><div class="sales-team-member-wrapper"><div class="sales-team-member"> ';
    foreach ($data["data"] as $salesTeamMember) {
      $data_ca = $data_us = [];
      foreach ($salesTeamMember["selected_states"] as $key => $canada_state) {
        foreach ($canada_state as $key => $value) {
          if ($key == "canada_state") {
            foreach ($value as $stvalue) {
              $data_ca[] = "CA-" . $stvalue . "";
            }
          }
        }
      }

      foreach ($salesTeamMember["selected_states"] as $key => $canada_state) {
        foreach ($canada_state as $key => $value) {
          if ($key == "usa_state") {
            foreach ($value as $ustvalue) {
              $data_us[] = "US-" . $ustvalue . "";
            }
          }
        }
      }

      $state_final = implode(", ", $data_ca);
      $state_final .= ", " . implode(", ", $data_us);

      $html .= '<div class="row"><div class="col-md-12"><div class="team-member" data="' . $state_final . '">';
      $html .= '<div class="team-image">';
      $html .= '<div class="color-box-section div-count-' . count($salesTeamMember['color_code']) . '">';
      $zone_color = [];
      foreach ($salesTeamMember['color_code'] as $country_color) {
        foreach ($country_color as $color_code) {
          foreach ($color_code as $value) {
            $html .= '<div class="color-box color-' . str_replace('#', '', $value['value']) . '"></div>';
            $zone_color[] = $value['value'];
          }
        }
      }
      $html .= '</div>';
      $html .= '<div class="team-image-box"><img src=' . file_create_url($salesTeamMember["image"]) . ' alt="' . $salesTeamMember["alt"] . '" title="' . $salesTeamMember["image_title"] . '"></div></div>
      <div class="member-detail-block"><div class="member-detail"><div class="team-title">' . $salesTeamMember["title"] . '</div><div class="team-email"><a href="mailto:' . $salesTeamMember["email"] . '"><em class="far fa-envelope"><span>email</span></em> </a></div><div class="team-phone"><strong>Phone:</strong><a href="tel:' . $salesTeamMember["phone"] . '">' . $salesTeamMember["phone"] . '</a></div></div>';
      $i = 0;
      if (!empty($salesTeamMember["selected_states"])) {
        $html .= '<div class="country">';
        foreach ($salesTeamMember["selected_states"] as $key => $state_label) {
          $html .= '<div class="zone-info"><div class="member-color-box color-' . str_replace('#', '', $zone_color[$i]) . '"></div>';
          $html .= '<p>';
          $flag_format = 1;
          foreach ($state_label as $key => $statename) {
            if ($key == "canada_label_state") {
              $flag_format = 2;
              $numItems = count($statename);
              $d = 0;
              foreach ($statename as $key => $finalstate) {
                if (++$d === $numItems) {
                  $html .= $finalstate;
                }
                else {
                  $html .= $finalstate . ', ';
                }
              }
            }
            if ($key == "usa_label_state") {
              $numItems = count($statename);
              $d = 0;
              foreach ($statename as $key => $finalstate) {
                if ($flag_format == 2) {
                  $html .= ', ';
                  $flag_format = 1;
                }
                if (++$d === $numItems) {
                  $html .= $finalstate;
                }
                else {
                  $html .= $finalstate . ', ';
                }
              }
            }
          }
          $html .= "</p></div>";
          $i++;
        }
        $html .= '</div>';
      }
      $html .= '</div></div></div></div>';
    }
    $html .= '</div></div></div>';
    return $html;
  }

  /**
   * Creating HTML Structure for International Team.
   */
  public function fetchInternationalTeamStructure() {
    $data = $this->getInternationalSalesTeamData();
    $html = '<div class="international-sales-team-content"><h2> International Sales Team </h2><div class="sales-team-member-wrapper international-sales-team-wrapper"><div class="sales-team-member international-sales-team"> ';
    foreach ($data["data"] as $internationalsalesTeamMember) {
      $data_attribute = $data_label = [];
      foreach ($internationalsalesTeamMember["selected_country"] as $country_array) {
        $data_attribute[] = $country_array["code"]["value"];
        $data_label[] = $country_array["name"];
      }

      if (isset($internationalsalesTeamMember["logo_image"])) {
        $html .= '<div class="row"><div class="col-md-12"><div class="team-member team-with-image international-team-member" data="' . implode(", ", $data_attribute) . '">';
      }
      else {
        $html .= '<div class="row"><div class="col-md-12"><div class="team-member international-team-member" data="' . implode(", ", $data_attribute) . '">';
      }

      $html .= '<div class="member-detail-block">';

      if (isset($internationalsalesTeamMember["logo_image"])) {
        $html .= '<div class="international-team-image"><img src=' . file_create_url($internationalsalesTeamMember["logo_image"]) . ' alt="' . $internationalsalesTeamMember["alt"] . '" title="' . $internationalsalesTeamMember["image_title"] . '"></div>';
      }

      foreach ($internationalsalesTeamMember['color_code'] as $country_color) {
        foreach ($country_color as $color_code) {
          $html .= '<div class="member-color-box color-' . str_replace('#', '', $color_code['value']) . '"></div>';
        }
      }

      $html .= '<div class="intermational-member-info"><div class="team-title international-team-title">' . $internationalsalesTeamMember["title"] . '</div>';

      if (isset($internationalsalesTeamMember["website"])) {
        $html .= '<div class="team-website international-team-website"><a href="' . $internationalsalesTeamMember["website"] . '" target="_blank"><em class="fas fa-globe"><span>website</span></em> </a></div>';
      }

      if (isset($internationalsalesTeamMember["email"]) && empty($internationalsalesTeamMember["email2"])) {
        $html .= '<div class="team-email international-team-email"><a href="mailto:' . $internationalsalesTeamMember["email"] . '"><em class="far fa-envelope"><span>email</span></em> </a></div>';
      }
      $html .= '<div class="international-team-category">' . $internationalsalesTeamMember["category"][0]["value"] . '</div>';
      if (isset($internationalsalesTeamMember["phone"]) && empty($internationalsalesTeamMember["phone2"])) {
        $html .= '<div class="team-phone international-team-phone"><strong>Phone:</strong><a href="tel:' . $internationalsalesTeamMember["phone"] . '">' . $internationalsalesTeamMember["phone"] . '</a></div>';
      }
      if (isset($internationalsalesTeamMember["body"])) {
        $html .= '<div class="team-address"> <strong>Address:</strong> ' . $internationalsalesTeamMember["body"] . '</div>';
      }

      if (!empty($internationalsalesTeamMember["phone2"]) && !empty($internationalsalesTeamMember["phone"])) {
        $html .= '<div class="team-phone"> <strong>Phone:</strong> <a href="tel:' . $internationalsalesTeamMember["phone"] . '">' . $internationalsalesTeamMember["phone"] . '</a> , <a href="tel:' . $internationalsalesTeamMember["phone2"] . '">' . $internationalsalesTeamMember["phone"] . '</a></div>';
      }

      if (!empty($internationalsalesTeamMember["email"]) && !empty($internationalsalesTeamMember["email2"])) {
        $html .= '<div class="team-email"> <strong>Email:</strong> <a href="mailto:' . $internationalsalesTeamMember["email"] . '">' . $internationalsalesTeamMember["email"] . '</a> , <a href="mailto:' . $internationalsalesTeamMember["email2"] . '">' . $internationalsalesTeamMember["email2"] . '</a></div>';
      }

      $html .= '</div>';
      if (isset($internationalsalesTeamMember["sales_reprensentative"]) && !empty($internationalsalesTeamMember["sales_reprensentative"])) {
        $html .= '<div class="international-team-sales-reprensentative">';
        foreach ($internationalsalesTeamMember["sales_reprensentative"] as $sales_reprensentative) {
          $html .= "<div>";
          if (!empty($sales_reprensentative["name"][0]["value"])) {
            $html .= '<strong>Sales Representative:</strong> <span class="sales-reprensentative-name">' . $sales_reprensentative["name"][0]["value"] . ' </span>';
          }
          if (!empty($sales_reprensentative["details"][0]["value"])) {
            $html .= ' <span class="sales-reprensentative-name">' . $sales_reprensentative["details"][0]["value"] . ' </span>';
          }
          $html .= "</div>";
        }
        $html .= '</div>';
      }

      if (isset($internationalsalesTeamMember['custom_territory']) && !empty($internationalsalesTeamMember['custom_territory'])) {
        $html .= '<div class="international-team-territory"><strong>Territory:</strong> ' . $internationalsalesTeamMember['custom_territory'] . '</div>';
      }

      $html .= '</div></div></div></div>';
    }
    $html .= '</div></div></div>';
    return $html;
  }

  /**
   * Builds the response.
   */
  public function build() {
    $data = '<div class="sales-team-content">';
    $data .= $this->fetchSalesTeamStructure();
    $data .= $this->fetchInternationalTeamStructure();
    $data .= '</div>';

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $data,
    ];

    return $build;
  }

}
