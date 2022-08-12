<div id="ags-xoiwc-settings-container">
    <div id="ags-xoiwc-settings">
        <div id="ags-xoiwc-settings-header">
            <div class="ags-xoiwc-settings-logo">
                <h3>Export Order Items</h3>
            </div>
            <div id="ags-xoiwc-settings-header-links">
                <a id="ags-xoiwc-settings-header-link-review" href="https://wordpress.org/plugins/export-order-items-for-woocommerce/#reviews"
                   target="_blank"><?php echo esc_html__('Leave Us A Review', 'export-order-items-for-woocommerce') ?></a>
                <a id="ags-xoiwc-settings-header-link-upgrade" href="https://aspengrovestudios.com/product/export-order-items-pro-for-woocommerce/?utm_source=export-order-items-for-woocommerce&amp;utm_medium=link&amp;utm_campaign=wp-plugin-upgrade-link"
                   target="_blank"><?php echo esc_html__('Upgrade To Pro', 'export-order-items-for-woocommerce') ?></a>
            </div>
        </div>

        <?php
        // Check for WooCommerce
        if (!class_exists('WooCommerce')) {
            echo('<div id="ags-xoiwc-settings-tabs-content"><p class="ags-xoiwc-notification ags-xoiwc-notification-warning">' . esc_html__('This plugin requires that WooCommerce is installed and activated.', 'export-order-items-for-woocommerce') . '</p></div></div></div>');
            return;
        } else if (!function_exists('wc_get_order_types')) {
            echo('<div id="ags-xoiwc-settings-tabs-content"><p class="ags-xoiwc-notification ags-xoiwc-notification-warning">' . esc_html__('The Export Order Items plugin requires WooCommerce 2.2 or higher. Please update your WooCommerce install.', 'export-order-items-for-woocommerce') . '</p></div></div></div>');
            return;
        }
        ?>

        <ul id="ags-xoiwc-settings-tabs">
            <li class="ags-xoiwc-settings-active"><a href="#settings"><?php esc_html_e('Report Settings', 'export-order-items-for-woocommerce'); ?></a></li>
            <li><a href="#addons"><?php esc_html_e('Addons', 'export-order-items-for-woocommerce'); ?></a></li>
        </ul>

        <div id="ags-xoiwc-settings-tabs-content">
            <?php
            // Print form
            echo('
            <div id="ags-xoiwc-settings-settings" class="ags-xoiwc-settings-active"> 
             <div class="ags-xoiwc-settings-left-area">
                    <form action="" method="post">
                        <input type="hidden" name="hm_xoiwc_do_export" value="1" />
                ');
            wp_nonce_field('hm_xoiwc_do_export');
            echo('
			    <div class="ags-xoiwc-settings-box">
                    <label for="hm_xoiwc_field_report_time">
						<span class="label">' . esc_html__('Report Period', 'export-order-items-for-woocommerce') . ':</span>
						<select name="report_time" id="hm_xoiwc_field_report_time">
							<option value="0d"' . ($reportSettings['report_time'] == '0d' ? ' selected="selected"' : '') . '>' . esc_html__('Today', 'export-order-items-for-woocommerce') . '</option>
							<option value="1d"' . ($reportSettings['report_time'] == '1d' ? ' selected="selected"' : '') . '>' . esc_html__('Yesterday', 'export-order-items-for-woocommerce') . '</option>
							<option value="7d"' . ($reportSettings['report_time'] == '7d' ? ' selected="selected"' : '') . '>' . esc_html__('Previous 7 days (excluding today)', 'export-order-items-for-woocommerce') . '</option>
							<option value="30d"' . ($reportSettings['report_time'] == '30d' ? ' selected="selected"' : '') . '>' . esc_html__('Previous 30 days (excluding today)', 'export-order-items-for-woocommerce') . '</option>
							<option value="0cm"' . ($reportSettings['report_time'] == '0cm' ? ' selected="selected"' : '') . '>' . esc_html__('Current calendar month', 'export-order-items-for-woocommerce') . '</option>
							<option value="1cm"' . ($reportSettings['report_time'] == '1cm' ? ' selected="selected"' : '') . '>' . esc_html__('Previous calendar month', 'export-order-items-for-woocommerce') . '</option>
							<option value="+7d"' . ($reportSettings['report_time'] == '+7d' ? ' selected="selected"' : '') . '>' . esc_html__('Next 7 days (future dated orders)', 'export-order-items-for-woocommerce') . '</option>
							<option value="+30d"' . ($reportSettings['report_time'] == '+30d' ? ' selected="selected"' : '') . '>' . esc_html__('Next 30 days (future dated orders)', 'export-order-items-for-woocommerce') . '</option>
							<option value="+1cm"' . ($reportSettings['report_time'] == '+1cm' ? ' selected="selected"' : '') . '>' . esc_html__('Next calendar month (future dated orders)', 'export-order-items-for-woocommerce') . '</option>
							<option value="all"' . ($reportSettings['report_time'] == 'all' ? ' selected="selected"' : '') . '>' . esc_html__('All time', 'export-order-items-for-woocommerce') . '</option>
							<option value="custom"' . ($reportSettings['report_time'] == 'custom' ? ' selected="selected"' : '') . '>' . esc_html__('Custom date range', 'export-order-items-for-woocommerce') . '</option>
						</select>
				   </label>
                 </div>
				
				<div class="ags-xoiwc-settings-box hm_xoiwc_custom_time">
                    <div class="ags-xoiwc-settings-multirow">
                        <label for="hm_xoiwc_field_report_start" class="ags-xoiwc-settings-title">
                           <span class="label">' . esc_html__('Start Date', 'export-order-items-for-woocommerce') . ':</span>
                         </label>
                        <div class="ags-xoiwc-settings-content">
                            <input type="date" name="report_start" id="hm_xoiwc_field_report_start" value="' . $reportSettings['report_start'] . '" />
                        </div>
                    </div>
                </div>
                    
                <div class="ags-xoiwc-settings-box hm_xoiwc_custom_time">
                    <div class="ags-xoiwc-settings-multirow">
                        <label for="hm_xoiwc_field_report_end" class="ags-xoiwc-settings-title">
                           <span class="label">' . esc_html__('End Date', 'export-order-items-for-woocommerce') . ':</span>
                         </label>
                        <div class="ags-xoiwc-settings-content">
                            <input type="date" name="report_end" id="hm_xoiwc_field_report_end" value="' . $reportSettings['report_end'] . '" />
                        </div>
                    </div>
                </div>
                
                <div class="ags-xoiwc-settings-box">
                    <div class="ags-xoiwc-settings-multirow">
                        <label for="hm_xoiwc_field_orderby" class="ags-xoiwc-settings-title">
                           <span class="label">' . esc_html__('Sort By', 'export-order-items-for-woocommerce') . ':</span>
                         </label>
                        <div class="ags-xoiwc-settings-content">
                            <select name="orderby" id="hm_xoiwc_field_orderby">
                                <option value="product_id"' . ($reportSettings['orderby'] == 'product_id' ? ' selected="selected"' : '') . '>' . esc_html__('Product ID', 'export-order-items-for-woocommerce') . '</option>
                                <option value="order_id"' . ($reportSettings['orderby'] == 'order_id' ? ' selected="selected"' : '') . '>' . esc_html__('Order ID', 'export-order-items-for-woocommerce') . '</option>
                            </select>
                            <select name="orderdir" id="hm_xoiwc_field_orderdir">
                                <option value="asc"' . ($reportSettings['orderdir'] == 'asc' ? ' selected="selected"' : '') . '>' . esc_html__('Ascending', 'export-order-items-for-woocommerce') . '</option>
                                <option value="desc"' . ($reportSettings['orderdir'] == 'desc' ? ' selected="selected"' : '') . '>' . esc_html__('Descending', 'export-order-items-for-woocommerce') . '</option>
                            </select>
			           </div>
                    </div>
                </div>
                
              <div class="ags-xoiwc-settings-box">
                <div class="ags-xoiwc-settings-cb-list">
                    <label  class="ags-xoiwc-settings-title">
                       <span class="label">' . esc_html__('Show Orders With Status', 'export-order-items-for-woocommerce') . ':</span>
                     </label>
                    <div class="ags-xoiwc-settings-content">
              ');
            foreach (wc_get_order_statuses() as $status => $statusName) {
                echo('<label class="ags-xoiwc-settings-cb-list-item"><input type="checkbox" name="order_statuses[]"' . (in_array($status, $reportSettings['order_statuses']) ? ' checked="checked"' : '') . ' value="' . $status . '" /> ' . $statusName . '</label>');
            }
            echo('</div>
                </div>
            </div>
				
            <div class="ags-xoiwc-settings-box">
                <div class="ags-xoiwc-settings-cb-list">
                    <label class="ags-xoiwc-settings-title">
                        <span class="label">' . esc_html__('Report Fields', 'export-order-items-for-woocommerce') . ':</span>
                    </label>
                    <div id="hm_xoiwc_report_field_selection" class="ags-xoiwc-settings-content">');
            $fieldOptions2 = $fieldOptions;
            foreach ($reportSettings['fields'] as $fieldId) {
                if (!isset($fieldOptions2[$fieldId]))
                    continue;
                echo('<label class="ags-xoiwc-settings-cb-list-item"><input type="checkbox" name="fields[]" checked="checked" value="' . $fieldId . '"' . (in_array($fieldId, array('variation_id', 'variation_attributes')) ? ' class="variation-field"' : '') . ' /> ' . $fieldOptions2[$fieldId] . '</label>');
                unset($fieldOptions2[$fieldId]);
            }
            foreach ($fieldOptions2 as $fieldId => $fieldDisplay) {
                echo('<label class="ags-xoiwc-settings-cb-list-item"><input type="checkbox" name="fields[]" value="' . $fieldId . '"' . (in_array($fieldId, array('variation_id', 'variation_attributes')) ? ' class="variation-field"' : '') . ' /> ' . $fieldDisplay . '</label>');
            }
            unset($fieldOptions2);
            echo('</div>
                </div>
            </div>
               
           <div class="ags-xoiwc-settings-box">
                <label>
                    <span class="label">' . esc_html__('Include header row', 'export-order-items-for-woocommerce') . '</span>
                    <input type="checkbox" name="include_header"' . (empty($reportSettings['include_header']) ? '' : ' checked="checked"') . ' />
                </label> 
            </div> 
                
            <p class="submit">
                <button type="submit" class="ags-xoiwc-button-primary" >' . esc_html__('Export', 'export-order-items-for-woocommerce') . '</button>
            </p>
        </form> 
     </div> <!-- ags-xoiwc-settings-left-area -->

    <div class="ags-xoiwc-settings-sidebar">
     
        <div class="ags-xoiwc-widget">
            <img src=" ' . plugins_url('images/export_order_items_pro.png', __FILE__) . ' " alt="Export Order Items Pro for WooCommerce" class="widget-thumb"/>
            <div class="inside">
                <h2>' . esc_html__('Upgrade to Pro', 'export-order-items-for-woocommerce') . '</h2>
                <p><strong>' . sprintf(esc_html__('Upgrade to %sExport Order Items Pro%s for the following additional features:', 'export-order-items-for-woocommerce'), '<a href="https://aspengrovestudios.com/product/export-order-items-pro-for-woocommerce/?utm_source=export-order-items-for-woocommerce&amp;utm_medium=link&amp;utm_campaign=wp-plugin-upgrade-link" target="_blank">', '</a>') . '</strong></p>
                <ul style="list-style-type: disc; padding-left: 1.5em;">
                    <li>' . esc_html__('Create multiple export presets to save time.', 'export-order-items-for-woocommerce') . '</li>
                    <li>' . esc_html__('Include product variation details.', 'export-order-items-for-woocommerce') . '</li>
                    <li>' . esc_html__('Include any custom field associated with an order, product order line item, product, or product variation.', 'export-order-items-for-woocommerce') . '</li>
                    <li>' . esc_html__('Limit the export to only include certain product IDs or product categories.', 'export-order-items-for-woocommerce') . '</li>
                    <li>' . esc_html__('Change the names and order of fields in the report.', 'export-order-items-for-woocommerce') . '</li>
                    <li>' . esc_html__('Export in XLS, XLSX, or HTML format (in addition to CSV).', 'export-order-items-for-woocommerce') . '</li>
                </ul>
                <p>' . sprintf(esc_html__('%sReceive a %s discount with the coupon code %sWCEXPORT10%s!%s (Not valid with any other discount)', 'export-order-items-for-woocommerce'), '<strong>', '10%', '<span style="color: #CC4A49;">', '</span>', '</strong>') . '</p>
                <a href="https://aspengrovestudios.com/product/export-order-items-pro-for-woocommerce/?utm_source=export-order-items-for-woocommerce&amp;utm_medium=link&amp;utm_campaign=wp-plugin-upgrade-link" target="_blank" class="ags-xoiwc-button-secondary">' . esc_html__('Buy Now', 'export-order-items-for-woocommerce') . '</a>
            </div>
        </div>

        <div class="ags-xoiwc-widget">
            <img src=" ' . plugins_url('images/scheduled_email_reports_for_woocommerce.png', __FILE__) . ' " alt="Scheduled Email Reports for WooCommerce" class="widget-thumb"/>
            <div class="inside">
                <h2>' . esc_html__('Schedule Email Reports', 'export-order-items-for-woocommerce') . '</h2>
                <p>' . esc_html__('Automatically send reports as email attachments on a recurring schedule.', 'export-order-items-for-woocommerce') . '</p>
                <a href="https://aspengrovestudios.com/product/scheduled-email-reports-for-woocommerce/?utm_source=export-order-items&amp;utm_medium=link&amp;utm_campaign=wp-plugin-upgrade-link" target="_blank" class="ags-xoiwc-button-secondary">' . esc_html__('Get the add-on plugin', 'export-order-items-for-woocommerce') . '</a>
            </div>
        </div>

        <div class="ags-xoiwc-widget">
            <img src=" ' . plugins_url('images/frontend-report-for-woocommerce.png', __FILE__) . ' " alt="Frontend Reports for WooCommerce" class="widget-thumb"/>
            <div class="inside">
                <h2>' . esc_html__('Embed Report in Frontend Pages', 'export-order-items-for-woocommerce') . '</h2>
                <p>' . esc_html__('Display the report or a download link in posts and pages using a shortcode.', 'export-order-items-for-woocommerce') . '</p>
                <a href="https://aspengrovestudios.com/product/frontend-reports-for-woocommerce/?utm_source=export-order-items&amp;utm_medium=link&amp;utm_campaign=wp-plugin-upgrade-link" target="_blank" class="ags-xoiwc-button-secondary">' . esc_html__('Get the add-on plugin', 'export-order-items-for-woocommerce') . '</a>
            </div>
        </div> 
             
     </div> <!-- /ags-xoiwc-settings-sidebar -->
</div> <!-- #ags-xoiwc-settings-settings -->

    <div id="ags-xoiwc-settings-addons"> ');
            define('AGS_XOIWC_ADDONS_URL', 'https://divi.space/wp-content/uploads/product-addons/export-order-items-free.json');
            require_once(dirname(__FILE__) . '/addons/addons.php');
            AGS_XOIWC_Addons::outputList();
            echo('
    </div>  <!-- #ags-xoiwc-settings-addons -->

        </div> <!-- ags-xoiwc-settings-tabs-content -->
    </div> <!-- ags-xoiwc-settings -->
</div> <!-- ags-xoiwc-settings-container --> '); ?>

            <script type="text/javascript" src="<?php echo plugins_url('/js/export-order-items.js', __FILE__); ?>"></script>
            <script>
                var ags_xoiwc_tabs_navigate = function () {
                    var tabs = [
                            {
                                tabsContainerId: 'ags-xoiwc-settings-tabs',
                                contentIdPrefix: 'ags-xoiwc-settings-'
                            }
                        ],
                        activeClass = 'ags-xoiwc-settings-active';
                    for (var i = 0; i < tabs.length; ++i) {
                        var $tabContent = jQuery('#' + tabs[i].contentIdPrefix + location.hash.substr(1));
                        if ($tabContent.length) {
                            var $tabs = jQuery('#' + tabs[i].tabsContainerId + ' > li');
                            $tabContent
                                .siblings()
                                .add($tabs)
                                .removeClass(activeClass);
                            $tabContent.addClass(activeClass);
                            $tabs
                                .filter(':has(a[href="' + location.hash + '"])')
                                .addClass(activeClass);
                            break;
                        }
                    }
                };
                if (location.hash) {
                    ags_xoiwc_tabs_navigate();
                }

                jQuery(window).on('hashchange', ags_xoiwc_tabs_navigate);
            </script>