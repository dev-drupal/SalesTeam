The Sales Team module is used to render the page with SVG Map and it's sales team member details. The sales teams members can be mapped with dynamic map Zones. The SVG Map zones are created with Drupal Taxonomy to manage the territory states and SVG Map color dynamically. 

1) Sales Team Custom service:
>> We have created this service to fetch all Sales Team member details available in the sales team content type and to fetch All zones States created by Admin using Drupal Taxonomy.

2) Sales Team Controller:
>> We have used the Drupal controller to render SVG Map North America and World with dynamic zone color provided in Taxonomy configuration.
>> We have used the Sales Team custom service to fetch data in controller. Once data is fetched, it will rendered the page with custom theming.

Initial Requirements of module are as below:
1) Sales Team Member details should be update by admin and will reflect on sales Team Page
2) Admin should change Zone States and Color. It should recreate the SVG Map with updated zone states.
3) Admin should update zone associate Members.

Page: https://www.alleneng.com/sales-team
