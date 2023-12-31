<?php

namespace Hurrytimer;

use Carbon\Carbon;
use Exception;
use Hurrytimer\Placeholders\Placeholder_Factory;

class CampaignBuilder
{
    use CampaignBuilderLegacy;

    /**
     * Build campaign Template.
     *
     * @return string
     */

    /**
     * @var \Hurrytimer\Campaign
     */
    protected $campaign;

    public function __construct( $campaign )
    {
        $this->campaign = $campaign;
        $this->campaign->loadSettings();
    }

    /**
     * Returns built template.
     *
     * @param string
     * @param boolean
     *
     * @return string
     */
    public function build( $content = '', $options = [] )
    {
        $config = $this->getClientConfig( $options );
        $json = htmlspecialchars( json_encode( $config ), ENT_QUOTES, 'UTF-8' );
        $legacyClass = $this->legacyCampaignClass( $this->campaign->get_id() );

        if ( $this->campaign->enableSticky === C::YES && $options[ 'sticky' ] ) {

            return '<div class="hurrytimer-sticky hurryt-loading hurrytimer-sticky-' . $this->campaign->get_id() . '"><div class="hurrytimer-sticky-inner"><div class="' . $legacyClass . ' hurrytimer-campaign hurrytimer-campaign-' . $this->campaign->get_id() . '" data-config="' . $json . '" >' . $content . '</div></div>' . $this->stickyBarCloseButton()
                . '</div>';

        }

        return '<div class="' . $legacyClass . ' hurrytimer-campaign hurryt-loading hurrytimer-campaign-' . $this->campaign->get_id() . '"'
            . ' data-config="' . $json . '" >' . $content . '</div>';
    }

    /**
     * The timer elements template.
     */
    public function template()
    {

        $template = ( $this->campaign->headlinePosition == C::HEADLINE_POSITION_ABOVE_TIMER
                ? $this->headline()
                : '' )
            . '<div class="' . $this->legacyTimerClass() . ' hurrytimer-timer"></div>'
            . ( $this->campaign->headlinePosition == C::HEADLINE_POSITION_BELOW_TIMER
                ? $this->headline() : '' )
            . $this->callToActionButton();


        return $template;

    }

    /**
     * Returns common client config.
     *
     * @return array
     */
    private function commonClientConfig()
    {

        $actions = $this->campaign->actions;
        foreach ( $actions as &$action ) {
            $action[ 'message' ] = apply_filters( 'hurryt_action_message', $action[ 'message' ],
                $this->campaign->get_id() );

            $action[ 'message' ] = nl2br( do_shortcode( $action[ 'message' ] ) );
        }

        return [
            'id' => $this->campaign->get_id(),
            'actions' => $actions,
            'template' => $this->timer(),
            'mode' => $this->campaign->get_mode_slug(),
            'sticky_bar_hide_timeout' => apply_filters( 'sticky_bar_hide_timeout',
                intval( $this->campaign->stickyBarDismissTimeout ), $this->campaign->get_id() )
        ];
    }

    private function evergreenClientConfig()
    {
        $evergreenCompaign = new EvergreenCampaign(
            $this->campaign->get_id(),
            new CookieDetection(),
            new IPDetection()
        );
        $evergreenCompaign->loadSettings();

        return [
            'isRegular' => false,
            'duration' => $this->campaign->duration_to_sec(),
            'should_reset' => $evergreenCompaign->should_reset(),
            'reset_token' => get_post_meta( $evergreenCompaign->get_id(), EvergreenCampaign::RESET_FLAG, true ),
            'restart' => apply_filters( 'hurryt_evergreen_restart', $this->campaign->getRestart() ),
            'endDate' => $evergreenCompaign->getEndDate(),
            'cookieName' => $evergreenCompaign->cookieName(),
            'reload_reset' => $evergreenCompaign->reloadReset
        ];

    }

    /**
     * Returns regular config.
     *
     * @return array
     */
    private function regularClientConfig()
    {
        try {
            $endDate = Carbon::parse( $this->campaign->getEndDatetime(), hurryt_tz() )
                ->getBrowserTimestamp();
            if ( $this->campaign->is_recurring() ) {
                $endDate = $this->campaign->get_current_recurrence_end_date();
                if ( $endDate ) {
                    $endDate = $endDate->getBrowserTimestamp();
                } else {
                    $endDate = null;
                }
            }

            $timeToNextRecurrence = $this->campaign->is_recurring()
                ? $this->campaign->get_time_to_next_recurrence()
                : 0;

            return [
                'recurr' => $this->campaign->is_recurring(),
                'timeToNextRecurrence' => $timeToNextRecurrence,
                'isRegular' => true,
                'endDate' => $endDate,
            ];

        } catch ( Exception $e ) {
            echo __( sprintf( 'HurryTimer Error: Invalid campaign (ID: %d). Please double check your settings.',
                $this->campaign->get_id() ), 'hurrytimer' );
        }
    }

    /**
     * Returns client config for the compaign.
     *
     * @return array|null
     */
    public function getClientConfig( $options = [] )
    {
        $config = $options;

        if ( $this->campaign->is_evergreen() ) {
            $config = array_merge( $config, $this->commonClientConfig(), $this->evergreenClientConfig() );

        } else {
            $config = array_merge( $config, $this->commonClientConfig(), $this->regularClientConfig() );
        }

        return $config;
    }

    /**
     * Returns timer.
     *
     * @return string
     */
    public function timer()
    {
        $blocks = array_filter( [
            $this->daysBlock(),
            $this->hoursBlock(),
            $this->minutesBlock(),
            $this->secondsBlock(),
        ] );

        $template = implode( $this->separator(), $blocks );


        $template = apply_filters( "hurryt_{$this->campaign->get_id()}_campaign_timer_template",
            $template, $this->campaign );

        $template = apply_filters( "hurryt_timer_template", $template, $this->campaign->get_id() );


        return $template;

    }

    /**
     * Returns separator.
     *
     * @return string
     */
    public function separator()
    {

        $separator = apply_filters( 'hurryt_block_separator', ':', $this->campaign->get_id() );

        return $this->campaign->blockSeparatorVisibility === C::YES
            ? '<div class="' . $this->legacySeparatorClass() . ' hurrytimer-timer-sep">' . $separator . '</div>'
            : '';
    }

    /**
     * Returns days block.
     *
     * @return string
     */
    public function daysBlock()
    {
        $label = __( $this->campaign->labels[ 'days' ], 'hurrytimer' );
        $label = apply_filters( "hurryt_{$this->campaign->get_id()}_campaign_timer_days_label",
            $label, $this->campaign );

        $label = apply_filters( 'hurryt_days_label', $label, $this->campaign->get_id() );
        return $this->campaign->daysVisibility === C::YES
            ? $this->block( "%D", $label )
            : '';
    }

    /**
     * Returns hours block.
     *
     * @return string
     */
    public function hoursBlock()
    {
        $label = __( $this->campaign->labels[ 'hours' ], 'hurrytimer' );

        $label = apply_filters( "hurryt_{$this->campaign->get_id()}_campaign_timer_hours_label",
            $label, $this->campaign );

        $label = apply_filters( 'hurryt_hours_label', $label, $this->campaign->get_id() );

        return $this->campaign->hoursVisibility === C::YES
            ? $this->block( "%H", $label )
            : '';
    }

    /**
     * Returns minutes block.
     *
     * @return string
     */
    public function minutesBlock()
    {
        $label = __( $this->campaign->labels[ 'minutes' ], 'hurrytimer' );

        $label = apply_filters( "hurryt_{$this->campaign->get_id()}_campaign_timer_minutes_label",
            $label, $this->campaign );

        $label = apply_filters( 'hurryt_minutes_label', $label, $this->campaign->get_id() );

        return $this->campaign->minutesVisibility === C::YES
            ? $this->block( "%M", $label )
            : '';

    }

    /**
     * Returns seconds block.
     *
     * @return string
     */
    public function secondsBlock()
    {

        $label = __( $this->campaign->labels[ 'seconds' ], 'hurrytimer' );

        $label = apply_filters( "hurryt_{$this->campaign->get_id()}_campaign_timer_seconds_label",
            $label, $this->campaign );

        $label = apply_filters( 'hurryt_seconds_label', $label, $this->campaign->get_id() );

        return $this->campaign->secondsVisibility === C::YES
            ? $this->block( "%S", $label )
            : '';

    }

    /**
     * Returns block.
     *
     * @param $digitFormat
     * @param $label
     *
     * @return string
     */
    public function block( $digitFormat, $label )
    {
        return '<div class="hurrytimer-timer-block ' . $this->legacyBlockClass() . '">'
            . $this->digit( $digitFormat )
            . $this->label( $label )
            . '</div>';
    }

    public function digit( $format )
    {
        return '<div class="hurrytimer-timer-digit ' . $this->legacyDigitClass() . '">' . $format
            . '</div>';
    }

    public function label( $text )
    {
        if ( $this->campaign->labelVisibility === "no" ) {
            return '';
        }

        return '<div class="hurrytimer-timer-label ' . $this->legacyLabelClass() . '" >'
            . __( $text, 'hurrytimer' ) . '</div>';

    }

    public function headline()
    {

        $headline = __( $this->campaign->headline, 'hurrytimer' );

        /**
         * Instead of this filter
         * @deprecated
         */
        $headline = __( apply_filters( "hurryt_{$this->campaign->get_id()}_campaign_headline",
            $headline, $this->campaign ), 'hurrytimer' );

        /**
         * use this one.
         */
        $headline = __( apply_filters( "hurryt_campaign_headline",
            $headline, $this->campaign->get_id() ), 'hurrytimer' );

        $headline = nl2br( Placeholder_Factory::parse( $headline, $this->campaign ) );

        return $this->campaign->headlineVisibility === C::YES
            ? '<div class="' . $this->legacyHeadlineClass() . ' hurrytimer-headline">'
            . $headline . '</div>'
            : '';

    }

    public function callToActionButton()
    {
        if ( $this->campaign->callToAction[ 'enabled' ] === C::NO ) {
            return '';
        }

        $cta_text = $this->campaign->callToAction[ 'text' ];

        $cta_url = $this->campaign->callToAction[ 'url' ];

        $target = $this->campaign->callToAction[ 'new_tab' ] === C::YES ? '_blank' : '_self';
        $template = "<a class='hurrytimer-button' target='" . $target . "' href='" . $cta_url
            . "' >" . $cta_text . "</a>";

        return "<div class='hurrytimer-button-wrap'>" . $template . "</div>";

    }

    public function stickyBarCloseButton()
    {
        if ( $this->campaign->stickyBarDismissible === C::NO
            || isset( $_COOKIE[ '_dismissed_sticky_' . $this->campaign->get_id() ] )
        ) {
            return '';
        }

        if ( $this->campaign->stickyBarDismissible === C::YES ) {
            return '<button type="button" class="hurrytimer-sticky-close"><svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 357 357">
<polygon points="357,35.7 321.3,0 178.5,142.8 35.7,0 0,35.7 142.8,178.5 0,321.3 35.7,357 178.5,214.2 321.3,357 357,321.3
        214.2,178.5"/></svg></button>';

        }

    }

}
