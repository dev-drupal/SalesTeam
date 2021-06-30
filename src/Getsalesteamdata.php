<?php

namespace Drupal\sales_team;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;

/**
 * FETCH SALES TEAM CONTENT SERVICE.
 */
class Getsalesteamdata {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityManager;

  /**
   * Constructs a new BranchLocationService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityManager = $entityTypeManager;
  }

  /**
   * Fetch Sales Team Zone Information.
   */
  public function getSalesTeamzone() {
    $custom_zone = $zone_list = [];
    $vid = 'sales_team_state';
    $terms = $this->entityManager->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_obj = $this->entityManager->getStorage('taxonomy_term')->load($term->tid);
      if (!empty($term_obj) && isset($term_obj)) {
        $canada_zone = $term_obj->get('field_canada_state2')->getValue();
        foreach ($canada_zone as $zone) {
          $zone_list[$term->name][] = "CA-" . $zone['value'];
        }
        $usa_zone = $term_obj->get('field_usa_state2')->getValue();
        foreach ($usa_zone as $zone) {
          $zone_list[$term->name][] = "US-" . $zone['value'];
        }
        $custom_zone[$term->name][] = implode(', ', $zone_list[$term->name]);
        $custom_zone[$term->name]['color'] = $term_obj->get('field_color_code')->value;
      }
    }
    return $custom_zone;
  }

  /**
   * Gets node data.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Sales node.
   *
   * @return string
   *   String of SalesTeamEmail
   */
  public function getSalesTeamEmail(NodeInterface $node) {
    return $node->get('field_email')->value;
  }

  /**
   * Fetch Sales Team Zone Information with respect to email.
   */
  public function getSalesTeamZoneForEmail($email_flag) {
    $vid = 'sales_team_state';
    $email = "";
    $terms = $this->entityManager->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_obj = $this->entityManager->getStorage('taxonomy_term')->load($term->tid);
      $zone_conditon = [
        'type' => 'sales_team',
        'status' => 1,
        'field_show_in_bottom' => 0,
        'field_assigned_sales_zone' => $term->tid,
      ];
      if (!empty($term_obj) && isset($term_obj)) {
        $canada_zone = $term_obj->get('field_canada_state2')->getValue();
        foreach ($canada_zone as $zone) {
          if ($zone['value'] == $email_flag) {
            $nodes = $this->entityManager->getStorage('node')
              ->loadByProperties($zone_conditon);
            if (isset($nodes) && !empty($nodes)) {
              foreach ($nodes as $node) {
                $email = $this->getSalesTeamEmail($node);
                return $email;
              }
            }
          }
        }
        $usa_zone = $term_obj->get('field_usa_state2')->getValue();
        foreach ($usa_zone as $zone) {
          if ($zone['value'] == $email_flag) {
            $nodes = $this->entityManager->getStorage('node')
              ->loadByProperties([
                'type' => 'sales_team',
                'status' => 1,
                'field_show_in_bottom' => 0,
                'field_assigned_sales_zone' => $term->tid,
              ]);
            if (isset($nodes) && !empty($nodes)) {
              foreach ($nodes as $node) {
                $email = $this->getSalesTeamEmail($node);
                return $email;
              }
            }
          }
        }
      }
    }
    return $email;
  }

  /**
   * Fetch Internation Sales Team Information.
   */
  public function getInternationalTeamzone() {
    $custom_zone = [];
    $vid = 'sales_team_country';
    $terms = $this->entityManager->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_obj = $this->entityManager->getStorage('taxonomy_term')->load($term->tid);
      if (!empty($term_obj) && isset($term_obj)) {
        $international_team_zone = $term_obj->get('field_territory_list')->getValue();
        foreach ($international_team_zone as $zone) {
          $zone_list[$term->name][] = $zone['value'];
        }
        $custom_zone[$term->name][] = implode(', ', $zone_list[$term->name]);
        $custom_zone[$term->name]['color'] = $term_obj->get('field_color_code')->value;
      }
    }
    return $custom_zone;
  }

  /**
   * Fetch Sales Team Content to render at Bottom of Page.
   */
  public function getSalesNode() {
    $data = [];
    $condition = [
      'type' => 'sales_team',
      'status' => 1,
      'field_show_in_bottom' => 0,
    ];
    $nodes = $this->entityManager->getStorage('node')
      ->loadByProperties($condition);
    foreach ($nodes as $node) {
      $data[] = $this->getNodeData($node);
    }
    return $data;
  }

  /**
   * Gets node data.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Sales node.
   *
   * @return array
   *   Array of node data.
   */
  public function getNodeData(NodeInterface $node) {
    $sales_state = NULL;
    $image = [];
    if (!$node->get('field_member_photo')->isEmpty()) {
      $image['uri'] = $node->get('field_member_photo')->entity->getFileUri();
      $image_attr = $node->get('field_member_photo')->getValue();
      $image['alt'] = $image_attr[0]['alt'];
      $image['title'] = $image_attr[0]['title'];
    }

    if (!$node->get('field_assigned_sales_zone')->isEmpty) {
      $sales_zone = $node->get('field_assigned_sales_zone')->referencedEntities();
      $i = 0;
      foreach ($sales_zone as $zone_state) {
        $canada_lable_list = $zone_state->get('field_canada_state2')->getFieldDefinition()->getSetting('allowed_values');
        $usa_lable_list = $zone_state->get('field_usa_state2')->getFieldDefinition()->getSetting('allowed_values');
        $canada_zone = $zone_state->get('field_canada_state2')->getValue();

        foreach ($canada_zone as $zone) {
          $sales_state[$i]['canada_label_state'][] = $canada_lable_list[$zone['value']];
          $sales_state[$i]['canada_state'][] = $zone['value'];
        }

        $usa_zone = $zone_state->get('field_usa_state2')->getValue();
        foreach ($usa_zone as $zone) {
          $sales_state[$i]['usa_label_state'][] = $usa_lable_list[$zone['value']];
          $sales_state[$i]['usa_state'][] = $zone['value'];
        }

        $color_code[$i][] = $zone_state->get('field_color_code')->getValue();
        $i++;
      }
    }

    $url = $node->toUrl()->toString(TRUE);

    return [
      'id' => $node->get('nid')->value,
      'url' => $url->getGeneratedUrl(),
      'title' => $node->get('title')->value,
      'email' => $node->get('field_email')->value,
      'phone' => $node->get('field_phone_number')->value,
      'website' => $node->get('field_website')->value,
      'selected_states' => $sales_state,
      'color_code' => $color_code,
      'image' => isset($image['uri']) ? $image['uri'] : NULL,
      'alt' => isset($image['alt']) ? $image['alt'] : '',
      'image_title' => isset($image['title']) ? $image['title'] : '',
    ];
  }

  /**
   * Code for International Sales team.
   */
  public function getInternationalSalesTeamNode() {
    $data = [];
    $nodes = $this->entityManager->getStorage('node')
      ->loadByProperties([
        'type' => 'international_sales_team',
        'status' => 1,
        'field_show_in_bottom' => 0,
      ]);
    foreach ($nodes as $node) {
      $data[] = $this->getNodeInternationalSalesTeam($node);
    }
    return $data;
  }

  /**
   * Gets node data.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Sales node.
   *
   * @return array
   *   Array of Internation Team node data.
   */
  public function getNodeInternationalSalesTeam(NodeInterface $node) {
    $country = $label = $color_code = [];
    $image = [];
    if (!$node->get('field_member_photo')->isEmpty()) {
      $image['uri'] = $node->get('field_member_photo')->entity->getFileUri();
      $image_attr = $node->get('field_member_photo')->getValue();
      $image['alt'] = $image_attr[0]['alt'];
      $image['title'] = $image_attr[0]['title'];
    }

    $logo_image = [];
    if (!$node->get('field_dealer_logo')->isEmpty()) {
      $logo_image['uri'] = $node->get('field_dealer_logo')->entity->getFileUri();
      $logo_image_attr = $node->get('field_dealer_logo')->getValue();
      $logo_image['alt'] = $logo_image_attr[0]['alt'];
      $logo_image['title'] = $logo_image_attr[0]['title'];
    }

    $country_data = $node->get('field_zone')->referencedEntities();
    foreach ($country_data as $value) {
      $country_code = $value->get('field_territory_list')->getValue();
      $label = $value->get('field_territory_list')->getFieldDefinition()->getSetting('allowed_values');
      for ($i = 0; $i < count($country_code); $i++) {
        $country[$i]['code'] = $country_code[$i];
        $country[$i]['name'] = $label[$country_code[$i]['value']];
      }
      $color_code[] = $value->get('field_color_code')->getValue();
    }

    $category = $node->get('field_category')->referencedEntities();
    foreach ($category as $value) {
      $category_name = $value->get('name')->getValue();
    }
    $url = $node->toUrl()->toString(TRUE);
    $paragraph_reprensentative = $node->field_reprensentative->getValue();

    for ($i = 0; $i < count($paragraph_reprensentative); $i++) {
      $sales_reprensentative_data = Paragraph::load($paragraph_reprensentative[$i]['target_id']);
      $sales_reprensentative[$i]['name'] = $sales_reprensentative_data->field_name->getValue();
      $sales_reprensentative[$i]['details'] = $sales_reprensentative_data->field_details->getValue();
    }

    return [
      'id' => $node->get('nid')->value,
      'url' => $url->getGeneratedUrl(),
      'title' => $node->get('title')->value,
      'body' => $node->get('body')->value,
      'category' => $category_name,
      'sales_reprensentative' => $sales_reprensentative,
      'email' => $node->get('field_member_email')->value,
      'phone' => $node->get('field_zone_phone_number')->value,
      'email2' => $node->get('field_email_2')->value,
      'phone2' => $node->get('field_phone_2')->value,
      'website' => $node->get('field_website_link')->value,
      'selected_country' => $country,
      'custom_territory' => $node->get('field_territory')->value,
      'color_code' => $color_code,
      'image' => isset($image['uri']) ? $image['uri'] : NULL,
      'logo_image' => isset($logo_image['uri']) ? $logo_image['uri'] : NULL,
      'alt' => isset($image['alt']) ? $image['alt'] : '',
      'image_title' => isset($image['title']) ? $image['title'] : '',
    ];
  }

  /**
   * Retrive Term From URL.
   *
   * @param string $term_name
   *   Product Category Term Name.
   */
  public function getProductCategory($term_name) {
    $term_id = NULL;
    $vid = 'product_category';
    $terms = $this->entityManager->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      if ($term_name === $term->name) {
        $term_id = $term->tid;
      }
    }
    return $term_id;
  }

}
