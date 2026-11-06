<?php
/**
 * Softkom Commercial Catalogue Admin V1.
 *
 * Provides an internal WordPress interface for managing
 * implementation and recurring-service pricing.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Register Commercial Catalogue admin page.
 */
function softkom_v3_register_commercial_catalogue_admin() {

    add_menu_page(
        'Softkom Commercial Catalogue',
        'Softkom Catalogue',
        'manage_options',
        'softkom-commercial-catalogue',
        'softkom_v3_render_commercial_catalogue_admin',
        'dashicons-money-alt',
        27
    );
}

add_action(
    'admin_menu',
    'softkom_v3_register_commercial_catalogue_admin'
);


/**
 * Render Commercial Catalogue.
 */
function softkom_v3_render_commercial_catalogue_admin() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $catalogue = softkom_v3_commercial_catalogue();

    ?>
    <div class="wrap">

        <h1>Softkom Commercial Catalogue</h1>

        <p>
            Manage implementation pricing and recurring monthly
            service plans used by the Softkom Revenue Engine.
        </p>

        <form method="post">

            <?php
            wp_nonce_field(
                'softkom_v3_save_commercial_catalogue',
                'softkom_v3_commercial_catalogue_nonce'
            );
            ?>

            <input
                type="hidden"
                name="softkom_commercial_action"
                value="save"
            >

            <?php foreach ( $catalogue as $service_key => $service ) : ?>

                <div
                    style="
                        background:#fff;
                        border:1px solid #dcdcde;
                        border-radius:8px;
                        padding:20px;
                        margin:20px 0;
                        max-width:1000px;
                    "
                >

                    <h2 style="margin-top:0;">
                        <?php echo esc_html( $service['name'] ); ?>
                    </h2>

                    <p>
                        <strong>Service Key:</strong>
                        <code><?php echo esc_html( $service_key ); ?></code>
                    </p>


                    <table class="form-table">

                        <tr>
                            <th>
                                <label>
                                    Service Name
                                </label>
                            </th>

                            <td>
                                <input
                                    type="text"
                                    class="regular-text"
                                    name="catalogue[<?php echo esc_attr( $service_key ); ?>][name]"
                                    value="<?php echo esc_attr( $service['name'] ); ?>"
                                >
                            </td>
                        </tr>


                        <tr>
                            <th>
                                <label>
                                    Category
                                </label>
                            </th>

                            <td>
                                <input
                                    type="text"
                                    class="regular-text"
                                    name="catalogue[<?php echo esc_attr( $service_key ); ?>][category]"
                                    value="<?php echo esc_attr( $service['category'] ); ?>"
                                >
                            </td>
                        </tr>


                        <tr>
                            <th>
                                <label>
                                    Implementation Name
                                </label>
                            </th>

                            <td>
                                <input
                                    type="text"
                                    class="regular-text"
                                    name="catalogue[<?php echo esc_attr( $service_key ); ?>][implementation][name]"
                                    value="<?php echo esc_attr( $service['implementation']['name'] ); ?>"
                                >
                            </td>
                        </tr>


                        <tr>
                            <th>
                                <label>
                                    Implementation Price From
                                </label>
                            </th>

                            <td>
                                R
                                <input
                                    type="number"
                                    min="0"
                                    step="1"
                                    name="catalogue[<?php echo esc_attr( $service_key ); ?>][implementation][price_from]"
                                    value="<?php echo esc_attr( $service['implementation']['price_from'] ); ?>"
                                >
                            </td>
                        </tr>

                    </table>


                    <h3>Recurring Plans</h3>

                    <table class="widefat striped">

                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Name</th>
                                <th>Monthly Price</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ( $service['plans'] as $plan_key => $plan ) : ?>

                                <tr>

                                    <td>
                                        <code>
                                            <?php echo esc_html( $plan_key ); ?>
                                        </code>
                                    </td>

                                    <td>
                                        <input
                                            type="text"
                                            name="catalogue[<?php echo esc_attr( $service_key ); ?>][plans][<?php echo esc_attr( $plan_key ); ?>][name]"
                                            value="<?php echo esc_attr( $plan['name'] ); ?>"
                                        >
                                    </td>

                                    <td>
                                        R
                                        <input
                                            type="number"
                                            min="0"
                                            step="1"
                                            name="catalogue[<?php echo esc_attr( $service_key ); ?>][plans][<?php echo esc_attr( $plan_key ); ?>][monthly]"
                                            value="<?php echo esc_attr( $plan['monthly'] ); ?>"
                                        >
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endforeach; ?>


            <?php submit_button( 'Save Commercial Catalogue' ); ?>

        </form>

    </div>
    <?php
}


/**
 * Save Commercial Catalogue settings.
 */
function softkom_v3_save_commercial_catalogue_admin() {

    if (
        ! is_admin() ||
        ! current_user_can( 'manage_options' )
    ) {
        return;
    }

    if (
        empty( $_POST['softkom_commercial_action'] ) ||
        'save' !== $_POST['softkom_commercial_action']
    ) {
        return;
    }

    if (
        empty( $_POST['softkom_v3_commercial_catalogue_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['softkom_v3_commercial_catalogue_nonce']
                )
            ),
            'softkom_v3_save_commercial_catalogue'
        )
    ) {
        return;
    }

    $submitted = isset( $_POST['catalogue'] )
        && is_array( $_POST['catalogue'] )
            ? wp_unslash( $_POST['catalogue'] )
            : array();

    $clean = array();

    foreach ( $submitted as $service_key => $service ) {

        $service_key = sanitize_key(
            $service_key
        );

        if ( '' === $service_key ) {
            continue;
        }

        $clean[ $service_key ] = array(

            'name' => isset( $service['name'] )
                ? sanitize_text_field( $service['name'] )
                : '',

            'category' => isset( $service['category'] )
                ? sanitize_text_field( $service['category'] )
                : '',

            'implementation' => array(

                'name' =>
                    isset( $service['implementation']['name'] )
                        ? sanitize_text_field(
                            $service['implementation']['name']
                        )
                        : '',

                'price_from' =>
                    isset( $service['implementation']['price_from'] )
                        ? max(
                            0,
                            (int) $service['implementation']['price_from']
                        )
                        : 0,
            ),

            'plans' => array(),
        );


        if (
            isset( $service['plans'] ) &&
            is_array( $service['plans'] )
        ) {

            foreach ( $service['plans'] as $plan_key => $plan ) {

                $plan_key = sanitize_key(
                    $plan_key
                );

                if ( '' === $plan_key ) {
                    continue;
                }

                $clean[ $service_key ]['plans'][ $plan_key ] = array(

                    'name' => isset( $plan['name'] )
                        ? sanitize_text_field(
                            $plan['name']
                        )
                        : '',

                    'monthly' => isset( $plan['monthly'] )
                        ? max(
                            0,
                            (int) $plan['monthly']
                        )
                        : 0,
                );
            }
        }
    }


    update_option(
        'softkom_v3_commercial_catalogue',
        $clean,
        false
    );


    wp_safe_redirect(
        add_query_arg(
            array(
                'page'    => 'softkom-commercial-catalogue',
                'updated' => '1',
            ),
            admin_url( 'admin.php' )
        )
    );

    exit;
}

add_action(
    'admin_init',
    'softkom_v3_save_commercial_catalogue_admin'
);