<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_License {

    protected static $version = '1.0.2';

    protected $manager = null;

    protected $url = "https://license.xplodedthemes.com/process.php";
    protected $is_fake = false;
    protected $option_key;
    protected $option_key_check;
    protected $prefix = '';

    public function __construct (&$manager) {

        $this->manager = $manager;

        if(empty($this->market_info()->id)) {

            $this->is_fake = true;

        }else {

            $this->option_key       = 'xt-license-' . $this->market_info()->id;
            $this->option_key_check = 'xt-license-check-' . $this->market_info()->id;

            $this->prefix = 'xt_license_' . $this->market_info()->id . '_';

            $refreshLicense = !empty($_REQUEST['xt-license-refresh']);

            if ( $refreshLicense || $this->refreshNeeded() ) {

                add_action( 'init', array( $this, 'refresh_license' ) );
            }

            add_action( 'wp_ajax_' . $this->prefix( 'activation' ), array( $this, 'ajax_activate' ) );
            add_action( 'wp_ajax_' . $this->prefix( 'revoke' ), array( $this, 'ajax_revoke' ) );

            add_filter( 'slickmenu_welcome_sections', array( $this, 'welcome_section_license' ), 1, 1 );
        }

        return $this;
    }

    public function refresh_license() {

        $license = $this->getLocalLicense();

        if ( ! empty( $license ) ) {
            $this->activate(
                $license->license->purchase_code,
                $license->license->domain,
                $license->license->installation_url
            );
        }
    }

    public function enqueue_scripts() {

        $dir_url = plugin_dir_url(__FILE__).'../';

        wp_enqueue_style( 'xt-license', $dir_url.'assets/css/license.css', array(), self::$version, 'all' );
        wp_enqueue_script( 'xt-license', $dir_url.'assets/js/license-min.js', array( 'jquery' ), self::$version, false );
    }

    public function welcome_section_license($sections) {

        if (!$this->manager->is_paying()) {
            $action_title = esc_html__('Activate License', 'slick-menu');
            $action_color = "#a00;";
        } else {
            $action_title = esc_html__('License Activated', 'slick-menu');
            $action_color = "green;";
        }

        return array_merge(array(
            array(
                'id' => 'license',
                'title' => esc_html__( 'License', 'slick-menu' ),
                'show_menu' => true,
                'action_link' => array(
                    'title' => $action_title,
                    'color' => $action_color
                ),
                'content' => array(
                    'type' => 'function',
                    'function' => array($this, 'form'),
                ),
                'callback' => array($this, 'enqueue_scripts')
            )
        ), $sections);
    }

    public function prefix($key) {

        return $this->prefix.$key;
    }

    public function product() {

        return $this->manager->product;
    }

    public function market() {

        return $this->manager->product->market;
    }

    public function market_info() {

        return $this->manager->market_info;
    }

    public function ajax_activate() {

        $nonce = $_POST['wpnonce'];
        if ( ! wp_verify_nonce( $nonce, $this->prefix('activation') ) ) {

            die( 'invalid nonce' );

        } else {

            $domain = !empty($_POST['domain']) ? $_POST['domain'] : '';
            $installation_url = !empty($_POST['installation_url']) ? $_POST['installation_url'] : '';
            $purchase_code = !empty($_POST['purchase_code']) ? $_POST['purchase_code'] : '';

            $response = $this->activate($purchase_code, $domain, $installation_url);

            die(json_encode($response));
        }
    }

    public function ajax_revoke() {

        $nonce = $_POST['wpnonce'];
        if ( ! wp_verify_nonce( $nonce, $this->prefix('revoke') ) ) {

            die( 'invalid nonce' );

        } else {

            $local = !empty($_POST['local']) ? true : false;
            $purchase_code = !empty($_POST['purchase_code']) ? $_POST['purchase_code'] : '';
            $domain = !empty($_POST['domain']) ? $_POST['domain'] : '';
            $response = $this->revoke($purchase_code, $domain, $local);

            die(json_encode($response));
        }
    }


    public function activate($purchase_code, $domain, $installation_url) {

        list($email, $first_name, $last_name) = $this->getUserInfo();

        $response = $this->process(array(
            'product_id' => $this->market_info()->id,
            'purchase_code' => $purchase_code,
            'domain' => $domain,
            'installation_url' => $installation_url,
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'action' => 'activate',
            'market' => $this->market()
        ));

        if(!empty($response) && !empty($response->code)) {

            if (in_array($response->code, array("valid", "expired"))) {

                $this->saveLocalLicense($response);

            } else {

                $this->deleteLocalLicense();
            }
        }

        return $response;
    }

    public function revoke($purchase_code, $domain = null, $local = false) {

        $response = $this->process(array(
            'purchase_code' => $purchase_code,
            'domain' => $domain,
            'action' => 'revoke'
        ));

        if($local) {
            $license = $this->getLocalLicense();
            $this->deleteLocalLicense($license->license->product_id);
        }

        return $response;
    }

    public function process($data) {

        $data['t'] = time();
        $data['version'] = self::$version;
        $url = add_query_arg($data, $this->url);

        $response = $this->remote_get($url);

        if(!empty($response)) {
            return json_decode($response);
        }
        return false;
    }

    public function getLocalLicenseInfo($type) {

        $license = $this->getLocalLicense();

        if(!empty($license->license->$type)) {
            return $license->license->$type;
        }
        return "";
    }

    public function getLocalLicenseSummary() {

        $license = $this->getLocalLicense();

        return $license->license_summary;
    }

    public function getLocalLicense() {

        return $this->get_option($this->option_key);
    }

    public function refreshNeeded() {

        return $this->get_transient($this->option_key_check) === false;
    }

    public function saveLocalLicense($license) {

        $this->add_option($this->option_key, $license);
        $this->set_transient($this->option_key_check, time(), WEEK_IN_SECONDS);
    }

    public function deleteLocalLicense($product_id = null) {

        if(!empty($product_id)) {

            $option_key = 'xt-license-'.$product_id;
            $option_key_check = 'xt-license-check-'.$product_id;

            $this->delete_option($option_key);
            $this->delete_transient($option_key_check);

        }

        $this->delete_option($this->option_key);
        $this->delete_transient($this->option_key_check);
    }

    public function isActivated() {

        return true;
    }

    public function isFound() {

        return true;

    }

    public function getUserInfo() {

        $email = '';
        $first_name = '';
        $last_name = '';
        $current_user = wp_get_current_user();

        if ( $current_user->exists() ) {
            $email = $current_user->user_email;
            $display_name = $current_user->display_name;
            $first_name = $current_user->user_firstname;
            $last_name = $current_user->user_lastname;

            if(empty($first_name) && empty($last_name)) {
                $first_name = $display_name;
            }
        }

        return array(
            $email,
            $first_name,
            $last_name
        );
    }

    public function form() {

        $domain = "";
        if(is_multisite() && function_exists('get_current_site')) {
            $domain = get_current_site()->domain;
        }

        $isActivated = $this->isActivated();
        $isFound = $this->isFound();

        $license_key_label = ($this->market() === 'envato') ? "Purchase Code" : 'License Key';

        ?>
        <div id="xt-license-activation-<?php echo esc_attr($this->market_info()->id);?>" data-id="<?php echo esc_attr($this->market_info()->id);?>" data-ajaxurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-homeurl="<?php echo esc_url(network_site_url('/'));?>" data-domain="<?php echo $domain;?>" class="xt-license-activation">

            <div id="xt-license-activation-form" class="xt-license-form<?php if($isFound):?> xt-license-hide<?php endif;?>">
                <p class="xt-license-status">
				    <span class="xt-license-msg">
					    <?php
                        echo apply_filters(
                            $this->prefix('msg_activate'),
                            sprintf(
                                esc_html__( 'Your Support License is %1$s.', 'slick-menu'),
                                "<span class='xt-license-status xt-license-invalid'><strong>".esc_html__('Not Activated', 'slick-menu')."</strong></span>"
                            ),
                            $this->market_info()
                        );
                        ?>
				    </span>
                    <span class="xt-license-submsg">
						<?php
                        echo apply_filters(
                            $this->prefix('submsg_activate'),
                            sprintf(
                                esc_html('Activate your %1$s to enable premium features, support & automated updates', 'slick-menu'),
                                $license_key_label,
                                "<strong>".$this->product()->menu_name."</strong>"
                            ),
                            $this->market_info()
                        );
                        ?>
					</span>
                    <span class="xt-license-submsg xt-license-revoke-info"></span>
                </p>
                <input type="hidden" name="action" value="<?php echo $this->prefix('activation');?>">
                <input type="hidden" name="wpnonce" value="<?php echo wp_create_nonce($this->prefix('activation'));?>">
                <input type="hidden" name="domain" value="">
                <input type="hidden" name="installation_url" value="">
                <input type="hidden" name="product_id" value="<?php echo esc_attr($this->market_info()->id);?>">
                <input class="regular-text" placeholder="<?php echo $license_key_label;?>..." name="purchase_code" value="">
                <input type="submit" class="button button-primary" value="<?php echo esc_html__('Validate', 'slick-menu'); ?>">
            </div>

            <div id="xt-license-revoke-form" class="xt-license-form xt-license-hide">

                <p class="xt-license-status">
                    <span class="xt-license-msg xt-license-invalid"><?php echo apply_filters($this->prefix('msg_active_invalid'), sprintf(esc_html__( 'This %1$s is activated somewhere else.', 'slick-menu'), $license_key_label), $this->market_info());?></span>
                    <span class="xt-license-submsg"><?php echo apply_filters($this->prefix('submsg_active_invalid'), esc_html('You can either revoke the below license then re-activate it here or buy a new license.', 'slick-menu'), $this->market_info());?></span>
                </p>

                <input type="hidden" name="action" value="<?php echo $this->prefix('revoke');?>">
                <input type="hidden" name="wpnonce" value="<?php echo wp_create_nonce($this->prefix('revoke'));?>">
                <input type="hidden" name="purchase_code" value="">
                <input type="hidden" name="domain" value="">
                <input type="button" class="button button xt-license-revoke-cancel" value="<?php echo esc_html__('Cancel', 'slick-menu'); ?>">

                <a href="<?php echo $this->market_info()->buy_url;?>" class="button" target="_blank"><?php echo esc_html__('Buy License', 'slick-menu'); ?></a>
                <input type="submit" class="button button-primary" value="<?php echo esc_html__('Revoke', 'slick-menu'); ?>">
            </div>

            <div id="xt-license-invalid" class="xt-license-hide">
                <p class="xt-license-status">
				    <span class="xt-license-msg">
				    <?php
                    echo apply_filters(
                        $this->prefix('msg_invalid'),
                        sprintf(
                            esc_html__( 'This %1$s is %2$s.', 'slick-menu'),
                            $license_key_label,
                            "<span class='xt-license-status xt-license-invalid'><strong>".esc_html__('Invalid', 'slick-menu')."</strong></span>"
                        ),
                        $this->market_info()
                    );
                    ?>
				    </span>
                    <span class="xt-license-timer"></span>
                </p>
            </div>

            <div id="xt-license-activated" class="<?php if(!$isFound):?>xt-license-hide<?php endif;?>">
                <div class="xt-license-status">
                    <div class="xt-license-status-active<?php if(!$isActivated):?> xt-license-hide<?php endif;?>">
                        <span class="xt-license-msg">
                            <?php
                            echo apply_filters(
                                $this->prefix('msg_activated'),
                                sprintf(
                                    esc_html__( 'Your Support License is %1$s.', 'slick-menu'),
                                    "<span class='xt-license-status xt-license-valid'><strong>".esc_html__('Valid', 'slick-menu')."</strong></span>"
                                ),
                                $this->market_info()
                            );
                            ?>
                        </span>
                        <span class="xt-license-submsg">
                            <?php
                            echo apply_filters(
                                $this->prefix('submsg_activated'),
                                sprintf(
                                    esc_html__( 'Support & automated updates are now %1$s', 'slick-menu'),
                                    "<strong>".esc_html__('Enabled', 'slick-menu')."</strong>"
                                ),
                                $this->market_info()
                            );
                            ?>
                        </span>
                    </div>
                    <div class="xt-license-status-expired<?php if($isActivated):?> xt-license-hide<?php endif;?>">
                        <span class="xt-license-msg">
                            <?php
                            echo apply_filters(
                                $this->prefix('msg_expired'),
                                sprintf(
                                    esc_html__( 'Your Support License has %1$s.', 'slick-menu'),
                                    "<span class='xt-license-status xt-license-invalid'><strong>".esc_html__('Expired', 'slick-menu')."</strong></span>"
                                ),
                                $this->market_info()
                            );
                            ?>
                        </span>
                        <span class="xt-license-submsg">
                            <?php
                            echo apply_filters(
                                $this->prefix('submsg_expired'),
                                sprintf(
                                    esc_html__( 'Support & automated updates are now %1$sDisabled%2$s!%3$sYou can still use the plugin and update it manually from Envato', 'slick-menu'),
                                    "<strong>",
                                    "</strong>",
                                    "<br>"
                                ),
                                $this->market_info()
                            );
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if($isFound):?>
                <div class="xt-license-info">
                    <?php echo $this->getLocalLicenseSummary();?>
                </div>
            <?php else: ?>
                <div class="xt-license-info"></div>
            <?php endif;?>

            <div id="xt-license-local-revoke-form" class="xt-license-form<?php if(!$isFound):?> xt-license-hide<?php endif;?>">

                <input type="hidden" name="action" value="<?php echo $this->prefix('revoke');?>">
                <input type="hidden" name="wpnonce" value="<?php echo wp_create_nonce($this->prefix('revoke'));?>">
                <input type="hidden" name="purchase_code" value="<?php echo $this->getLocalLicenseInfo('purchase_code'); ?>">
                <input type="hidden" name="domain" value="<?php echo $this->getLocalLicenseInfo('domain'); ?>">
                <input type="hidden" name="local" value="1">
                <a href="<?php echo $this->market_info()->buy_url;?>" class="button" target="_blank"><?php echo esc_html__('Buy License', 'slick-menu'); ?></a>
                <input type="submit" class="button button-primary" value="<?php echo esc_html__('Revoke License', 'slick-menu'); ?>">
            </div>
        </div>

        <?php
    }

    public function remote_get($url) {

        $data = null;

        // First, we try to use wp_remote_get
        $response = wp_remote_get( $url, array(
            'timeout' => 120,
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
        ));

        if( is_wp_error( $response ) || (!empty($response["response"]["code"]) && $response["response"]["code"] === 403 )) {

            if(function_exists('curl_init')) {

                // And if that doesn't work, then we'll try curl
                $curl = curl_init( $url );

                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $curl, CURLOPT_HEADER, 0 );
                curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0' );
                curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

                $response = curl_exec( $curl );
                if( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {

                    // If that doesn't work, then we'll try file_get_contents
                    $response = @file_get_contents( $url );

                } // end if
                curl_close( $curl );

            }else{

                // If curl is not availaible try file_get_contents
                $response = @file_get_contents( $url );

            }// end if

            if( null == $response ) {
                $response = null;
            }

            if(!empty($response)) {
                $data = maybe_unserialize($response);
            }

        }else{

            // Parse remote HTML file
            $data = wp_remote_retrieve_body( $response );

            // Check for error
            if ( !is_wp_error( $data ) ) {
                $data = maybe_unserialize($data);
            }
        }

        return $data;
    }

    function set_transient($key, $value, $expiration = 0) {

        if(is_multisite()) {
            set_site_transient($key, $value, $expiration);
        }else{
            set_transient($key, $value, $expiration);
        }
    }

    function get_transient($key) {

        if(is_multisite()) {
            return get_site_transient($key);
        }else{
            return get_transient($key);
        }
    }

    function delete_transient($key) {

        if(is_multisite()) {
            delete_site_transient($key);
        }else{
            delete_transient($key);
        }
    }

    function add_option($key, $value) {

        if(is_multisite()) {
            add_site_option($key, $value);
        }else{
            add_option($key, $value, '', 'no');
        }
    }

    function get_option($key) {

        $option = $this->get_transient($key);

        if($option === false) {

            if(is_multisite()) {
                return get_site_option($key);
            }else{
                return get_option($key);
            }
        }

        return $option;
    }

    function delete_option($key) {

        $this->delete_transient($key);

        if(is_multisite()) {
            delete_site_option($key);
        }else{
            delete_option($key);
        }
    }

}
