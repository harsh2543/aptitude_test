<?php
/**
 * Plugin Name: Screener Dropdown
 * Description: A WordPress plugin for filtering data with dropdowns and dynamic filter rows.
 * Version: 1.4
 * Author: admin
 */

// Register assets (styles)
function screener_enqueue_styles() {
    wp_enqueue_style('select2-style', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css');
    wp_enqueue_style('datatables-style', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
    wp_enqueue_style('screener-style', plugin_dir_url(__FILE__) . 'screener-style.css');
}
add_action('wp_enqueue_scripts', 'screener_enqueue_styles');

// Register assets (scripts)
function screener_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js', array('jquery'), null, true);
    wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'screener_enqueue_scripts');

// Add the shortcode
function screener_dropdown_shortcode() {
    // Load data from JSON files
    $screener_list = json_decode(file_get_contents(plugin_dir_path(__FILE__) . 'screener_list.json'), true);
    $screener_data = json_decode(file_get_contents(plugin_dir_path(__FILE__) . 'screener_data.json'), true);

    // Handle case where JSON files fail to load
    if (empty($screener_list) || empty($screener_data)) {
        return '<p>No data found. Please ensure screener_list.json and screener_data.json are correctly set up.</p>';
    }

    ob_start();
    ?>
    <div id="screener-dropdown">
        <!-- Filters Section -->
        <div id="filter-container">
            <div class="filter-row">
                <select class="metric-select screener-dropdown filter-item">
                    <option value="">Select Metric</option>
                    <?php foreach ($screener_list as $item): ?>
                        <option value="<?php echo esc_attr($item['value']); ?>"><?php echo esc_html($item['label']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="operator-select filter-item">
	    <option value="select_operator">Select Operator</option>
	    <option value="greater_than">Greater Than</option>
                    <option value="less_than">Less Than</option>
                    <option value="equal_to">Equal To</option>
	    <option value="greater_than_or_equal_to">Greater than or Equal To</option>
	    <option value="less_than_or_equal_to">Less than or Equal To</option>
                </select>
                <input type="number" class="operator-select filter-item" placeholder="Enter Value">
	<select class="operator-select">
                    <option value="million">One</option>
                    <option value="thousand">Thousand</option>
                    <option value="million">Million</option>
	    <option value="billion">Billion</option>
                </select>
                <button type="button" class="remove-row">×</button>
            </div>
        </div>
        <button id="add-filter-button" class="filter-item">+</button>

        <!-- Table Section -->
        <table id="screener-table" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ticker</th>
                    <th>Company</th>
                    <th>Industry</th>
                    <th>Sector</th>
                    <th>Accounts Payables (Annual)</th>
                    <th>Book Value per Share</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($screener_data as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row['id']); ?></td>
                        <td><?php echo esc_html($row['ticker']); ?></td>
                        <td><?php echo esc_html($row['name']); ?></td>
                        <td><?php echo esc_html($row['industry']); ?></td>
                        <td><?php echo esc_html($row['sector']); ?></td>
                        <td><?php echo esc_html(number_format($row['accounts_payables_annual'])); ?></td>
                        <td><?php echo esc_html(number_format($row['book_value_per_share'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
     jQuery(document).ready(function ($) {
    function initializeSelect2() {
        $('.metric-select').select2();
    }

    
    $('#add-filter-button').on('click', function () {
        var newRow = `
            <div class="filter-row">
                <select class="metric-select">
	<option value="">Select Metric</option>
	<?php foreach ($screener_list as $item): ?>
                        <option value="<?php echo esc_attr($item['value']); ?>"><?php echo esc_html($item['label']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="operator-select filter-item">
	    <option value="select_operator">Select Operator</option>
                    <option value="greater_than">Greater Than</option>
                    <option value="less_than">Less Than</option>
                    <option value="equal_to">Equal To</option>
	    <option value="greater_than_or_equal_to">Greater than or Equal To</option>
	    <option value="less_than_or_equal_to">Less than or Equal To</option>
                </select>
                <input type="number" class="operator-select filter-item" placeholder="Enter Value">
	<select class="operator-select">
                    <option value="million">One</option>
                    <option value="thousand">Thousand</option>
                    <option value="million">Million</option>
	    <option value="billion">Billion</option>
                </select>
                <button type="button" class="remove-row">×</button>
            </div>`;
        
              $('#filter-container').append(newRow);
             initializeSelect2(); // Reinitialize Select2 for dynamically added rows
         });

            
            $('#filter-container').on('click', '.remove-row', function () {
            $(this).closest('.filter-row').remove();
       });

           
           initializeSelect2();
       });

    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('screener-dropdown', 'screener_dropdown_shortcode');