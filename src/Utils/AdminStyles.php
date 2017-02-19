<?php
namespace EvolveEngine\Utils;

class AdminStyles
{

    // Make sure only loaded max once
    protected static $loadedCustomACFStyle = false;

    /**
     * Remove unused metaboxes
     *
     * @return void
     */
    public function removeMetaBoxes()
    {
        // Hide metaboxes
        remove_meta_box('revisionsdiv', 'page', 'normal');
        remove_meta_box('commentstatusdiv', 'page', 'normal');
    }

    /**
     * Generates footer link to admin screen (wordpress backend)
     *
     * @return void
     */
    public function trueFooter()
    {
        ?>
            <style>
                #wpfooter a {
                    outline: none !important; text-decoration: none !important;
                } 
                .wp-footer-true-link {
                    position: relative;
                    display: block; color: #aeaeae;
                    -webkit-transition: color 0.22s ease;
                    -o-transition: color 0.22s ease;
                    transition: color 0.22s ease;
                }
                .wp-footer-true-link:before {
                    content: '';
                    position: absolute;
                    bottom: 0; margin-left: 50%;
                    width: 0%; height: 1px;
                    background-color: #333;
                    -webkit-transition: width 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3),
                                margin-left 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3);
                    -o-transition: width 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3),
                                margin-left 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3);
                    transition: width 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3),
                                margin-left 0.28s cubic-bezier(0.63, 0.62, 0.48, 1.3);
                }
                .wp-footer-true-link > img {
                    width: 45px; height: auto; position: relative; top: 2px;
                    opacity: 0.7;
                    -webkit-transition: opacity 0.22s ease;
                    -o-transition: opacity 0.22s ease;
                    transition: opacity 0.22s ease;
                }
                .wp-footer-true-link:hover {
                    color: #333;
                }
                .wp-footer-true-link:hover:before {
                    width: 100%; margin-left: 0%;
                }
                .wp-footer-true-link:hover > img {
                    opacity: 1;
                }
                .acf-field.acf-field-image.acf-banner-image .acf-image-uploader .hide-if-value {
                    margin-top: 50px;
                }
                .acf-field.acf-field-image.acf-banner-image .acf-image-uploader.has-value {
                    min-height: 100px;
                }
                .acf-field.acf-field-image.acf-banner-image .view.show-if-value {
                    position: absolute;
                    z-index: 100;
                    height: auto;
                    max-height: 100px;
                    overflow: hidden;
                    -webkit-box-shadow: 1px 3px 12px transparent;
                    box-shadow: 1px 3px 12px transparent;
                    -webkit-transition: height .32s ease, max-height .32s ease, box-shadow .32s ease;
                    -o-transition: height .32s ease, max-height .32s ease, box-shadow .32s ease;
                    transition: height .32s ease, max-height .32s ease, box-shadow .32s ease
                }
                .acf-field.acf-field-image.acf-banner-image .view.show-if-value img {
                    -webkit-transform: translate(0, -30px);
                    -ms-transform: translate(0, -30px);
                    -o-transform: translate(0, -30px);
                    transform: translate(0, -30px);
                    -webkit-transition: transform .32s ease;
                    -o-transition: transform .32s ease;
                    transition: transform .32s ease
                }
                .acf-field.acf-field-image.acf-banner-image .view.show-if-value:after {
                    content: '';
                    position: absolute;
                    height: 20px;
                    width: 100%;
                    bottom: 0;
                    left: 0;
                    background-image: -webkit-linear-gradient(top, transparent 0, rgba(0, 0, 0, .25) 100%);
                    background-image: -o-linear-gradient(top, transparent 0, rgba(0, 0, 0, .25) 100%);
                    background-image: linear-gradient(to bottom, transparent 0, rgba(0, 0, 0, .25) 100%);
                    background-repeat: repeat-x;
                    opacity: 1;
                    filter: alpha(opacity=100);
                    -webkit-transition: opacity .01s linear .32s;
                    -o-transition: opacity .01s linear .32s;
                    transition: opacity .01s linear .32s
                }
                .acf-field.acf-field-image.acf-banner-image .view.show-if-value:hover {
                    max-height: 470px;
                    -webkit-box-shadow: 1px 3px 12px rgba(0, 0, 0, .45);
                    box-shadow: 1px 3px 12px rgba(0, 0, 0, .45)
                    z-index: 1000;
                }
                .acf-field.acf-field-image.acf-banner-image .view.show-if-value:hover img {
                    -webkit-transform: translate(0, 0);
                    -ms-transform: translate(0, 0);
                    -o-transform: translate(0, 0);
                    transform: translate(0, 0)
                }
                .acf-field.acf-field-image.acf-banner-image .view.show-if-value:hover:after {
                    opacity: 0;
                    filter: alpha(opacity=0);
                    -webkit-transition: opacity .01s;
                    -o-transition: opacity .01s;
                    transition: opacity .01s
                }
            </style>
            <a href="http://www.trueagency.com.au" target="_blank" class="wp-footer-true-link">
                <img src="<?= \TrueLib::getImageURL('common/true-footer-logo.png') ?>" alt="Digital Agency Melbourne">
            </a>
        <?php
    }

    /**
     * Show recommended size in prettier way
     *
     * Usage: [widthxheight]
     * E.g.: [300x500]
     * 
     */
    public function renderSize($field)
    {
        preg_match_all("/\[(.*?)\]/", $field['instructions'], $matches);

        if (count($matches) > 1) {
            if (count($matches[1]) > 0) {
                $match = $matches[1][0];
                $orig = '['.$match.']';

                $sizeArr = explode('x', $match);
                if (count($sizeArr) !== 2) {
                    return $field;
                }
                $width = $sizeArr[0];
                $height = $sizeArr[1];

                $render = '<span class="acf-recommended-size"> <span>'.$width.'px</span><span>'.$height.'px</span></span>';
                $field['instructions'] = str_replace($orig, $render, $field['instructions']);
            }
        }

        return $field;
    }

    /**
     * Custom admin styling for login
     * 
     * @return void
     */
    public function loginStyles()
    {
        ?>
        <style type="text/css">
            body {
                position: relative;
                background-color: #fff;
                background-position: bottom center;
                background-repeat: no-repeat;
            }
            h1 a
            {
                /** Change width and height according to logo */
                width:200px !important; height:100px !important;
                background: url('<?= \TrueLib::getImageURL('logoLogin.png')?>') no-repeat center center !important;
                -webkit-background-size: auto auto !important; background-size: auto auto !important;
            }
        </style>
        <?php
    }

    /**
     * Custom ACF style modifications
     *
     * @return void
     */
    public function customACFStyle()
    {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
            return;
        }
        
        if (!self::$loadedCustomACFStyle) {
        ?>
            <style>
            .acf-field.field_type-message {
                padding: 15px 0px 0px !important;
                width: 100%;
            }
            .acf-field.field_type-message .acf-label {
                margin-bottom: 0px; display: inline-block; padding: 5px 10px; background-color: rgba(59, 150, 170, 1); color: #fff;
            }
            .acf-field.field_type-message .acf-input h4,
            .acf-field.field_type-message .acf-input h5,
            .acf-field.field_type-message .acf-input p
             {
                margin-top: 0px; margin-bottom: 0px;
                padding: 10px 15px; background-color: #2DA7AD; color: #fff;
            }
            .acf-image-uploader {
                padding-top: 50px;
            }
            span.acf-recommended-size {
                background-color: #3498db; color: #fff; position: absolute; display: inline-block; z-index: 101;
                padding: 0px 0px 0px 30px; margin-top: 10px;
                line-height: 30px; height: 30px; padding-bottom: 0px;
                -webkit-transition: padding-bottom 0.2s ease; -o-transition: padding-bottom 0.2s ease; transition: padding-bottom 0.2s ease;
            }

            span.acf-recommended-size:before {
                position: absolute; content: '';
                width: 30px; height: 30px;
                background-image: url("<?= asset('img/common/icon-size.png') ?>");
                background-position: center center; background-repeat: no-repeat;
                top: 0px; left: 0px;
                background-color: #2980b9;
                 -webkit-transition: padding-bottom 0.2s ease; -o-transition: padding-bottom 0.2s ease; transition: padding-bottom 0.2s ease;
            }

            @media only screen and (-webkit-min-device-pixel-ratio: 2),
            only screen and (   min--moz-device-pixel-ratio: 2),
            only screen and (     -o-min-device-pixel-ratio: 2/1),
            only screen and (        min-device-pixel-ratio: 2),
            only screen and (                min-resolution: 192dpi),
            only screen and (                min-resolution: 2dppx) {
                span.acf-recommended-size:before {
                    background-image: url("<?= asset('img/common/icon-size.png') ?>");
                    background-size: 25px 25px;
                    -webkit-background-size: 25px 25px;
                }
            }
            span.acf-recommended-size > span {
                line-height: 30px; height: 30px; width: 50px; text-align: center;
                display: inline-block; position: relative;
            }

            span.acf-recommended-size > span:before {
                position: absolute; content: 'Width';
                line-height: 24px; height: 24px; width: 50px; text-align: center;
                bottom: -24px; left: 0px; width: 50px;
                background-color: #1abc9c; opacity: 0;
                -webkit-transition: opacity 0.2s ease 0s; -o-transition: opacity 0.2s ease 0s; transition: opacity 0.2s ease 0s;
            }

            span.acf-recommended-size > span + span {
                background-color: #52ADEA;
            }

            span.acf-recommended-size > span + span:before {
                background-color: #37BCA1; content: 'Height';
            }

            span.acf-recommended-size:hover {
                padding-bottom: 24px;
            }

            span.acf-recommended-size:hover:before {
                padding-bottom: 24px;
            }
            span.acf-recommended-size:hover > span:before {
                opacity: 1;
                -webkit-transition: opacity 0.2s ease 0.08s; -o-transition: opacity 0.2s ease 0.08s; transition: opacity 0.2s ease 0.08s;
            }
            .acf-image-uploader.has-value {
                padding-top: 0px;
            }
            </style>
        <?php
        }
        self::$loadedCustomACFStyle = true;
    }

    /**
     * Add more dashboard widgets
     */
    public function addDashWidgets()
    {
        wp_add_dashboard_widget('custom_help_widget_2017', 'True Agency', [$this, 'brandDashboard']);
    }

    /**
     * Brand dashboard to our liking
     *
     * @return void
     */
    public function brandDashboard()
    {
        $trueSettingsDir = plugins_url() . '/trueagency-options/modules/true-settings';
        echo '<div class="true_widget_logo" style="float:left; width:190px;"><a href="http://www.trueagency.com.au"><img alt="True Agency" src="' . $trueSettingsDir . '/images/logo.png" /></a></div><div class="true_widget_detail" style="font-family:helvetica;min-height:170px;"><strong>Welcome to your Dashboard</strong><br/><br/>For support, please contact us by emailing <a href="mailto:support@trueagency.com.au">support@trueagency.com.au</a> or calling 03 9529 1850. Please note that support for any plugins/extensions/code not implemented by True Agency will be quoted separately.<br/><br/><a href="http://www.trueagency.com.au">True Agency</a> specialise in websites, ecommerce and mobile apps.</div>';  
    }
}

