(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.MyModuleBehavior = {
    attach: function (context, settings) {
      /* Sales Team Zones */
      var zones = drupalSettings.salesTeamZones;
      var sale_team = Object.values(zones);
      var i = 0;
      while (i < sale_team.length) {
        jQuery(".usa-canada-map path,.usa-canada-map g").each(function () {
          if (sale_team[i][0]) {
            var n = sale_team[i][0].includes(this.id);
            if (n) {
              jQuery(this).attr("style", "fill:" + sale_team[i]["color"] + ";");
            }
          }
        });
        i = i + 1;
      }
      /* End of Sales Team Zones */

      /* International Sales Team Zones */
      international_zones = drupalSettings.InternationalTeamzone;
      var international_sale_team = Object.values(international_zones);
      var i = 0;
      while (i < international_sale_team.length) {
        jQuery(".world-map path").each(function () {
          if (international_sale_team[i][0]) {
            var n = international_sale_team[i][0].includes(this.id);
            if (n) {
              jQuery(this).attr(
                "style",
                "fill:" + international_sale_team[i]["color"] + ";"
              );
            }
          }
        });
        i = i + 1;
      }

      /* End of International Sales Team Zones */
    },
  };
})(jQuery, Drupal, drupalSettings);

jQuery(document).on("click", ".worldwide-map-menu", function (e) {
  jQuery(".usa-canada-map").hide();
  jQuery(".sales-team-member-content").hide();
  jQuery(".world-map").show();
  jQuery(".international-sales-team-content").show();
  jQuery(".international-team-member").show();
  jQuery(".row").removeClass("current-member");
  jQuery(".view-id-sales_team_bottom_block.view-display-id-block_1").hide();
  jQuery(".view-id-sales_team_bottom_block.view-display-id-block_2").show();

  /* World Map Zoom in Zoomout */

  var panZoomMap = svgPanZoom(".world-map svg", {
    controlIconsEnabled: true,
  });
  var svgElement = document.querySelector(".world-map svg");
  var panZoomMap = svgPanZoom(".world-map svg", {
    viewportSelector: ".panzoom",
    panEnabled: true,
    controlIconsEnabled: true,
    zoomEnabled: true,
    dblClickZoomEnabled: true,
    mouseWheelZoomEnabled: false,
    preventMouseEventsDefault: true,
    zoomScaleSensitivity: 0.2,
    minZoom: 0.5,
    maxZoom: 6,
    fit: true,
    contain: false,
    center: true,
    refreshRate: "auto",
    startTransform: "scale(1.0)",
    beforeZoom: function () {},
    onZoom: function () {},
    beforePan: function () {},
    onPan: function () {},
    customEventsHandler: {},
    eventsListenerElement: null,
  });  
});

jQuery(".team-member").each(function () {
  var flag = jQuery(this).find(".zone-info").length;
  if (flag == 1) {
    jQuery(this).find(".member-color-box").hide();
    jQuery(this).find(".zone-info p").css("float", "left");
  }
});

jQuery("path").each(function () {
  jQuery(this).bind("click touchstart", function () {
    var path_id = jQuery(this).attr("id");
    jQuery(".team-member").each(function () {
      var data = jQuery(this).attr("data").split(", ");
      if (jQuery.inArray(path_id, data) !== -1) {
        jQuery(this).attr("style", "display:block");
        jQuery(this).closest(".row").addClass("current-member");
        if (jQuery(this).find(".member-color-box").length == 1) {
          jQuery(this).find(".member-color-box").hide();
          jQuery(this).find(".zone-info p").css("float", "left");
        }
      } else {
        jQuery(this).attr("style", "display:none");
        jQuery(this).closest(".row").removeClass("current-member");
        jQuery(this).find(".member-color-box").show();
        // jQuery(this).find(".color-box-section").show();
        jQuery(this).find(".zone-info p").css("float", "right");
      }
    });
    jQuery(".international-team-member").each(function () {
      var data = jQuery(this).attr("data").split(", ");
      if (jQuery.inArray(path_id, data) !== -1) {
        jQuery(this).attr("style", "display:block");
        jQuery(this).closest(".row").addClass("current-member");
      } else {
        jQuery(this).attr("style", "display:none");
        jQuery(this).closest(".row").removeClass("current-member");
      }
    });
  });
});
jQuery(".usa-canada-map g").each(function () {
  jQuery(this).bind("click", function () {
    var g_id = jQuery(this).attr("id");
    jQuery(".team-member").each(function () {
      var data = jQuery(this).attr("data").split(", ");
      if (jQuery.inArray(g_id, data) !== -1) {
        jQuery(this).attr("style", "display:block");
        jQuery(this).closest(".row").addClass("current-member");
        if (jQuery(this).find(".member-color-box").length == 1) {
          jQuery(this).find(".member-color-box").hide();
          // jQuery(this).find(".color-box-section").hide();
          jQuery(this).find(".zone-info p").css("float", "left");
        }
      } else {
        jQuery(this).attr("style", "display:none");
        jQuery(this).closest(".row").removeClass("current-member");
        jQuery(this).find(".member-color-box").show();
        jQuery(this).find(".zone-info p").css("float", "right");
      }
    });
  });
});
/*
 * Below function is used to show member info on Click.
 */
var saleswidth = jQuery(window).width();


jQuery(".usa-canada-map path,.usa-canada-map g").each(function () {
  jQuery(this).bind("click", function (e) {
    jQuery("html,body").animate(
      {
        scrollTop: jQuery(".sales-team-content").offset().top - 200,
      },
      "slow"
    );
    var tooltip = document.getElementById("tooltipusa");
    var countryId = jQuery(this).attr("id");
    var x = e.originalEvent.layerX;
    var y = e.originalEvent.layerY;
    var height = (y * 100) / jQuery(".usa-canada-map svg").height();
    var width = (x * 100) / jQuery(".usa-canada-map svg").width();
    tooltip.style.display = "block";
    if((width>66) && (saleswidth < 990)){
      tooltip.style.left = "45%";
    }else{
      tooltip.style.left = width - 10 + "%";
    }
    tooltip.style.top = height + "%";
    tooltip.classList.add("active");
    jQuery(".team-member").each(function () {
      var data = jQuery(this).attr("data").split(", ");
      if (jQuery.inArray(countryId, data) !== -1) {
        tooltip.innerHTML =
          '<div class="member-info">' +
          jQuery(this).find(".member-detail").html() +
          '<div class="close-icon-member"><img src="/themes/custom/allen/images/close-icon.png" alt="close"/></div></div>';
      }
      tooltip.classList.add("active");
    });
  });
  if (saleswidth > 990) {
    jQuery(this)
      .mousemove(function (e) {
        var tooltip = document.getElementById("usanainfo");
        var countryId = jQuery(this).attr("id");
        var x = e.clientX;
        var y = e.clientY;
        tooltip.style.left = x + 20 + "px";
        tooltip.style.top = y - 20 + "px";
        tooltip.classList.add("active");
        jQuery(".team-member").each(function () {
          var data = jQuery(this).attr("data").split(", ");
          if (jQuery.inArray(countryId, data) !== -1) {
            tooltip.innerHTML =
              '<div class="click-member-info">' +
              jQuery(this).find(".team-title").html() +
              "<br><span>click for info</span></div>";
          }
          tooltip.classList.add("active");
        });
      })
      .mouseleave(function () {
        var tooltip = document.getElementById("usanainfo");
        tooltip.classList.remove("active");
      });
  }
});

jQuery(document).on("click", ".close-icon-member", function () {
  jQuery("#tooltipusa").hide();
  jQuery("#tooltip").hide();
});

/* Map Show hide */
jQuery(".world-map").hide();
jQuery(".international-sales-team-content").hide();
jQuery(".view-id-sales_team_bottom_block.view-display-id-block_2").hide();
jQuery(document).on("click", ".usa-canada-menu", function (e) {
  jQuery(".world-map").hide();
  jQuery(".international-sales-team-content").hide();
  jQuery(".usa-canada-map").show();
  jQuery(".sales-team-member-content").show();
  jQuery(".team-member").show();
  jQuery(".row").removeClass("current-member");
  jQuery(".view-id-sales_team_bottom_block.view-display-id-block_2").hide();
  jQuery(".view-id-sales_team_bottom_block.view-display-id-block_1").show();
});

jQuery(".sales-team-map-menu div").click(function () {
  jQuery(".sales-team-map-menu div").removeClass("sales-active");
  jQuery(this).addClass("sales-active");
});

var isFirefox = typeof InstallTrigger !== 'undefined';
jQuery(".world-map path").each(function () {
  var tooltip = document.getElementById("tooltip");
  jQuery(this).bind("click touchstart", function (e) {
    var countryId = jQuery(this).attr("id");
    var flag = jQuery(this).attr("style");
    var data2 = ["US", "CA"];
    if (typeof flag !== "undefined") {
      if (jQuery.inArray(countryId, data2) == -1) {
        jQuery("html,body").animate(
          {
            scrollTop:
              jQuery(".international-sales-team-content").offset().top - 200,
          },
          "slow"
        );
      }
    } else {
      jQuery("html,body").animate(
        {
          scrollTop:
            jQuery(
              "#block-views-block-sales-team-bottom-block-block-2"
            ).offset().top - 200,
        },
        "slow"
      );
    }
    var x = e.originalEvent.layerX;
    var y = e.originalEvent.layerY;
    var height = (y * 100) / jQuery(".world-map svg").height();
    var width = (x * 100) / jQuery(".world-map svg").width();
    if((saleswidth < 990) || (isFirefox)){
      tooltip.style.left = "46%";
      tooltip.style.top =  "50%";
    } else {
      tooltip.style.left = width + "%";
      tooltip.style.top = height + "%";
    }
    tooltip.style.display = "block";
    tooltip.classList.add("active");
    if (typeof flag !== "undefined") {
      jQuery(".international-team-member").each(function () {
        var data = jQuery(this).attr("data").split(", ");
        if (jQuery.inArray(countryId, data) !== -1) {
          var teamPhone = "",
            teamEmail = "";
          if (jQuery(this).find(".international-team-phone").length > 0) {
            teamPhone = jQuery(this).find(".international-team-phone").html();
          }
          if (jQuery(this).find(".international-team-email").length > 0) {
            teamEmail = jQuery(this).find(".international-team-email").html();

          }
          tooltip.innerHTML =
            '<div class="member-info">' +
            jQuery(this).find(".international-team-title").html() +
            teamPhone +
            teamEmail +
            '<div class="close-icon-member"><img src="/themes/custom/allen/images/close-icon.png" alt="close"/></div></div>';
        } else {
          var data2 = ["US", "CA"];
          if (jQuery.inArray(countryId, data2) !== -1) {
            tooltip.innerHTML =
              '<div class="click-member-info north-america">North America<br><span class="america-info">Click the North America button above for North America sales territories</span><div class="close-icon-member"><img src="/themes/custom/allen/images/close-icon.png" alt="close"/></div></div>';
            jQuery(".international-sales-team-content").show();
            jQuery(".international-team-member").show();
          }
        }
        tooltip.classList.add("active");
      });
    } else {
      tooltip.innerHTML =
        '<div class="member-info">Joey Ward<br>' +
        jQuery("#international-sales-team").find(".sales_email").html() +
        jQuery("#international-sales-team").find(".sales_phone_number").html() +
        '<div class="close-icon-member"><img src="/themes/custom/allen/images/close-icon.png" alt="close"/></div></div>';
      jQuery(".international-sales-team-content").show();
      jQuery(".international-team-member").show();
    }
  });
  if (saleswidth > 990) {
    jQuery(this)
      .mousemove(function (e) {
        var tooltip = document.getElementById("worldmapinfo");
        var countryId = jQuery(this).attr("id");
        var flag = jQuery(this).attr("style");
        var x = e.clientX;
        var y = e.clientY;
        tooltip.style.left = x + 20 + "px";
        tooltip.style.top = y - 20 + "px";
        tooltip.classList.add("active");
        if (typeof flag !== "undefined") {
          jQuery(".international-team-member").each(function () {
            var data = jQuery(this).attr("data").split(", ");
            if (jQuery.inArray(countryId, data) !== -1) {
              tooltip.innerHTML =
                '<div class="click-member-info">' +
                jQuery(this).find(".international-team-title").html() +
                "<br><span>click for info</span></div>";
            } else {
              var data2 = ["US", "CA"];
              if (jQuery.inArray(countryId, data2) !== -1) {
                tooltip.innerHTML =
                  '<div class="click-member-info">North America<br><span>click for info</span></div>';
              }
            }
            tooltip.classList.add("active");
          });
        } else {
          tooltip.innerHTML =
            '<div class="click-member-info">Joey Ward<br><span>click for info</span></div>';
        }
      })
      .mouseleave(function () {
        var tooltip = document.getElementById("worldmapinfo");
        tooltip.classList.remove("active");
      });
  }
});