<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Slick_Menu
 * @subpackage Slick_Menu/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Slick_Menu_Welcome
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */

if(!class_exists('Slick_Menu_Welcome')) {

    class Slick_Menu_Welcome
    {

        /**
         * Core class reference.
         *
         * @since    1.0.0
         * @access   private
         * @var      Slick_Menu    core    Core Class
         */
        private $core;

        public $logo = '';
        public $description = '';
        public $sections = [];
        public $default_section;
        public $page_hooks = array();


        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @var      Slick_Menu    core    Core Class
         */
        public function __construct(&$core, $sections, $logo = null, $description = null)
        {

            usort($sections, array($this, 'sort_section'));

            $this->core = $core;

            $this->logo = $logo;
            $this->description = $description;
            $this->sections = $sections;

            if (!empty($this->sections)) {
                $this->default_section = current($this->sections)['id'];
            }

            $this->active_section = $this->default_section;

            if (!empty($_GET['page'])) {
                $section_id = str_replace($this->core->plugin_slug() . '-', '', esc_html($_GET['page']));
                if ($this->section_exists($section_id)) {
                    $this->active_section = $section_id;
                }
            }

            add_action('admin_menu', array($this, 'settings_menu'), 1);
            add_filter('plugin_action_links_' . plugin_basename($this->core->plugin_file()), array($this, 'action_links'), 99);

            if ($this->is_welcome_page()) {
                add_action('admin_body_class', array($this, 'admin_body_class'), 1);
                add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'), 1);
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 1);
            }
        }

        function sort_section($a, $b)
        {

            if (!isset($a['order'])) {
                $a['order'] = 0;
            }

            if (!isset($b['order'])) {
                $b['order'] = 0;
            }

            return $a['order'] - $b['order'];
        }

        function section_exists($id)
        {

            foreach ($this->sections as $section) {

                if ($section['id'] === $id) {
                    return true;
                }
            }

            return false;
        }

        function is_welcome_page()
        {

            return !empty($_GET['page']) && (($_GET['page'] === $this->core->plugin_slug()) || ($_GET['page'] === $this->core->plugin_slug($this->active_section)));
        }

        function admin_body_class($classes)
        {

            if ($this->is_welcome_page()) {
                $classes .= ' xt-welcome-page';
            }

            return $classes;
        }

        /**
         * Register the stylesheets for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_styles()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Slick_Menu_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Slick_Menu_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_style($this->core->plugin_slug('welcome'), esc_url( $this->core->url  . 'admin/welcome/assets/css/welcome.css'), array(), $this->core->plugin_version(), 'all');

        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Slick_Menu_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Slick_Menu_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_script($this->core->plugin_slug('xt-follow'), '//xplodedthemes.s3.amazonaws.com/widgets/xt-follow/xt-follow-min.js', array(), $this->core->plugin_version(), false);
        }

        function action_links($links)
        {

            foreach ($this->sections as $i => $section) {

                if (empty($section['action_link'])) {
                    continue;
                }

                $id = $i > 0 ? $section['id'] : '';
                $url = $this->get_section_url($id);

                $action_link = $section['action_link'];

                if (is_array($action_link)) {

                    $url = !empty($action_link['url']) ? $action_link['url'] : $url;
                    $title = !empty($action_link['title']) ? $action_link['title'] : $section['title'];
                    $color = !empty($action_link['color']) ? $action_link['color'] : '';

                } else {

                    $title = $section['title'];
                    $color = '';
                }

                $links[] = '<a style="color: ' . esc_attr($color) . '" href="' . esc_url($url) . '">' . sanitize_text_field($title) . '</a>';

            }

            return $links;
        }

        public function settings_menu()
        {

            add_menu_page($this->core->plugin()->name, $this->core->plugin()->menu_name, 'manage_options', $this->core->plugin_slug(), array($this, 'settings_page'), $this->core->plugin_icon());

            foreach ($this->sections as $section) {

                $id = $section['id'];
                $title = !empty($section['menu_title']) ? $section['menu_title'] : $section['title'];
                $show_menu = $section['show_menu'];
                $redirect = !empty($section['external']) ? $section['external'] : '';
                $redirect = !empty($section['redirect']) ? $section['redirect'] : $redirect;
                $submenu_parent = $show_menu ? $this->core->plugin_slug() : null;

                if ($id == $this->default_section) {
                    $this->page_hooks[$id] = add_submenu_page($submenu_parent, $title, $title, 'manage_options', $this->core->plugin_slug(), array($this, 'settings_page'));
                } else {
                    $this->page_hooks[$id] = add_submenu_page($submenu_parent, $title, $title, 'manage_options', $this->core->plugin_slug($id), function () use ($id, $redirect) {

                        if (!$redirect) {
                            $this->active_section = $id;
                            $this->settings_page();
                        } else {
                            wp_redirect($redirect);
                            exit;
                        }
                    });
                }

                if (!empty($section['callback'])) {

                    add_action("load-" . $this->page_hooks[$id], $section['callback']);
                }
            }
        }


        public function settings_page()
        {

            ?>
            <div class="wrap xt-welcome-wrap <?php echo $this->core->plugin_slug("welcome-wrap"); ?>">

                <div class="xt-welcome-header">

                    <div class="xt-version">
                        V.<?php echo $this->core->plugin_version(); ?>
                        <?php if (!$this->core->fs()->can_use_premium_code__premium_only()): ?>
                            <?php if ($this->core->plugin_market() === 'freemius'): ?>
                                <em>Free Version</em>
                            <?php else: ?>
                                <em>Not Activated</em>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($this->logo)): ?>
                        <div class="xt-welcome-logo">
                            <img src="<?php echo esc_url($this->logo); ?>" class="image-50"/>
                        </div>
                    <?php else: ?>
                        <h1><img class="xt-logo" src="<?php echo esc_url($this->core->url.'admin/welcome/assets/images/xploded.png');?>" class="image-50"/><?php echo $this->core->plugin_name(); ?></h1>
                    <?php endif; ?>

                    <?php if (!empty($this->description)): ?>
                        <div class="xt-welcome-description">
                            <?php echo $this->description; ?>
                        </div>
                    <?php endif; ?>

                </div>

                <?php $this->show_nav(); ?>

                <div class="xt-welcome-section xt-<?php echo $this->get_section_id(); ?>-section">

                    <?php $this->show_section(); ?>

                </div>

            </div>
            <?php
        }

        public function show_nav()
        {

            echo '<div class="nav-tab-wrapper">';

            echo '<script type="text/javascript">';
            echo '  XT_FOLLOW.init();';
            echo '</script>';


            foreach ($this->sections as $section) {

                $hide_tab = !empty($section['hide_tab']);

                if ($hide_tab) {
                    continue;
                }

                $id = $section['id'];
                $url = $this->get_section_url($id);
                $featured = !empty($section['featured']);

                $target = '_self';

                if (!empty($section['external'])) {
                    $url = $section['external'];
                    $target = '_blank';
                }

                if (!empty($section['redirect'])) {
                    $url = $section['redirect'];
                    $target = '_self';
                }

                if (!empty($section['target'])) {
                    $target = $section['target'];
                }

                $classes = array();

                if ($this->is_section($id)) {
                    $classes[] = 'nav-tab-active';
                }

                if ($featured) {
                    $classes[] = 'nav-tab-featured';
                }

                echo '<a href="' . esc_url($url) . '" class="nav-tab ' . implode(" ", $classes) . '" target="' . esc_attr($target) . '">' . sanitize_text_field($section['title']) . '</a>';
            }

            echo '</div>';
        }

        public function show_section()
        {

            $section = $this->get_section();
            $id = $section['id'];
            $url = $this->get_section_url($id, array('nocache' => '1'));

            if (!empty($section['content'])) {

                $content = $section['content'];

                if (!empty($content['show_refresh'])) {

                    echo '
                    <a class="xt-refresh-link" href="' . esc_url($url) . '">
                        <span class="dashicons dashicons-image-rotate"></span> Refresh
                    </a>';
                }

                if (!empty($content['title'])) {

                    echo '<h3>' . $content['title'] . '</h3>';
                }

                if ($content['type'] === 'template') {

                    if (file_exists($content['template'])) {
                        include($content['template']);
                    }

                } else if ($content['type'] === 'function') {

                    $args = !empty($content['args']) ? $content['args'] : array();
                    if (!empty($content['function'])) {
                        echo call_user_func_array($content['function'], $args);
                    }

                } else if ($content['type'] === 'html') {

                    echo $content['html'];

                } else if ($content['type'] === 'url') {

                    $json_decode = !empty($content['json']);
                    echo $this->remote_get($content['url'], $json_decode);

                } else if ($content['type'] === 'changelog') {

                    echo $this->get_changelog();
                }
            }
        }

        public function is_section($section)
        {

            return $this->active_section === $section;
        }


        public function get_url()
        {

            return esc_url($this->core->plugin_url());
        }

        public function get_section()
        {

            $section_key = array_search($this->active_section, array_column($this->sections, 'id'));
            $section = $this->sections[$section_key];

            return $section;
        }

        public function get_section_id()
        {

            return $this->active_section;
        }

        public function get_section_url($section = '', $args = array())
        {

            if ($section == $this->default_section) {
                $section = '';
            }
            return esc_url($this->core->plugin_url($section, $args));
        }

        public function remote_get($url, $json_decode = false)
        {

            $cache_key = md5($url);

            $content = get_site_transient($cache_key);

            if ($content === false || !empty($_GET['nocache']) !== null) {

                if (!empty($_GET['nocache'])) {
                    $url = add_query_arg('nocache', intval($_GET['nocache']), $url);
                }

                $response = wp_remote_get($url, array('sslverify' => false));

                // Stop here if the is an error.
                if (is_wp_error($response)) {

                    $content = '';

                    // Set temporary transient.
                    set_site_transient($cache_key, $content, MINUTE_IN_SECONDS);

                } else {

                    // Retrieve data from the body and decode json format.
                    $content = wp_remote_retrieve_body($response);

                    set_site_transient($cache_key, $content, DAY_IN_SECONDS);
                }

            }

            if ($json_decode) {
                $content = json_decode($content, true);
            }

            return $content;
        }


        function get_changelog()
        {

            $readme_file = $this->core->plugin_path('/', 'readme.txt');

            require_once $this->core->plugin_path() . 'includes/class-parsedown.php';

            $parsedown = new XT_Parsedown();

            $changelog = '';

            $data = file_get_contents($readme_file);

            if (!empty($data)) {
                $data = explode('== Changelog ==', $data);
                if (!empty($data[1])) {

                    $changelog = $data[1];
                    $changelog = preg_replace(
                        array(
                            '/\[\/\/\]\: \# fs_.+?_only_begin/',
                            '/\[\/\/\]\: \# fs_.+?_only_end/',
                        ),
                        '',
                        $changelog
                    );

                    $changelog = $parsedown->text($changelog);

                    $changelog = preg_replace(
                        array(
                            '/\<strong\>(.+?)\<\/strong>\:(.+?)\n/i',
                            '/\<p\>/',
                            '/\<\/p\>/'
                        ),
                        array(
                            '<span class="update-type $1">$1</span><span class="update-txt">$2</span>',
                            '',
                            ''
                        ),
                        $changelog
                    );

                }
            }

            return '<div class="xt-changelog">' . wp_kses($changelog, wp_kses_allowed_html('post')) . '</div>';
        }

    }
}
